<?php
declare(strict_types=1);
require_once __DIR__ . '/../../inc/vusdt-treasury.php';
$pdo = ev_vusdt_pdo();
$s = ev_vusdt_audit_summary();
$rows = $pdo->query("SELECT * FROM ev_vusdt_treasury_audit ORDER BY id DESC LIMIT 100")->fetchAll();
?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>vUSDT Treasury Audit</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
body{margin:0;font-family:system-ui;background:#fff1f2;color:#111827}.wrap{max-width:1180px;margin:auto;padding:24px 16px 100px}
.card{background:#fff;border:1px solid #fecdd3;border-radius:24px;padding:20px;box-shadow:0 14px 36px rgba(230,0,18,.12);margin-bottom:16px}
h1{color:#E60012}.grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px}.kpi{border:1px solid #fee2e2;border-radius:18px;padding:14px}.kpi b{display:block;font-size:22px;color:#E60012}
.ok{color:#059669;font-weight:900}.bad{color:#dc2626;font-weight:900}table{width:100%;border-collapse:collapse;font-size:13px}td,th{padding:9px;border-bottom:1px solid #fee2e2;text-align:left}
@media(max-width:760px){.grid{grid-template-columns:1fr}table{font-size:12px}}
</style>
</head>
<body><main class="wrap">
<section class="card">
<h1>vUSDT Treasury Audit</h1>
<p><b>Locked:</b> 10,000,000 initial vUSDT, mintable, no max supply, 1:1 reference peg with USDT-TON.</p>
<p>Status: <span class="<?= $s['is_fully_backed'] ? 'ok' : 'bad' ?>"><?= $s['is_fully_backed'] ? 'FULLY BACKED' : 'BACKING GAP' ?></span></p>
<div class="grid">
<div class="kpi">Initial Supply<b><?=htmlspecialchars($s['initial_supply_vusdt'])?></b></div>
<div class="kpi">Backed USDT-TON<b><?=htmlspecialchars($s['backed_usdt_ton'])?></b></div>
<div class="kpi">Circulating vUSDT<b><?=htmlspecialchars($s['circulating_vusdt'])?></b></div>
<div class="kpi">Backing Gap<b><?=htmlspecialchars($s['backing_gap_usdt_ton_minus_circulating'])?></b></div>
</div>
</section>

<section class="card">
<h2>Latest Audit Events</h2>
<table>
<thead><tr><th>ID</th><th>Event</th><th>Wallet</th><th>vUSDT</th><th>USDT-TON</th><th>TX</th><th>Ref</th><th>Time</th></tr></thead>
<tbody>
<?php foreach($rows as $r): ?>
<tr>
<td><?=htmlspecialchars((string)$r['id'])?></td>
<td><?=htmlspecialchars((string)$r['event_type'])?></td>
<td><?=htmlspecialchars((string)$r['wallet'])?></td>
<td><?=htmlspecialchars((string)$r['amount_vusdt'])?></td>
<td><?=htmlspecialchars((string)$r['amount_usdt_ton'])?></td>
<td><?=htmlspecialchars((string)$r['tx_hash'])?></td>
<td><?=htmlspecialchars((string)$r['ref'])?></td>
<td><?=htmlspecialchars((string)$r['created_at'])?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</section>
<script src="/assets/js/991-wallet-sync.js?v=991-unified"></script>
<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
</body></html>
