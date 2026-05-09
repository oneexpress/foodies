<?php
declare(strict_types=1);
require_once '/var/www/html/visa/public/inc/rewards-payment-hook.php';

header('Content-Type: application/json; charset=utf-8');

$user = 'EVW'.date('ymd').strtoupper(substr(bin2hex(random_bytes(4)),0,8));
setcookie('ev_wallet_user',$user,time()+86400*365,'/');

echo json_encode([
  "ok"=>true,
  "user"=>$user
], JSON_UNESCAPED_SLASHES);


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
