<?php
require_once __DIR__.'/../../inc/rewards-engine.php';

header('Content-Type: application/json');

$wallet = $_COOKIE['ev_ton_wallet'] ?? '';
if (!$wallet) {
  echo json_encode(['ok'=>false,'error'=>'no wallet']); exit;
}

$pdo = ev_pdo();

$stmt = $pdo->prepare("
SELECT type, token, amount, meta_json, created_at
FROM ev_wallet_ledger
WHERE wallet=:w
ORDER BY id DESC
LIMIT 20
");
$stmt->execute([':w'=>$wallet]);

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
  'ok'=>true,
  'wallet'=>$wallet,
  'history'=>$rows
]);


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
