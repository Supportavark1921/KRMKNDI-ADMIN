@extends('layouts.app', ['title' => 'Panchang Monitor - ARK Jyotish'])

@section('content')
<main class="dashboard">
    <header class="topbar">
        <div class="page-heading"><span>Panchang API Monitor</span><small>Navamsha API usage and cache statistics</small></div>
        <a class="nav-link topbar-link" href="{{ route('dashboard') }}">Dashboard</a>
    </header>

    {{-- Stats grid --}}
    <div class="metric-grid" style="max-width:1100px;margin:32px auto 22px">
        <article>
            <span>API calls today</span>
            <strong>{{ number_format($callsToday) }}</strong>
            <small>Navamsha requests sent</small>
        </article>
        <article>
            <span>API calls this month</span>
            <strong>{{ number_format($callsMonth) }}</strong>
            <small>of ~10,000 free tier</small>
        </article>
        <article>
            <span>Cached records</span>
            <strong>{{ number_format($totalCached) }}</strong>
            <small>total across all features</small>
        </article>
        <article>
            <span>Errors today</span>
            <strong style="color:{{ $errorsToday > 0 ? '#c0392b' : '#27ae60' }}">{{ $errorsToday }}</strong>
            <small>failed API calls</small>
        </article>
        <article>
            <span>Avg response time</span>
            <strong>{{ $avgResponseMs ? round($avgResponseMs) . ' ms' : '—' }}</strong>
            <small>today's API calls</small>
        </article>
        <article>
            <span>Cache hit rate</span>
            <strong style="color:#27ae60">
                @if($callsToday + $totalCached > 0)
                    {{ number_format($totalCached / max(1, $totalCached + $callsToday) * 100, 1) }}%
                @else —
                @endif
            </strong>
            <small>est. (cached / total served)</small>
        </article>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;max-width:1100px;margin:0 auto 24px">

        {{-- Feature breakdown --}}
        <div style="padding:26px;border:1px solid #e6e8f0;border-radius:18px;background:#fff">
            <h2 style="margin:0 0 18px;font-size:15px;color:#15233d">API calls this month by feature</h2>
            @forelse($featureStats as $row)
            <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid #f0f2f8">
                <span style="flex:1;font-size:13px;color:#15233d;font-weight:600">{{ $row->feature }}</span>
                <span style="font-size:13px;color:#69758b">{{ number_format($row->calls) }} calls</span>
                @if($row->errors > 0)
                <span style="padding:2px 8px;border-radius:10px;background:#fdeaea;color:#c0392b;font-size:11px;font-weight:700">{{ $row->errors }} err</span>
                @endif
            </div>
            @empty
            <p style="color:#8a95a9;font-size:13px">No API calls recorded this month.</p>
            @endforelse
        </div>

        {{-- Cached by feature --}}
        <div style="padding:26px;border:1px solid #e6e8f0;border-radius:18px;background:#fff">
            <h2 style="margin:0 0 18px;font-size:15px;color:#15233d">Cached records by feature</h2>
            @forelse($cacheByFeature as $row)
            <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid #f0f2f8">
                <span style="flex:1;font-size:13px;color:#15233d;font-weight:600">{{ $row->feature }}</span>
                <span style="padding:3px 10px;border-radius:10px;background:#e8f4ff;color:#2563eb;font-size:12px;font-weight:700">{{ number_format($row->count) }}</span>
            </div>
            @empty
            <p style="color:#8a95a9;font-size:13px">No cached records yet.</p>
            @endforelse
        </div>
    </div>

    {{-- Recent API call log --}}
    <div style="max-width:1100px;margin:0 auto 40px;padding:26px;border:1px solid #e6e8f0;border-radius:18px;background:#fff">
        <h2 style="margin:0 0 18px;font-size:15px;color:#15233d">Recent API calls (last 50)</h2>
        <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse;font-size:12px">
                <thead>
                    <tr style="background:#f7f8fc;color:#69758b;text-transform:uppercase;font-size:10px;letter-spacing:.06em">
                        <th style="padding:10px 12px;text-align:left">Date</th>
                        <th style="padding:10px 12px;text-align:left">Feature</th>
                        <th style="padding:10px 12px;text-align:left">Location</th>
                        <th style="padding:10px 12px;text-align:left">TZ</th>
                        <th style="padding:10px 12px;text-align:right">HTTP</th>
                        <th style="padding:10px 12px;text-align:right">ms</th>
                        <th style="padding:10px 12px;text-align:center">Status</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($recentLogs as $log)
                <tr style="border-bottom:1px solid #f0f2f8">
                    <td style="padding:9px 12px;color:#536078">{{ $log->request_date->format('d M Y') }}</td>
                    <td style="padding:9px 12px;color:#15233d;font-weight:600">{{ $log->feature }}</td>
                    <td style="padding:9px 12px;color:#536078">{{ $log->latitude ? number_format($log->latitude, 4).','.number_format($log->longitude, 4) : '—' }}</td>
                    <td style="padding:9px 12px;color:#536078">{{ $log->timezone !== null ? ($log->timezone >= 0 ? '+' : '').$log->timezone : '—' }}</td>
                    <td style="padding:9px 12px;text-align:right;color:#536078">{{ $log->http_status ?? '—' }}</td>
                    <td style="padding:9px 12px;text-align:right;color:#536078">{{ $log->response_time_ms ?? '—' }}</td>
                    <td style="padding:9px 12px;text-align:center">
                        @if($log->success)
                            <span style="padding:2px 8px;border-radius:10px;background:#e4f6ea;color:#276946;font-size:10px;font-weight:800">OK</span>
                        @else
                            <span style="padding:2px 8px;border-radius:10px;background:#fdeaea;color:#c0392b;font-size:10px;font-weight:800" title="{{ $log->error_message }}">ERR</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="padding:20px;text-align:center;color:#8a95a9">No API calls logged yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</main>
@endsection
