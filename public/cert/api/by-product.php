<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

$pid=(int)($_GET['product_id'] ?? 0);
if($pid<=0){ echo json_encode(["ok"=>false]); exit; }

$pdo=new PDO("mysql:host=localhost;dbname=visa_db;charset=utf8mb4","oneexpressvisa",'$Express4653',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);

$st=$pdo->prepare("SELECT cert_code,brand_name,status FROM ev_vendor_certs WHERE product_id=? AND status='active' LIMIT 1");
$st->execute([$pid]);
$r=$st->fetch();

if(!$r){ echo json_encode(["ok"=>false]); exit; }

echo json_encode([
  "ok"=>true,
  "code"=>$r['cert_code'],
  "brand"=>$r['brand_name']
], JSON_UNESCAPED_SLASHES);


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
