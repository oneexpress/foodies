<?php
header('Content-Type: application/json');

$nonce = bin2hex(random_bytes(16));
session_start();
$_SESSION['ton_nonce'] = $nonce;

echo json_encode([
  'ok' => true,
  'nonce' => $nonce
]);


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
