<?php
declare(strict_types=1);

$envFile = '/var/www/html/visa/.env';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $api = trim((string)($_POST['TONCENTER_API_KEY'] ?? ''));
    $treasury = trim((string)($_POST['VUSDT_TREASURY_TON'] ?? ''));

    $lines = [];
    if (is_file($envFile)) {
        foreach (file($envFile, FILE_IGNORE_NEW_LINES) as $line) {
            if (str_starts_with($line, 'TONCENTER_API_KEY=') || str_starts_with($line, 'VUSDT_TREASURY_TON=')) continue;
            $lines[] = $line;
        }
    }

    $lines[] = 'TONCENTER_API_KEY=' . $api;
    $lines[] = 'VUSDT_TREASURY_TON=' . $treasury;
    file_put_contents($envFile, implode("\n", $lines) . "\n");

    header('Location: /admin/boost/toncenter-env.php?saved=1');
    exit;
}

$env = [];
if (is_file($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_contains($line, '=')) {
            [$k,$v] = explode('=', $line, 2);
            $env[$k] = $v;
        }
    }
}

function h($s){return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>TONCenter Boost Verify Config</title>
<style>
body{font-family:Arial;background:#fff7f7;padding:24px}
.card{max-width:760px;margin:auto;background:#fff;border-radius:20px;padding:24px;box-shadow:0 18px 50px rgba(0,0,0,.08)}
input{width:100%;height:46px;border:1px solid #ddd;border-radius:12px;padding:0 12px;margin:8px 0 16px}
button,a{display:inline-flex;align-items:center;justify-content:center;height:44px;padding:0 16px;border-radius:999px;background:#e60023;color:#fff;text-decoration:none;border:0;font-weight:900}
.note{background:#fff1f2;border:1px solid #fecdd3;border-radius:14px;padding:14px;color:#b1b}
</style>
</head>
<body>
<div class="card">
  <h1>TONCenter Boost Verify Config</h1>
  <div class="note">
    Enter the treasury TON wallet that receives boost payments. User payment comment must include the Boost Ref or Post Ref.
  </div>

  <?php if(isset($_GET['saved'])): ?><p><b>Saved.</b></p><?php endif; ?>

  <form method="post">
    <label>TONCenter API Key</label>
    <input name="TONCENTER_API_KEY" value="<?=h($env['TONCENTER_API_KEY'] ?? '')?>">

    <label>vUSDT / Treasury TON Wallet</label>
    <input name="VUSDT_TREASURY_TON" value="<?=h($env['VUSDT_TREASURY_TON'] ?? '')?>" required>

    <button>Save Config</button>
    <a href="/admin/boost/">Back</a>
  </form>
</div>
<script src="/assets/-bottom-bar.js?v=991-logo-final" defer></script>
<script src="/assets/js/991-wallet-sync.js?v=991-unified"></script>
</body>
</html>
