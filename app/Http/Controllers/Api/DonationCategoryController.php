<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\DonationCategoryRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DonationCategoryController extends Controller
{
    public function __construct(
        private readonly DonationCategoryRepositoryInterface $categoryRepository
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $page = (int) $request->query('page', 1);
        $categories = $this->categoryRepository->getAll($page);

        return response()->json([
            'data' => $categories->items(),
            'meta' => [
                'current_page' => $categories->currentPage(),
                'last_page' => $categories->lastPage(),
                'per_page' => $categories->perPage(),
                'total' => $categories->total(),
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $category = $this->categoryRepository->findById($id);

        if ($category === null) {
            return response()->json(['message' => 'Kategori tidak ditemukan.'], 404);
        }

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

    public function update(Request $request, int $id): JsonResponse
    {
        $category = $this->categoryRepository->findById($id);

        if ($category === null) {
            return response()->json(['message' => 'Kategori tidak ditemukan.'], 404);
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $this->categoryRepository->update($category, $validated);

        return response()->json($this->categoryRepository->findById($id));
    }

    public function destroy(int $id): JsonResponse
    {
        $category = $this->categoryRepository->findById($id);

        if ($category === null) {
            return response()->json(['message' => 'Kategori tidak ditemukan.'], 404);
        }

        $this->categoryRepository->delete($category);

        return response()->json([
            'message' => 'Kategori donasi berhasil dihapus.',
        ]);
    }
}