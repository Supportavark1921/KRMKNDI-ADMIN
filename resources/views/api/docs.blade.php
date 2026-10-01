@extends('layouts.app')
@section('content')
<style>
:root{--bg:#0c111c;--surface:#111827;--surface2:#161f30;--border:#1e2c45;--muted:#4f617e;--dim:#8a9ab8;--text:#d4deef;--bright:#e8eef8;--violet:#6246ea;--blue:#2a6ef5;--gold:#f3b44c;--green:#29a96e;--red:#e04a4a}
*{box-sizing:border-box}
.docs-page{background:var(--bg)!important;min-height:100vh;font-family:Inter,ui-sans-serif,system-ui,sans-serif;color:var(--text)}
.btn-ghost{display:inline-flex;align-items:center;gap:5px;padding:7px 13px;border-radius:8px;border:1px solid var(--border);color:var(--dim);font-size:12px;font-weight:700;background:transparent;cursor:pointer;text-decoration:none;transition:.15s}
.btn-ghost:hover{border-color:var(--violet);color:var(--text);background:#1a1545}
.btn-primary{background:var(--violet);border-color:var(--violet);color:#fff}
.btn-primary:hover{background:#4934c4}

/* ── Hero ── */
.api-hero{padding:36px 32px 28px;background:linear-gradient(130deg,#0c111c 0%,#130d35 55%,#0c111c 100%);border-bottom:1px solid var(--border)}
.hero-kicker{color:var(--gold);font-size:10px;font-weight:800;letter-spacing:.14em;text-transform:uppercase;margin-bottom:10px}
.hero-title{font-size:30px;font-weight:800;color:var(--bright);margin:0 0 8px;letter-spacing:-.02em}
.hero-sub{color:var(--muted);font-size:14px;line-height:1.6;max-width:600px;margin:0 0 18px}
.hero-meta{display:flex;flex-wrap:wrap;gap:8px}
.meta-badge{display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:7px;font-size:11px;font-weight:700}
.mb-ver{background:#6246ea18;color:#9d80ef;border:1px solid #6246ea30}
.mb-base{background:#2a6ef518;color:#7eb8f8;border:1px solid #2a6ef530;font-family:ui-monospace,monospace}
.mb-fmt{background:#29a96e18;color:#4caf7a;border:1px solid #29a96e30}

/* ── Layout ── */
.docs-layout{display:grid;grid-template-columns:220px 1fr;gap:0;min-height:calc(100vh - 60px)}
.docs-sidebar{position:sticky;top:0;height:100vh;overflow-y:auto;padding:24px 16px;border-right:1px solid var(--border);background:var(--surface);scrollbar-width:thin;scrollbar-color:var(--border) transparent}
.docs-sidebar::-webkit-scrollbar{width:4px}
.docs-sidebar::-webkit-scrollbar-thumb{background:var(--border);border-radius:4px}
.sidebar-section{margin-bottom:24px}
.sidebar-label{color:var(--muted);font-size:10px;font-weight:800;letter-spacing:.1em;text-transform:uppercase;padding:0 8px;margin-bottom:8px}
.sidebar-nav{display:grid;gap:2px}
.sidebar-nav a{display:flex;align-items:center;gap:8px;padding:7px 8px;border-radius:7px;color:var(--dim);font-size:13px;font-weight:600;text-decoration:none;transition:.15s;cursor:pointer}
.sidebar-nav a:hover,.sidebar-nav a.active{color:var(--bright);background:#ffffff0c}
.sidebar-nav a.active{color:var(--violet)}
.sidebar-dot{width:6px;height:6px;border-radius:50%;flex-shrink:0}
.dot-get{background:var(--blue)}
.dot-post{background:var(--green)}
.dot-put{background:var(--gold)}
.dot-delete{background:var(--red)}

/* ── Main content ── */
.docs-main{padding:32px 40px 80px;overflow-x:hidden}
.section-anchor{scroll-margin-top:70px}

/* ── Group card ── */
.api-group{margin-bottom:16px;border:1px solid var(--border);border-radius:14px;overflow:hidden;background:var(--surface)}
.group-header{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;cursor:pointer;user-select:none;background:var(--surface2);transition:.15s}
.group-header:hover{background:#18263d}
.group-left{display:flex;align-items:center;gap:12px}
.group-icon{display:grid;width:34px;height:34px;place-items:center;border-radius:9px;font-size:18px;flex-shrink:0}
.group-title{font-size:16px;font-weight:800;color:var(--bright)}
.group-sub{font-size:12px;color:var(--muted);margin-top:2px}
.group-count{padding:3px 9px;border-radius:20px;font-size:11px;font-weight:800;color:var(--dim);background:#ffffff0a;border:1px solid var(--border)}
.group-chevron{color:var(--muted);font-size:18px;transition:transform .2s;flex-shrink:0}
.group-chevron.open{transform:rotate(180deg)}
.group-body{border-top:1px solid var(--border)}
.group-body.collapsed{display:none}

/* ── Endpoint row ── */
.endpoint{display:grid;grid-template-columns:80px 1fr auto;gap:14px;align-items:start;padding:14px 20px;border-bottom:1px solid var(--border);cursor:pointer;transition:.15s}
.endpoint:last-child{border-bottom:0}
.endpoint:hover{background:#ffffff05}
.endpoint.open{background:#0d1525}
.method-badge{display:inline-flex;align-items:center;justify-content:center;padding:4px 8px;border-radius:7px;font-size:10px;font-weight:800;letter-spacing:.04em;min-width:64px;margin-top:1px}
.m-get{background:#2a6ef520;color:#7eb8f8;border:1px solid #2a6ef540}
.m-post{background:#29a96e20;color:#4caf7a;border:1px solid #29a96e40}
.m-put{background:#f3b44c20;color:#f3b44c;border:1px solid #f3b44c40}
.m-patch{background:#e09a2020;color:#e0b44c;border:1px solid #e0b44c40}
.m-delete{background:#e04a4a20;color:#e07a7a;border:1px solid #e04a4a40}
.endpoint-path{font-family:ui-monospace,monospace;font-size:13px;color:var(--bright);font-weight:600;word-break:break-all;line-height:1.4}
.endpoint-path em{color:var(--violet);font-style:normal}
.endpoint-summary{font-size:12px;color:var(--dim);margin-top:4px}
.endpoint-arrow{color:var(--muted);font-size:14px;margin-top:3px;transition:transform .2s}
.endpoint.open .endpoint-arrow{transform:rotate(90deg)}

/* ── Endpoint detail panel ── */
.endpoint-panel{display:none;padding:20px 24px;background:#080e1a;border-bottom:1px solid var(--border)}
.endpoint-panel.open{display:block}
.panel-row{display:grid;grid-template-columns:120px 1fr;gap:12px;margin-bottom:16px;font-size:13px}
.panel-label{color:var(--muted);font-weight:700;padding-top:2px}
.panel-value{color:var(--text)}
.params-table{width:100%;border-collapse:collapse;font-size:12px;margin-top:6px}
.params-table th{padding:7px 10px;text-align:left;color:var(--muted);font-weight:700;font-size:10px;letter-spacing:.06em;text-transform:uppercase;border-bottom:1px solid var(--border);background:#0d1627}
.params-table td{padding:8px 10px;border-bottom:1px solid #0f1c30;vertical-align:top}
.params-table tr:last-child td{border-bottom:0}
.param-name{font-family:ui-monospace,monospace;color:var(--bright);font-weight:600}
.param-type{display:inline-block;padding:2px 6px;border-radius:4px;background:#6246ea18;color:#9d80ef;font-size:10px;font-weight:700;font-family:ui-monospace,monospace}
.param-in{display:inline-block;padding:2px 6px;border-radius:4px;background:#f3b44c18;color:var(--gold);font-size:10px;font-weight:700}
.param-req{color:var(--red);font-size:10px;font-weight:800;margin-left:4px}
.code-block{position:relative;padding:14px 16px;border-radius:10px;background:#060c18;border:1px solid var(--border);font-family:ui-monospace,monospace;font-size:12px;color:#c8d4f0;line-height:1.6;overflow-x:auto;margin-top:6px;white-space:pre}
.code-block .s{color:#7ec8f8}
.code-block .n{color:#9d80ef}
.code-block .v{color:#f3b44c}
.code-block .b{color:#4caf7a}
.response-tabs{display:flex;gap:6px;margin-bottom:10px}
.rtab{padding:5px 11px;border-radius:6px;font-size:11px;font-weight:700;cursor:pointer;border:1px solid var(--border);color:var(--muted);background:transparent;transition:.15s}
.rtab.active,.rtab:hover{color:var(--bright);border-color:var(--violet);background:#1a1545}
.status-200{color:#4caf7a}
.status-404{color:#e07a7a}
.status-422{color:var(--gold)}
.try-btn{margin-top:14px;padding:8px 16px;border-radius:8px;background:var(--violet);border:none;color:#fff;font-size:12px;font-weight:700;cursor:pointer;transition:.15s;text-decoration:none;display:inline-flex;align-items:center;gap:6px}
.try-btn:hover{background:#4934c4}

/* ── Quick links bar ── */
.quick-bar{padding:18px 40px;background:var(--surface2);border-bottom:1px solid var(--border)}
.quick-bar-title{color:var(--muted);font-size:10px;font-weight:800;letter-spacing:.1em;text-transform:uppercase;margin-bottom:10px}
.quick-links{display:flex;flex-wrap:wrap;gap:7px}
.ql{display:inline-flex;align-items:center;gap:7px;padding:6px 11px;border-radius:8px;background:#0a1220;border:1px solid var(--border);color:var(--dim);font-size:11px;font-weight:600;text-decoration:none;transition:.15s}
.ql:hover{border-color:var(--violet);color:var(--text);background:#12103a}
.ql .qm{display:inline-block;padding:2px 5px;border-radius:4px;font-size:9px;font-weight:800;letter-spacing:.04em}
.ql .qp{font-family:ui-monospace,monospace;font-size:10px}
.qm-get{background:var(--blue);color:#fff}
.qm-post{background:var(--green);color:#fff}

@media(max-width:900px){.docs-layout{grid-template-columns:1fr}.docs-sidebar{display:none}.docs-main{padding:20px}.endpoint{grid-template-columns:64px 1fr auto}}
</style>

<div class="dashboard docs-page">

{{-- Hero --}}
<div class="api-hero" style="display:flex;align-items:flex-start;justify-content:space-between;gap:20px">
    <div>
        <div class="hero-kicker">REST API · OpenAPI 3.0</div>
        <h1 class="hero-title">ARK Jyotish API</h1>
        <p class="hero-sub">All endpoints consumed by the ARK Jyotish mobile app. All responses are JSON.</p>
        <div class="hero-meta">
            <span class="meta-badge mb-ver">v1.0.0</span>
            <span class="meta-badge mb-base">{{ config('app.url') }}/api</span>
            <span class="meta-badge mb-fmt">JSON · UTF-8</span>
        </div>
    </div>
    <a href="/api/openapi.json" target="_blank" class="btn-ghost" style="flex-shrink:0;margin-top:6px">⬇ OpenAPI JSON</a>
</div>

{{-- Quick test links --}}
<div class="quick-bar">
    <div class="quick-bar-title">⚡ Quick Test</div>
    <div class="quick-links">
        @foreach([
            ['GET',  '/api/services'],
            ['GET',  '/api/services?language=hi'],
            ['GET',  '/api/gurus'],
            ['GET',  '/api/donation/fee-config'],
            ['GET',  '/api/locations/countries'],
            ['GET',  '/api/locations/countries/iso/IN/states'],
            ['GET',  '/api/locations/pincodes/452001'],
            ['GET',  '/api/locations/search?q=indore'],
            ['GET',  '/api/v1/panchang?latitude=22.7196&longitude=75.8577&timezone=5.5&date='.date('Y-m-d')],
        ] as [$m, $p])
        <a href="{{ config('app.url') }}{{ $p }}" target="_blank" class="ql">
            <span class="qm qm-{{ strtolower($m) }}">{{ $m }}</span>
            <span class="qp">{{ $p }}</span>
        </a>
        @endforeach
    </div>
</div>

{{-- Main docs layout --}}
<div class="docs-layout">

    {{-- Sidebar --}}
    <aside class="docs-sidebar">
        <div class="sidebar-section">
            <div class="sidebar-label">Groups</div>
            <nav class="sidebar-nav">
                @foreach([
                    ['🕉',  'gurujis',   'Gurujis',   '#group-gurujis'],
                    ['₹',   'donations', 'Donations', '#group-donations'],
                    ['✦',   'services',  'Services',  '#group-services'],
                    ['📍',  'location',  'Location',  '#group-location'],
                    ['🌙',  'panchang',  'Panchang',  '#group-panchang'],
                ] as [$icon, $id, $label, $href])
                <a href="{{ $href }}" onclick="scrollTo('{{ $id }}')">
                    <span>{{ $icon }}</span> {{ $label }}
                </a>
                @endforeach
            </nav>
        </div>
        <div class="sidebar-section">
            <div class="sidebar-label">Resources</div>
            <nav class="sidebar-nav">
                <a href="/api/openapi.json" target="_blank"><span class="sidebar-dot dot-get"></span> OpenAPI spec</a>
                <a href="{{ route('admin.location.sync') }}"><span class="sidebar-dot dot-put"></span> Location sync</a>
            </nav>
        </div>
    </aside>

    {{-- Content --}}
    <main class="docs-main">

        @php
        $groups = [
            'gurujis' => [
                'icon'  => '🕉',
                'title' => 'Gurujis',
                'sub'   => 'Guruji profiles, donation categories, per-Guruji stats',
                'color' => 'background:#2a1a45;color:#c49ef8',
                'endpoints' => [
                    ['GET',  '/api/gurus',                       'List all active Gurujis',
                        'desc' => 'Returns all Gurujis with status = active. Used by the APK to populate the Guruji selection screen.',
                        'params' => [],
                        'response' => '{ "data": [{ "id": 1, "name": "Shri XYZ Maharaj", "description": "...", "image": null, "status": "active" }] }',
                    ],
                    ['GET',  '/api/gurus/{id}',                  'Get a single Guruji with stats',
                        'desc' => 'Returns full Guruji profile including total donations, donors and transaction count.',
                        'params' => [['id','path','integer','required','Guruji ID','1']],
                        'response' => '{ "data": { "id": 1, "name": "Shri XYZ Maharaj", "stats": { "total_donations": 52500, "total_donors": 120, "total_transactions": 145 } } }',
                    ],
                    ['GET',  '/api/gurus/{id}/donation-categories','Get Guruji donation categories + fee config',
                        'desc' => 'Returns the Guruji\'s active donation categories with the current fee config. Use this to build the APK donation screen — it gives everything needed for the payment breakdown.',
                        'params' => [['id','path','integer','required','Guruji ID','1']],
                        'response' => '{ "data": { "guru": { "id": 1, "name": "..." }, "categories": [...], "fee_config": { "handling_charge": 1.00, "gst_rate": 18.00 } } }',
                    ],
                ],
            ],
            'donations' => [
                'icon'  => '₹',
                'title' => 'Donations',
                'sub'   => 'App handling fee config and donation transactions',
                'color' => 'background:#1a2a1a;color:#4caf7a',
                'endpoints' => [
                    ['GET',  '/api/donation/fee-config', 'Get current app handling fee configuration',
                        'desc' => 'Returns the active handling charge and GST rate, plus a live example on ₹500. The APK must use this to calculate and display the breakdown before payment — never hardcode the fee.',
                        'params' => [],
                        'response' => '{ "data": { "handling_charge": 1.00, "gst_rate": 18.00, "gst_label": "GST @ 18%", "example_on_500": { "total_amount": 501.18 } } }',
                    ],
                    ['POST', '/api/donations', 'Create a donation transaction',
                        'desc' => 'Creates a new donation. The server always recalculates handling_charge, gst_amount, and total_amount. If transaction_id already exists, the existing record is returned with created: false.',
                        'params' => [
                            ['guru_id','body','integer','required','Guruji ID','1'],
                            ['category_id','body','integer','required','Donation category ID','1'],
                            ['donation_amount','body','number','required','Pure donation amount (excl. fees)','500'],
                            ['payment_status','body','string','required','pending | success | failed | cancelled','success'],
                            ['transaction_id','body','string','optional','Idempotency key','TXN20261001001'],
                            ['payment_method','body','string','optional','UPI | card | netbanking | wallet','UPI'],
                            ['payment_id','body','string','optional','Gateway payment ID','pay_Abc123XYZ'],
                            ['user_id','body','integer','optional','App user ID',null],
                        ],
                        'response' => '{ "data": { "id": 1, "donation_id": "DON-00001", "breakdown": { "donation_amount": 500, "total_amount": 501.18 }, "payment_status": "success" }, "created": true }',
                    ],
                ],
            ],
            'services' => [
                'icon'  => '✦',
                'title' => 'Services',
                'sub'   => 'Multilingual booking service catalogue',
                'color' => 'background:#1a1a2a;color:#7eb8f8',
                'endpoints' => [
                    ['GET', '/api/services', 'List all services',
                        'desc' => 'Returns services with translatable fields resolved for the requested language. Falls back to English when the translation is unavailable.',
                        'params' => [
                            ['language','query','string','optional','BCP-47 code: en | hi | mr | gu | ta | te | bn','hi'],
                            ['status','query','string','optional','active | inactive | all (default: active)','active'],
                        ],
                        'response' => '{ "data": [{ "id": 1, "name": "ज्योतिष परामर्श", "language": "hi", "fallback_used": false, "pricing": { "amount": 999, "currency": "INR" }, "status": "active" }] }',
                    ],
                    ['GET', '/api/services/{id}', 'Get a single service',
                        'desc' => 'Returns full service detail for one record.',
                        'params' => [
                            ['id','path','integer','required','Service ID','1'],
                            ['language','query','string','optional','BCP-47 language code','en'],
                        ],
                        'response' => '{ "data": { "id": 1, "name": "Jyotish Consultation", "language": "en", "available_languages": ["en","hi"], "pricing": { "amount": 999 } } }',
                    ],
                ],
            ],
            'location' => [
                'icon'  => '📍',
                'title' => 'Location',
                'sub'   => 'Cascading master — Country → State → District → City → PIN',
                'color' => 'background:#1a2218;color:#67c98a',
                'endpoints' => [
                    ['GET', '/api/locations/countries', 'List all active countries',
                        'desc' => 'Returns all active countries. Currently India (IN) is seeded.',
                        'params' => [],
                        'response' => '[{ "id": 1, "name": "India", "iso_code": "IN", "phone_code": "+91", "currency_code": "INR" }]',
                    ],
                    ['GET', '/api/locations/countries/{country_id}/states', 'List states / UTs for a country',
                        'desc' => 'Returns all active states and Union Territories belonging to the given country ID.',
                        'params' => [['country_id','path','integer','required','Country ID','1']],
                        'response' => '[{ "id": 13, "name": "Madhya Pradesh", "code": "MP", "type": "STATE" }, { "id": 32, "name": "Delhi", "code": "DL", "type": "UNION_TERRITORY" }]',
                    ],
                    ['GET', '/api/locations/countries/iso/{iso}/states', 'List states by ISO code',
                        'desc' => 'Shorthand — pass the 2-letter ISO code instead of the numeric ID.',
                        'params' => [['iso','path','string','required','2-letter ISO country code','IN']],
                        'response' => '[{ "id": 13, "name": "Madhya Pradesh", "code": "MP", "type": "STATE" }, ...]',
                    ],
                    ['GET', '/api/locations/states/{state_id}/districts', 'List districts for a state',
                        'desc' => 'Returns all active districts belonging to the given state. Only districts for that state are returned — Indore never appears under Maharashtra.',
                        'params' => [['state_id','path','integer','required','State ID','13']],
                        'response' => '[{ "id": 1, "state_id": 13, "name": "Indore" }, { "id": 2, "state_id": 13, "name": "Bhopal" }]',
                    ],
                    ['GET', '/api/locations/districts/{district_id}/cities', 'List cities for a district',
                        'desc' => 'Returns all active cities / localities within the given district.',
                        'params' => [['district_id','path','integer','required','District ID','1']],
                        'response' => '[{ "id": 1, "name": "Indore" }, { "id": 2, "name": "Rau" }, { "id": 3, "name": "Mhow" }]',
                    ],
                    ['GET', '/api/locations/cities/{city_id}/pincodes', 'List PIN codes for a city',
                        'desc' => 'One city can have multiple post offices and multiple PIN codes. Returns all active entries for that city.',
                        'params' => [['city_id','path','integer','required','City ID','1']],
                        'response' => '[{ "id": 1, "pincode": "452001", "post_office_name": "Indore H.O", "office_type": "HEAD POST OFFICE", "delivery_status": "Delivery" }, { "id": 2, "pincode": "452002", "post_office_name": "Indore Cloth Market S.O" }]',
                    ],
                    ['GET', '/api/locations/pincodes/{pincode}', 'Reverse PIN code lookup',
                        'desc' => 'Enter a 6-digit PIN code to get all matching post offices with their state, district and city resolved. Use this to auto-fill an address form.',
                        'params' => [['pincode','path','string','required','6-digit India PIN code','452001']],
                        'response' => '[{ "id": 1, "pincode": "452001", "post_office_name": "Indore H.O", "state": { "name": "Madhya Pradesh", "code": "MP" }, "district": { "name": "Indore" }, "city": { "name": "Indore" } }]',
                    ],
                    ['GET', '/api/locations/search?q=', 'Search cities, districts, or PIN codes',
                        'desc' => 'Type-aware: a digit string (e.g. "452") searches PIN prefixes; text (e.g. "indore") searches cities and districts. Minimum 2 characters.',
                        'params' => [['q','query','string','required','Search term — text or digit prefix','indore']],
                        'response' => '[{ "type": "city", "label": "Indore, Indore, Madhya Pradesh", "city": "Indore", "district": "Indore", "state": "Madhya Pradesh", "state_code": "MP" }]',
                    ],
                ],
            ],
            'panchang' => [
                'icon'  => '🌙',
                'title' => 'Panchang',
                'sub'   => 'Daily Vedic Panchang — Tithi, Nakshatra, Yoga, Karana, Rahu Kaal, Abhijit',
                'color' => 'background:#1a1a0a;color:#f3e07a',
                'endpoints' => [
                    ['GET', '/api/v1/panchang', 'Get Vedic Panchang for a location and date',
                        'desc' => 'Returns full or feature-specific Panchang data. Coordinates are rounded to 2 decimal places (~1 km grid) for cache efficiency. The first call for a new location+date fetches from Navamsha API; subsequent calls within the same calendar day are served from cache. No authentication required — called directly from the mobile APK.',
                        'params' => [
                            ['latitude',  'query', 'float',  'required', 'Decimal latitude (-90 to 90)',                    '22.7196'],
                            ['longitude', 'query', 'float',  'required', 'Decimal longitude (-180 to 180)',                 '75.8577'],
                            ['timezone',  'query', 'float',  'required', 'UTC offset in decimal hours (e.g. 5.5 for IST)',  '5.5'],
                            ['date',      'query', 'string', 'optional', 'Date in YYYY-MM-DD (defaults to today)',          date('Y-m-d')],
                            ['feature',   'query', 'string', 'optional', 'panchang_full | choghadiya | hora | rahu_kaal | sun_times | abhijit', 'panchang_full'],
                            ['location',  'query', 'string', 'optional', 'Human-readable location name (stored in cache)', 'Indore, Madhya Pradesh'],
                        ],
                        'response' => '{ "success": true, "cached": false, "date": "'.date('Y-m-d').'", "location": { "name": "Indore, Madhya Pradesh", "latitude": 22.72, "longitude": 75.86, "timezone": 5.5 }, "data": { "tithi": { "name": "Tritiya", "number": 3, "paksha": "Shukla" }, "nakshatra": { "name": "Rohini", "number": 4 }, "yoga": { "name": "Shobhana" }, "karana": { "name": "Bava" }, "weekday": { "name": "Thursday" }, "sun_rise": "06:17:42", "sun_set": "18:08:33", "rahu_kaal": { "start": "13:45:00", "end": "15:15:00" } } }',
                    ],
                ],
            ],
        ];
        @endphp

        @foreach($groups as $groupId => $group)
        <div class="api-group section-anchor" id="group-{{ $groupId }}">
            <div class="group-header" onclick="toggleGroup('{{ $groupId }}')">
                <div class="group-left">
                    <div class="group-icon" style="{{ $group['color'] }}">{{ $group['icon'] }}</div>
                    <div>
                        <div class="group-title">{{ $group['title'] }}</div>
                        <div class="group-sub">{{ $group['sub'] }}</div>
                    </div>
                </div>
                <div style="display:flex;align-items:center;gap:10px">
                    <span class="group-count">{{ count($group['endpoints']) }} endpoints</span>
                    <span class="group-chevron open" id="chevron-{{ $groupId }}">⌄</span>
                </div>
            </div>

            <div class="group-body" id="body-{{ $groupId }}">
                @foreach($group['endpoints'] as $epIdx => $ep)
                @php
                    $epId = $groupId . '-' . $epIdx;
                    $methodClass = 'm-' . strtolower($ep[0]);
                @endphp
                <div class="endpoint" id="ep-{{ $epId }}" onclick="toggleEndpoint('{{ $epId }}')">
                    <span class="method-badge {{ $methodClass }}">{{ $ep[0] }}</span>
                    <div>
                        <div class="endpoint-path">{{ preg_replace('/\{(\w+)\}/', '<em>{$1}</em>', $ep[1]) }}</div>
                        <div class="endpoint-summary">{{ $ep[2] }}</div>
                    </div>
                    <span class="endpoint-arrow">›</span>
                </div>
                <div class="endpoint-panel" id="panel-{{ $epId }}">
                    {{-- Description --}}
                    @if(!empty($ep['desc']))
                    <div class="panel-row">
                        <span class="panel-label">Description</span>
                        <span class="panel-value" style="color:var(--dim)">{{ $ep['desc'] }}</span>
                    </div>
                    @endif

                    {{-- Parameters --}}
                    @if(!empty($ep['params']))
                    <div class="panel-row">
                        <span class="panel-label">Parameters</span>
                        <div>
                            <table class="params-table">
                                <thead><tr>
                                    <th>Name</th><th>In</th><th>Type</th><th>Required</th><th>Description</th><th>Example</th>
                                </tr></thead>
                                <tbody>
                                @foreach($ep['params'] as $param)
                                <tr>
                                    <td><span class="param-name">{{ $param[0] }}</span></td>
                                    <td><span class="param-in">{{ $param[1] }}</span></td>
                                    <td><span class="param-type">{{ $param[2] }}</span></td>
                                    <td style="color:{{ $param[3]==='required' ? 'var(--red)' : 'var(--muted)' }};font-size:11px;font-weight:700">{{ ucfirst($param[3]) }}</td>
                                    <td style="color:var(--dim);font-size:12px">{{ $param[4] }}</td>
                                    <td><code style="font-family:ui-monospace,monospace;font-size:11px;color:var(--gold)">{{ $param[5] ?? '—' }}</code></td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    {{-- Response example --}}
                    @if(!empty($ep['response']))
                    <div class="panel-row">
                        <span class="panel-label">Response</span>
                        <div>
                            <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px">
                                <span style="padding:3px 8px;border-radius:5px;background:#29a96e18;color:#4caf7a;font-size:11px;font-weight:800;border:1px solid #29a96e30">200 OK</span>
                                <span style="color:var(--muted);font-size:11px">application/json</span>
                            </div>
                            <div class="code-block">{{ $ep['response'] }}</div>
                        </div>
                    </div>
                    @endif

                    {{-- Try it --}}
                    @php
                        $tryUrl = str_contains($ep[1], '{')
                            ? null
                            : config('app.url') . $ep[1];
                    @endphp
                    @if($tryUrl && $ep[0] === 'GET')
                    <a href="{{ $tryUrl }}" target="_blank" class="try-btn">▶ Try in browser</a>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

    </main>
</div>

</div>

<script>
function toggleGroup(id) {
    const body    = document.getElementById('body-' + id);
    const chevron = document.getElementById('chevron-' + id);
    const open    = !body.classList.contains('collapsed');
    body.classList.toggle('collapsed', open);
    chevron.classList.toggle('open', !open);
}

function toggleEndpoint(id) {
    const ep    = document.getElementById('ep-' + id);
    const panel = document.getElementById('panel-' + id);
    const open  = ep.classList.contains('open');
    ep.classList.toggle('open', !open);
    panel.classList.toggle('open', !open);
}

function scrollTo(groupId) {
    const el = document.getElementById('group-' + groupId);
    if (el) {
        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        // Ensure group is open
        const body = document.getElementById('body-' + groupId);
        const chevron = document.getElementById('chevron-' + groupId);
        if (body.classList.contains('collapsed')) {
            body.classList.remove('collapsed');
            chevron.classList.add('open');
        }
    }
    return false;
}

// Highlight active sidebar link on scroll
const observer = new IntersectionObserver(entries => {
    entries.forEach(e => {
        if (e.isIntersecting) {
            document.querySelectorAll('.sidebar-nav a').forEach(a => a.classList.remove('active'));
            const id = e.target.id.replace('group-', '');
            const link = document.querySelector(`.sidebar-nav a[onclick*="${id}"]`);
            if (link) link.classList.add('active');
        }
    });
}, { rootMargin: '-20% 0px -70% 0px' });

document.querySelectorAll('.section-anchor').forEach(el => observer.observe(el));
</script>
@endsection
