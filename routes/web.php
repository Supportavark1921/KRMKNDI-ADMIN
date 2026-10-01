<?php

use App\Http\Controllers\Admin\Location\CityAdminController;
use App\Http\Controllers\Admin\Location\CountryAdminController;
use App\Http\Controllers\Admin\Location\DistrictAdminController;
use App\Http\Controllers\Admin\Location\LocationSyncController;
use App\Http\Controllers\Admin\Location\PincodeAdminController;
use App\Http\Controllers\Admin\Location\StateAdminController;
use App\Http\Controllers\Admin\PanchangMonitorController;
use App\Http\Controllers\Api\PanchangController;
use App\Http\Controllers\ApiDocsController;
use App\Http\Controllers\DonationCategoryController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\DonationFeeController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\LocationController;
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

    // Location master-data admin
    Route::prefix('admin/location')->name('admin.location.')->group(function () {
        // Countries
        Route::get('countries',               [CountryAdminController::class, 'index'])->name('countries.index');
        Route::get('countries/create',        [CountryAdminController::class, 'create'])->name('countries.create');
        Route::post('countries',              [CountryAdminController::class, 'store'])->name('countries.store');
        Route::get('countries/{country}/edit',[CountryAdminController::class, 'edit'])->name('countries.edit');
        Route::put('countries/{country}',     [CountryAdminController::class, 'update'])->name('countries.update');

        // States / UTs
        Route::get('states',               [StateAdminController::class, 'index'])->name('states.index');
        Route::get('states/create',        [StateAdminController::class, 'create'])->name('states.create');
        Route::post('states',              [StateAdminController::class, 'store'])->name('states.store');
        Route::get('states/{state}/edit',  [StateAdminController::class, 'edit'])->name('states.edit');
        Route::put('states/{state}',       [StateAdminController::class, 'update'])->name('states.update');

        // Districts
        Route::get('districts',                  [DistrictAdminController::class, 'index'])->name('districts.index');
        Route::get('districts/create',           [DistrictAdminController::class, 'create'])->name('districts.create');
        Route::post('districts',                 [DistrictAdminController::class, 'store'])->name('districts.store');
        Route::get('districts/{district}/edit',  [DistrictAdminController::class, 'edit'])->name('districts.edit');
        Route::put('districts/{district}',       [DistrictAdminController::class, 'update'])->name('districts.update');

        // Cities
        Route::get('cities',              [CityAdminController::class, 'index'])->name('cities.index');
        Route::get('cities/create',       [CityAdminController::class, 'create'])->name('cities.create');
        Route::post('cities',             [CityAdminController::class, 'store'])->name('cities.store');
        Route::get('cities/{city}/edit',  [CityAdminController::class, 'edit'])->name('cities.edit');
        Route::put('cities/{city}',       [CityAdminController::class, 'update'])->name('cities.update');

        // PIN codes
        Route::get('pincodes',                [PincodeAdminController::class, 'index'])->name('pincodes.index');
        Route::get('pincodes/{pincode}/edit', [PincodeAdminController::class, 'edit'])->name('pincodes.edit');
        Route::put('pincodes/{pincode}',      [PincodeAdminController::class, 'update'])->name('pincodes.update');

        // Data sync
        Route::get('sync',  [LocationSyncController::class, 'show'])->name('sync');
        Route::post('sync', [LocationSyncController::class, 'sync'])->name('sync.run');
    });

    // Panchang API usage monitor + live test
    Route::get('/admin/panchang',      [PanchangMonitorController::class, 'index'])->name('admin.panchang.monitor');
    Route::get('/admin/panchang/test', fn () => view('panchang.test'))->name('admin.panchang.test');
});

// Panchang public API — no auth (APK calls this)
Route::prefix('api/v1')->name('api.v1.')->group(function () {
    Route::get('panchang', [PanchangController::class, 'show'])->name('panchang');
});

// Location API (cascading dropdowns) — auth required
Route::middleware('auth')->prefix('api/locations')->name('api.locations.')->group(function () {
    Route::get('countries',                          [LocationController::class, 'countries'])->name('countries');
    Route::get('countries/{country}/states',         [LocationController::class, 'states'])->name('states');
    Route::get('countries/iso/{iso}/states',         [LocationController::class, 'statesByIso'])->name('states.iso');
    Route::get('states/{state}/districts',           [LocationController::class, 'districts'])->name('districts');
    Route::get('districts/{district}/cities',        [LocationController::class, 'cities'])->name('cities');
    Route::get('cities/{city}/pincodes',             [LocationController::class, 'pincodes'])->name('pincodes');
    Route::get('pincodes/{pincode}',                 [LocationController::class, 'lookup'])->name('lookup');
    Route::get('search',                             [LocationController::class, 'search'])->name('search');
});
