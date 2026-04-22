<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/welcome', function () {
    return view('welcome');
});
