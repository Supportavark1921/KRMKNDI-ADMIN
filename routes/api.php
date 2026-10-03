<?php

use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\AvailabilityController;
use App\Http\Controllers\Api\DonationController;
use App\Http\Controllers\Api\GuruController;
use App\Http\Controllers\Api\OpenApiController;
use App\Http\Controllers\Api\PanchangController;
use App\Http\Controllers\Api\PromotionsApiController;
use App\Http\Controllers\Api\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/openapi.json', [OpenApiController::class, 'spec']);

// Auth — OTP send/verify are public; logout + me require a valid token
Route::prefix('auth')->group(function () {
    Route::post('/otp/send',   [AuthController::class, 'sendOtp']);
    Route::post('/otp/verify', [AuthController::class, 'verifyOtp']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/refresh',       [AuthController::class, 'refresh']);
        Route::post('/logout',        [AuthController::class, 'logout']);
        Route::get('/me',             [AuthController::class, 'me']);
        Route::patch('/profile',      [AuthController::class, 'updateProfile']);
    });
});

// Panchang
Route::get('/v1/panchang', [PanchangController::class, 'show']);

// Promotions
Route::get('/v1/promotions', [PromotionsApiController::class, 'index']);

// Availability
Route::get('/availability/{month}', [AvailabilityController::class, 'month']);
Route::get('/availability/{date}/slots', [AvailabilityController::class, 'slots']);

// Services
Route::get('/services', [ServiceController::class, 'index']);
Route::get('/services/{service}', [ServiceController::class, 'show']);

// Booking charges (public) + appointments (auth required)
Route::get('/booking/charges', [BookingController::class, 'charges']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/appointments', [AppointmentController::class, 'store']);
    Route::get('/appointments',  [AppointmentController::class, 'index']);
});

// Gurus & donations
Route::get('/gurus', [GuruController::class, 'index']);
Route::get('/gurus/{guru}', [GuruController::class, 'show']);
Route::get('/gurus/{guru}/donation-categories', [GuruController::class, 'categories']);
Route::get('/donation/fee-config', [DonationController::class, 'feeConfig']);
Route::post('/donations', [DonationController::class, 'store']);
