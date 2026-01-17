<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\TimingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::resource('timings', TimingController::class)->names('timings');
Route::resource('clients', ClientController::class)->names('clients');
Route::resource('bookings', BookingController::class)->names('bookings');

require __DIR__.'/settings.php';
