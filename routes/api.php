<?php

use App\Http\Controllers\Api\CampaignController;
use App\Http\Controllers\Api\DonationCategoryController;
use App\Http\Controllers\DonationController;
use Illuminate\Support\Facades\Route;

Route::prefix('campaigns')->group(function (): void {
    Route::get('/', [CampaignController::class, 'index']);
    Route::get('/status/{status}', [CampaignController::class, 'getByStatus']);

    Route::post('/', [CampaignController::class, 'store']);

    Route::get('/{campaign}/donations/total', [DonationController::class, 'campaignTotal']);
    Route::get('/{campaign}', [CampaignController::class, 'show']);
    Route::put('/{campaign}', [CampaignController::class, 'update']);
    Route::delete('/{campaign}', [CampaignController::class, 'destroy']);
    Route::patch('/{campaign}/status', [CampaignController::class, 'updateStatus']);
});

Route::prefix('donation-categories')->group(function (): void {
    Route::get('/', [DonationCategoryController::class, 'index']);

    Route::get('/{category}', [DonationCategoryController::class, 'show']);
    Route::post('/', [DonationCategoryController::class, 'store']);
    Route::put('/{category}', [DonationCategoryController::class, 'update']);
    Route::delete('/{category}', [DonationCategoryController::class, 'destroy']);
});

Route::post('/donations', [DonationController::class, 'store']);
