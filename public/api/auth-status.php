<?php
declare(strict_types=1);
session_start();
header('Content-Type: application/json; charset=UTF-8');
echo json_encode([
  'ok'=>true,
  'logged_in'=>!empty($_SESSION['ev_account_id']) || !empty($_SESSION['ev_username']) || !empty($_SESSION['ev_ton_address']),
  'username'=>$_SESSION['ev_username'] ?? '',
  'ton_address'=>$_SESSION['ev_ton_address'] ?? ($_COOKIE['ev_ton_address'] ?? '')
], JSON_UNESCAPED_UNICODE);


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
