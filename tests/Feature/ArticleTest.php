<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);
    }

    // ── Forbidden without permission ──────────────────────────────────────────

    public function test_user_without_articles_view_gets_403_on_index(): void
    {
        $user = User::factory()->create(['role' => 'user', 'status' => 'active']);
        $user->syncRoles(['user']);

        $this->actingAs($user)->get(route('admin.articles.index'))->assertForbidden();
    }

    public function test_user_without_articles_create_gets_403_on_store(): void
    {
        $support = User::factory()->create(['role' => 'support', 'status' => 'active']);
        $support->syncRoles(['support']);

        $this->actingAs($support)
            ->post(route('admin.articles.store'), $this->articlePayload())
            ->assertForbidden();
    }

    public function test_user_without_articles_delete_gets_403_on_destroy(): void
    {
        $article = $this->makeArticle();
        $guruji = User::factory()->create(['role' => 'guruji', 'status' => 'active']);
        $guruji->syncRoles(['guruji']); // guruji has no articles.delete

        $this->actingAs($guruji)
            ->delete(route('admin.articles.destroy', $article))
            ->assertForbidden();
    }

    // ── Allowed with permission ───────────────────────────────────────────────

    public function test_admin_can_view_articles_index(): void
    {
        $admin = $this->adminUser();

        $this->actingAs($admin)->get(route('admin.articles.index'))->assertOk();
    }

    public function test_admin_can_create_article(): void
    {
        $admin = $this->adminUser();

        $this->actingAs($admin)
            ->post(route('admin.articles.store'), $this->articlePayload())
            ->assertRedirect(route('admin.articles.index'));

        $this->assertDatabaseHas('articles', ['title' => 'Test Article Title', 'status' => 'draft']);
    }

    public function test_admin_can_update_article(): void
    {
        $article = $this->makeArticle();
        $admin = $this->adminUser();

        $this->actingAs($admin)
            ->put(route('admin.articles.update', $article), array_merge($this->articlePayload(), ['title' => 'Updated Title']))
            ->assertRedirect(route('admin.articles.index'));

        $this->assertDatabaseHas('articles', ['id' => $article->id, 'title' => 'Updated Title']);
    }

    // ── Soft delete: record kept, hidden from default list, restorable ────────

    public function test_admin_soft_deletes_article(): void
    {
        $article = $this->makeArticle();
        $admin = $this->adminUser();

        $this->actingAs($admin)
            ->delete(route('admin.articles.destroy', $article))
            ->assertRedirect(route('admin.articles.index'));

        $this->assertSoftDeleted('articles', ['id' => $article->id]);
        $this->assertDatabaseHas('articles', ['id' => $article->id]); // row still exists
    }

    public function test_admin_can_restore_article(): void
    {
        $article = $this->makeArticle();
        $article->delete();
        $admin = $this->adminUser();

        $this->actingAs($admin)
            ->post(route('admin.articles.restore', $article->id))
            ->assertRedirect();

        $this->assertDatabaseHas('articles', ['id' => $article->id, 'deleted_at' => null]);
    }

    // ── Audit log entry ───────────────────────────────────────────────────────

    public function test_creating_article_logs_activity(): void
    {
        $admin = $this->adminUser();

        $this->actingAs($admin)
            ->post(route('admin.articles.store'), $this->articlePayload());

        $article = Article::first();
        $this->assertNotNull($article);

        $this->assertDatabaseHas('activity_log', [
            'subject_type' => Article::class,
            'subject_id' => $article->id,
            'event' => 'created',
        ]);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function adminUser(): User
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $admin->syncRoles(['admin']);

        return $admin;
    }

    private function makeArticle(): Article
    {
        $admin = $this->adminUser();

        return Article::create([
            'title' => 'Existing Article',
            'slug' => 'existing-article',
            'content' => 'Some content here.',
            'category' => 'general',
            'status' => 'draft',
            'author_id' => $admin->id,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);
    }

    private function articlePayload(): array
    {
        return [
            'title' => 'Test Article Title',
            'slug' => '',
            'excerpt' => 'A brief excerpt.',
            'content' => 'Full article content goes here.',
            'category' => 'general',
            'status' => 'draft',
            'tags' => 'test, phpunit',
        ];
    }
}
