<?php

namespace App\Http\Controllers\Api\Store;

use App\Http\Controllers\Controller;
use App\Models\Mataji;
use Illuminate\Http\JsonResponse;

class MatajisApiController extends Controller
{
    public function index(): JsonResponse
    {
        $matajis = Mataji::active()
            ->withCount(['products' => fn ($q) => $q->active()])
            ->orderBy('name')
            ->get()
            ->map(fn ($m) => $this->format($m));

        return response()->json(['success' => true, 'data' => $matajis]);
    }

    public function show(Mataji $mataji): JsonResponse
    {
        if ($mataji->status !== 'active') {
            return response()->json(['success' => false, 'error' => 'Not found.'], 404);
        }

        return response()->json(['success' => true, 'data' => $this->format($mataji)]);
    }

    private function format(Mataji $m): array
    {
        return [
            'id'                 => $m->id,
            'name'               => $m->name,
            'temple_name'        => $m->temple_name,
            'city'               => $m->city,
            'state'              => $m->state,
            'description'        => $m->description,
            'image'              => $m->image ? asset('storage/' . $m->image) : null,
            'offering_available' => $m->offering_available,
            'products_count'     => $m->products_count ?? null,
        ];
    }
}
