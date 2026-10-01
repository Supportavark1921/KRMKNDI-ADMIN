<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>API Docs — ARK Jyotish</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/5.17.14/swagger-ui.min.css">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: Inter, ui-sans-serif, system-ui, sans-serif; background: #f7f8fc; }

.docs-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 28px;
    background: #fff;
    border-bottom: 1px solid #e6e8f0;
    position: sticky;
    top: 0;
    z-index: 100;
    gap: 16px;
}
.docs-header-left { display: flex; align-items: center; gap: 12px; }
.docs-header-logo {
    width: 32px; height: 32px; border-radius: 8px;
    background: linear-gradient(135deg,#6246ea,#3b82f6);
    display: grid; place-items: center; color: #fff; font-size: 16px; flex-shrink: 0;
}
.docs-header h1 { font-size: 16px; font-weight: 800; color: #15233d; }
.docs-header p  { font-size: 12px; color: #8a9ab8; margin-top: 1px; }
.docs-header-right { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
.btn-back {
    padding: 7px 13px; border: 1px solid #e6e8f0; border-radius: 8px;
    font-size: 12px; font-weight: 700; color: #536078; text-decoration: none;
    background: #fff; transition: .15s;
}
.btn-back:hover { border-color: #6246ea; color: #6246ea; }
.btn-json {
    padding: 7px 13px; border: 1px solid #e6e8f0; border-radius: 8px;
    font-size: 12px; font-weight: 700; color: #536078; text-decoration: none;
    background: #fff; transition: .15s;
}
.btn-json:hover { border-color: #536078; color: #15233d; }

#swagger-ui-wrapper { max-width: 1200px; margin: 0 auto; padding: 24px 20px 80px; }

/* ── Swagger UI overrides ── */
.swagger-ui .topbar                       { display: none !important; }
.swagger-ui .info                         { margin: 0 0 20px; padding: 20px 24px; background: #fff; border: 1px solid #e6e8f0; border-radius: 12px; }
.swagger-ui .info .title                  { font-size: 20px; font-weight: 800; color: #15233d; }
.swagger-ui .info .description p          { color: #536078; font-size: 13px; line-height: 1.6; }
.swagger-ui .scheme-container            { background: #fff; border: 1px solid #e6e8f0; border-radius: 12px; padding: 14px 20px; margin-bottom: 20px; box-shadow: none; }
.swagger-ui .servers label               { font-size: 12px; font-weight: 700; color: #8a9ab8; }
.swagger-ui .servers select              { border: 1px solid #dde1ef; border-radius: 6px; font-size: 13px; padding: 4px 8px; }

/* Tags / groups */
.swagger-ui .opblock-tag                 { font-size: 14px; font-weight: 800; color: #15233d; border-bottom: 1px solid #e6e8f0; padding: 12px 4px; }
.swagger-ui .opblock-tag:hover           { background: #f7f8fc; }
.swagger-ui .opblock-tag-section h3 span { font-weight: 800; }

/* Operation blocks */
.swagger-ui .opblock                     { border-radius: 10px; border: 1px solid #e6e8f0 !important; box-shadow: none !important; margin-bottom: 8px; }
.swagger-ui .opblock.opblock-get         { background: #f0f6ff; border-color: #bdd5f8 !important; }
.swagger-ui .opblock.opblock-post        { background: #f0fff6; border-color: #a8e6c3 !important; }
.swagger-ui .opblock .opblock-summary    { padding: 10px 16px; }
.swagger-ui .opblock .opblock-summary-method { font-size: 11px; font-weight: 800; border-radius: 5px; min-width: 60px; padding: 5px 8px; }
.swagger-ui .opblock-summary-path        { font-size: 13px; font-weight: 600; }
.swagger-ui .opblock-summary-description { font-size: 12px; color: #69758b; }

/* Execute / try it out buttons */
.swagger-ui .btn.try-out__btn            { font-size: 12px; font-weight: 700; border-radius: 6px; padding: 5px 12px; }
.swagger-ui .btn.execute                 { background: #6246ea; border-color: #6246ea; border-radius: 6px; font-size: 12px; font-weight: 700; }
.swagger-ui .btn.execute:hover           { background: #4934c4; }
.swagger-ui .btn.cancel                  { border-color: #e6e8f0; color: #536078; border-radius: 6px; font-size: 12px; }

/* Params table */
.swagger-ui table thead tr th            { font-size: 11px; font-weight: 700; color: #8a9ab8; text-transform: uppercase; letter-spacing: .05em; }
.swagger-ui .parameter__name            { font-size: 13px; font-weight: 600; }
.swagger-ui .parameter__type            { font-size: 11px; color: #6246ea; }
.swagger-ui input[type=text],
.swagger-ui textarea,
.swagger-ui select                       { font-size: 13px; border: 1px solid #dde1ef; border-radius: 6px; padding: 5px 8px; }
.swagger-ui input[type=text]:focus,
.swagger-ui textarea:focus               { border-color: #6246ea; outline: none; box-shadow: 0 0 0 3px #6246ea18; }

/* Responses */
.swagger-ui .responses-wrapper          { border-top: 1px solid #e6e8f0; padding-top: 12px; }
.swagger-ui .response-col_status        { font-size: 13px; font-weight: 700; }
.swagger-ui .microlight                 { font-size: 12px; line-height: 1.5; }
.swagger-ui .highlight-code > pre       { background: #f7f8fc; border: 1px solid #e6e8f0; border-radius: 8px; padding: 12px; }

/* Models */
.swagger-ui section.models              { border: 1px solid #e6e8f0; border-radius: 12px; overflow: hidden; }
.swagger-ui section.models h4           { font-size: 14px; font-weight: 800; color: #15233d; padding: 12px 20px; background: #f7f8fc; }
</style>
</head>
<body>

<div class="docs-header">
    <div class="docs-header-left">
        <div class="docs-header-logo">🕉</div>
        <div>
            <h1>ARK Jyotish API</h1>
            <p>Interactive docs — fill params and click Execute to test live</p>
        </div>
    </div>
    <div class="docs-header-right">
        <a href="{{ route('dashboard') }}" class="btn-back">← Dashboard</a>
        <a href="/api/openapi.json" target="_blank" class="btn-json">⬇ OpenAPI JSON</a>
    </div>
</div>

<div id="swagger-ui-wrapper">
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
        defaultModelsExpandDepth: -1,
        tryItOutEnabled: true,
        filter: true,
    });
};
</script>
</body>
</html>
