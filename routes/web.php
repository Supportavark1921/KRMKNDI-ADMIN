<?php

use App\Http\Controllers\ApiDocsController;
use App\Http\Controllers\DonationCategoryController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\DonationFeeController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ClientProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => auth()->check() ? redirect()->route('dashboard') : redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/book', [AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::patch('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.status');
    Route::get('/availability', [AvailabilityController::class, 'index'])->name('availability.index');
    Route::put('/availability', [AvailabilityController::class, 'update'])->name('availability.update');
    Route::get('/available-times', [AvailabilityController::class, 'times'])->name('availability.times');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
    Route::get('/profile', [ClientProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ClientProfileController::class, 'update'])->name('profile.update');
    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
    Route::get('/services/create', [ServiceController::class, 'create'])->name('services.create');
    Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
    Route::get('/services/{service}', [ServiceController::class, 'show'])->name('services.show');
    Route::get('/services/{service}/edit', [ServiceController::class, 'edit'])->name('services.edit');
    Route::put('/services/{service}', [ServiceController::class, 'update'])->name('services.update');
    Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');
    Route::get('/clients', [ClientProfileController::class, 'clients'])->name('clients.index');
    Route::get('/clients/{user}', [ClientProfileController::class, 'show'])->name('clients.show');
    Route::put('/clients/{user}/notes', [ClientProfileController::class, 'notes'])->name('clients.notes');
    Route::get('/api-docs', [ApiDocsController::class, 'index'])->name('api.docs');

    // Guruji management
    Route::resource('gurus', GuruController::class);

    // Donation categories
    Route::resource('donation-categories', DonationCategoryController::class)->except(['show']);

    // Donation listing & detail (admin read-only)
    Route::get('/donations', [DonationController::class, 'index'])->name('donations.index');
    Route::get('/donations/{donation}', [DonationController::class, 'show'])->name('donations.show');

    // Donation fee settings
    Route::get('/donation-fees', [DonationFeeController::class, 'index'])->name('donation-fees.index');
    Route::put('/donation-fees', [DonationFeeController::class, 'update'])->name('donation-fees.update');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
