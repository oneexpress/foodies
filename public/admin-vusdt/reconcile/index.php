<?php
declare(strict_types=1);
require_once __DIR__ . '/../../inc/visa-vusdt.php';

$pdo = visa_pdo();

function scalar_sql(string $sql): string {
    return (string)(visa_pdo()->query($sql)->fetchColumn() ?: '0.000000');
}

function envv(string $k, string $default=''): string {
    return getenv($k) ?: $default;
}

function toncenter_json(string $url): ?array {
    $api = envv('TONCENTER_API_KEY');
    $headers = "Accept: application/json\r\n";
    if ($api !== '') $headers .= "X-API-Key: {$api}\r\n";
    $ctx = stream_context_create(['http'=>['timeout'=>15,'header'=>$headers]]);
    $raw = @file_get_contents($url, false, $ctx);
    if (!$raw) return null;
    $j = json_decode($raw, true);
    return is_array($j) ? $j : null;
}

function fetch_treasury_balance(): array {
    $base = rtrim(envv('TONCENTER_BASE','https://toncenter.com/api/v3'), '/');
    $treasury = envv('VISA_VUSDT_TON_TREASURY');
    $jetton = envv('VUSDT_TON_JETTON_MASTER');

    if ($treasury === '') {
        return ['ok'=>false,'balance'=>'0.000000','raw'=>'Missing VISA_VUSDT_TON_TREASURY'];
    }

    $url = $base . '/jetton/wallets?owner_address=' . rawurlencode($treasury);
    if ($jetton !== '') $url .= '&jetton_address=' . rawurlencode($jetton);

    $j = toncenter_json($url);
    if (!$j) return ['ok'=>false,'balance'=>'0.000000','raw'=>'Toncenter fetch failed'];

    $wallets = $j['jetton_wallets'] ?? $j['wallets'] ?? $j['result'] ?? [];
    if (!is_array($wallets)) $wallets = [];

    $best = null;
    foreach ($wallets as $w) {
        if (!is_array($w)) continue;
        $candidateMaster = $w['jetton'] ?? $w['jetton_address'] ?? $w['jetton_master'] ?? '';
        if ($jetton === '' || stripos((string)$candidateMaster, $jetton) !== false) {
            $best = $w;
            break;
        }
    }

    if (!$best && isset($wallets[0]) && is_array($wallets[0])) $best = $wallets[0];
    if (!$best) return ['ok'=>false,'balance'=>'0.000000','raw'=>'No jetton wallet found'];

    $rawBal = $best['balance'] ?? $best['amount'] ?? '0';
    $balance = number_format(((float)$rawBal) / 1000000, 6, '.', '');

    return ['ok'=>true,'balance'=>$balance,'raw'=>json_encode($best, JSON_UNESCAPED_SLASHES)];
}

$on = fetch_treasury_balance();

$totalReloaded = scalar_sql("SELECT COALESCE(SUM(amount_vusdt),0) FROM visa_vusdt_reloads WHERE status='confirmed'");
$totalPaid = scalar_sql("SELECT COALESCE(SUM(amount_vusdt),0) FROM visa_vusdt_payments WHERE status='paid'");
$liability = scalar_sql("SELECT COALESCE(SUM(balance_vusdt),0) FROM visa_vusdt_balances");
$pendingReloads = scalar_sql("SELECT COUNT(*) FROM visa_vusdt_reloads WHERE status='pending_payment'");
$confirmedReloads = scalar_sql("SELECT COUNT(*) FROM visa_vusdt_reloads WHERE status='confirmed'");

$onchain = (float)$on['balance'];
$offchain = (float)$liability;
$gap = $onchain - $offchain;
$status = $gap >= 0 ? 'SAFE' : 'CRITICAL';

