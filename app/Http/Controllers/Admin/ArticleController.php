<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('articles.view');

        $query = Article::with('author')->withTrashed();

        if ($s = $request->query('search')) {
            $query->search($s);
        }
        if ($cat = $request->query('category')) {
            $query->where('category', $cat);
        }
        if ($status = $request->query('status')) {
            if ($status === 'trashed') {
                $query->onlyTrashed();
            } else {
                $query->where('status', $status)->withoutTrashed();
            }
        }

        return view('admin.articles.index', [
            'articles' => $query->orderByDesc('updated_at')->paginate(20)->withQueryString(),
            'categories' => Article::CATEGORIES,
            'statuses' => Article::STATUSES,
        ]);
    }

    public function create(): View
    {
        Gate::authorize('articles.create');

        return view('admin.articles.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('articles.create');

        $data = $this->validateArticle($request);
        $data['author_id'] = auth()->id();
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('articles', 'public');
        }

        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        Article::create($data);

        return redirect()->route('admin.articles.index')->with('success', 'Article created.');
    }

    public function show(Article $article): View
    {
        Gate::authorize('articles.view');

        return view('admin.articles.show', ['article' => $article->load('author')]);
    }

    public function edit(Article $article): View
    {
        Gate::authorize('articles.update');

        return view('admin.articles.edit', array_merge($this->formData(), ['article' => $article]));
    }

    public function update(Request $request, Article $article): RedirectResponse
    {
        Gate::authorize('articles.update');

        $data = $this->validateArticle($request, $article->id);
        $data['updated_by'] = auth()->id();

        if ($request->hasFile('cover_image')) {
            if ($article->cover_image) {
                Storage::disk('public')->delete($article->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('articles', 'public');
        }

        if ($data['status'] === 'published' && ! $article->published_at) {
            $data['published_at'] = now();
        }

        $article->update($data);

        return redirect()->route('admin.articles.index')->with('success', 'Article updated.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        Gate::authorize('articles.delete');
        $article->delete();

        return redirect()->route('admin.articles.index')->with('success', 'Article archived.');
    }

    public function restore(int $id): RedirectResponse
    {
        Gate::authorize('articles.restore');
        Article::withTrashed()->findOrFail($id)->restore();

        return back()->with('success', 'Article restored.');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function formData(): array
    {
        return [
            'categories' => Article::CATEGORIES,
            'statuses' => Article::STATUSES,
        ];
    }

    private function validateArticle(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable', 'string', 'max:255',
                'regex:/^[a-z0-9-]+$/',
                'unique:articles,slug'.($ignoreId ? ",{$ignoreId}" : ''),
            ],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'category' => ['required', 'in:'.implode(',', Article::CATEGORIES)],
            'tags' => ['nullable', 'string'],
            'status' => ['required', 'in:'.implode(',', Article::STATUSES)],
            'published_at' => ['nullable', 'date'],
            'translations.hi.title' => ['nullable', 'string', 'max:255'],
            'translations.hi.excerpt' => ['nullable', 'string', 'max:500'],
            'translations.hi.content' => ['nullable', 'string'],
        ]);

        // Auto-generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        // Parse comma-separated tags into array
        $data['tags'] = array_values(array_filter(
            array_map('trim', explode(',', $data['tags'] ?? ''))
        )) ?: null;

        // Build translations
        $data['translations'] = array_filter([
            'hi' => array_filter([
                'title' => $request->input('translations.hi.title'),
                'excerpt' => $request->input('translations.hi.excerpt'),
                'content' => $request->input('translations.hi.content'),
            ]),
        ]) ?: null;

        unset($data['cover_image']); // handled separately

        return $data;
    }
}
