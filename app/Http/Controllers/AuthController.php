<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    protected $userRepository;

    // Inject Repository
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * REGISTER USER
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:personal,organization,donor,organizer',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, [
                'fields' => $validator->errors(),
            ]);
        }

        $roleMap = [
            'personal' => 'personal',
            'organization' => 'organization',
            'donor' => 'personal',
            'organizer' => 'organization',
        ];

        $normalizedRole = $roleMap[strtolower((string) $request->role)] ?? 'personal';

        $payload = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $normalizedRole,
            'is_verified' => false,
        ];

        $user = $this->userRepository->create($payload);

        return $this->successResponse('User registered successfully', [
            'user' => $user,
        ], 201);
    }

    /**
     * LOGIN USER (JWT)
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, [
                'fields' => $validator->errors(),
            ]);
        }

        $user = $this->userRepository->findByEmail($request->email);

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->errorResponse('Invalid credentials', 401);
        }

        $token = JWTAuth::fromUser($user);

        return $this->successResponse('Login successful', [
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in_minutes' => JWTAuth::factory()->getTTL(),
            'user' => $user,
        ]);
    }

    /**
     * VERIFY ORGANIZATION ACCOUNT
     */
    public function verify(int $id): JsonResponse
    {
        $actor = Auth::user();

        if (!$actor || $actor->role !== 'admin') {
            return $this->errorResponse('Forbidden: admin access required', 403);
        }

        $user = $this->userRepository->findById($id);

        if (!$user) {
            return $this->errorResponse('User not found', 404);
        }

        if (!in_array($user->role, ['organization', 'organizer'], true)) {
            return $this->errorResponse('User is not an organization account', 403);
        }

        if ($user->is_verified) {
            return $this->successResponse('Organization already verified', [
                'user' => $user,
            ]);
        }

        $user->is_verified = true;
        $user->save();

        if ($user->role === 'organizer') {
            $user->role = 'organization';
        }

        return $this->successResponse('Organization verified successfully', [
            'user' => $user,
        ]);
    }

    /**
     * REFRESH TOKEN
     */
    public function refresh(): JsonResponse
    {
        try {
            $token = JWTAuth::refresh(JWTAuth::getToken());

            return $this->successResponse('Token refreshed successfully', [
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in_minutes' => JWTAuth::factory()->getTTL(),
            ]);
        } catch (JWTException $exception) {
            return $this->errorResponse('Could not refresh token', 401);
        }
    }

    /**
     * LOGOUT USER
     */
    public function logout(): JsonResponse
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return $this->successResponse('Logout successful');
        } catch (JWTException $exception) {
            return $this->errorResponse('Could not logout', 401);
        }
    }

    private function successResponse(string $message, array $data = [], int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    private function errorResponse(string $message, int $status, array $errors = []): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }
}
