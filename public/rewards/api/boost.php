<?php
header('Content-Type: application/json');

$pdo = new PDO("mysql:host=localhost;dbname=visa_db","root","");

$post_ref = $_POST['post_ref'] ?? '';
$wallet   = $_POST['wallet'] ?? '';
$amount   = floatval($_POST['amount'] ?? 0);

if (!$post_ref || !$wallet || $amount <= 0) {
    echo json_encode(['ok'=>false,'error'=>'invalid']); exit;
}

/*
Boost logic:
1 vUSDT = 10 boost score
*/

$score = intval($amount * 10);

$stmt = $pdo->prepare("
INSERT INTO ev_listing_boost (post_ref,wallet,amount_vusdt,boost_score)
VALUES (?,?,?,?)
");
$stmt->execute([$post_ref,$wallet,$amount,$score]);

echo json_encode([
  'ok'=>true,
  'post_ref'=>$post_ref,
  'boost_score'=>$score
]);


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
