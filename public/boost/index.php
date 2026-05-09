<?php
declare(strict_types=1);
$pdo = new PDO("mysql:host=localhost;dbname=visa_db;charset=utf8mb4","oneexpressvisa",'$Express4653',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);

$postId = (int)($_GET['post_id'] ?? 0);
$ton = trim($_COOKIE['ev_ton_wallet'] ?? '');

if ($postId <= 0) die('Missing post_id');
if (!$ton) {
  header("Location: /wallet/?next=/boost/?post_id=".$postId);
  exit;
}

$stmt=$pdo->prepare("SELECT * FROM visa_free_posts WHERE id=? LIMIT 1");
$stmt->execute([$postId]);
$post=$stmt->fetch();
if(!$post) die('Post not found');

$stmt=$pdo->prepare("
SELECT COALESCE(SUM(CASE
  WHEN status='confirmed' AND direction IN ('reload','adjust') THEN amount
  WHEN status='confirmed' AND direction='debit' THEN -amount
  ELSE 0 END),0) bal
FROM ev_wallet_ledger WHERE ton_wallet=?
");
$stmt->execute([$ton]);
$balance=(float)($stmt->fetch()['bal'] ?? 0);

if($_SERVER['REQUEST_METHOD']==='POST'){
  if($balance < 0.01) {
    $err = 'Insufficient vUSDT balance. Please reload wallet.';
  } else {
    $ref='BOOST'.date('YmdHis').strtoupper(substr(bin2hex(random_bytes(3)),0,6));

    $pdo->beginTransaction();

    $pdo->prepare("INSERT INTO ev_wallet_ledger(wallet_ref,user_key,ton_wallet,token,direction,amount,status,note)
      VALUES(?,?,?,?,?,?,?,?)")
      ->execute([$ref,'BOOST',$ton,'vUSDT','debit',0.01,'confirmed','Post boost debit']);

    $pdo->prepare("INSERT INTO ev_post_boosts(post_id,ton_wallet,amount,status,wallet_ref,confirmed_at)
      VALUES(?,?,0.01,'confirmed',?,NOW())")
      ->execute([$postId,$ton,$ref]);

    $pdo->prepare("UPDATE visa_free_posts
      SET boost_level=1, boost_expiry=DATE_ADD(NOW(), INTERVAL 3 DAY), boost_wallet_ref=?
      WHERE id=?")
      ->execute([$ref,$postId]);

    $pdo->prepare("
      REPLACE INTO ev_marketplace_boost_map(post_id,product_id,boost_level,boost_expiry)
      SELECT id, marketplace_product_id, boost_level, boost_expiry
      FROM visa_free_posts
      WHERE id=? AND marketplace_product_id IS NOT NULL AND marketplace_product_id > 0
    ")->execute([$postId]);

    $pdo->commit();

    header("Location: /boost/success.php?post_id=".$postId);
    exit;
  }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Boost Listing</title>
<style>
body{margin:0;background:#0b0b10;color:#fff;font-family:Arial,sans-serif}.wrap{max-width:760px;margin:auto;padding:28px}
.card{background:#16161d;border:1px solid #2d2d38;border-radius:20px;padding:22px;box-shadow:0 18px 40px #0008}
.btn{background:#e60012;color:white;border:0;border-radius:12px;padding:13px 18px;font-weight:900;text-decoration:none;display:inline-block}
.notice{background:#22070a;border:1px solid #5a1018;border-radius:14px;padding:14px;color:#ffd7dc;margin:14px 0}.addr{word-break:break-all;font-family:monospace}
</style></head>
<body><div class="wrap"><div class="card">
<h1>Boost Listing</h1>
<p><b><?=htmlspecialchars($post['title'])?></b></p>
<p>Boost price: <b>0.01 vUSDT</b></p>
<p>Your balance: <b><?=number_format($balance,2)?> vUSDT</b></p>
<p>TON Wallet:</p><div class="addr"><?=htmlspecialchars($ton)?></div>
<?php if(!empty($err)): ?><div class="notice"><?=htmlspecialchars($err)?></div><?php endif; ?>
<?php if($balance < 0.01): ?>
  <a class="btn" href="/wallet/">Reload vUSDT</a>
<?php else: ?>
  <form method="post"><button class="btn">Pay 0.01 vUSDT & Boost 3 Days</button></form>
<?php endif; ?>
<script src="/assets/js/991-wallet-sync.js?v=991-unified"></script>
<script src="/assets/991-balance-sync.js?v=991-balance-sync-ux-v1" defer></script>
<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
</body></html>
