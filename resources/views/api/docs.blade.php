@extends('layouts.app')
@section('content')
<style>
.docs-page{background:#0f1420!important;min-height:100vh}
.docs-topbar{display:flex;justify-content:space-between;align-items:center;padding:18px 28px;border-bottom:1px solid #1e2a45}
.docs-topbar .brand{color:#e0e6f8;font-size:16px;font-weight:700;display:flex;align-items:center;gap:8px}
.docs-topbar .brand span{color:#f3b44c}
.swagger-outer{padding:0 0 60px}

/* Custom Swagger UI overrides */
#swagger-ui .swagger-ui{font-family:Inter,ui-sans-serif,system-ui,sans-serif}
#swagger-ui .swagger-ui .info{margin:30px 0 20px}
#swagger-ui .swagger-ui .info .title{color:#e0e6f8!important;font-size:28px!important}
#swagger-ui .swagger-ui .info p,#swagger-ui .swagger-ui .info li{color:#8a99bb!important}
#swagger-ui .swagger-ui .info .base-url{color:#6246ea!important}
#swagger-ui .topbar{display:none!important}
#swagger-ui .swagger-ui .scheme-container{background:#131b2e!important;padding:14px 20px!important;border:1px solid #1e2a45!important;border-radius:12px!important}
#swagger-ui .swagger-ui select{background:#1a2540!important;color:#c8d4f0!important;border-color:#2a3a5c!important;border-radius:8px!important}
#swagger-ui .swagger-ui .opblock-tag{color:#c8d4f0!important;border-bottom:1px solid #1e2a45!important;padding:14px 0!important;font-size:18px!important}
#swagger-ui .swagger-ui .opblock-tag:hover{background:#131b2e!important}
#swagger-ui .swagger-ui .opblock{border-radius:12px!important;border:1px solid #1e2a45!important;margin-bottom:10px!important;overflow:hidden!important}
#swagger-ui .swagger-ui .opblock.opblock-get .opblock-summary{background:#0d1f3c!important;border-color:#1e4080!important}
#swagger-ui .swagger-ui .opblock.opblock-get .opblock-summary-method{background:#1a4fcf!important;border-radius:8px!important;font-size:12px!important;min-width:70px!important}
#swagger-ui .swagger-ui .opblock.opblock-get{background:#0a1628!important;border-color:#1e3560!important}
#swagger-ui .swagger-ui .opblock-summary-path{color:#7ec8f8!important;font-weight:600!important;font-size:15px!important}
#swagger-ui .swagger-ui .opblock-summary-description{color:#6b7fa6!important}
#swagger-ui .swagger-ui .opblock-body{background:#0d1627!important}
#swagger-ui .swagger-ui .opblock-section-header{background:#111e35!important;border-color:#1e2a45!important}
#swagger-ui .swagger-ui .opblock-section-header label{color:#8a99bb!important}
#swagger-ui .swagger-ui table thead tr td,#swagger-ui .swagger-ui table thead tr th{color:#7882a0!important;border-color:#1e2a45!important;font-size:12px!important}
#swagger-ui .swagger-ui .parameter__name{color:#c8d4f0!important}
#swagger-ui .swagger-ui .parameter__type{color:#6246ea!important}
#swagger-ui .swagger-ui .parameter__in{color:#f6c453!important;background:#2a2010!important;border-radius:4px!important;padding:2px 6px!important;font-size:10px!important}
#swagger-ui .swagger-ui .parameter__deprecated{color:#e04a4a!important}
#swagger-ui .swagger-ui table tbody tr td{border-color:#1e2a45!important;color:#8a99bb!important}
#swagger-ui .swagger-ui input[type=text],#swagger-ui .swagger-ui input[type=number]{background:#1a2540!important;border:1px solid #2a3a5c!important;border-radius:8px!important;color:#c8d4f0!important;padding:8px 12px!important}
#swagger-ui .swagger-ui input[type=text]:focus,#swagger-ui .swagger-ui input[type=number]:focus{border-color:#6246ea!important;box-shadow:0 0 0 3px #6246ea22!important}
#swagger-ui .swagger-ui textarea{background:#1a2540!important;border:1px solid #2a3a5c!important;border-radius:8px!important;color:#c8d4f0!important}
#swagger-ui .swagger-ui .btn{border-radius:8px!important;font-weight:700!important;font-size:13px!important;padding:8px 16px!important}
#swagger-ui .swagger-ui .btn.execute{background:#6246ea!important;border-color:#6246ea!important;color:#fff!important;box-shadow:0 6px 14px #6246ea33!important}
#swagger-ui .swagger-ui .btn.execute:hover{background:#4934c4!important}
#swagger-ui .swagger-ui .btn.cancel{background:#1e2a45!important;border-color:#2a3a5c!important;color:#8a99bb!important}
#swagger-ui .swagger-ui .btn.authorize{background:#1a4fcf!important;border-color:#1a4fcf!important;color:#fff!important}
#swagger-ui .swagger-ui .responses-inner{background:#0a1220!important}
#swagger-ui .swagger-ui .response-col_status{color:#7ec8f8!important;font-weight:700!important}
#swagger-ui .swagger-ui .response-col_description{color:#8a99bb!important}
#swagger-ui .swagger-ui .microlight,#swagger-ui .swagger-ui .highlight-code{background:#060d1a!important;border-radius:8px!important;border:1px solid #1e2a45!important}
#swagger-ui .swagger-ui .microlight *{color:#c8d4f0!important}
#swagger-ui .swagger-ui .response-control-media-type__accept-message{color:#8a99bb!important}
#swagger-ui .swagger-ui .markdown p,#swagger-ui .swagger-ui .markdown li{color:#8a99bb!important}
#swagger-ui .swagger-ui .model-box{background:#0d1627!important;border:1px solid #1e2a45!important;border-radius:8px!important}
#swagger-ui .swagger-ui .model .property{color:#c8d4f0!important}
#swagger-ui .swagger-ui .model-title{color:#c8d4f0!important}
#swagger-ui .swagger-ui section.models{border:1px solid #1e2a45!important;border-radius:12px!important;background:#0d1627!important}
#swagger-ui .swagger-ui section.models h4{color:#8a99bb!important}
#swagger-ui .swagger-ui .prop-type{color:#6246ea!important}
#swagger-ui .swagger-ui .prop-format{color:#f6c453!important}
#swagger-ui .swagger-ui span.prop-type{color:#6246ea!important}
#swagger-ui .swagger-ui .renderedMarkdown p{color:#8a99bb!important}
#swagger-ui .swagger-ui .servers>label select{background:#1a2540!important}
#swagger-ui .swagger-ui .copy-to-clipboard{background:#1a2540!important}
#swagger-ui .swagger-ui .copy-to-clipboard button{background:transparent!important;color:#8a99bb!important}
#swagger-ui .swagger-ui .curl{color:#c8d4f0!important;background:#060d1a!important}
#swagger-ui .swagger-ui .request-url{background:#060d1a!important;border:1px solid #1e2a45!important;border-radius:8px!important;color:#7ec8f8!important;padding:10px 14px!important}
#swagger-ui .swagger-ui .wrapper{padding:0 28px!important;max-width:100%!important}
#swagger-ui .swagger-ui .response-body pre{background:#060d1a!important}
#swagger-ui .swagger-ui table.model td{color:#8a99bb!important;border-color:#1e2a45!important}
#swagger-ui .swagger-ui .tab li{color:#8a99bb!important}
#swagger-ui .swagger-ui .tab li.active{color:#e0e6f8!important}
#swagger-ui .swagger-ui .tab li:after{background:#6246ea!important}
#swagger-ui .swagger-ui .no-margin{color:#e0e6f8!important}
#swagger-ui .swagger-ui .info a{color:#7ec8f8!important}

/* Hero bar */
.api-hero{display:flex;align-items:center;justify-content:space-between;gap:24px;padding:32px 28px 24px;background:linear-gradient(118deg,#0f1420,#1a1060 60%,#0f1420)}
.api-hero-left h1{margin:8px 0;font-size:28px;color:#e0e6f8}
.api-hero-left p{margin:0;color:#6b7fa6;font-size:14px;line-height:1.6}
.api-overline{color:#f3b44c;font-size:11px;font-weight:800;letter-spacing:.13em;text-transform:uppercase}
.api-badges{display:flex;gap:8px;flex-wrap:wrap;margin-top:14px}
.api-badge{display:inline-flex;align-items:center;gap:5px;padding:5px 10px;border-radius:8px;font-size:11px;font-weight:700}
.badge-version{background:#6246ea22;color:#9d80ef;border:1px solid #6246ea33}
.badge-base{background:#1a4fcf22;color:#7ec8f8;border:1px solid #1a4fcf33}
.badge-format{background:#1a3320;color:#4caf7a;border:1px solid #2a5a3a}
.api-links{display:flex;flex-direction:column;gap:8px;align-items:flex-end}
.api-link-btn{display:inline-flex;align-items:center;gap:6px;padding:9px 14px;border-radius:9px;border:1px solid #2a3a5c;color:#8a99bb;font-size:12px;font-weight:700;text-decoration:none;background:#131b2e;transition:.15s;cursor:pointer}
.api-link-btn:hover{border-color:#6246ea;color:#9d80ef;background:#1a1545}
.api-link-btn.primary{background:#6246ea;border-color:#6246ea;color:#fff}
.api-link-btn.primary:hover{background:#4934c4}

/* Quick test panel */
.quick-panel{margin:0 28px 24px;padding:20px 24px;border:1px solid #1e2a45;border-radius:14px;background:#0d1627}
.quick-panel-title{color:#8a99bb;font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;margin:0 0 14px}
.quick-links{display:flex;flex-wrap:wrap;gap:8px}
.quick-link{display:inline-flex;align-items:center;gap:7px;padding:8px 12px;border-radius:9px;background:#0a1628;border:1px solid #1e3560;color:#7ec8f8;font-size:12px;font-weight:600;text-decoration:none;transition:.15s;cursor:pointer}
.quick-link:hover{border-color:#6246ea;color:#9d80ef;background:#12103a}
.quick-link .method{display:inline-block;padding:2px 6px;border-radius:4px;background:#1a4fcf;color:#fff;font-size:9px;font-weight:800;letter-spacing:.04em}
.quick-link .path{font-family:ui-monospace,monospace;font-size:11px}
</style>

<div class="dashboard docs-page">
    {{-- Topbar --}}
    <div class="docs-topbar">
        <div class="brand"><span>✦</span> ARK Jyotish — API Docs</div>
        <div style="display:flex;gap:10px;align-items:center">
            <a href="{{ route('services.index') }}" class="api-link-btn">← Admin Panel</a>
            <form method="POST" action="{{ route('logout') }}">@csrf
                <button type="submit" class="api-link-btn">Sign out</button>
            </form>
        </div>
    </div>

    {{-- Hero --}}
    <div class="api-hero">
        <div class="api-hero-left">
            <span class="api-overline">REST API · OpenAPI 3.0</span>
            <h1>ARK Jyotish API</h1>
            <p>Explore, test, and inspect all API endpoints. Used by the mobile APK for fetching multilingual service data.</p>
            <div class="api-badges">
                <span class="api-badge badge-version">v1.0.0</span>
                <span class="api-badge badge-base">{{ config('app.url') }}/api</span>
                <span class="api-badge badge-format">JSON</span>
            </div>
        </div>
        <div class="api-links">
            <a href="/api/openapi.json" target="_blank" class="api-link-btn">⬇ openapi.json</a>
            <a href="/api/services" target="_blank" class="api-link-btn primary">▶ Try Live</a>
        </div>
    </div>

    {{-- Quick test links --}}
    <div class="quick-panel">
        <p class="quick-panel-title">⚡ Quick Test Endpoints</p>
        <div class="quick-links">
            <a href="/api/services" target="_blank" class="quick-link">
                <span class="method">GET</span><span class="path">/api/services</span>
            </a>
            <a href="/api/services?language=hi" target="_blank" class="quick-link">
                <span class="method">GET</span><span class="path">/api/services?language=hi</span>
            </a>
            <a href="/api/services?status=all" target="_blank" class="quick-link">
                <span class="method">GET</span><span class="path">/api/services?status=all</span>
            </a>
            <a href="/api/services?language=en&status=active" target="_blank" class="quick-link">
                <span class="method">GET</span><span class="path">/api/services?language=en&status=active</span>
            </a>
            <a href="/api/services/1" target="_blank" class="quick-link">
                <span class="method">GET</span><span class="path">/api/services/1</span>
            </a>
            <a href="/api/services/1?language=hi" target="_blank" class="quick-link">
                <span class="method">GET</span><span class="path">/api/services/1?language=hi</span>
            </a>
            <a href="/api/openapi.json" target="_blank" class="quick-link">
                <span class="method" style="background:#2a7a2a">GET</span><span class="path">/api/openapi.json</span>
            </a>
        </div>
    </div>

    {{-- Swagger UI --}}
    <div class="swagger-outer">
        <div id="swagger-ui"></div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5.17.14/swagger-ui.css">
<script src="https://unpkg.com/swagger-ui-dist@5.17.14/swagger-ui-bundle.js"></script>
<script>
window.addEventListener('load', () => {
    SwaggerUIBundle({
        url: '/api/openapi.json',
        dom_id: '#swagger-ui',
        deepLinking: true,
        presets: [
            SwaggerUIBundle.presets.apis,
            SwaggerUIBundle.SwaggerUIStandalonePreset,
        ],
        layout: 'BaseLayout',
        supportedSubmitMethods: ['get', 'post', 'put', 'patch', 'delete'],
        defaultModelsExpandDepth: 1,
        defaultModelExpandDepth: 2,
        docExpansion: 'list',
        filter: true,
        tryItOutEnabled: true,
        requestInterceptor(req) {
            // Attach CSRF token for any future POST/PUT/DELETE endpoints
            req.headers['X-Requested-With'] = 'XMLHttpRequest';
            return req;
        },
        responseInterceptor(res) {
            return res;
        },
        onComplete() {
            // Auto-expand all operations
            document.querySelectorAll('.opblock-summary').forEach(el => {
                if (!el.closest('.opblock').classList.contains('is-open')) {
                    el.click();
                }
            });
        },
    });
});
</script>
@endsection
