<?php

use App\Http\Controllers\TimingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::resource('timings', TimingController::class)->names('timings');

require __DIR__.'/settings.php';
