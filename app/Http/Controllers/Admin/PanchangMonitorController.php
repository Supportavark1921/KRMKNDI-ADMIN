<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NavamshaApiLog;
use App\Models\PanchangCache;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PanchangMonitorController extends Controller
{
    public function index(): View
    {
        Gate::authorize('manage-appointments');

        $today = today()->toDateString();

        $callsToday  = NavamshaApiLog::where('request_date', $today)->count();
        $callsMonth  = NavamshaApiLog::whereYear('request_date', today()->year)
            ->whereMonth('request_date', today()->month)->count();
        $errorsToday = NavamshaApiLog::where('request_date', $today)->where('success', false)->count();
        $totalCached = PanchangCache::count();

        // Cache hits = total requests - API calls (approximate: unique cache_keys served multiple times)
        // We track API calls (misses) directly; hits are inferred from total serving vs misses.
        $recentLogs = NavamshaApiLog::orderByDesc('created_at')->limit(50)->get();

        // Per-feature breakdown this month
        $featureStats = NavamshaApiLog::selectRaw("feature, COUNT(*) as calls, SUM(CASE WHEN success=0 THEN 1 ELSE 0 END) as errors")
            ->whereYear('request_date', today()->year)
            ->whereMonth('request_date', today()->month)
            ->groupBy('feature')
            ->get();

        // Cached records by feature
        $cacheByFeature = PanchangCache::selectRaw('feature, COUNT(*) as count')
            ->groupBy('feature')
            ->get();

        // Avg response time today
        $avgResponseMs = NavamshaApiLog::where('request_date', $today)
            ->whereNotNull('response_time_ms')
            ->avg('response_time_ms');

        return view('panchang.monitor', compact(
            'callsToday', 'callsMonth', 'errorsToday', 'totalCached',
            'recentLogs', 'featureStats', 'cacheByFeature', 'avgResponseMs'
        ));
    }
}
