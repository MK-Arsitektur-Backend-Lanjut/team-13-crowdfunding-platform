<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Repositories\CampaignRepositoryInterface;
use App\Services\DonationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CampaignController extends Controller
{
    public function __construct(
        private readonly CampaignRepositoryInterface $campaignRepository,
        private readonly DonationService $donationService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $perPage = max(1, min($perPage, 100));

        $campaigns = $this->campaignRepository->getAll($perPage);

        return response()->json($campaigns);
    }

    public function show(int $campaign): JsonResponse
    {
        return response()->json(
            $this->campaignRepository->findWithTotalDonations($campaign)
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'         => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'target_amount' => ['required', 'numeric', 'min:0'],
            'status'        => ['nullable', Rule::in(['aktif', 'selesai'])],
        ]);

        if (array_key_exists('status', $validated) && $validated['status'] === null) {
            unset($validated['status']);
        }

        $campaign = $this->campaignRepository->create($validated);

        return response()->json($this->enrichWithTotal($campaign), 201);
    }

    public function update(Request $request, Campaign $campaign): JsonResponse
    {
        $validated = $request->validate([
            'title'         => ['sometimes', 'required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'target_amount' => ['sometimes', 'required', 'numeric', 'min:0'],
            'status'        => ['sometimes', Rule::in(['aktif', 'selesai'])],
        ]);

        $this->campaignRepository->update($campaign, $validated);

        return response()->json($this->enrichWithTotal($campaign->fresh()));
    }

    public function destroy(Campaign $campaign): JsonResponse
    {
        $this->campaignRepository->delete($campaign);

        return response()->json([
            'message' => 'Kampanye berhasil dihapus.',
        ]);
    }

    public function updateStatus(Request $request, Campaign $campaign): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['aktif', 'selesai'])],
        ]);

        $this->campaignRepository->updateStatus($campaign, $validated['status']);

        return response()->json($this->enrichWithTotal($campaign->fresh()));
    }

    private function enrichWithTotal(Campaign $campaign): Campaign
    {
        $campaign->total_donations = $this->donationService->getCampaignTotal($campaign->id);

        return $campaign;
    }

    public function getByStatus(Request $request, string $status): JsonResponse
    {
        if (! in_array($status, ['aktif', 'selesai'], true)) {
            return response()->json([
                'message' => 'Status tidak valid. Gunakan aktif atau selesai.',
            ], 422);
        }

        $perPage = (int) $request->query('per_page', 15);
        $perPage = max(1, min($perPage, 100));

        $campaigns = $this->campaignRepository->getByStatus($status, $perPage);

        return response()->json($campaigns);
    }
}