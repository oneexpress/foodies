<?php
declare(strict_types=1);
$pdo=new PDO("mysql:host=localhost;dbname=visa_db;charset=utf8mb4","oneexpressvisa",'$Express4653',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
$code=trim($_GET['code'] ?? '');
$st=$pdo->prepare("SELECT * FROM ev_vendor_certs WHERE cert_code=? LIMIT 1");
$st->execute([$code]);
$c=$st->fetch();
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>ONE Cert Verify</title>
<style>
body{margin:0;background:#0b0b10;color:#fff;font-family:Arial}.wrap{max-width:760px;margin:auto;padding:28px}
.card{background:#16161d;border:1px solid #2d2d38;border-radius:22px;padding:24px;box-shadow:0 20px 50px #0008}
.logo{width:64px;background:#fff;border-radius:16px;padding:8px}.ok{color:#22c55e}.bad{color:#ef4444}.code{font-size:30px;font-weight:900;color:#e60012;word-break:break-all}
</style></head><body><div class="wrap"><div class="card">
<h1>ONE Vendor Quality Cert</h1>
<?php if($c): ?>
<div class="code"><?=htmlspecialchars($c['cert_code'])?></div>
<h2 class="<?=$c['status']==='active'?'ok':'bad'?>"><?=strtoupper(htmlspecialchars($c['status']))?></h2>
<p>Brand: <b><?=htmlspecialchars($c['brand_name'])?></b></p>
<p>Type: <?=htmlspecialchars($c['cert_type'])?></p>
<p>Issued: <?=htmlspecialchars($c['issued_at'])?></p>
<?php else: ?>
<h2 class="bad">CERT NOT FOUND</h2>
<p>Code: <?=htmlspecialchars($code)?></p>
<?php endif; ?>
<script src="/assets/js/991-wallet-sync.js?v=991-unified"></script>
<script src="/assets/991-balance-sync.js?v=991-balance-sync-ux-v1" defer></script>
<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
</body></html>
