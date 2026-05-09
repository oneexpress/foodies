<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../inc/rewards-engine.php';

$wallet = trim((string)($_POST['wallet'] ?? $_GET['wallet'] ?? ev_active_wallet()));
$type = trim((string)($_POST['type'] ?? $_GET['type'] ?? 'manual_boost'));
$multiplier = (float)($_POST['multiplier'] ?? $_GET['multiplier'] ?? 1.2);
$hours = (int)($_POST['hours'] ?? $_GET['hours'] ?? 24);
$ref = trim((string)($_POST['ref'] ?? $_GET['ref'] ?? ''));

if ($wallet === '') {
    echo json_encode(['ok'=>false,'error'=>'WALLET_REQUIRED']); exit;
}

$ok = ev_rewards_add_boost($wallet, $type, $multiplier, $ref, $hours, [
    'engine'=>'Foodies Rewards Engine',
    'label'=>'Participation Boost'
]);

echo json_encode([
    'ok'=>$ok,
    'wallet'=>$wallet,
    'type'=>$type,
    'multiplier'=>number_format($multiplier,4,'.',''),
    'hours'=>$hours,
    'vShare'=>ev_vshare_balance($wallet)
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
