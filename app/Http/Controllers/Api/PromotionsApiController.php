<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PromotionsApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $placement = $request->query('placement');
        $lang = $request->query('lang', 'en');
        $audience = $request->query('audience', 'all'); // caller passes app user's role

        $cacheKey = "promotions:{$placement}:{$lang}:{$audience}";

        $items = Cache::remember($cacheKey, 300, function () use ($placement, $audience) {
            $q = Promotion::live()->forAudience($audience);

            if ($placement) {
                $q->forPlacement($placement);
            }

            return $q->orderBy('sort_order')->get();
        });

        $data = $items->map(fn (Promotion $p) => [
            'id' => $p->id,
            'title' => $p->translatedField('title', $lang),
            'description' => $p->translatedField('description', $lang),
            'image' => $p->imageUrl(),
            'type' => $p->type,
            'placement' => $p->placement,
            'audience' => $p->audience,
            'cta' => [
                'type' => $p->cta_type,
                'value' => $p->cta_value,
            ],
            'starts_at' => $p->starts_at?->toIso8601String(),
            'ends_at' => $p->ends_at?->toIso8601String(),
        ]);

        return response()->json(['success' => true, 'data' => $data]);
    }
}
