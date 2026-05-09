<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/admin_db.php';

$db = ev_admin_db();
$table = ev_submit_table($db);

$res = $db->query("SELECT * FROM `$table` ORDER BY id DESC LIMIT 100");
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>ExpressVisa FoodTruck Marketplace Approval</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body{font-family:Arial,sans-serif;background:#f6f7fb;margin:0;color:#111}
.wrap{max-width:1200px;margin:auto;padding:24px}
.card{background:#fff;border:1px solid #e5e7eb;border-radius:18px;padding:18px;margin:14px 0;box-shadow:0 10px 26px rgba(0,0,0,.05)}
.badge{display:inline-block;padding:5px 10px;border-radius:999px;background:#fff1f2;color:#d0021b;font-weight:700;font-size:12px}
h1{margin:0 0 18px}
h3{margin:10px 0}
.meta{color:#666;line-height:1.7}
.actions{display:flex;gap:10px;margin-top:14px;flex-wrap:wrap}
button,a.btn{border:0;border-radius:12px;padding:10px 14px;font-weight:800;text-decoration:none;cursor:pointer}
.approve{background:#d0021b;color:#fff}
.reject{background:#111;color:#fff}
.preview{background:#f3f4f6;color:#111}
textarea,input{width:100%;padding:10px;border:1px solid #ddd;border-radius:10px;margin-top:8px}
</style>
</head>
<body>
<div class="wrap">
<h1>FoodTruck Marketplace Approval Queue</h1>
<?php while($r = $res->fetch_assoc()): ?>
<div class="card">
  <span class="badge"><?=htmlspecialchars($r['status'] ?? 'pending')?></span>
  <h3><?=htmlspecialchars($r['title'] ?? '')?></h3>
  <div class="meta">
    Service: <?=htmlspecialchars($r['service_name'] ?? '-')?> |
    Nationality: <?=htmlspecialchars($r['nationality_name'] ?? '-')?> |
    Location: <?=htmlspecialchars($r['location_name'] ?? '-')?> / <?=htmlspecialchars($r['area_name'] ?? '-')?><br>
    WhatsApp: <?=htmlspecialchars($r['whatsapp'] ?? '-')?> |
    Price: <?=htmlspecialchars((string)($r['price'] ?? '0'))?>
  </div>
  <p><?=nl2br(htmlspecialchars($r['description'] ?? ''))?></p>
  <?php if (!empty($r['product_id'])): ?>
    <a class="btn preview" target="_blank" href="/marketplace/index.php?route=product/product&product_id=<?=(int)$r['product_id']?>">View Product</a>
  <?php endif; ?>
  <form class="actions" method="post" action="action.php">
    <input type="hidden" name="id" value="<?=(int)$r['id']?>">
    <button class="approve" name="action" value="approve">Approve & Publish</button>
    <button class="reject" name="action" value="reject">Reject</button>
  </form>
</div>
<?php endwhile; ?>
</div>
<script src="/assets/js/991-wallet-sync.js?v=991-unified"></script>
<script src="/assets/991-balance-sync.js?v=991-balance-sync-ux-v1" defer></script>
<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
</body>
</html>
