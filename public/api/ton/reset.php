<?php
declare(strict_types=1);
session_start();
header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store');

unset($_SESSION['ev_ton_nonce'], $_SESSION['ev_ton_nonce_expires'], $_SESSION['ev_ton_address']);

echo json_encode(['ok'=>true,'msg'=>'TON session reset completed.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
