<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DonationController extends Controller
{
    public function history(): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        $donations = Donation::query()
            ->where('user_id', $user->id)
            ->latest('id')
            ->get();

        return response()->json([
            'message' => 'Donation history fetched successfully',
            'data' => $donations,
        ]);
    }
}