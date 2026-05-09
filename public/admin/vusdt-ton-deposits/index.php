<?php
declare(strict_types=1);
require_once __DIR__ . '/../../inc/vusdt-treasury.php';
$pdo = ev_vusdt_pdo();
$rows = $pdo->query("SELECT * FROM ev_vusdt_ton_deposits ORDER BY id DESC LIMIT 100")->fetchAll();
?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>vUSDT TON Deposits</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
body{margin:0;font-family:system-ui;background:#fff1f2;color:#111827}.wrap{max-width:1180px;margin:auto;padding:24px 16px 100px}
.card{background:#fff;border:1px solid #fecdd3;border-radius:24px;padding:20px;box-shadow:0 14px 36px rgba(230,0,18,.12)}
h1{color:#E60012}table{width:100%;border-collapse:collapse;font-size:13px}td,th{padding:9px;border-bottom:1px solid #fee2e2;text-align:left}.pending{color:#d97706}.minted{color:#059669}.rejected{color:#dc2626}
</style>
</head>
<body><main class="wrap"><section class="card">
<h1>vUSDT TON Deposit Inbox</h1>
<p>Auto mint source: verified USDT-TON deposits only.</p>
<table>
<thead><tr><th>ID</th><th>Status</th><th>Wallet</th><th>Amount</th><th>TX</th><th>Reject</th><th>Created</th><th>Minted</th></tr></thead>
<tbody>
<?php foreach($rows as $r): ?>
<tr>
<td><?=htmlspecialchars((string)$r['id'])?></td>
<td class="<?=htmlspecialchars((string)$r['status'])?>"><?=htmlspecialchars((string)$r['status'])?></td>
<td><?=htmlspecialchars((string)$r['user_wallet'])?></td>
<td><?=htmlspecialchars((string)$r['amount_usdt_ton'])?></td>
<td><?=htmlspecialchars((string)$r['tx_hash'])?></td>
<td><?=htmlspecialchars((string)$r['reject_reason'])?></td>
<td><?=htmlspecialchars((string)$r['created_at'])?></td>
<td><?=htmlspecialchars((string)$r['minted_at'])?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<script src="/assets/js/991-wallet-sync.js?v=991-unified"></script>
<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
</body>
</html>
