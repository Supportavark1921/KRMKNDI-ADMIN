@extends('layouts.app', ['title' => 'Panchang API Test'])
@section('content')
<style>
.test-wrap{max-width:900px;margin:32px auto;padding:0 20px 80px}
.test-card{background:#fff;border:1px solid #e6e8f0;border-radius:18px;padding:28px;margin-bottom:22px}
.test-card h2{margin:0 0 18px;font-size:15px;font-weight:800;color:#15233d}
.field-row{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px}
.field-row.triple{grid-template-columns:1fr 1fr 1fr}
.field-group label{display:block;font-size:11px;font-weight:700;color:#8a9ab8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:5px}
.field-group input,.field-group select{width:100%;padding:9px 12px;border:1px solid #dde1ef;border-radius:9px;font-size:14px;color:#15233d;background:#fafbfc;transition:.15s}
.field-group input:focus,.field-group select:focus{border-color:#6246ea;outline:none;box-shadow:0 0 0 3px #6246ea18;background:#fff}
.btn-run{width:100%;padding:12px;background:#6246ea;color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:800;cursor:pointer;transition:.15s;margin-top:4px}
.btn-run:hover{background:#4934c4}
.btn-run:disabled{background:#b0b8d4;cursor:not-allowed}
.response-area{display:none}
.status-bar{display:flex;align-items:center;gap:10px;margin-bottom:16px;padding:10px 14px;border-radius:10px;font-size:13px;font-weight:700}
.status-ok{background:#e4f6ea;color:#276946;border:1px solid #a8e6c3}
.status-err{background:#fdeaea;color:#c0392b;border:1px solid #f5a0a0}
.status-cached{background:#e8f4ff;color:#2563eb;border:1px solid #a0c8f8}
.tabs{display:flex;gap:6px;margin-bottom:14px;border-bottom:2px solid #e6e8f0;padding-bottom:0}
.tab{padding:8px 16px;font-size:12px;font-weight:700;color:#8a9ab8;cursor:pointer;border-bottom:2px solid transparent;margin-bottom:-2px;transition:.15s}
.tab.active{color:#6246ea;border-bottom-color:#6246ea}
.tab-panel{display:none}
.tab-panel.active{display:block}
pre.json-out{background:#0c111c;color:#c8d4f0;padding:18px;border-radius:12px;font-size:12px;line-height:1.6;overflow:auto;max-height:480px;margin:0;white-space:pre-wrap;word-break:break-word}
.json-key{color:#7eb8f8}
.json-str{color:#a8e6c3}
.json-num{color:#f3b44c}
.json-bool{color:#e07a7a}
.json-null{color:#8a9ab8}
.kv-grid{display:grid;grid-template-columns:180px 1fr;gap:6px 12px;font-size:13px}
.kv-label{color:#8a9ab8;font-weight:600;padding:3px 0}
.kv-val{color:#15233d;padding:3px 0;font-weight:500}
.section-divider{margin:12px 0;border:0;border-top:1px solid #f0f2f8}
.timing{font-size:11px;color:#8a9ab8;margin-left:auto}
</style>

<main class="dashboard">
<header class="topbar">
    <div class="page-heading"><span>🌙 Panchang API Test</span><small>Live test — calls Navamsha and shows full response</small></div>
    <div style="display:flex;gap:10px">
        <a class="nav-link topbar-link" href="{{ route('admin.panchang.monitor') }}">Monitor</a>
        <a class="nav-link topbar-link" href="{{ route('dashboard') }}">Dashboard</a>
    </div>
</header>

<div class="test-wrap">

    {{-- Input form --}}
    <div class="test-card">
        <h2>Request Parameters</h2>
        <div class="field-row triple">
            <div class="field-group">
                <label>Latitude</label>
                <input type="number" id="lat" step="any" value="22.7196" placeholder="22.7196">
            </div>
            <div class="field-group">
                <label>Longitude</label>
                <input type="number" id="lon" step="any" value="75.8577" placeholder="75.8577">
            </div>
            <div class="field-group">
                <label>Timezone (UTC offset)</label>
                <input type="number" id="tz" step="0.5" value="5.5" placeholder="5.5">
            </div>
        </div>
        <div class="field-row">
            <div class="field-group">
                <label>Date</label>
                <input type="date" id="date" value="{{ date('Y-m-d') }}">
            </div>
            <div class="field-group">
                <label>Feature</label>
                <select id="feature">
                    <option value="panchang_full">panchang_full (default)</option>
                    <option value="choghadiya">choghadiya</option>
                    <option value="hora">hora</option>
                    <option value="rahu_kaal">rahu_kaal</option>
                    <option value="sun_times">sun_times</option>
                    <option value="abhijit">abhijit</option>
                </select>
            </div>
        </div>
        <div class="field-row" style="grid-template-columns:1fr">
            <div class="field-group">
                <label>Location name (optional)</label>
                <input type="text" id="location" value="Indore, Madhya Pradesh" placeholder="e.g. Indore, Madhya Pradesh">
            </div>
        </div>
        <button class="btn-run" id="runBtn" onclick="runTest()">▶ Execute</button>
    </div>

    {{-- Response --}}
    <div class="test-card response-area" id="responseArea">
        <h2 style="display:flex;align-items:center">
            Response
            <span class="timing" id="timing"></span>
        </h2>

        <div id="statusBar"></div>

        <div class="tabs">
            <div class="tab active" onclick="switchTab('formatted')">Formatted</div>
            <div class="tab" onclick="switchTab('raw')">Raw JSON</div>
            <div class="tab" onclick="switchTab('navamsha')">Navamsha Response</div>
        </div>

        <div id="tab-formatted" class="tab-panel active">
            <div id="formattedOut"></div>
        </div>
        <div id="tab-raw" class="tab-panel">
            <pre class="json-out" id="rawOut"></pre>
        </div>
        <div id="tab-navamsha" class="tab-panel">
            <pre class="json-out" id="navamshaOut"></pre>
        </div>
    </div>

</div>
</main>

<script>
const API_URL = '{{ url("/api/v1/panchang") }}';

async function runTest() {
    const btn = document.getElementById('runBtn');
    btn.disabled = true;
    btn.textContent = '⏳ Calling Navamsha…';

    const params = new URLSearchParams({
        latitude:  document.getElementById('lat').value,
        longitude: document.getElementById('lon').value,
        timezone:  document.getElementById('tz').value,
        date:      document.getElementById('date').value,
        feature:   document.getElementById('feature').value,
        location:  document.getElementById('location').value,
    });

    const t0 = performance.now();
    try {
        const res  = await fetch(`${API_URL}?${params}`);
        const ms   = Math.round(performance.now() - t0);
        const body = await res.json();

        document.getElementById('timing').textContent = `${ms} ms`;
        renderResponse(body, res.status);
    } catch (e) {
        document.getElementById('statusBar').innerHTML =
            `<div class="status-bar status-err">✗ Network error: ${e.message}</div>`;
        document.getElementById('responseArea').style.display = 'block';
    } finally {
        btn.disabled = false;
        btn.textContent = '▶ Execute';
    }
}

function renderResponse(body, httpStatus) {
    const area = document.getElementById('responseArea');
    area.style.display = 'block';

    // Status bar
    let barClass = body.success ? (body.cached ? 'status-cached' : 'status-ok') : 'status-err';
    let barText  = body.success
        ? (body.cached ? `✓ HTTP ${httpStatus} · Served from cache` : `✓ HTTP ${httpStatus} · Live from Navamsha API`)
        : `✗ HTTP ${httpStatus} · ${body.error?.code ?? 'Error'}`;
    document.getElementById('statusBar').innerHTML =
        `<div class="status-bar ${barClass}">${barText}</div>`;

    // Formatted tab
    document.getElementById('formattedOut').innerHTML = body.success
        ? buildFormatted(body)
        : `<div style="color:#c0392b;font-size:13px;padding:8px 0">${body.error?.message ?? 'Unknown error'}</div>`;

    // Raw tab
    document.getElementById('rawOut').innerHTML = syntaxHighlight(
        JSON.stringify({ ...body, raw_response: undefined }, null, 2)
    );

    // Navamsha raw tab
    document.getElementById('navamshaOut').innerHTML = body.raw_response
        ? syntaxHighlight(JSON.stringify(body.raw_response, null, 2))
        : '<span style="color:#8a9ab8">Not available (served from cache or error)</span>';

    area.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function buildFormatted(body) {
    const d = body.data ?? {};
    let html = `<div class="kv-grid">`;

    html += kv('Date', body.date);
    html += kv('Location', body.location?.name ?? '—');
    html += kv('Coordinates', `${body.location?.latitude}, ${body.location?.longitude}`);
    html += kv('Timezone', `UTC ${body.location?.timezone >= 0 ? '+' : ''}${body.location?.timezone}`);

    html += `</div><hr class="section-divider">`;

    if (Object.keys(d).length === 0) {
        html += `<p style="color:#8a9ab8;font-size:13px">No Panchang data returned.</p>`;
    } else {
        html += `<div class="kv-grid">` + flatKv(d) + `</div>`;
    }

    return html;
}

function flatKv(obj, prefix = '') {
    let out = '';
    for (const [k, v] of Object.entries(obj)) {
        const label = prefix ? `${prefix} › ${k}` : k;
        if (v !== null && typeof v === 'object' && !Array.isArray(v)) {
            out += flatKv(v, label);
        } else if (Array.isArray(v)) {
            out += kv(label, JSON.stringify(v));
        } else {
            out += kv(label, v ?? '—');
        }
    }
    return out;
}

function kv(label, val) {
    return `<div class="kv-label">${label}</div><div class="kv-val">${val}</div>`;
}

function switchTab(name) {
    document.querySelectorAll('.tab').forEach((t, i) => {
        const names = ['formatted','raw','navamsha'];
        t.classList.toggle('active', names[i] === name);
    });
    document.querySelectorAll('.tab-panel').forEach(p => {
        p.classList.toggle('active', p.id === `tab-${name}`);
    });
}

function syntaxHighlight(json) {
    return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+\.?\d*([eE][+-]?\d+)?)/g, m => {
        if (/^"/.test(m)) return /:$/.test(m)
            ? `<span class="json-key">${m}</span>`
            : `<span class="json-str">${m}</span>`;
        if (/true|false/.test(m)) return `<span class="json-bool">${m}</span>`;
        if (/null/.test(m))       return `<span class="json-null">${m}</span>`;
        return `<span class="json-num">${m}</span>`;
    });
}

document.addEventListener('keydown', e => {
    if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') runTest();
});
</script>
@endsection
