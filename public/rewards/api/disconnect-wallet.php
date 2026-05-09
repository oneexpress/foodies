<?php
declare(strict_types=1);
session_start();
header('Content-Type: application/json; charset=utf-8');

unset($_SESSION['ev_ton_address'], $_SESSION['ton_wallet'], $_SESSION['wallet_address']);
setcookie('ev_ton_wallet', '', [
    'expires' => time() - 3600,
    'path' => '/',
    'secure' => true,
    'httponly' => false,
    'samesite' => 'Lax',
]);

echo json_encode(['ok'=>true]);


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
