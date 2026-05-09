<?php
declare(strict_types=1);
$pdo=new PDO('mysql:host=localhost;dbname=visa_db;charset=utf8mb4','oneexpressvisa','$Express4653');

function h($s){return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}

$rows=$pdo->query("
SELECT *
FROM visa_free_posts
WHERE sync_status IN ('synced','pending')
ORDER BY id DESC
LIMIT 200
")->fetchAll(PDO::FETCH_ASSOC);

$total=0;
foreach($rows as $r){
  if(!empty($r['price'])) $total+=(float)$r['price'];
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Payment Dashboard</title>
<style>
body{font-family:Arial;background:#f8fafc;padding:20px}
.card{background:#fff;padding:20px;border-radius:14px;margin-bottom:20px;box-shadow:0 10px 30px rgba(0,0,0,.08)}
table{width:100%;border-collapse:collapse}
td,th{padding:10px;border-bottom:1px solid #eee}
.badge{padding:4px 8px;border-radius:6px;font-size:12px}
.pending{background:#fef3c7}
.synced{background:#dcfce7}
</style>
</head>
<body>

<div class="card">
<h2>Total Revenue (Rough)</h2>
<h1>RM <?=number_format($total,2)?></h1>
</div>

<div class="card">
<h2>Recent Posts / Payments</h2>
<table>
<tr>
<th>Ref</th>
<th>Title</th>
<th>Price</th>
<th>Status</th>
</tr>

<?php foreach($rows as $r): ?>
<tr>
<td><?=h($r['post_ref'])?></td>
<td><?=h($r['title'])?></td>
<td>RM <?=number_format((float)$r['price'],2)?></td>
<td>
<span class="badge <?=h($r['sync_status'])?>">
<?=h($r['sync_status'])?>
</span>
</td>
</tr>
<?php endforeach; ?>

</table>
</div>
<script src="/assets/js/991-wallet-sync.js?v=991-unified"></script>
<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
</body>
</html>
