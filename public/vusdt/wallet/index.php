<?php
declare(strict_types=1);
require_once __DIR__ . '/../../inc/visa-vusdt.php';

$wallet = trim($_GET['wallet_key'] ?? $_POST['wallet_key'] ?? '');
$bal = '0.000000';
$ledger = [];
$payments = [];

if ($wallet !== '') {
    $bal = vusdt_get_balance($wallet);

    $s = visa_pdo()->prepare("SELECT * FROM visa_vusdt_ledger WHERE wallet_key=? ORDER BY id DESC LIMIT 50");
    $s->execute([$wallet]);
    $ledger = $s->fetchAll();

    $p = visa_pdo()->prepare("SELECT * FROM visa_vusdt_payments WHERE wallet_key=? ORDER BY id DESC LIMIT 50");
    $p->execute([$wallet]);
    $payments = $p->fetchAll();
}
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>vUSDT Wallet</title>
<style>
body{font-family:Arial;margin:0;background:#f7f7f8}.wrap{max-width:960px;margin:auto;padding:24px}.hero{background:linear-gradient(135deg,#e60023,#ff6a00);color:#fff;border-radius:28px;padding:28px}.card{background:#fff;border-radius:24px;padding:22px;margin-top:18px;box-shadow:0 10px 30px #0001}input{width:100%;box-sizing:border-box;padding:13px;border:1px solid #ddd;border-radius:14px;margin:6px 0}.btn{display:inline-block;background:linear-gradient(135deg,#e60023,#ff6a00);color:#fff;border:0;border-radius:999px;padding:14px 24px;font-weight:800;text-decoration:none}table{width:100%;border-collapse:collapse}td,th{border-bottom:1px solid #eee;padding:8px;text-align:left}.bal{font-size:34px;font-weight:900}
</style></head><body><div class="wrap">
<div class="hero"><h1>vUSDT Wallet</h1><p>OffChain balance, reload history, and booking payments.</p></div>
<div class="card">
<form method="get">
<input name="wallet_key" value="<?=h($wallet)?>" placeholder="Wallet / Account Key">
<button class="btn">View Wallet</button>
<a class="btn" href="/vusdt/">Reload vUSDT</a>
</form>
<?php if($wallet): ?>
<h2>Balance</h2>
<div class="bal"><?=h($bal)?> vUSDT</div>

<h2>Ledger</h2>
<table><tr><th>Time</th><th>Type</th><th>Amount</th><th>Balance After</th><th>Note</th></tr>
<?php foreach($ledger as $r): ?>
<tr><td><?=h($r['created_at'])?></td><td><?=h($r['movement'])?></td><td><?=h($r['amount_vusdt'])?></td><td><?=h($r['balance_after'])?></td><td><?=h($r['note'])?></td></tr>
<?php endforeach; ?>
</table>

<h2>Payments</h2>
<table><tr><th>Payment Ref</th><th>Booking Ref</th><th>Amount</th><th>Status</th><th>Time</th></tr>
<?php foreach($payments as $r): ?>
<tr><td><?=h($r['payment_ref'])?></td><td><?=h($r['booking_ref'])?></td><td><?=h($r['amount_vusdt'])?></td><td><?=h($r['status'])?></td><td><?=h($r['created_at'])?></td></tr>
<?php endforeach; ?>
</table>
<?php endif; ?>
<script src="/assets/js/991-wallet-sync.js?v=991-unified"></script>
<script src="/assets/991-balance-sync.js?v=991-balance-sync-ux-v1" defer></script>
<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-canonical">
<script src="/assets/js/991-bottom-nav.js?v=991-canonical" defer></script>
</body></html>
