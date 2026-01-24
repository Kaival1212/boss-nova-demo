<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::resource('clients', ClientController::class)->names('clients');
Route::resource('bookings', BookingController::class)->names('bookings');
// Route::resource('users', UserController::class)->names('users');


Route::prefix('users')
    ->middleware(['auth', 'verified'])
    ->name('users.')
    ->group(function () {
        Route::livewire('/', 'pages::user.⚡index')->name('index');
        Route::livewire('/add', 'pages::user.⚡create')->name('create');
        Route::livewire('/{user}', 'pages::user.⚡show')->name('show');
    });

require __DIR__.'/settings.php';
