<?php
declare(strict_types=1);
require_once __DIR__ . '/../../inc/visa-vusdt.php';

$pdo = visa_pdo();

function one($sql) {
    return visa_pdo()->query($sql)->fetchColumn() ?: '0.000000';
}

$totalReloaded = one("SELECT COALESCE(SUM(amount_vusdt),0) FROM visa_vusdt_reloads WHERE status='confirmed'");
$totalPaid = one("SELECT COALESCE(SUM(amount_vusdt),0) FROM visa_vusdt_payments WHERE status='paid'");
$outstanding = one("SELECT COALESCE(SUM(balance_vusdt),0) FROM visa_vusdt_balances");
$pendingReloads = one("SELECT COUNT(*) FROM visa_vusdt_reloads WHERE status='pending_payment'");
$paidBookings = one("SELECT COUNT(*) FROM visa_bookings WHERE payment_status='paid'");
$unpaidBookings = one("SELECT COUNT(*) FROM visa_bookings WHERE COALESCE(payment_status,'unpaid')='unpaid'");

$daily = $pdo->query("
    SELECT DATE(created_at) d,
           SUM(CASE WHEN movement='credit' THEN amount_vusdt ELSE 0 END) credit,
           SUM(CASE WHEN movement='debit' THEN amount_vusdt ELSE 0 END) debit
    FROM visa_vusdt_ledger
    GROUP BY DATE(created_at)
    ORDER BY d DESC
    LIMIT 14
")->fetchAll();

$recentReloads = $pdo->query("SELECT * FROM visa_vusdt_reloads ORDER BY id DESC LIMIT 20")->fetchAll();
$recentPayments = $pdo->query("SELECT * FROM visa_vusdt_payments ORDER BY id DESC LIMIT 20")->fetchAll();
$topBalances = $pdo->query("SELECT * FROM visa_vusdt_balances ORDER BY balance_vusdt DESC LIMIT 20")->fetchAll();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>vUSDT Settlement Dashboard</title>
<style>
body{font-family:Arial;margin:0;background:#101114;color:#eee}
.wrap{max-width:1200px;margin:auto;padding:24px}
.hero{background:linear-gradient(135deg,#e60023,#ff6a00);border-radius:28px;padding:28px;color:white}
.grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-top:18px}
.card{background:#181a20;border:1px solid #2a2d35;border-radius:22px;padding:20px}
.num{font-size:30px;font-weight:900;color:#fff}
.label{color:#aaa;font-size:13px}
table{width:100%;border-collapse:collapse;margin-top:12px}
td,th{border-bottom:1px solid #2a2d35;padding:9px;text-align:left;font-size:13px}
h2{margin-top:28px}
.good{color:#22c55e}.warn{color:#f59e0b}.bad{color:#ef4444}
@media(max-width:800px){.grid{grid-template-columns:1fr}}
</style>
</head>
<body>
<div class="wrap">
  <div class="hero">
    <h1>vUSDT Settlement Dashboard</h1>
    <p>Payment analytics, OffChain liability, booking revenue and daily settlement flow.</p>
  </div>

  <div class="grid">
    <div class="card"><div class="label">Total Reloaded</div><div class="num good"><?=h(number_format((float)$totalReloaded,6))?></div><div>vUSDT</div></div>
    <div class="card"><div class="label">Total Paid / Used</div><div class="num"><?=h(number_format((float)$totalPaid,6))?></div><div>vUSDT</div></div>
    <div class="card"><div class="label">Outstanding OffChain Liability</div><div class="num warn"><?=h(number_format((float)$outstanding,6))?></div><div>vUSDT</div></div>
    <div class="card"><div class="label">Pending Reloads</div><div class="num bad"><?=h($pendingReloads)?></div></div>
    <div class="card"><div class="label">Paid Bookings</div><div class="num good"><?=h($paidBookings)?></div></div>
    <div class="card"><div class="label">Unpaid Bookings</div><div class="num warn"><?=h($unpaidBookings)?></div></div>
  </div>

  <h2>Daily vUSDT Flow</h2>
  <div class="card">
    <table>
      <tr><th>Date</th><th>Reload Credit</th><th>Booking Debit</th><th>Net</th></tr>
      <?php foreach($daily as $r): $net=(float)$r['credit']-(float)$r['debit']; ?>
      <tr>
        <td><?=h($r['d'])?></td>
        <td class="good"><?=h(number_format((float)$r['credit'],6))?></td>
        <td><?=h(number_format((float)$r['debit'],6))?></td>
        <td class="<?= $net>=0?'good':'bad' ?>"><?=h(number_format($net,6))?></td>
      </tr>
      <?php endforeach; ?>
    </table>
  </div>

  <h2>Top OffChain Balances</h2>
  <div class="card">
    <table>
      <tr><th>Wallet Key</th><th>Holder</th><th>WhatsApp</th><th>Balance</th></tr>
      <?php foreach($topBalances as $r): ?>
      <tr>
        <td><?=h($r['wallet_key'])?></td>
        <td><?=h($r['holder_name'])?></td>
        <td><?=h($r['whatsapp'])?></td>
        <td class="warn"><?=h($r['balance_vusdt'])?></td>
      </tr>
      <?php endforeach; ?>
    </table>
  </div>

  <h2>Recent Reloads</h2>
  <div class="card">
    <table>
      <tr><th>Ref</th><th>Wallet</th><th>Amount</th><th>Status</th><th>TX</th><th>Time</th></tr>
      <?php foreach($recentReloads as $r): ?>
      <tr>
        <td><?=h($r['reload_ref'])?></td>
        <td><?=h($r['wallet_key'])?></td>
        <td><?=h($r['amount_vusdt'])?></td>
        <td><?=h($r['status'])?></td>
        <td><?=h($r['tx_hash'])?></td>
        <td><?=h($r['created_at'])?></td>
      </tr>
      <?php endforeach; ?>
    </table>
  </div>

  <h2>Recent Booking Payments</h2>
  <div class="card">
    <table>
      <tr><th>Payment Ref</th><th>Booking Ref</th><th>Wallet</th><th>Amount</th><th>Status</th><th>Time</th></tr>
      <?php foreach($recentPayments as $r): ?>
      <tr>
        <td><?=h($r['payment_ref'])?></td>
        <td><?=h($r['booking_ref'])?></td>
        <td><?=h($r['wallet_key'])?></td>
        <td><?=h($r['amount_vusdt'])?></td>
        <td><?=h($r['status'])?></td>
        <td><?=h($r['created_at'])?></td>
      </tr>
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
