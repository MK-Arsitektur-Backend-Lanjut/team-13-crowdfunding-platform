<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CampaignController;
use App\Http\Controllers\Api\DonationCategoryController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/refresh', [AuthController::class, 'refresh'])->middleware('jwt.auth');
Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('jwt.auth');
Route::post('/auth/verify/{id}', [AuthController::class, 'verify'])->middleware(['jwt.auth', 'admin']);

/*
|--------------------------------------------------------------------------
| CAMPAIGN ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('campaigns')->middleware('throttle:1000,1')->group(function (): void {
    Route::get('/', [CampaignController::class, 'index']);
    Route::get('/status/{status}', [CampaignController::class, 'getByStatus']);
    Route::post('/', [CampaignController::class, 'store']);

    Route::get('/{campaign}/donations/total', [DonationController::class, 'campaignTotal']);
    Route::get('/{campaign}', [CampaignController::class, 'show']);
    Route::put('/{campaign}', [CampaignController::class, 'update']);
    Route::delete('/{campaign}', [CampaignController::class, 'destroy']);
    Route::patch('/{campaign}/status', [CampaignController::class, 'updateStatus']);
});

/*
|--------------------------------------------------------------------------
| DONATION CATEGORY ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('donation-categories')->group(function (): void {
    Route::get('/', [DonationCategoryController::class, 'index']);
    Route::get('/{category}', [DonationCategoryController::class, 'show']);
    Route::post('/', [DonationCategoryController::class, 'store']);
    Route::put('/{category}', [DonationCategoryController::class, 'update']);
    Route::delete('/{category}', [DonationCategoryController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| DONATION ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/donations/stats', [DonationController::class, 'stats']);

Route::middleware('throttle:1000,1')->group(function () {
    Route::post('/donations', [DonationController::class, 'store']);
});

Route::prefix('donations')->middleware('jwt.auth')->group(function () {
    Route::get('/history', [DonationController::class, 'history']);
    Route::get('/history/{id}', [DonationController::class, 'show']);
    Route::delete('/history/{id}', [DonationController::class, 'destroy']);
});