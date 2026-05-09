<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../inc/rewards-engine.php';

$wallet = trim((string)($_POST['wallet'] ?? ev_active_wallet()));
$amount = (float)($_POST['amount'] ?? 0);
$action = trim((string)($_POST['action'] ?? 'manual_activity'));
$ref = trim((string)($_POST['ref'] ?? ''));

$ok = ev_reward_vshare($wallet, $amount, $action, $ref ?: null, ['source'=>'api']);
echo json_encode([
  'ok' => $ok,
  'engine' => 'Foodies Rewards Engine',
  'wallet' => $wallet,
  'vShare' => ev_vshare_balance($wallet)
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
