<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DonationCategory;
use App\Repositories\DonationCategoryRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DonationCategoryController extends Controller
{
    public function __construct(
        private readonly DonationCategoryRepositoryInterface $categoryRepository
    ) {
    }

    public function index(): JsonResponse
    {
        $categories = $this->categoryRepository->getAll();

        return response()->json($categories);
    }

    public function show(DonationCategory $category): JsonResponse
    {
        return response()->json($category);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $category = $this->categoryRepository->create($validated);

        return response()->json($category, 201);
    }

    public function update(Request $request, DonationCategory $category): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $this->categoryRepository->update($category, $validated);

        return response()->json($category->fresh());
    }

    public function destroy(DonationCategory $category): JsonResponse
    {
        $this->categoryRepository->delete($category);

        return response()->json([
            'message' => 'Kategori donasi berhasil dihapus.',
        ]);
    }
}