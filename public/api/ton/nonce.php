<?php
declare(strict_types=1);
session_start();
header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store');

$nonce = bin2hex(random_bytes(16));
$_SESSION['ev_ton_nonce'] = $nonce;
$_SESSION['ev_ton_nonce_expires'] = time() + 180;

echo json_encode([
  'ok' => true,
  'payload' => $nonce,
  'nonce' => $nonce,
  'expires_in' => 180
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
