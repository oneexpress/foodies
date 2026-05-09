<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../inc/rewards-engine.php';

$wallet = trim((string)($_POST['wallet'] ?? $_GET['wallet'] ?? ev_active_wallet()));
$amount = (float)($_POST['amount'] ?? $_GET['amount'] ?? 1.0000);
$ref = 'TEST-' . date('YmdHis');

$ok = ev_reward_vshare($wallet, $amount, 'manual_test_credit', $ref, ['source'=>'test-credit']);
echo json_encode([
  'ok'=>$ok,
  'wallet'=>$wallet,
  'credited'=>$amount,
  'vShare'=>ev_vshare_balance($wallet),
  'ref'=>$ref
], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
