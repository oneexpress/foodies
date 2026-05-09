<?php
declare(strict_types=1);
session_start();
$_SESSION=[];
if (ini_get('session.use_cookies')) {
  $p=session_get_cookie_params();
  setcookie(session_name(),'',time()-42000,$p['path'],$p['domain'],$p['secure'],$p['httponly']);
}
setcookie('ev_ton_address','',time()-3600,'/');
session_destroy();
header('Content-Type: application/json; charset=UTF-8');
echo json_encode(['ok'=>true]);


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