$recent = $pdo->query("SELECT * FROM visa_vusdt_reloads ORDER BY id DESC LIMIT 30")->fetchAll();
$balances = $pdo->query("SELECT * FROM visa_vusdt_balances ORDER BY balance_vusdt DESC LIMIT 30")->fetchAll();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>vUSDT Treasury Reconciliation</title>
<style>
body{font-family:Arial;margin:0;background:#101114;color:#eee}
.wrap{max-width:1200px;margin:auto;padding:24px}
.hero{background:linear-gradient(135deg,#e60023,#ff6a00);border-radius:28px;padding:28px;color:#fff}
.grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-top:18px}
.card{background:#181a20;border:1px solid #2a2d35;border-radius:22px;padding:20px}
.num{font-size:30px;font-weight:900}
.good{color:#22c55e}.bad{color:#ef4444}.warn{color:#f59e0b}
table{width:100%;border-collapse:collapse;margin-top:12px}
td,th{border-bottom:1px solid #2a2d35;padding:9px;text-align:left;font-size:13px}
pre{white-space:pre-wrap;word-break:break-word;background:#0b0c0f;padding:14px;border-radius:14px}
@media(max-width:800px){.grid{grid-template-columns:1fr}}
</style>
</head>
<body>
<div class="wrap">
  <div class="hero">
    <h1>vUSDT Treasury Reconciliation</h1>
    <p>Control rule: OnChain vUSDT Reserve ≥ OffChain vUSDT Liability.</p>
  </div>

  <div class="grid">
    <div class="card"><b>OnChain Treasury Balance</b><div class="num <?= $on['ok']?'good':'bad' ?>"><?=h($on['balance'])?></div><p>vUSDT-TON</p></div>
    <div class="card"><b>OffChain Liability</b><div class="num warn"><?=h(number_format($offchain,6))?></div><p>SUM user balances</p></div>
    <div class="card"><b>Reserve Gap</b><div class="num <?= $gap>=0?'good':'bad' ?>"><?=h(number_format($gap,6))?></div><p><?=h($status)?></p></div>
    <div class="card"><b>Total Reloaded</b><div class="num good"><?=h(number_format((float)$totalReloaded,6))?></div></div>
    <div class="card"><b>Total Paid / Used</b><div class="num"><?=h(number_format((float)$totalPaid,6))?></div></div>
    <div class="card"><b>Pending Reloads</b><div class="num bad"><?=h($pendingReloads)?></div><p>Confirmed reloads: <?=h($confirmedReloads)?></p></div>
  </div>

  <h2>Reconciliation Source</h2>
  <div class="card">
    <p><b>Treasury:</b> <?=h(envv('VISA_VUSDT_TON_TREASURY','NOT SET'))?></p>
    <p><b>Jetton Master:</b> <?=h(envv('VUSDT_TON_JETTON_MASTER','NOT SET'))?></p>
    <p><b>Fetch Status:</b> <?=h($on['ok'] ? 'OK' : 'FAILED')?></p>
    <pre><?=h($on['raw'])?></pre>
  </div>

  <h2>Top OffChain Balances</h2>
  <div class="card">
    <table><tr><th>Wallet Key</th><th>Holder</th><th>WhatsApp</th><th>Balance</th></tr>
    <?php foreach($balances as $r): ?>
    <tr><td><?=h($r['wallet_key'])?></td><td><?=h($r['holder_name'])?></td><td><?=h($r['whatsapp'])?></td><td><?=h($r['balance_vusdt'])?></td></tr>
    <?php endforeach; ?>
    </table>
  </div>

  <h2>Recent Reloads</h2>
  <div class="card">
    <table><tr><th>Ref</th><th>Wallet</th><th>Amount</th><th>Status</th><th>TX</th><th>Time</th></tr>
    <?php foreach($recent as $r): ?>
    <tr><td><?=h($r['reload_ref'])?></td><td><?=h($r['wallet_key'])?></td><td><?=h($r['amount_vusdt'])?></td><td><?=h($r['status'])?></td><td><?=h($r['tx_hash'])?></td><td><?=h($r['created_at'])?></td></tr>
    <?php endforeach; ?>
    </table>
  </div>
</div>
<script src="/assets/js/991-wallet-sync.js?v=991-unified"></script>
<script src="/assets/991-balance-sync.js?v=991-balance-sync-ux-v1" defer></script>
<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
</body>
</html>
