<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\PageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Default halaman → login
Route::get('/', [PageController::class, 'loginPage']);

// =====================
// PAGE (VIEW)
// =====================
Route::get('/register', [PageController::class, 'registerPage']);
Route::get('/login', [PageController::class, 'loginPage']);
Route::get('/register-page', [PageController::class, 'registerPage']);
Route::get('/login-page', [PageController::class, 'loginPage']);
Route::get('/dashboard', [PageController::class, 'dashboard']);


// =====================
// API (LOGIC)
// =====================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (JWT)
Route::middleware('jwt.auth')->group(function () {
	Route::get('/donations/history', [DonationController::class, 'history']);
});

// Admin-only route
Route::middleware(['jwt.auth', 'admin'])->group(function () {
	Route::post('/verify/{id}', [AuthController::class, 'verify']);
});