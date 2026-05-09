<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=UTF-8');

function out(array $a): void {
  echo json_encode($a, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  exit;
}

$db = new mysqli('localhost', 'root', '', 'visa_db');
if ($db->connect_errno) out(['ok'=>false,'error'=>'db_failed']);
$db->set_charset('utf8mb4');

$userKey = trim($_POST['user_key'] ?? $_GET['user_key'] ?? '');
$eventType = trim($_POST['event_type'] ?? $_GET['event_type'] ?? '');
$sourceRef = trim($_POST['source_ref'] ?? $_GET['source_ref'] ?? '');
$sourceModule = trim($_POST['source_module'] ?? $_GET['source_module'] ?? 'marketplace');

if ($userKey === '' || $eventType === '') {
  out(['ok'=>false,'error'=>'missing_required']);
}

$policyStmt = $db->prepare("
  SELECT reward_amount,daily_limit,enabled
  FROM visa_vusdt_reward_policy
  WHERE event_type=?
  LIMIT 1
");
$policyStmt->bind_param('s', $eventType);
$policyStmt->execute();
$policy = $policyStmt->get_result()->fetch_assoc();

if (!$policy || (int)$policy['enabled'] !== 1) {
  out(['ok'=>false,'error'=>'event_disabled']);
}

$amount = (string)$policy['reward_amount'];
$dailyLimit = (int)$policy['daily_limit'];

$stmt = $db->prepare("SELECT wallet_ref,balance,status FROM visa_vusdt_wallets WHERE user_key=? LIMIT 1");
$stmt->bind_param('s', $userKey);
$stmt->execute();
$wallet = $stmt->get_result()->fetch_assoc();

if (!$wallet) {
  $walletRef = 'vUSDT-' . date('YmdHis') . '-' . strtoupper(substr(hash('sha256', $userKey . microtime(true)), 0, 8));
  $ins = $db->prepare("INSERT INTO visa_vusdt_wallets(wallet_ref,user_key,balance,status) VALUES(?,?,0,'active')");
  $ins->bind_param('ss', $walletRef, $userKey);
  $ins->execute();
  $wallet = ['wallet_ref'=>$walletRef,'balance'=>'0.000000','status'=>'active'];
}

if ($wallet['status'] !== 'active') {
  out(['ok'=>false,'error'=>'wallet_not_active']);
}

$walletRef = $wallet['wallet_ref'];

$cap = $db->prepare("
  SELECT COUNT(*) c
  FROM visa_vusdt_reward_events
  WHERE wallet_ref=?
    AND event_type=?
    AND status='credited'
    AND created_at >= CURDATE()
");
$cap->bind_param('ss', $walletRef, $eventType);
$cap->execute();
$capRow = $cap->get_result()->fetch_assoc();

if ((int)$capRow['c'] >= $dailyLimit) {
  out(['ok'=>false,'error'=>'daily_limit_reached','daily_limit'=>$dailyLimit]);
}

$eventRef = 'EVR-' . substr(strtoupper(hash('sha256', $walletRef.'|'.$eventType.'|'.$sourceRef)), 0, 24);

$chk = $db->prepare("
  SELECT id
  FROM visa_vusdt_reward_events
  WHERE wallet_ref=? AND event_type=? AND source_ref=?
  LIMIT 1
");
$chk->bind_param('sss', $walletRef, $eventType, $sourceRef);
$chk->execute();

if ($chk->get_result()->fetch_assoc()) {
  out(['ok'=>false,'error'=>'duplicate_reward','event_ref'=>$eventRef]);
}

$db->begin_transaction();

try {
  $meta = json_encode([
    'user_key'=>$userKey,
    'event_type'=>$eventType,
    'source_ref'=>$sourceRef,
    'source_module'=>$sourceModule,
    'policy_amount'=>$amount,
    'daily_limit'=>$dailyLimit
  ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

  $ev = $db->prepare("
    INSERT INTO visa_vusdt_reward_events
    (event_ref,wallet_ref,event_type,amount,source_module,source_ref,status,meta_json)
    VALUES
    (?,?,?,?,?,?, 'credited', ?)
  ");
  $ev->bind_param('sssssss', $eventRef, $walletRef, $eventType, $amount, $sourceModule, $sourceRef, $meta);
  $ev->execute();

  $txRef = 'TX-' . $eventRef;
  $note = 'Reward: ' . $eventType;

  $lg = $db->prepare("
    INSERT INTO visa_vusdt_ledger(wallet_ref,tx_ref,tx_type,amount,note)
    VALUES(?,?,'credit',?,?)
  ");
  $lg->bind_param('ssss', $walletRef, $txRef, $amount, $note);
  $lg->execute();

  $up = $db->prepare("UPDATE visa_vusdt_wallets SET balance = balance + CAST(? AS DECIMAL(24,6)) WHERE wallet_ref=?");
  $up->bind_param('ss', $amount, $walletRef);
  $up->execute();

  $db->commit();

  out([
    'ok'=>true,
    'event_ref'=>$eventRef,
    'wallet_ref'=>$walletRef,
    'amount'=>$amount,
    'event_type'=>$eventType,
    'daily_limit'=>$dailyLimit
  ]);
} catch (Throwable $e) {
  $db->rollback();
  out(['ok'=>false,'error'=>'credit_failed','detail'=>$e->getMessage()]);
}

