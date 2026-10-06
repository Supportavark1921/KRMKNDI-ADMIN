<?php

use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\SamagriController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\AvailabilityController;
use App\Http\Controllers\Api\DonationController;
use App\Http\Controllers\Api\FcmTokenController;
use App\Http\Controllers\Api\GuruController;
use App\Http\Controllers\Api\NotificationController;
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

// Samagri (shop)
Route::prefix('v1/samagri')->name('samagri.')->group(function () {
    Route::get('/categories',  [SamagriController::class, 'categories'])->name('categories');
    Route::get('/products',    [SamagriController::class, 'index'])->name('index');
    Route::get('/products/{product}', [SamagriController::class, 'show'])->name('show');
});

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
Route::get('/donation-categories/{category}', [DonationController::class, 'category']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/donations',  [DonationController::class, 'index']);
    Route::post('/donations', [DonationController::class, 'store']);
});

// FCM device token — public POST so anonymous (pre-login) devices can register.
// user_id is linked when a bearer token is present; null otherwise.
Route::post('/v1/fcm-token', [FcmTokenController::class, 'store']);

// Admin: send push notifications (role-gated on controller/policy level)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/admin/notifications/send', [NotificationController::class, 'send']);
});
