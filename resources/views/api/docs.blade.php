@extends('layouts.app', ['title' => 'API Docs — ARK Jyotish'])
@section('content')
<style>
/* Override sidebar shell so Swagger UI gets the full viewport */
.main-content { padding: 0 !important; }

/* Swagger UI theme tweaks */
.swagger-ui .topbar { display: none }
.swagger-ui .info { margin: 20px 0 10px }
.swagger-ui .info .title { font-size: 22px }
.swagger-ui .scheme-container { padding: 12px 20px }
.swagger-ui .opblock-tag { font-size: 15px }
.swagger-ui .opblock .opblock-summary { padding: 8px 16px }
.swagger-ui select, .swagger-ui input[type=text], .swagger-ui textarea { font-size: 13px }
.swagger-ui .btn.execute { background: #6246ea; border-color: #6246ea }
.swagger-ui .btn.execute:hover { background: #4934c4 }
.swagger-ui .response-col_status { font-size: 13px }
.swagger-ui .model-box { background: #f7f8fc }

#swagger-wrapper { padding: 0 20px 60px; }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/5.17.14/swagger-ui.min.css">

<div id="swagger-wrapper">
    <div style="display:flex;align-items:center;justify-content:space-between;padding:18px 0 4px">
        <div>
            <h1 style="margin:0;font-size:22px;font-weight:800;color:#15233d">ARK Jyotish API</h1>
            <p style="margin:4px 0 0;color:#69758b;font-size:13px">Interactive docs — fill params and click <strong>Execute</strong> to test live</p>
        </div>
        <a href="/api/openapi.json" target="_blank"
           style="padding:7px 14px;border:1px solid #dde1ef;border-radius:8px;font-size:12px;font-weight:700;color:#536078;text-decoration:none">
            ⬇ OpenAPI JSON
        </a>
    </div>
    <div id="swagger-ui"></div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/5.17.14/swagger-ui-bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/5.17.14/swagger-ui-standalone-preset.min.js"></script>
<script>
window.onload = function () {
    SwaggerUIBundle({
        url: "{{ url('/api/openapi.json') }}",
        dom_id: '#swagger-ui',
        presets: [SwaggerUIBundle.presets.apis, SwaggerUIStandalonePreset],
        layout: 'StandaloneLayout',
        deepLinking: true,
        displayRequestDuration: true,
        defaultModelsExpandDepth: 1,
        defaultModelExpandDepth: 1,
        tryItOutEnabled: true,
        requestInterceptor: function (req) {
            // Strip the X-Requested-With header that Axios adds — it causes CORS preflight
            delete req.headers['X-Requested-With'];
            return req;
        },
    });
};
</script>
@endsection
