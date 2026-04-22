<?php

use App\Http\Controllers\DonationController;
use Illuminate\Support\Facades\Route;

Route::post('/donations', [DonationController::class, 'store']);
Route::get('/campaigns/{campaignId}/donations/total', [DonationController::class, 'campaignTotal']);