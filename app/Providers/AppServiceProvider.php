<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ── Core admin gates (unchanged behaviour) ────────────────────────────
        Gate::define('manage-appointments', fn ($user) => $user->isAdmin());

        // ── Roles ─────────────────────────────────────────────────────────────
        Gate::define('is-admin',    fn ($user) => $user->isAdmin());
        Gate::define('is-guruji',   fn ($user) => $user->isGuruji());
        Gate::define('is-vendor',   fn ($user) => $user->isVendor());
        Gate::define('is-end-user', fn ($user) => $user->isEndUser());

        // ── Store permissions ─────────────────────────────────────────────────
        // manage-store: admin can manage everything in the store
        Gate::define('manage-store', fn ($user) => $user->isAdmin());

        // vendor-store: vendor can manage their own products/inventory
        Gate::define('vendor-store', fn ($user) => $user->isVendor() && $user->vendor?->isActive());

        // access-store-admin: either admin or active vendor
        Gate::define('access-store-admin', fn ($user) =>
            $user->isAdmin() || ($user->isVendor() && $user->vendor?->isActive())
        );

        // manage-vendors: admin only (approve, suspend, etc.)
        Gate::define('manage-vendors', fn ($user) => $user->isAdmin());

        // ── Guruji permissions ────────────────────────────────────────────────
        Gate::define('guruji-access', fn ($user) => $user->isGuruji() || $user->isAdmin());
    }
}
