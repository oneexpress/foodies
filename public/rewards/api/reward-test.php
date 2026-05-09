<?php
require_once __DIR__.'/../../inc/rewards-engine.php';

header('Content-Type: application/json');

$wallet = $_GET['wallet'] ?? '';
$amount = floatval($_GET['amount'] ?? 5);

if (!$wallet) {
    echo json_encode(['ok'=>false,'error'=>'wallet required']); exit;
}

$pdo = ev_pdo();
ev_vshare_credit($pdo, $wallet, $amount, 'manual_test');

echo json_encode([
  'ok'=>true,
  'wallet'=>$wallet,
  'amount'=>$amount
]);


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
