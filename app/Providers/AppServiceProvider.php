<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Admin always passes every gate check (Spatie also handles this via
        // HasRoles, but we keep a Gate::before so legacy @can directives work).
        Gate::before(fn ($user) => $user->isAdmin() ? true : null);

        // ── Legacy gate aliases (keep so existing controllers/views don't break) ─
        Gate::define('manage-appointments', fn ($user) => $user->can('appointments.update'));
        Gate::define('manage-store', fn ($user) => $user->can('products.create'));
        Gate::define('access-store-admin', fn ($user) => $user->can('products.view'));
        Gate::define('manage-vendors', fn ($user) => $user->can('vendors.update'));
        Gate::define('guruji-access', fn ($user) => $user->can('mataji-orders.view'));

        // ── Role-type shortcuts ───────────────────────────────────────────────
        Gate::define('is-admin', fn ($user) => $user->isAdmin());
        Gate::define('is-guruji', fn ($user) => $user->isGuruji());
        Gate::define('is-vendor', fn ($user) => $user->isVendor());
        Gate::define('is-end-user', fn ($user) => $user->isEndUser());

        Gate::define('vendor-store', fn ($user) => $user->isVendor() && $user->vendor?->isActive()
        );

        // ── Suspended users can't do anything ────────────────────────────────
        Gate::after(fn ($user) => $user->isActive() ? null : false);
    }
}
