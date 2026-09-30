<?php
/**
 * deploy.php — one-click server deploy helper
 *
 * Access: /deploy.php?token=YOUR_SECRET_TOKEN
 * Change DEPLOY_TOKEN below before uploading to the server.
 * DELETE this file once the deploy is stable.
 */

define('DEPLOY_TOKEN', 'krmkndi-deploy-2026');   // ← change this
define('BASE_DIR', dirname(__DIR__));              // project root (one level up from public/)

// ── Auth ────────────────────────────────────────────────────────────────────
$token = $_GET['token'] ?? '';
if ($token !== DEPLOY_TOKEN) {
    http_response_code(403);
    echo '<!doctype html><html><body style="font-family:monospace;padding:40px;background:#0d0d0f;color:#ff4a4a">
        <h2>403 — Forbidden</h2>
        <p>Append <code>?token=YOUR_TOKEN</code> to the URL.</p>
    </body></html>';
    exit;
}

// ── Commands ────────────────────────────────────────────────────────────────
$commands = [
    'git_pull'  => ['label' => '① git pull',              'cmd' => 'git -C ' . escapeshellarg(BASE_DIR) . ' pull origin main 2>&1'],
    'composer'  => ['label' => '② composer install',      'cmd' => 'composer install --no-dev --optimize-autoloader --working-dir=' . escapeshellarg(BASE_DIR) . ' 2>&1'],
    'migrate'   => ['label' => '③ php artisan migrate',   'cmd' => 'php ' . escapeshellarg(BASE_DIR . '/artisan') . ' migrate --force 2>&1'],
    'seed'      => ['label' => '④ php artisan db:seed',   'cmd' => 'php ' . escapeshellarg(BASE_DIR . '/artisan') . ' db:seed --force 2>&1'],
    'cache'     => ['label' => '⑤ optimize & cache',      'cmd' => 'php ' . escapeshellarg(BASE_DIR . '/artisan') . ' config:cache 2>&1 && php ' . escapeshellarg(BASE_DIR . '/artisan') . ' route:cache 2>&1 && php ' . escapeshellarg(BASE_DIR . '/artisan') . ' view:cache 2>&1'],
    'storage'   => ['label' => '⑥ storage:link',          'cmd' => 'php ' . escapeshellarg(BASE_DIR . '/artisan') . ' storage:link 2>&1'],
];

// "Run All" executes every command in order
if (isset($_POST['run_all'])) {
    $_POST['run'] = array_keys($commands);
}

$results = [];
if (!empty($_POST['run'])) {
    foreach ((array) $_POST['run'] as $key) {
        if (!isset($commands[$key])) continue;
        $start  = microtime(true);
        $output = [];
        exec($commands[$key]['cmd'], $output, $code);
        $results[$key] = [
            'label'   => $commands[$key]['label'],
            'output'  => implode("\n", $output),
            'code'    => $code,
            'elapsed' => round(microtime(true) - $start, 2),
        ];
    }
}

