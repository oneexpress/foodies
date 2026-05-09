<?php

require_once '/var/www/html/visa/public/inc/rewards-payment-hook.php';
declare(strict_types=1);
session_start();
header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store');

function out(array $p, int $code=200): void {
  http_response_code($code);
  echo json_encode($p, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  exit;
}

function req_json(): array {
  $raw = file_get_contents('php://input') ?: '';
  $j = json_decode($raw, true);
  return is_array($j) ? $j : ($_POST ?: []);
}

function addr_ok(string $a): bool {
  if ($a === '') return false;
  if (preg_match('/^[0-9a-fA-F]{1,4}:[0-9a-fA-F]{64}$/', $a)) return true;
  if (preg_match('/^[UEk0][A-Za-z0-9_-]{30,120}$/', $a)) return true;
  return false;
}

function proof_payload(array $proof): string {
  foreach ([
    $proof['payload'] ?? null,
    $proof['proof']['payload'] ?? null,
    $proof['tonProof']['payload'] ?? null
  ] as $v) {
    if (is_string($v) && trim($v) !== '') return trim($v);
  }
  return '';
}

function proof_domain(array $proof): string {
  foreach ([
    $proof['domain']['value'] ?? null,
    $proof['proof']['domain']['value'] ?? null,
    $proof['tonProof']['domain']['value'] ?? null
  ] as $v) {
    if (is_string($v) && trim($v) !== '') return strtolower(trim($v));
  }
  return '';
}

try {
  $in = req_json();
  $addr = trim((string)($in['ton_address'] ?? ''));
  $proof = is_array($in['proof'] ?? null) ? $in['proof'] : [];

  if (!addr_ok($addr)) out(['ok'=>false,'error'=>'invalid_address'], 422);
  if (!$proof) out(['ok'=>false,'error'=>'missing_proof'], 422);

  $nonce = (string)($_SESSION['ev_ton_nonce'] ?? '');
  $exp = (int)($_SESSION['ev_ton_nonce_expires'] ?? 0);

  if ($nonce === '' || time() > $exp) out(['ok'=>false,'error'=>'missing_or_expired_nonce'], 422);
  if (!hash_equals($nonce, proof_payload($proof))) out(['ok'=>false,'error'=>'nonce_mismatch'], 422);

  $domain = proof_domain($proof);
  if ($domain !== '' && $domain !== 'expressvisa.one') out(['ok'=>false,'error'=>'domain_mismatch'], 422);

  $pdo = new PDO(
    'mysql:host=localhost;dbname=visa_ops_db;charset=utf8mb4',
    'oneexpressvisa',
    '$Express4653',
    [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]
  );

  $sid = session_id();
  $ip = $_SERVER['REMOTE_ADDR'] ?? '';
  $ua = substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255);

  $stmt = $pdo->prepare("
    INSERT INTO visa_ton_wallets (ton_address, session_id, last_ip, last_ua)
    VALUES (?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE
      session_id=VALUES(session_id),
      last_ip=VALUES(last_ip),
      last_ua=VALUES(last_ua),
      updated_at=NOW()
  ");
  $stmt->execute([$addr,$sid,$ip,$ua]);

  $_SESSION['ev_ton_address'] = $addr;
  unset($_SESSION['ev_ton_nonce'], $_SESSION['ev_ton_nonce_expires']);

  out([
    'ok'=>true,
    'message'=>'TON wallet connected.',
    'wallet_address'=>$addr,
    'ton_address'=>$addr
  ]);
} catch (Throwable $e) {
  out(['ok'=>false,'error'=>'verify_failed','message'=>$e->getMessage()], 500);
}


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
