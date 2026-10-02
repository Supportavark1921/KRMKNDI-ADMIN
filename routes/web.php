<?php

use App\Http\Controllers\Admin\AuditLogController as AdminAuditLogController;
use App\Http\Controllers\Admin\Location\CityAdminController;
use App\Http\Controllers\Admin\Location\CountryAdminController;
use App\Http\Controllers\Admin\Location\DistrictAdminController;
use App\Http\Controllers\Admin\Location\LocationSyncController;
use App\Http\Controllers\Admin\Location\PincodeAdminController;
use App\Http\Controllers\Admin\Location\StateAdminController;
use App\Http\Controllers\Admin\MatajOrderController as AdminMatajOrderController;
use App\Http\Controllers\Admin\PanchangMonitorController;
use App\Http\Controllers\Admin\PromotionController as AdminPromotionController;
use App\Http\Controllers\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\Store\CategoriesController as StoreCategoriesController;
use App\Http\Controllers\Admin\Store\InventoryController as StoreInventoryController;
use App\Http\Controllers\Admin\Store\MatajisController as StoreMatajisController;
use App\Http\Controllers\Admin\Store\ProductsController as StoreProductsController;
use App\Http\Controllers\Admin\Store\VendorsController as StoreVendorsController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\PanchangController;
use App\Http\Controllers\Api\PromotionsApiController;
use App\Http\Controllers\Api\Store\CategoriesApiController;
use App\Http\Controllers\Api\Store\MatajisApiController;
use App\Http\Controllers\Api\Store\ProductsApiController;
use App\Http\Controllers\ApiDocsController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\ClientProfileController;
use App\Http\Controllers\DonationCategoryController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\DonationFeeController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ServiceController;
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
        Route::get('countries', [CountryAdminController::class, 'index'])->name('countries.index');
        Route::get('countries/create', [CountryAdminController::class, 'create'])->name('countries.create');
        Route::post('countries', [CountryAdminController::class, 'store'])->name('countries.store');
        Route::get('countries/{country}/edit', [CountryAdminController::class, 'edit'])->name('countries.edit');
        Route::put('countries/{country}', [CountryAdminController::class, 'update'])->name('countries.update');

        // States / UTs
        Route::get('states', [StateAdminController::class, 'index'])->name('states.index');
        Route::get('states/create', [StateAdminController::class, 'create'])->name('states.create');
        Route::post('states', [StateAdminController::class, 'store'])->name('states.store');
        Route::get('states/{state}/edit', [StateAdminController::class, 'edit'])->name('states.edit');
        Route::put('states/{state}', [StateAdminController::class, 'update'])->name('states.update');

        // Districts
        Route::get('districts', [DistrictAdminController::class, 'index'])->name('districts.index');
        Route::get('districts/create', [DistrictAdminController::class, 'create'])->name('districts.create');
        Route::post('districts', [DistrictAdminController::class, 'store'])->name('districts.store');
        Route::get('districts/{district}/edit', [DistrictAdminController::class, 'edit'])->name('districts.edit');
        Route::put('districts/{district}', [DistrictAdminController::class, 'update'])->name('districts.update');

        // Cities
        Route::get('cities', [CityAdminController::class, 'index'])->name('cities.index');
        Route::get('cities/create', [CityAdminController::class, 'create'])->name('cities.create');
        Route::post('cities', [CityAdminController::class, 'store'])->name('cities.store');
        Route::get('cities/{city}/edit', [CityAdminController::class, 'edit'])->name('cities.edit');
        Route::put('cities/{city}', [CityAdminController::class, 'update'])->name('cities.update');

        // PIN codes
        Route::get('pincodes', [PincodeAdminController::class, 'index'])->name('pincodes.index');
        Route::get('pincodes/{pincode}/edit', [PincodeAdminController::class, 'edit'])->name('pincodes.edit');
        Route::put('pincodes/{pincode}', [PincodeAdminController::class, 'update'])->name('pincodes.update');

        // Data sync
        Route::get('sync', [LocationSyncController::class, 'show'])->name('sync');
        Route::post('sync', [LocationSyncController::class, 'sync'])->name('sync.run');
    });

    // Panchang API usage monitor + live test
    Route::get('/admin/panchang', [PanchangMonitorController::class, 'index'])->name('admin.panchang.monitor');
    Route::get('/admin/panchang/test', fn () => view('panchang.test'))->name('admin.panchang.test');

    // ── User management ───────────────────────────────────────────────────────
    Route::prefix('admin/users')->name('admin.users.')->group(function () {
        Route::get('/', [AdminUserController::class, 'index'])->name('index');
        Route::get('/create', [AdminUserController::class, 'create'])->name('create');
        Route::post('/', [AdminUserController::class, 'store'])->name('store');
        Route::get('/{user}', [AdminUserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [AdminUserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [AdminUserController::class, 'update'])->name('update');
        Route::delete('/{user}', [AdminUserController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [AdminUserController::class, 'restore'])->name('restore');
        Route::post('/{user}/suspend', [AdminUserController::class, 'suspend'])->name('suspend');
        Route::post('/{user}/activate', [AdminUserController::class, 'activate'])->name('activate');
    });

    // ── App Content / Promotions ─────────────────────────────────────────────
    Route::prefix('admin/promotions')->name('admin.promotions.')->group(function () {
        Route::get('/', [AdminPromotionController::class, 'index'])->name('index');
        Route::get('/create', [AdminPromotionController::class, 'create'])->name('create');
        Route::post('/', [AdminPromotionController::class, 'store'])->name('store');
        Route::get('/{promotion}/edit', [AdminPromotionController::class, 'edit'])->name('edit');
        Route::put('/{promotion}', [AdminPromotionController::class, 'update'])->name('update');
        Route::delete('/{promotion}', [AdminPromotionController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [AdminPromotionController::class, 'restore'])->name('restore');
        Route::post('/{promotion}/activate', [AdminPromotionController::class, 'activate'])->name('activate');
        Route::post('/{promotion}/deactivate', [AdminPromotionController::class, 'deactivate'])->name('deactivate');
    });

    // ── Mataji orders ────────────────────────────────────────────────────────
    Route::prefix('admin/mataji-orders')->name('admin.mataji-orders.')->group(function () {
        Route::get('/', [AdminMatajOrderController::class, 'index'])->name('index');
        Route::get('/create', [AdminMatajOrderController::class, 'create'])->name('create');
        Route::post('/', [AdminMatajOrderController::class, 'store'])->name('store');
        Route::get('/{matajOrder}', [AdminMatajOrderController::class, 'show'])->name('show');
        Route::get('/{matajOrder}/edit', [AdminMatajOrderController::class, 'edit'])->name('edit');
        Route::put('/{matajOrder}', [AdminMatajOrderController::class, 'update'])->name('update');
        Route::delete('/{matajOrder}', [AdminMatajOrderController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [AdminMatajOrderController::class, 'restore'])->name('restore');
        Route::post('/{matajOrder}/confirm', [AdminMatajOrderController::class, 'confirm'])->name('confirm');
        Route::post('/{matajOrder}/cancel', [AdminMatajOrderController::class, 'cancel'])->name('cancel');
    });

    // ── Audit log ────────────────────────────────────────────────────────────
    Route::prefix('admin/audit')->name('admin.audit.')->group(function () {
        Route::get('/', [AdminAuditLogController::class, 'index'])->name('index');
        Route::get('/{activity}', [AdminAuditLogController::class, 'show'])->name('show');
    });

    // ── Role management ──────────────────────────────────────────────────────
    Route::prefix('admin/roles')->name('admin.roles.')->group(function () {
        Route::get('/', [AdminRoleController::class, 'index'])->name('index');
        Route::get('/create', [AdminRoleController::class, 'create'])->name('create');
        Route::post('/', [AdminRoleController::class, 'store'])->name('store');
        Route::get('/{role}/edit', [AdminRoleController::class, 'edit'])->name('edit');
        Route::put('/{role}', [AdminRoleController::class, 'update'])->name('update');
        Route::delete('/{role}', [AdminRoleController::class, 'destroy'])->name('destroy');
    });

    // ── Mataji Vastra Store — Admin ───────────────────────────────────────────
    Route::prefix('admin/store')->name('admin.store.')->group(function () {
        // Matajis
        Route::get('matajis', [StoreMatajisController::class, 'index'])->name('matajis.index');
        Route::get('matajis/create', [StoreMatajisController::class, 'create'])->name('matajis.create');
        Route::post('matajis', [StoreMatajisController::class, 'store'])->name('matajis.store');
        Route::get('matajis/{mataji}/edit', [StoreMatajisController::class, 'edit'])->name('matajis.edit');
        Route::put('matajis/{mataji}', [StoreMatajisController::class, 'update'])->name('matajis.update');
        Route::delete('matajis/{mataji}', [StoreMatajisController::class, 'destroy'])->name('matajis.destroy');

        // Categories
        Route::get('categories', [StoreCategoriesController::class, 'index'])->name('categories.index');
        Route::get('categories/create', [StoreCategoriesController::class, 'create'])->name('categories.create');
        Route::post('categories', [StoreCategoriesController::class, 'store'])->name('categories.store');
        Route::get('categories/{category}/edit', [StoreCategoriesController::class, 'edit'])->name('categories.edit');
        Route::put('categories/{category}', [StoreCategoriesController::class, 'update'])->name('categories.update');
        Route::delete('categories/{category}', [StoreCategoriesController::class, 'destroy'])->name('categories.destroy');

        // Products
        Route::get('products', [StoreProductsController::class, 'index'])->name('products.index');
        Route::get('products/create', [StoreProductsController::class, 'create'])->name('products.create');
        Route::post('products', [StoreProductsController::class, 'store'])->name('products.store');
        Route::get('products/{product}', [StoreProductsController::class, 'show'])->name('products.show');
        Route::get('products/{product}/edit', [StoreProductsController::class, 'edit'])->name('products.edit');
        Route::put('products/{product}', [StoreProductsController::class, 'update'])->name('products.update');
        Route::delete('products/{product}', [StoreProductsController::class, 'destroy'])->name('products.destroy');
        Route::post('products/{id}/restore', [StoreProductsController::class, 'restore'])->name('products.restore');
        Route::delete('product-images/{image}', [StoreProductsController::class, 'deleteImage'])->name('products.images.delete');
        Route::post('product-images/{image}/primary', [StoreProductsController::class, 'setPrimaryImage'])->name('products.images.primary');

        // Inventory
        Route::get('inventory', [StoreInventoryController::class, 'index'])->name('inventory.index');
        Route::post('inventory/{product}/add', [StoreInventoryController::class, 'addStock'])->name('inventory.add');
        Route::post('inventory/{product}/adjust', [StoreInventoryController::class, 'adjust'])->name('inventory.adjust');
        Route::get('inventory/{product}/history', [StoreInventoryController::class, 'history'])->name('inventory.history');

        // Vendors
        Route::get('vendors', [StoreVendorsController::class, 'index'])->name('vendors.index');
        Route::get('vendors/create', [StoreVendorsController::class, 'create'])->name('vendors.create');
        Route::post('vendors', [StoreVendorsController::class, 'store'])->name('vendors.store');
        Route::get('vendors/{vendor}', [StoreVendorsController::class, 'show'])->name('vendors.show');
        Route::get('vendors/{vendor}/edit', [StoreVendorsController::class, 'edit'])->name('vendors.edit');
        Route::put('vendors/{vendor}', [StoreVendorsController::class, 'update'])->name('vendors.update');
        Route::post('vendors/{vendor}/approve', [StoreVendorsController::class, 'approve'])->name('vendors.approve');
        Route::post('vendors/{vendor}/suspend', [StoreVendorsController::class, 'suspend'])->name('vendors.suspend');
    });
});

// Public API — no auth (APK calls these)
Route::prefix('api/v1')->name('api.v1.')->group(function () {
    Route::get('panchang', [PanchangController::class, 'show'])->name('panchang');

    // Store catalog
    Route::get('products', [ProductsApiController::class, 'index'])->name('store.products.index');
    Route::get('products/{id}', [ProductsApiController::class, 'show'])->name('store.products.show');
    Route::get('resale-products', [ProductsApiController::class, 'resale'])->name('store.products.resale');
    Route::get('categories', [CategoriesApiController::class, 'index'])->name('store.categories.index');
    Route::get('matajis', [MatajisApiController::class, 'index'])->name('store.matajis.index');
    Route::get('matajis/{mataji}', [MatajisApiController::class, 'show'])->name('store.matajis.show');
    Route::get('promotions', [PromotionsApiController::class, 'index'])->name('promotions.index');
});

// Location API (cascading dropdowns) — auth required
Route::middleware('auth')->prefix('api/locations')->name('api.locations.')->group(function () {
    Route::get('countries', [LocationController::class, 'countries'])->name('countries');
    Route::get('countries/{country}/states', [LocationController::class, 'states'])->name('states');
    Route::get('countries/iso/{iso}/states', [LocationController::class, 'statesByIso'])->name('states.iso');
    Route::get('states/{state}/districts', [LocationController::class, 'districts'])->name('districts');
    Route::get('districts/{district}/cities', [LocationController::class, 'cities'])->name('cities');
    Route::get('cities/{city}/pincodes', [LocationController::class, 'pincodes'])->name('pincodes');
    Route::get('pincodes/{pincode}', [LocationController::class, 'lookup'])->name('lookup');
    Route::get('search', [LocationController::class, 'search'])->name('search');
});