$tokenQ = '?token=' . urlencode(DEPLOY_TOKEN);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Deploy — KRMKNDI</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:ui-monospace,'Cascadia Code','Fira Code',monospace;background:#0d0d0f;color:#e2e2e6;min-height:100vh;padding:32px 20px}
.wrap{max-width:900px;margin:0 auto}
h1{font-size:22px;font-weight:800;color:#fff;margin-bottom:4px}
.sub{font-size:13px;color:#666;margin-bottom:28px}
.sub code{background:#1a1a22;padding:2px 7px;border-radius:5px;color:#a78bfa}

/* Command cards */
.cmd-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:10px;margin-bottom:22px}
.cmd-card{padding:14px 16px;border:1px solid #1e1e28;border-radius:12px;background:#111116;display:flex;align-items:center;justify-content:space-between;gap:10px}
.cmd-label{font-size:13px;color:#c8c8d8;font-weight:600}
.btn-single{padding:7px 14px;border-radius:8px;border:1px solid #2e2e40;background:#18181f;color:#a78bfa;font:600 12px ui-monospace,monospace;cursor:pointer;transition:.15s;white-space:nowrap}
.btn-single:hover{background:#1e1e2e;border-color:#a78bfa;color:#c4b5fd}

/* Run All button */
.run-all-wrap{margin-bottom:28px}
.btn-all{display:inline-flex;align-items:center;gap:8px;padding:13px 28px;border-radius:12px;border:0;background:linear-gradient(110deg,#5b21b6,#7c3aed);color:#fff;font:800 15px ui-monospace,monospace;cursor:pointer;box-shadow:0 8px 24px #7c3aed30;transition:.2s;letter-spacing:.02em}
.btn-all:hover{transform:translateY(-2px);box-shadow:0 12px 30px #7c3aed40}

/* Results */
.result{margin-bottom:16px;border:1px solid #1e1e28;border-radius:14px;overflow:hidden}
.result-header{display:flex;align-items:center;justify-content:space-between;padding:12px 16px;background:#14141c}
.result-label{font-size:13px;font-weight:700;color:#e2e2f0}
.result-meta{display:flex;align-items:center;gap:10px}
.badge{padding:3px 9px;border-radius:20px;font-size:11px;font-weight:800}
.ok{background:#14321f;color:#4ade80}
.fail{background:#2d1414;color:#f87171}
.elapsed{color:#555;font-size:11px}
pre{padding:16px;font-size:12px;line-height:1.65;color:#9ca3af;background:#0d0d0f;white-space:pre-wrap;word-break:break-all;max-height:360px;overflow-y:auto;border-top:1px solid #1a1a24}
pre::-webkit-scrollbar{width:6px}
pre::-webkit-scrollbar-track{background:#111}
pre::-webkit-scrollbar-thumb{background:#333;border-radius:3px}

.warn{padding:12px 16px;border-radius:10px;background:#2d1f00;border:1px solid #503800;color:#fbbf24;font-size:12px;margin-bottom:20px;line-height:1.6}
.warn strong{color:#fcd34d}
</style>
</head>
<body>
<div class="wrap">
    <h1>🚀 KRMKNDI Deploy</h1>
    <p class="sub">Server: <code><?= htmlspecialchars(gethostname()) ?></code> &nbsp;·&nbsp; Base: <code><?= htmlspecialchars(BASE_DIR) ?></code> &nbsp;·&nbsp; PHP <?= PHP_VERSION ?></p>

    <div class="warn">
        <strong>⚠ Security reminder:</strong> This file grants shell access to anyone with the token.
        <strong>Delete <code>public/deploy.php</code></strong> from the server once the deploy is complete.
    </div>

    <!-- Run All -->
    <div class="run-all-wrap">
        <form method="POST" action="<?= htmlspecialchars($tokenQ) ?>">
            <button class="btn-all" type="submit" name="run_all" value="1"
                onclick="return confirm('Run all 6 steps in order?')">
                ▶ Run All Steps
            </button>
        </form>
    </div>

    <!-- Individual commands -->
    <div class="cmd-grid">
        <?php foreach ($commands as $key => $cmd): ?>
        <div class="cmd-card">
            <span class="cmd-label"><?= htmlspecialchars($cmd['label']) ?></span>
            <form method="POST" action="<?= htmlspecialchars($tokenQ) ?>">
                <input type="hidden" name="run[]" value="<?= htmlspecialchars($key) ?>">
                <button class="btn-single" type="submit">Run</button>
            </form>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Results -->
    <?php if (!empty($results)): ?>
        <h2 style="font-size:15px;font-weight:800;color:#6d6d8a;margin-bottom:14px;text-transform:uppercase;letter-spacing:.08em">Output</h2>
        <?php foreach ($results as $key => $r): ?>
        <div class="result">
            <div class="result-header">
                <span class="result-label"><?= htmlspecialchars($r['label']) ?></span>
                <span class="result-meta">
                    <span class="elapsed"><?= $r['elapsed'] ?>s</span>
                    <span class="badge <?= $r['code'] === 0 ? 'ok' : 'fail' ?>">
                        <?= $r['code'] === 0 ? '✓ OK' : '✗ exit ' . $r['code'] ?>
                    </span>
                </span>
            </div>
            <pre><?= htmlspecialchars($r['output'] ?: '(no output)') ?></pre>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>
