<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Repositories\CampaignRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CampaignController extends Controller
{
    public function __construct(
        private readonly CampaignRepositoryInterface $campaignRepository
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $page = (int) $request->query('page', 1);
        $campaigns = $this->campaignRepository->getAll($page);

        return response()->json([
            'data' => $campaigns->items(),
            'meta' => [
                'current_page' => $campaigns->currentPage(),
                'last_page' => $campaigns->lastPage(),
                'per_page' => $campaigns->perPage(),
                'total' => $campaigns->total(),
            ],
        ]);
    }

    public function show(Campaign $campaign): JsonResponse
    {
        return response()->json($campaign);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'target_amount' => ['required', 'numeric', 'min:0'],
            'status' => ['nullable', Rule::in(['aktif', 'selesai'])],
        ]);

        if (array_key_exists('status', $validated) && $validated['status'] === null) {
            unset($validated['status']);
        }

        $campaign = $this->campaignRepository->create($validated);

        return response()->json($campaign, 201);
    }

    public function update(Request $request, Campaign $campaign): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'target_amount' => ['sometimes', 'required', 'numeric', 'min:0'],
            'status' => ['sometimes', Rule::in(['aktif', 'selesai'])],
        ]);

        $this->campaignRepository->update($campaign, $validated);

        return response()->json($campaign->fresh());
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

        return response()->json($campaign->fresh());
    }

    public function getByStatus(Request $request, string $status): JsonResponse
    {
        if (! in_array($status, ['aktif', 'selesai'], true)) {
            return response()->json([
                'message' => 'Status tidak valid. Gunakan aktif atau selesai.',
            ], 422);
        }

        $page = (int) $request->query('page', 1);
        $campaigns = $this->campaignRepository->getByStatus($status, $page);

        return response()->json([
            'data' => $campaigns->items(),
            'meta' => [
                'current_page' => $campaigns->currentPage(),
                'last_page' => $campaigns->lastPage(),
                'per_page' => $campaigns->perPage(),
                'total' => $campaigns->total(),
            ],
        ]);
    }
}