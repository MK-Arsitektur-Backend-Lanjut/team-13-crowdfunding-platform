<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:donor,organizer',
        ]);

        $payload = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_verified' => false,
        ];

        $user = $this->userRepository->create($payload);

        return response()->json([
            'message' => 'User registered successfully',
            'data' => $user
        ], 201);
    }

    /**
     * LOGIN USER (JWT)
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = $this->userRepository->findByEmail($request->email);

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'error' => 'Invalid credentials'
            ], 401);
        }

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user
        ]);
    }

    /**
     * VERIFIKASI AKUN ORGANIZER
     */
    public function verify(int $id): JsonResponse
    {
        $actor = Auth::user();

        if (!$actor || $actor->role !== 'admin') {
            return response()->json([
                'error' => 'Forbidden: admin access required',
            ], 403);
        }

        $user = $this->userRepository->findById($id);

        if (!$user) {
            return response()->json([
                'error' => 'User not found'
            ], 404);
        }

        if ($user->role !== 'organizer') {
            return response()->json([
                'error' => 'User is not an organizer'
            ], 403);
        }

        if ($user->is_verified) {
            return response()->json([
                'message' => 'Organizer already verified',
                'data' => $user,
            ]);
        }

        $user->is_verified = true;
        $user->save();

        return response()->json([
            'message' => 'Organizer verified successfully',
            'data' => $user
        ]);
    }
}
