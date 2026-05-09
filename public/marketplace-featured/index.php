<?php
declare(strict_types=1);
$pdo = new PDO("mysql:host=localhost;dbname=visa_db;charset=utf8mb4","oneexpressvisa",'$Express4653',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);

$rows=$pdo->query("
SELECT id,post_ref,title,description,price,whatsapp,marketplace_product_id,boost_level,boost_expiry
FROM visa_free_posts
WHERE sync_status='synced'
ORDER BY
  CASE WHEN boost_level>0 AND boost_expiry>NOW() THEN 0 ELSE 1 END,
  boost_expiry DESC,
  id DESC
LIMIT 100
")->fetchAll();

function wa($s){$n=preg_replace('/\D+/','',$s ?? ''); return $n ? 'https://wa.me/'.$n : '';}
?>
<!doctype html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Featured FoodTruck Marketplace</title>
<link rel="stylesheet" href="/assets/css/ev-marketplace-boost.css">
<style>
body{margin:0;background:#0b0b10;color:#fff;font-family:Arial,sans-serif}.wrap{max-width:1180px;margin:auto;padding:28px}
h1{margin:0 0 16px}.grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}
.card{background:#16161d;border:1px solid #2d2d38;border-radius:18px;padding:18px;color:#fff;text-decoration:none;box-shadow:0 18px 35px #0007}
.price{font-size:22px;font-weight:900;color:#fff}.btn{display:inline-block;background:#e60012;color:#fff;padding:10px 12px;border-radius:12px;text-decoration:none;font-weight:900;margin-top:10px}
.desc{color:#bbb;min-height:56px}.muted{color:#aaa}
@media(max-width:900px){.grid{grid-template-columns:1fr}}
</style></head>
<body><div class="wrap">
<h1>Featured FoodTruck Marketplace</h1>
<p class="muted">Boosted listings appear first. Boost price: 0.01 vUSDT.</p>
<div class="grid">
<?php foreach($rows as $r): 
  $boosted = ((int)$r['boost_level']>0 && !empty($r['boost_expiry']) && strtotime($r['boost_expiry'])>time());
  $url = '/marketplace/index.php?route=product/product&product_id='.(int)$r['marketplace_product_id'];
?>
  <div class="card">
    <?php if($boosted): ?><div class="ev-boost-badge">FEATURED</div><?php endif; ?>
    <h2><?=htmlspecialchars($r['title'])?></h2>
<?php
$cert=$pdo->prepare("SELECT cert_code FROM ev_vendor_certs WHERE post_id=? AND status='active' LIMIT 1");
$cert->execute([$r['id']]);
$c=$cert->fetch();
if($c):
?>
<div style="margin:6px 0;padding:6px 10px;border-radius:10px;background:#16161d;border:1px solid #2d2d38;font-size:12px">
🏅 <b><?=htmlspecialchars($c['cert_code'])?></b>
<a href="/cert/verify.php?code=<?=urlencode($c['cert_code'])?>" style="color:#22c55e">verify</a>
</div>
<?php endif; ?>
    <div class="price">RM <?=number_format((float)$r['price'],2)?></div>
    <p class="desc"><?=htmlspecialchars(mb_substr($r['description'] ?? '',0,130))?></p>
    <a class="btn" href="<?=$url?>">View Product</a>
    <?php if(wa($r['whatsapp'] ?? '')): ?><a class="btn" href="<?=wa($r['whatsapp'])?>">WhatsApp</a><?php endif; ?>
    <a class="btn" href="/boost/?post_id=<?=$r['id']?>">Boost 0.01 vUSDT</a>
  </div>
<?php endforeach; ?>
</div>
</div><script src="/assets/ev-pixel/config.js?v=1"></script><script src="/assets/ev-pixel/ev-pixel.js?v=1"></script>
<script src="/assets/js/991-wallet-sync.js?v=991-unified"></script>
<script src="/assets/991-balance-sync.js?v=991-balance-sync-ux-v1" defer></script>
<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
</body></html>
