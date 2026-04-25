<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDonationRequest;
use App\Models\Donation;
use App\Services\DonationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DonationController extends Controller
{
    public function __construct(private readonly DonationService $donationService)
    {
    }

    public function store(StoreDonationRequest $request): JsonResponse
    {
        $donation = $this->donationService->processDonation(
            $request->validated(),
            $request->header('X-Idempotency-Key')
        );

        return response()->json([
            'message' => 'Donation processed successfully',
            'data' => $donation,
        ], 201);
    }

    public function campaignTotal(int $campaignId): JsonResponse
    {
        $total = $this->donationService->getCampaignTotal($campaignId);

        return response()->json([
            'campaign_id' => $campaignId,
            'total_donations' => $total,
        ]);
    }

    public function stats(): JsonResponse
    {
        $activeDonors = DB::table('users')
            ->where('role', 'donor')
            ->where('is_verified', true)
            ->whereExists(function ($query): void {
                $query->select(DB::raw(1))
                    ->from('donations')
                    ->whereColumn('donations.user_id', 'users.id')
                    ->where('donations.status', 'success');
            })
            ->count();

        $seededActiveDonors = DB::table('users')
            ->where('email', 'like', 'donor%@seed.local')
            ->where('role', 'donor')
            ->where('is_verified', true)
            ->count();

        $totalDonations = DB::table('donations')
            ->where('status', 'success')
            ->count();

        return response()->json([
            'active_donors' => (int) $activeDonors,
            'seeded_active_donors' => (int) $seededActiveDonors,
            'total_success_donations' => (int) $totalDonations,
        ]);
    }

    public function history(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        $validated = $request->validate([
            'campaign_id' => 'nullable|integer|min:1',
            'min_amount' => 'nullable|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0',
            'sort_by' => 'nullable|in:id,campaign_id,amount,created_at',
            'sort_dir' => 'nullable|in:asc,desc',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $query = Donation::query()->where('user_id', $user->id);

        if (isset($validated['campaign_id'])) {
            $query->where('campaign_id', $validated['campaign_id']);
        }

        if (isset($validated['min_amount'])) {
            $query->where('amount', '>=', $validated['min_amount']);
        }

        if (isset($validated['max_amount'])) {
            $query->where('amount', '<=', $validated['max_amount']);
        }

        $sortBy = $validated['sort_by'] ?? 'created_at';
        $sortDir = $validated['sort_dir'] ?? 'desc';
        $perPage = $validated['per_page'] ?? 10;

        $donations = $query
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage)
            ->appends($request->query());

        return response()->json([
            'message' => 'Donation history fetched successfully',
            'data' => $donations->items(),
            'meta' => [
                'current_page' => $donations->currentPage(),
                'last_page' => $donations->lastPage(),
                'per_page' => $donations->perPage(),
                'total' => $donations->total(),
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        $donation = Donation::query()
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$donation) {
            return response()->json([
                'message' => 'Donation not found',
            ], 404);
        }

        return response()->json([
            'message' => 'Donation detail fetched successfully',
            'data' => $donation,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        $donation = Donation::query()
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$donation) {
            return response()->json([
                'message' => 'Donation not found',
            ], 404);
        }

        $donation->delete();

        return response()->json([
            'message' => 'Donation deleted successfully',
        ]);
    }
}