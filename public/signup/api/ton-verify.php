<?php

require_once '/var/www/html/visa/public/inc/rewards-payment-hook.php';
header('Content-Type: application/json');
session_start();

$data = json_decode(file_get_contents("php://input"), true);

$address = $data['address'] ?? '';
$signature = $data['signature'] ?? '';
$nonce = $_SESSION['ton_nonce'] ?? '';

if (!$address || !$signature || !$nonce) {
    echo json_encode(['ok'=>false,'msg'=>'Missing data']);
    exit;
}

/*
NOTE:
Proper TON signature verification requires:
- ton-crypto or TON SDK
- For now we simulate strict binding

PRODUCTION:
Replace with real signature verify
*/

$_SESSION['ton_verified'] = true;
$_SESSION['ton_wallet'] = $address;

echo json_encode([
  'ok' => true,
  'wallet' => $address
]);


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
