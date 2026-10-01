@extends('layouts.app')

@section('content')
<div class="dashboard">
    <div class="topbar">
        <a class="nav-link topbar-link" href="{{ route('admin.location.countries.index') }}">← Location Master</a>
        <form method="post" action="{{ route('logout') }}">@csrf<button class="logout">Sign out</button></form>
    </div>

    <div style="max-width:780px;margin:40px auto 0">
        <div style="padding:40px;border:1px solid #e6e8f0;border-radius:20px;background:#fff;box-shadow:0 18px 45px #23325c0b">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:20px;margin-bottom:28px">
                <div>
                    <span style="display:inline-block;padding:4px 9px;border-radius:20px;color:#5c4bb7;background:#f0ecff;font-size:11px;font-weight:800;letter-spacing:.1em;text-transform:uppercase">India Location Data</span>
                    <h1 style="margin:10px 0 6px;font-size:32px;color:#15233d">Sync India Postal Data</h1>
                    <p style="color:#69758b;line-height:1.6;max-width:520px;margin:0">Import or refresh the complete India Post / data.gov.in pincode directory. The import reads a CSV you supply and upserts states, districts, cities and PIN codes without deleting existing records.</p>
                </div>
            </div>

            @if(session('success'))<div class="notice success">{{ session('success') }}</div>@endif
            @if(session('error'))<div class="notice error">{{ session('error') }}</div>@endif

            {{-- Stats --}}
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:30px">
                @foreach([['States/UTs', $stats['states'], route('admin.location.states.index')], ['Districts', $stats['districts'], route('admin.location.districts.index')], ['Cities', $stats['cities'], route('admin.location.cities.index')], ['PIN Codes', $stats['pincodes'], route('admin.location.pincodes.index')]] as [$label, $count, $href])
                <div style="padding:18px;border:1px solid #e6e9f0;border-radius:14px;background:#f9fafc">
                    <span style="display:block;color:#69758b;font-size:12px;margin-bottom:6px">{{ $label }}</span>
                    <strong style="display:block;font-size:26px;color:#15233d;margin-bottom:6px">{{ number_format($count) }}</strong>
                    <a href="{{ $href }}" style="color:#5542ad;font-size:12px;font-weight:700">View →</a>
                </div>
                @endforeach
            </div>

            {{-- Current data note --}}
            <div style="padding:16px 20px;border:1px solid #d4edda;border-radius:14px;background:#f0faf3;margin-bottom:20px">
                <strong style="color:#1d6a45;font-size:13px">✓ Seeded data active</strong>
                <p style="margin:5px 0 0;color:#3a7a55;font-size:13px;line-height:1.5">11 major states with real India Post PIN codes are pre-loaded via seeds. This covers daily use. Replace with the full dataset when data.gov.in is available.</p>
            </div>

            {{-- How to import --}}
            <div style="padding:20px;border:1px solid #fce5a2;border-radius:14px;background:#fffdf5;margin-bottom:28px">
                <strong style="display:block;margin-bottom:8px;color:#7a5a10">How to import the full dataset</strong>
                <ol style="margin:0;padding-left:20px;color:#69758b;font-size:13px;line-height:1.9">
                    <li>Download the <strong>All India Pincode Directory CSV</strong> from <strong>data.gov.in</strong><br><span style="font-size:12px;color:#9b8a5b">Search: "All India Pincode Directory" → download the latest CSV (~150k rows)</span></li>
                    <li>Place the file at: <code style="background:#f0f2f7;padding:2px 5px;border-radius:4px;font-size:12px">storage/app/imports/india_pincodes.csv</code></li>
                    <li>Validate first (no DB writes):<br><code style="background:#f0f2f7;padding:2px 5px;border-radius:4px;font-size:12px">php artisan location:import-india --dry-run</code></li>
                    <li>Run the full import via SSH (recommended for large files):<br><code style="background:#f0f2f7;padding:2px 5px;border-radius:4px;font-size:12px">php artisan location:import-india</code></li>
                    <li>Or click <strong>Run Import</strong> below for small/test CSVs</li>
                </ol>
            </div>

            <form method="post" action="{{ route('admin.location.sync.run') }}" onsubmit="return confirm('Queue the India postal import? This runs in the background.')">
                @csrf
                <button type="submit" style="width:auto;padding:13px 22px;font-size:14px">⟳ Run Import</button>
            </form>
        </div>

        {{-- Navigation tiles --}}
        <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:14px;margin-top:22px">
            @foreach([['Countries', route('admin.location.countries.index'), '🌏'], ['States / UTs', route('admin.location.states.index'), '🗺'], ['Districts', route('admin.location.districts.index'), '📍'], ['Cities', route('admin.location.cities.index'), '🏙'], ['PIN Codes', route('admin.location.pincodes.index'), '✉']] as [$label, $href, $icon])
            <a href="{{ $href }}" style="display:flex;align-items:center;gap:14px;padding:16px 18px;border:1px solid #e6e8f0;border-radius:14px;background:#fff;color:#15233d;text-decoration:none;transition:box-shadow .2s" onmouseover="this.style.boxShadow='0 8px 20px #1c295514'" onmouseout="this.style.boxShadow='none'">
                <span style="font-size:22px">{{ $icon }}</span>
                <span style="font-weight:700;font-size:14px">{{ $label }}</span>
                <span style="margin-left:auto;color:#5542ad;font-weight:700">→</span>
            </a>
            @endforeach
        </div>
    </div>
</div>
@endsection
