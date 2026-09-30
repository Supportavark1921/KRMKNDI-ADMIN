<?php
/**
 * deploy.php — one-click server deploy helper
 *
 * Access: /deploy.php?token=YOUR_SECRET_TOKEN
 * Change DEPLOY_TOKEN below before uploading to the server.
 * DELETE this file once the deploy is stable.
 */

define('DEPLOY_TOKEN', 'krmkndi-deploy-2026');   // ← change this
define('BASE_DIR', dirname(__DIR__));              // project root (one level above public/)
define('PHP_BIN', PHP_BINARY);                    // current PHP executable path

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

$artisan = escapeshellarg(BASE_DIR . '/artisan');
$php     = escapeshellarg(PHP_BIN);
$base    = escapeshellarg(BASE_DIR);

// ── Command definitions ──────────────────────────────────────────────────────
// Each entry: label, cmd, note (optional advisory shown before run)
$commands = [
    'safe_dir' => [
        'label' => '① Fix git safe.directory',
        'cmd'   => 'git config --global --add safe.directory ' . BASE_DIR . ' 2>&1',
        'note'  => 'Fixes "dubious ownership" error on shared hosting. Run this first.',
    ],
    'git_pull' => [
        'label' => '② git pull',
        'cmd'   => 'git -C ' . $base . ' pull origin main 2>&1',
    ],
    'composer' => [
        'label' => '③ composer install',
        'cmd'   => 'composer install --optimize-autoloader --working-dir=' . $base . ' 2>&1',
        'note'  => '--no-dev omitted: shared hosting blocks deletion of existing vendor files.',
    ],
    'migrate' => [
        'label' => '④ migrate (update — skips existing)',
        'cmd'   => $php . ' ' . $artisan . ' migrate --force 2>&1',
        'note'  => 'Runs only new migrations. Safe for existing databases.',
    ],
    'migrate_fresh' => [
        'label' => '④-FRESH migrate:fresh + seed',
        'cmd'   => $php . ' ' . $artisan . ' migrate:fresh --seed --force 2>&1',
        'note'  => '⚠ DROPS ALL TABLES then rebuilds. Use only on a clean/empty database.',
        'danger'=> true,
    ],
    'seed' => [
        'label' => '⑤ db:seed',
        'cmd'   => $php . ' ' . $artisan . ' db:seed --force 2>&1',
        'note'  => 'Uses firstOrCreate — safe to re-run; will not duplicate admin/test users.',
    ],
    'cache' => [
        'label' => '⑥ config + route + view cache',
        'cmd'   => $php . ' ' . $artisan . ' config:cache 2>&1 && '
                 . $php . ' ' . $artisan . ' route:cache  2>&1 && '
                 . $php . ' ' . $artisan . ' view:cache   2>&1',
    ],
    'storage' => [
        'label' => '⑦ storage:link',
        'cmd'   => $php . ' ' . $artisan . ' storage:link 2>&1',
        'note'  => 'May fail with "Permission denied" on shared hosting — ask your host to run: <code>php artisan storage:link</code> as the system user, or create the symlink manually.',
    ],
];

// "Run All (update)" — skips migrate:fresh
$update_steps = ['safe_dir','git_pull','composer','migrate','seed','cache','storage'];
// "First Deploy" — uses fresh
$fresh_steps  = ['safe_dir','git_pull','composer','migrate_fresh','cache','storage'];

if (isset($_POST['run_all_update'])) {
    $_POST['run'] = $update_steps;
}
if (isset($_POST['run_all_fresh'])) {
    if (($_POST['confirm_fresh'] ?? '') !== 'YES') {
        $error = 'Type YES in the confirmation box to run a fresh deploy.';
    } else {
        $_POST['run'] = $fresh_steps;
    }
}

