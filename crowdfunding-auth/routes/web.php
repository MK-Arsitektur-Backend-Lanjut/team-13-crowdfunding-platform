<?php
use Illuminate\Support\Facades\Route;
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