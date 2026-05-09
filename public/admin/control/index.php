<?php
declare(strict_types=1);
require __DIR__ . '/../_guard.php';

$pdo = new PDO("mysql:host=localhost;dbname=visa_db;charset=utf8mb4","oneexpressvisa",'$Express4653',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);

$stats = [];
$stats['posts'] = $pdo->query("SELECT COUNT(*) FROM visa_free_posts")->fetchColumn();
$stats['synced'] = $pdo->query("SELECT COUNT(*) FROM visa_free_posts WHERE sync_status='synced'")->fetchColumn();
$stats['boosted'] = $pdo->query("SELECT COUNT(*) FROM visa_free_posts WHERE boost_level>0 AND boost_expiry>NOW()")->fetchColumn();
$stats['certs'] = $pdo->query("SELECT COUNT(*) FROM ev_vendor_certs WHERE status='active'")->fetchColumn();
$stats['wallet_rows'] = $pdo->query("SELECT COUNT(*) FROM ev_wallet_ledger")->fetchColumn();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>ExpressVisa Control Center</title>
<style>
body{margin:0;background:#0b0b10;color:#fff;font-family:Arial,sans-serif}
.wrap{max-width:1180px;margin:auto;padding:28px}
h1{margin:0 0 8px}.sub{color:#aaa;margin-bottom:22px}
.grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}
.card{background:#16161d;border:1px solid #2d2d38;border-radius:20px;padding:20px;box-shadow:0 18px 40px #0008;color:#fff;text-decoration:none}
.card:hover{border-color:#e60012}
.num{font-size:34px;font-weight:900;color:#e60012}
.btn{display:inline-block;background:#e60012;color:#fff;text-decoration:none;border-radius:12px;padding:12px 14px;font-weight:900;margin:6px 6px 0 0}
@media(max-width:900px){.grid{grid-template-columns:1fr}}
</style>
</head>
<body>
<div class="wrap">
<h1>ExpressVisa Control Center</h1>
<div class="sub">Post approval, marketplace sync, vUSDT wallet, boost, and ONE cert modules.</div>

<div class="grid">
  <a class="card" href="/admin/post-approval/"><div class="num"><?=$stats['posts']?></div><h2>Posts</h2><p>Approve and sync posts.</p></a>
  <a class="card" href="/admin/post-approval/"><div class="num"><?=$stats['synced']?></div><h2>Synced</h2><p>OpenCart + Community synced.</p></a>
  <a class="card" href="/marketplace-featured/"><div class="num"><?=$stats['boosted']?></div><h2>Boosted</h2><p>Featured listings active.</p></a>
  <a class="card" href="/cert/verify.php"><div class="num"><?=$stats['certs']?></div><h2>ONE Certs</h2><p>Vendor quality certs.</p></a>
  <a class="card" href="/admin/wallet/"><div class="num"><?=$stats['wallet_rows']?></div><h2>Wallet Ledger</h2><p>vUSDT records.</p></a>
  <a class="card" href="/foodtruck/"><div class="num">10</div><h2>FoodTruck UX</h2><p>Locked categories.</p></a>
</div>

<br>
<a class="btn" href="/admin/post-approval/">Approval</a>
<a class="btn" href="/admin/wallet/">Wallet Admin</a>
<a class="btn" href="/wallet/">Wallet</a>
<a class="btn" href="/foodtruck/">FoodTruck</a>
<a class="btn" href="/marketplace-featured/">Featured</a>
<a class="btn" href="/post/">Post Form</a>
</div>
<script src="/assets/js/991-wallet-sync.js?v=991-unified"></script>
<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
</body>
</html>
