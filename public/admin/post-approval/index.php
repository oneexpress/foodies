<?php
declare(strict_types=1);
require dirname(__DIR__) . '/_guard.php';

$pdo = new PDO("mysql:host=localhost;dbname=visa_db;charset=utf8mb4","oneexpressvisa",'$Express4653',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);

$q = trim($_GET['q'] ?? '');
$status = trim($_GET['status'] ?? '');

$where = [];
$args = [];

if ($q !== '') {
  $where[] = "(title LIKE ? OR post_ref LIKE ? OR whatsapp LIKE ?)";
  $args[]="%$q%"; $args[]="%$q%"; $args[]="%$q%";
}
if ($status !== '') {
  $where[] = "sync_status=?";
  $args[]=$status;
}

$sql = "SELECT * FROM visa_free_posts";
if ($where) $sql .= " WHERE ".implode(" AND ",$where);
$sql .= " ORDER BY id DESC LIMIT 100";

$st=$pdo->prepare($sql);
$st->execute($args);
$rows=$st->fetchAll();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>ExpressVisa Post Approval</title>
<style>
:root{--red:#e60012;--card:#16161d;--line:#2d2d38}
body{margin:0;background:#0b0b10;color:#fff;font-family:Arial,sans-serif}
.wrap{max-width:1280px;margin:auto;padding:26px}
h1{margin:0 0 18px}
.card{background:var(--card);border:1px solid var(--line);border-radius:18px;padding:16px;margin-bottom:14px}
input,select{background:#08080d;color:#fff;border:1px solid #333;border-radius:10px;padding:10px}
.btn{display:inline-block;background:var(--red);color:#fff;text-decoration:none;border-radius:10px;padding:9px 12px;font-weight:800}
.btn2{background:#333}
table{width:100%;border-collapse:collapse}
td,th{padding:10px;border-bottom:1px solid #2d2d38;text-align:left;vertical-align:top}
.badge{padding:5px 9px;border-radius:999px;background:#333}
.synced{background:#0b6b45}.pending{background:#7a4b00}.failed{background:#7a1010}
.desc{max-width:360px;color:#bbb}
</style>
</head>
<body>
<div class="wrap">
  <h1>ExpressVisa Post Approval</h1>

  <form class="card" method="get">
    <input name="q" value="<?=htmlspecialchars($q)?>" placeholder="Search title/ref/WhatsApp">
    <select name="status">
      <option value="">All Status</option>
      <?php foreach(['pending','synced','failed'] as $s): ?>
        <option value="<?=$s?>" <?=$status===$s?'selected':''?>><?=$s?></option>
      <?php endforeach; ?>
    </select>
    <button class="btn">Filter</button>
    <a class="btn btn2" href="/post/">Public Post Form</a>
  </form>

  <div class="card">
    <table>
      <tr>
        <th>ID</th><th>Ref</th><th>Target</th><th>Category</th><th>Title</th><th>Price</th><th>Status</th><th>IDs</th><th>Action</th>
      </tr>
      <?php foreach($rows as $r): ?>
      <tr>
        <td><?=$r['id']?></td>
        <td><?=htmlspecialchars($r['post_ref'])?></td>
        <td><?=htmlspecialchars($r['target'])?></td>
        <td><?=htmlspecialchars(($r['category_slug'] ?? '').' / '.($r['subcategory_slug'] ?? ''))?></td>
        <td>
          <b><?=htmlspecialchars($r['title'])?></b><br>
          <div class="desc"><?=htmlspecialchars(mb_substr($r['description'] ?? '',0,160))?></div>
        </td>
        <td>RM <?=number_format((float)$r['price'],2)?></td>
        <td><span class="badge <?=htmlspecialchars($r['sync_status'])?>"><?=htmlspecialchars($r['sync_status'])?></span></td>
        <td>
          Product: <?=htmlspecialchars((string)$r['marketplace_product_id'])?><br>
          Discussion: <?=htmlspecialchars((string)$r['community_discussion_id'])?>
        </td>
        <td>
          <a class="btn" href="/admin/post-approval/approve.php?id=<?=$r['id']?>">Approve → Sync</a><br><br><a class="btn btn2" href="/boost/?post_id=<?=$r['id']?>">Boost 0.01 vUSDT</a><br><br><a class="btn btn2" href="/cert/issue.php?post_id=<?=$r['id']?>">Issue ONE Cert</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>
  </div>
</div>
<script src="/assets/js/991-wallet-sync.js?v=991-unified"></script>
<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
</body>
</html>
