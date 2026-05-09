<?php
header('Content-Type: application/json');

$db = new mysqli('localhost', 'root', '', 'visa_db');
if ($db->connect_errno) {
  echo json_encode(['ok'=>false,'error'=>'db_failed']);
  exit;
}

$userKey = trim($_GET['user_key'] ?? $_POST['user_key'] ?? '');
if ($userKey === '') {
  $userKey = 'guest:' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
}

$stmt = $db->prepare("SELECT wallet_ref,balance,status FROM visa_vusdt_wallets WHERE user_key=? LIMIT 1");
$stmt->bind_param('s', $userKey);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row) {
  $walletRef = 'vUSDT-' . date('YmdHis') . '-' . strtoupper(substr(hash('sha256', $userKey . microtime(true)), 0, 8));
  $ins = $db->prepare("INSERT INTO visa_vusdt_wallets(wallet_ref,user_key,balance,status) VALUES(?,?,0,'active')");
  $ins->bind_param('ss', $walletRef, $userKey);
  $ins->execute();

  $row = [
    'wallet_ref' => $walletRef,
    'balance' => '0.000000',
    'status' => 'active'
  ];
}

$ledger = [];
$q = $db->prepare("SELECT tx_ref,tx_type,amount,note,created_at FROM visa_vusdt_ledger WHERE wallet_ref=? ORDER BY id DESC LIMIT 20");
$q->bind_param('s', $row['wallet_ref']);
$q->execute();
$res = $q->get_result();
while ($r = $res->fetch_assoc()) {
  $ledger[] = $r;
}

echo json_encode([
  'ok' => true,
  'wallet' => $row,
  'ledger' => $ledger
]);

