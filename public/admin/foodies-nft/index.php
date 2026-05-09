<?php
declare(strict_types=1);
require_once __DIR__ . '/../../foodies-nft/_db.php';

$pdo = fnft_pdo();
$rows = $pdo->query("SELECT * FROM ev_foodies_nft_redeems ORDER BY id DESC LIMIT 200")->fetchAll();

$stats = [
  'pending'=>0,
  'approved'=>0,
  'minted'=>0,
  'total_weight'=>0,
];
foreach ($rows as $r) {
    $s = (string)$r['status'];
    if (isset($stats[$s])) $stats[$s]++;
    if (in_array($s, ['pending','approved','minted'], true)) $stats['total_weight'] += (int)$r['weight'];
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Foodies NFT Admin</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
body{font-family:Arial;background:#050505;color:#fff;padding:20px}
.grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:16px}
.card{background:#111;border:1px solid #333;border-radius:18px;padding:16px}
.card span{display:block;color:#aaa;font-size:12px}.card b{display:block;font-size:26px;margin-top:8px}
table{width:100%;border-collapse:collapse;background:#111}
td,th{padding:10px;border:1px solid #333;text-align:left;font-size:13px}
.badge{padding:4px 8px;border-radius:999px;background:#e60012}
button{border:0;border-radius:999px;padding:8px 10px;background:#e60012;color:#fff;font-weight:bold;cursor:pointer;margin:2px}
a{color:#16f2a5}
@media(max-width:800px){.grid{grid-template-columns:1fr}table{font-size:12px}}
</style>
</head>
<body>
<h1>Foodies NFT Redeem Admin</h1>

<div class="grid">
  <div class="card"><span>Pending</span><b><?=htmlspecialchars((string)$stats['pending'])?></b></div>
  <div class="card"><span>Approved</span><b><?=htmlspecialchars((string)$stats['approved'])?></b></div>
  <div class="card"><span>Minted</span><b><?=htmlspecialchars((string)$stats['minted'])?></b></div>
  <div class="card"><span>Total Weight</span><b><?=htmlspecialchars((string)$stats['total_weight'])?></b></div>
</div>

<table>
<tr>
<th>ID</th><th>UID</th><th>Wallet</th><th>Tier</th><th>Stars</th><th>Weight</th><th>Status</th><th>Verify</th><th>Action</th>
</tr>
<?php foreach($rows as $r): ?>
<tr>
<td><?=htmlspecialchars((string)$r['id'])?></td>
<td><?=htmlspecialchars($r['redeem_uid'])?></td>
<td><?=htmlspecialchars($r['wallet'])?></td>
<td><?=htmlspecialchars($r['tier_name_en'])?> / <?=htmlspecialchars($r['tier_name_zh'])?></td>
<td><?=htmlspecialchars($r['stars'])?></td>
<td><?=htmlspecialchars((string)$r['weight'])?></td>
<td><span class="badge"><?=htmlspecialchars($r['status'])?></span></td>
<td><a target="_blank" href="/foodies-nft/verify.php?uid=<?=urlencode($r['redeem_uid'])?>">verify</a></td>
<td>
  <form method="post" action="/admin/foodies-nft/action.php" style="display:inline">
    <input type="hidden" name="id" value="<?=htmlspecialchars((string)$r['id'])?>">
    <input type="hidden" name="action" value="approve">
    <button type="submit">Approve</button>
  </form>
  <form method="post" action="/admin/foodies-nft/action.php" style="display:inline">
    <input type="hidden" name="id" value="<?=htmlspecialchars((string)$r['id'])?>">
    <input type="hidden" name="action" value="minted">
    <button type="submit">Mark Minted</button>
  </form>
</td>
</tr>
<?php endforeach; ?>
</table>
<script src="/assets/js/991-wallet-sync.js?v=991-unified"></script>
<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
</body>
</html>
