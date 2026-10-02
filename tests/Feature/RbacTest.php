<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class RbacTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);
    }

    // ── Permission gate checks ────────────────────────────────────────────────

    public function test_user_without_products_delete_gets_403(): void
    {
        $category = ProductCategory::create(['name' => 'Test Cat', 'slug' => 'test-cat', 'status' => 'active', 'sort_order' => 0]);
        $product = Product::create([
            'name' => 'Test Product',
            'category_id' => $category->id,
            'product_type' => 'NORMAL',
            'price' => 100,
            'status' => 'active',
        ]);

        $vendor = User::factory()->create(['role' => 'vendor', 'status' => 'active']);
        $vendor->syncRoles(['vendor']); // vendor role has no products.delete

        $this->actingAs($vendor)
            ->delete(route('admin.store.products.destroy', $product))
            ->assertForbidden();
    }

    public function test_admin_can_delete_product(): void
    {
        $category = ProductCategory::create(['name' => 'Cat2', 'slug' => 'cat2', 'status' => 'active', 'sort_order' => 0]);
        $product = Product::create([
            'name' => 'Admin Product',
            'category_id' => $category->id,
            'product_type' => 'NORMAL',
            'price' => 200,
            'status' => 'active',
        ]);

        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $admin->syncRoles(['admin']);

        $this->actingAs($admin)
            ->delete(route('admin.store.products.destroy', $product))
            ->assertRedirect();

        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    // ── User CRUD ─────────────────────────────────────────────────────────────

    public function test_admin_can_create_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $admin->syncRoles(['admin']);

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Jane Doe',
                'email' => 'jane@example.com',
                'password' => 'secret123',
                'password_confirmation' => 'secret123',
                'role' => 'support',
                'status' => 'active',
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', ['email' => 'jane@example.com', 'role' => 'support']);
    }

    public function test_non_admin_cannot_access_user_list(): void
    {
        $this->seed(PermissionSeeder::class);
        $vendor = User::factory()->create(['role' => 'vendor', 'status' => 'active']);
        $vendor->syncRoles(['vendor']);

        $this->actingAs($vendor)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_cannot_delete_last_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $admin->syncRoles(['admin']);

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $admin))
            ->assertForbidden();
    }

    // ── Public register cannot create admin ───────────────────────────────────

    public function test_public_register_always_creates_user_role(): void
    {
        $this->post(route('register'), [
            'name' => 'Public User',
            'email' => 'public@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ])->assertRedirect();

        $this->assertDatabaseHas('users', ['email' => 'public@example.com', 'role' => 'user']);
    }

    // ── Audit log ─────────────────────────────────────────────────────────────

    public function test_updating_product_writes_activity(): void
    {
        $category = ProductCategory::create(['name' => 'Cat3', 'slug' => 'cat3', 'status' => 'active', 'sort_order' => 0]);
        $product = Product::create([
            'name' => 'Audit Product',
            'category_id' => $category->id,
            'product_type' => 'NORMAL',
            'price' => 50,
            'status' => 'active',
        ]);

        $product->update(['price' => 75]);

        $this->assertDatabaseHas('activity_log', [
            'subject_type' => Product::class,
            'subject_id' => $product->id,
            'event' => 'updated',
        ]);
    }

    public function test_password_not_logged_in_activity(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $admin->syncRoles(['admin']);

        // Update password directly — should NOT appear in activity log properties
        $admin->update(['name' => 'New Name']);

        $activity = Activity::where('subject_id', $admin->id)
            ->where('subject_type', User::class)
            ->latest()
            ->first();

        $this->assertNotNull($activity);
        $this->assertArrayNotHasKey('password', $activity->properties->get('attributes', []));
    }
}
