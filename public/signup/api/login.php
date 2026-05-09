<?php
declare(strict_types=1);

session_start();

function ev991_login_failed_page(string $msg = 'Login failed'): void {
    http_response_code(401);
    header('Content-Type: text/html; charset=utf-8');

    $safe = htmlspecialchars($msg, ENT_QUOTES, 'UTF-8');

    echo <<<HTML
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Login Failed · ExpressVisa One</title>
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<style>
:root{--red:#E60012;--dark:#111827;--muted:#6b7280;--soft:#fff1f2}
*{box-sizing:border-box}
body{margin:0;min-height:100vh;font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;background:linear-gradient(135deg,#fff,#fff1f2);display:grid;place-items:center;padding:22px;color:var(--dark)}
.card{width:min(460px,94vw);background:rgba(255,255,255,.94);border:1px solid rgba(230,0,18,.16);border-radius:28px;box-shadow:0 24px 70px rgba(0,0,0,.14);padding:26px;text-align:center}
.logo{width:76px;height:76px;object-fit:contain;margin:0 auto 12px;display:block}
.badge{display:inline-flex;align-items:center;gap:8px;background:var(--soft);color:var(--red);border:1px solid rgba(230,0,18,.2);border-radius:999px;padding:8px 12px;font-weight:900;font-size:13px}
h1{font-size:26px;margin:16px 0 8px;letter-spacing:-.03em}
p{margin:0;color:var(--muted);line-height:1.55;font-size:15px}
.reason{margin-top:14px;background:#f9fafb;border:1px solid #eee;border-radius:16px;padding:12px;color:#374151;font-weight:800}
.actions{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:20px}
a{display:flex;align-items:center;justify-content:center;text-decoration:none;border-radius:999px;padding:13px 14px;font-weight:900}
.primary{background:var(--red);color:#fff;box-shadow:0 12px 26px rgba(230,0,18,.25)}
.secondary{background:#111827;color:#fff}
.zh{margin-top:14px;font-size:13px;color:#6b7280}
@media(max-width:420px){.actions{grid-template-columns:1fr}.card{padding:22px}}
</style>
</head>
<body>
  <main class="card">
    <div class="badge">⚠ Login Required / 登录失败</div>
    <h1>Login Failed</h1>
    <p>Your account session could not be verified. Please login again to continue.</p>
    <div class="reason">{$safe}</div>
    <div class="actions">
      <a class="primary" href="/signup/">Try Again</a>
      <a class="secondary" href="/">Back Home</a>
    </div>
    <div class="zh">登录未完成，请重新登录后继续使用 ExpressVisa One。</div>
  </main>
<script src="/assets/js/991-wallet-sync.js?v=991-unified"></script>
<script src="/assets/991-balance-sync.js?v=991-balance-sync-ux-v1" defer></script>
<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
</body>
</html>
HTML;
    exit;
}

try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=visa_db;charset=utf8mb4',
        'oneexpressvisa',
        '$Express4653',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (Throwable $e) {
    ev991_login_failed_page('System temporarily unavailable');
}

$id   = trim((string)($_POST['identity'] ?? ''));
$pw   = (string)($_POST['password'] ?? '');
$next = (string)($_POST['next'] ?? '/post/');

if ($id === '' || $pw === '') {
    ev991_login_failed_page('Please enter username/email and password');
}

$s = $pdo->prepare("
    SELECT *
    FROM visa_unified_accounts
    WHERE email = ? OR username = ?
    LIMIT 1
");
$s->execute([$id, $id]);
$u = $s->fetch();

if (!$u || empty($u['password_hash']) || !password_verify($pw, (string)$u['password_hash'])) {
    ev991_login_failed_page('Invalid username/email or password');
}

$_SESSION['ev_account_id']  = $u['id'];
$_SESSION['ev_username']    = $u['username'] ?? '';
$_SESSION['ev_ton_address'] = $u['ton_address'] ?? '';

if ($next === '' || str_starts_with($next, 'http') || !str_starts_with($next, '/')) {
    $next = '/post/';
}

header('Location: ' . $next);
exit;
