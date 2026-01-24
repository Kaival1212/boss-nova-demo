<?php

use App\Http\Controllers\ApiBookingController;
use Illuminate\Support\Facades\Route;

Route::get('/booking.available.times', [ApiBookingController::class, 'getLatestAvailableBookingTimes'])->name('api.booking.available.times');
Route::get('/doctors.list', [ApiBookingController::class, 'getdoctors'])->name('api.doctors.list');
Route::post('/booking.create', [ApiBookingController::class, 'createBooking'])->name('api.booking.create');

Route::get('/booking.get', [ApiBookingController::class, 'getBooking'])->name('api.booking.get');
