<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;

Route::get('/', function () {
    return view('dashboard');
});

Route::get('/campaigns', function () {
    return view('dashboard');
})->name('campaign.dashboard');

Route::get('/donation-processing', function () {
    return view('donation-processing');
})->name('donation.processing');

Route::get('/donations', function () {
    return view('donation-processing');
})->name('donation.module');

Route::get('/donation-categories', function () {
    return view('donation-categories');
})->name('donation.categories');

Route::get('/welcome', function () {
    return view('welcome');
});

Route::prefix('auth')->group(function () {
    Route::get('/register', [PageController::class, 'registerPage'])->name('auth.register');
    Route::get('/login', [PageController::class, 'loginPage'])->name('auth.login');
    Route::get('/dashboard', [PageController::class, 'dashboard'])->name('auth.dashboard');
});
