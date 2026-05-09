<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../inc/rewards-engine.php';

$wallet = trim((string)($_POST['wallet'] ?? ev_active_wallet()));
$type = trim((string)($_POST['type'] ?? 'food_tasting'));
$amount = (float)($_POST['amount'] ?? 0);

$res = ev_redeem_vshare($wallet, $type, $amount, ['source'=>'public_redeem']);
$res['engine'] = 'Foodies Rewards Engine';
$res['wallet'] = $wallet;
$res['vShare'] = ev_vshare_balance($wallet);
echo json_encode($res, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
