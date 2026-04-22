<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DonationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public API endpoints
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected API endpoints (JWT)
Route::middleware('jwt.auth')->group(function () {
    // Auth
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Donations
    Route::get('/donations/history', [DonationController::class, 'history']);
    Route::get('/donations/history/{id}', [DonationController::class, 'show']);
    Route::delete('/donations/history/{id}', [DonationController::class, 'destroy']);

    // Admin-only
    Route::middleware('admin')->group(function () {
        Route::post('/verify/{id}', [AuthController::class, 'verify']);
    });
});