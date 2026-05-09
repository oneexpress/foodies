<?php
require_once __DIR__ . '/../../rewards/_db.php';
$pdo = ev_pdo();
$rows = $pdo->query("SELECT * FROM ev_reward_rules ORDER BY id ASC")->fetchAll();
?>
<!doctype html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Rewards Admin</title>
<style>body{font-family:Arial;background:#050505;color:#fff;padding:20px}table{width:100%;border-collapse:collapse;background:#111}td,th{padding:12px;border:1px solid #333}a{color:#fff}</style>
</head><body>
<h1>Rewards Engine Admin</h1>
<table><tr><th>Rule</th><th>Score</th><th>vSHARE</th><th>vUSDT</th><th>Daily Limit</th><th>Active</th></tr>
<?php foreach($rows as $r): ?>
<tr>
<td><?=htmlspecialchars($r['rule_name'])?></td>
<td><?=htmlspecialchars($r['score_value'])?></td>
<td><?=htmlspecialchars($r['vshare_value'])?></td>
<td><?=htmlspecialchars($r['vusdt_value'])?></td>
<td><?=htmlspecialchars((string)$r['daily_limit'])?></td>
<td><?=((int)$r['is_active'] ? 'YES':'NO')?></td>
</tr>
<?php endforeach; ?>
</table>
<script src="/assets/-bottom-bar.js?v=991-logo-final" defer></script>
<script src="/assets/js/991-wallet-sync.js?v=991-unified"></script>
</body></html>
