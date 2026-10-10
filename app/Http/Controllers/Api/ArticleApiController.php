<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $lang = $request->query('lang', 'en');
        $category = $request->query('category');
        $search = $request->query('search');

        $query = Article::published()->where('approval_status', 'approved')->with('author:id,name');

        if ($category) {
            $query->where('category', $category);
        }
        if ($search) {
            $query->search($search);
        }

        $articles = $query->orderByDesc('published_at')->paginate(15);

        $data = $articles->getCollection()->map(fn (Article $a) => $this->summary($a, $lang));

        return response()->json([
            'success' => true,
            'data' => $data,
            'meta' => [
                'current_page' => $articles->currentPage(),
                'last_page' => $articles->lastPage(),
                'total' => $articles->total(),
            ],
        ]);
    }

    public function show(Article $article, Request $request): JsonResponse
    {
        if ($article->status !== 'published' || ($article->approval_status ?? 'approved') !== 'approved') {
            return response()->json(['success' => false, 'message' => 'Not found.'], 404);
        }

        $lang = $request->query('lang', 'en');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $article->id,
                'title' => $article->translatedField('title', $lang),
                'slug' => $article->slug,
                'excerpt' => $article->translatedField('excerpt', $lang),
                'content' => $article->translatedField('content', $lang),
                'cover_image' => $article->coverImageUrl(),
                'category' => $article->category,
                'tags' => $article->tags ?? [],
                'author' => $article->author?->name,
                'published_at' => $article->published_at?->toIso8601String(),
            ],
        ]);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function summary(Article $article, string $lang): array
    {
        return [
            'id' => $article->id,
            'title' => $article->translatedField('title', $lang),
            'slug' => $article->slug,
            'excerpt' => $article->translatedField('excerpt', $lang),
            'cover_image' => $article->coverImageUrl(),
            'category' => $article->category,
            'tags' => $article->tags ?? [],
            'author' => $article->author?->name,
            'published_at' => $article->published_at?->toIso8601String(),
        ];
    }
}
