<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ── 1. Create all permissions ─────────────────────────────────────────
        $map = config('permissions');
        foreach ($map as $menu => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => "{$menu}.{$action}", 'guard_name' => 'web']);
            }
        }

        // ── 2. Create roles ───────────────────────────────────────────────────
        $admin = Role::firstOrCreate(['name' => 'admin',   'guard_name' => 'web']);
        $manager = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $support = Role::firstOrCreate(['name' => 'support', 'guard_name' => 'web']);
        $guruji = Role::firstOrCreate(['name' => 'guruji',  'guard_name' => 'web']);
        $vendor = Role::firstOrCreate(['name' => 'vendor',  'guard_name' => 'web']);
        $user = Role::firstOrCreate(['name' => 'user',    'guard_name' => 'web']);

        // ── 3. Assign default permissions per role ────────────────────────────

        // Admin: all (enforced via Gate::before — but give permissions anyway for consistency)
        $admin->syncPermissions(Permission::all());

        // Manager: view/create/edit on store + appointments; delete products/categories; view audit
        $manager->syncPermissions($this->managerPermissions());

        // Support: view most; edit appointments/clients/notifications; no delete; no pricing
        $support->syncPermissions($this->supportPermissions());

        // Guruji: own profile area + mataji orders + limited store view + articles
        $guruji->syncPermissions($this->gurujIPermissions());

        // Vendor: own products + inventory (row-level scoping handled in controller)
        $vendor->syncPermissions([
            'products.view', 'products.create', 'products.update',
            'inventory.view', 'inventory.create', 'inventory.update',
        ]);

        // Enduser: minimal — own appointments + notifications
        $user->syncPermissions([
            'appointments.view', 'appointments.create',
            'notifications.view',
        ]);

        // ── 4. Backfill existing users into Spatie roles ──────────────────────
        User::withTrashed()->each(function (User $u) {
            $roleName = in_array($u->role, ['admin', 'manager', 'support', 'guruji', 'vendor', 'user'], true)
                ? $u->role : 'user';
            $u->syncRoles([$roleName]);
        });
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function managerPermissions(): array
    {
        return [
            // Store
            'matajis.view', 'matajis.create', 'matajis.update', 'matajis.delete', 'matajis.restore',
            'categories.view', 'categories.create', 'categories.update', 'categories.delete', 'categories.restore',
            'products.view', 'products.create', 'products.update', 'products.delete', 'products.restore',
            'inventory.view', 'inventory.create', 'inventory.update',
            'vendors.view', 'vendors.create', 'vendors.update',
            // Appointments
            'appointments.view', 'appointments.create', 'appointments.update', 'appointments.delete', 'appointments.restore',
            'availability.view', 'availability.create', 'availability.update', 'availability.delete',
            'services.view', 'services.create', 'services.update', 'services.delete', 'services.restore',
            'clients.view', 'clients.create', 'clients.update', 'clients.delete',
            'gurus.view', 'gurus.create', 'gurus.update', 'gurus.delete', 'gurus.restore',
            // Donations
            'donation-categories.view', 'donation-categories.create', 'donation-categories.update',
            'donations.view', 'donations.create', 'donations.update',
            // Notifications
            'notifications.view', 'notifications.create', 'notifications.update',
            // Audit log (read-only)
            'audit-log.view',
            // Panchang
            'panchang.view', 'api-docs.view',
            // App Content
            'promotions.view', 'promotions.create', 'promotions.update', 'promotions.delete', 'promotions.restore',
            // Articles
            'articles.view', 'articles.create', 'articles.update', 'articles.delete', 'articles.restore',
            // Locations
            'locations.view', 'locations.create', 'locations.update',
            // Mataji orders
            'mataji-orders.view', 'mataji-orders.create', 'mataji-orders.update', 'mataji-orders.delete', 'mataji-orders.restore',
            // Samagri orders
            'samagri-orders.view', 'samagri-orders.update', 'samagri-orders.delete', 'samagri-orders.restore',
        ];
    }

    private function supportPermissions(): array
    {
        return [
            'appointments.view', 'appointments.create', 'appointments.update',
            'availability.view',
            'services.view',
            'clients.view', 'clients.create', 'clients.update',
            'gurus.view',
            'donation-categories.view',
            'donations.view',
            'matajis.view',
            'categories.view',
            'products.view',
            'inventory.view',
            'vendors.view',
            'notifications.view', 'notifications.create', 'notifications.update', 'notifications.delete',
            'audit-log.view',
            'panchang.view',
            'mataji-orders.view',
            'samagri-orders.view',
        ];
    }

    private function gurujIPermissions(): array
    {
        return [
            'appointments.view', 'appointments.create', 'appointments.update',
            'availability.view', 'availability.create', 'availability.update', 'availability.delete',
            'clients.view',
            'matajis.view',
            'products.view',
            'mataji-orders.view', 'mataji-orders.create', 'mataji-orders.update', 'mataji-orders.delete', 'mataji-orders.restore',
            'donations.view',
            'notifications.view',
            // Services: Guruji can view the list/detail only.
            // Their per-Guruji pricing rows are managed via GuruServiceController (services.view gate).
            'services.view',
            // Articles (guruji can create their own content)
            'articles.view', 'articles.create', 'articles.update',
        ];
    }
}
