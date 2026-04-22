<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDonationRequest;
use App\Services\DonationService;
use Illuminate\Http\JsonResponse;

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
}