$results = [];
if (!empty($_POST['run']) && empty($error)) {
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
            'note'    => $commands[$key]['note'] ?? null,
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
.wrap{max-width:940px;margin:0 auto}
h1{font-size:22px;font-weight:800;color:#fff;margin-bottom:4px}
.sub{font-size:12px;color:#555;margin-bottom:26px}
.sub code{background:#1a1a22;padding:2px 7px;border-radius:5px;color:#a78bfa}
.warn{padding:12px 16px;border-radius:10px;background:#2d1f00;border:1px solid #503800;color:#fbbf24;font-size:12px;margin-bottom:22px;line-height:1.7}
.warn strong{color:#fcd34d}
.err{padding:12px 16px;border-radius:10px;background:#2d0a0a;border:1px solid #6a1a1a;color:#f87171;font-size:13px;font-weight:600;margin-bottom:18px}

/* Sections */
.section{margin-bottom:28px}
.section-title{font-size:10px;font-weight:800;color:#555;text-transform:uppercase;letter-spacing:.1em;margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid #1a1a22}

/* Run-all cards */
.run-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:8px}
@media(max-width:600px){.run-grid{grid-template-columns:1fr}}
.run-card{padding:18px 20px;border:1px solid #1e1e28;border-radius:14px;background:#111116}
.run-card.danger-card{border-color:#3d1a1a;background:#130d0d}
.run-card h3{font-size:14px;font-weight:800;color:#e2e2f0;margin-bottom:6px}
.run-card.danger-card h3{color:#f87171}
.run-card p{font-size:11px;color:#666;line-height:1.6;margin-bottom:14px}
.run-card p strong{color:#999}
.btn-all{width:100%;padding:11px;border-radius:9px;border:0;color:#fff;font:700 13px ui-monospace,monospace;cursor:pointer;transition:.2s}
.btn-update{background:linear-gradient(110deg,#5b21b6,#7c3aed);box-shadow:0 6px 18px #7c3aed25}
.btn-update:hover{opacity:.9}
.btn-danger{background:linear-gradient(110deg,#7f1d1d,#dc2626);box-shadow:0 6px 18px #dc262625}
.btn-danger:hover{opacity:.9}
.confirm-row{display:flex;gap:8px;margin-bottom:10px}
.confirm-input{flex:1;padding:8px 10px;border-radius:7px;border:1px solid #3d1a1a;background:#1a0d0d;color:#f87171;font:inherit;font-size:13px;outline:none}
.confirm-input:focus{border-color:#dc2626}
.confirm-input::placeholder{color:#5a3030}

/* Individual commands */
.cmd-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:9px}
.cmd-card{padding:12px 15px;border:1px solid #1e1e28;border-radius:11px;background:#111116}
.cmd-card.danger-cmd{border-color:#3d1a1a}
.cmd-top{display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:0}
.cmd-label{font-size:12px;color:#c8c8d8;font-weight:600;flex:1}
.cmd-card.danger-cmd .cmd-label{color:#f87171}
.btn-single{padding:6px 12px;border-radius:7px;border:1px solid #2e2e40;background:#18181f;color:#a78bfa;font:600 11px ui-monospace,monospace;cursor:pointer;transition:.15s;white-space:nowrap}
.btn-single:hover{background:#1e1e2e;border-color:#a78bfa}
.danger-cmd .btn-single{border-color:#4a1a1a;color:#f87171}
.danger-cmd .btn-single:hover{border-color:#dc2626;background:#1f0d0d}
.cmd-note{font-size:10px;color:#555;margin-top:6px;line-height:1.5}
.cmd-note code{color:#888;background:#1a1a22;padding:1px 4px;border-radius:3px}

/* Results */
.results-section{margin-top:28px}
.result{margin-bottom:12px;border:1px solid #1e1e28;border-radius:13px;overflow:hidden}
.result-header{display:flex;align-items:center;justify-content:space-between;padding:11px 15px;background:#14141c}
.result-label{font-size:13px;font-weight:700;color:#e2e2f0}
.result-meta{display:flex;align-items:center;gap:8px}
.badge{padding:3px 9px;border-radius:20px;font-size:11px;font-weight:800}
.ok{background:#14321f;color:#4ade80}.fail{background:#2d1414;color:#f87171}
.elapsed{color:#444;font-size:11px}
pre{padding:14px 16px;font-size:11px;line-height:1.65;color:#9ca3af;background:#0d0d0f;white-space:pre-wrap;word-break:break-all;max-height:380px;overflow-y:auto;border-top:1px solid #1a1a24}
pre::-webkit-scrollbar{width:5px}pre::-webkit-scrollbar-track{background:#111}pre::-webkit-scrollbar-thumb{background:#2a2a35;border-radius:3px}
</style>
</head>
<body>
<div class="wrap">
    <h1>🚀 KRMKNDI Deploy</h1>
    <p class="sub">Server: <code><?= htmlspecialchars(gethostname()) ?></code> &nbsp;·&nbsp;
       Dir: <code><?= htmlspecialchars(BASE_DIR) ?></code> &nbsp;·&nbsp;
       PHP: <code><?= PHP_VERSION ?></code> (<?= htmlspecialchars(PHP_BIN) ?>)</p>

    <div class="warn">
        <strong>⚠ Security:</strong> This file gives shell access to anyone with the token.
        <strong>Delete <code>public/deploy.php</code></strong> from the server after use.
    </div>

    <?php if (!empty($error)): ?>
        <div class="err">⚠ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- ── Run All ─────────────────────────────────────────────────────── -->
    <div class="section">
        <div class="section-title">Quick Deploy</div>
        <div class="run-grid">
            <!-- Update existing -->
            <div class="run-card">
                <h3>▶ Update Existing Deploy</h3>
                <p>Runs: safe.directory → git pull → composer install →
                   <strong>migrate</strong> (skips existing tables) →
                   seed (idempotent) → cache → storage:link</p>
                <form method="POST" action="<?= htmlspecialchars($tokenQ) ?>">
                    <button class="btn-all btn-update" name="run_all_update" value="1" type="submit">
                        ▶ Run Update Steps
                    </button>
                </form>
            </div>

            <!-- Fresh install -->
            <div class="run-card danger-card">
                <h3>⚠ First / Clean Deploy</h3>
                <p>Runs: safe.directory → git pull → composer install →
                   <strong>migrate:fresh + seed</strong> (DROPS all tables) →
                   cache → storage:link.<br>
                   <strong>Only use on an empty database.</strong></p>
                <form method="POST" action="<?= htmlspecialchars($tokenQ) ?>">
                    <div class="confirm-row">
                        <input class="confirm-input" type="text" name="confirm_fresh"
                               placeholder='Type YES to confirm' autocomplete="off">
                    </div>
                    <button class="btn-all btn-danger" name="run_all_fresh" value="1" type="submit">
                        ⚠ Fresh Deploy (drops DB)
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- ── Individual commands ──────────────────────────────────────────── -->
    <div class="section">
        <div class="section-title">Individual Steps</div>
        <div class="cmd-grid">
            <?php foreach ($commands as $key => $cmd):
                $isDanger = !empty($cmd['danger']); ?>
            <div class="cmd-card <?= $isDanger ? 'danger-cmd' : '' ?>">
                <div class="cmd-top">
                    <span class="cmd-label"><?= htmlspecialchars($cmd['label']) ?></span>
                    <form method="POST" action="<?= htmlspecialchars($tokenQ) ?>">
                        <input type="hidden" name="run[]" value="<?= htmlspecialchars($key) ?>">
                        <?php if ($isDanger): ?>
                            <button class="btn-single" type="submit"
                                onclick="return confirm('This DROPS ALL TABLES. Continue?')">Run</button>
                        <?php else: ?>
                            <button class="btn-single" type="submit">Run</button>
                        <?php endif; ?>
                    </form>
                </div>
                <?php if (!empty($cmd['note'])): ?>
                    <div class="cmd-note"><?= $cmd['note'] ?></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ── Results ──────────────────────────────────────────────────────── -->
    <?php if (!empty($results)): ?>
    <div class="results-section">
        <div class="section-title">Output</div>
        <?php foreach ($results as $r): ?>
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
    </div>
    <?php endif; ?>
</div>
</body>
</html>
