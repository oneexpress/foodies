<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__.'/_bootstrap.php';
try {
    $pdo=ev991_pdo(); ev991_revenue_bootstrap($pdo);
    $amount=(float)($_POST['amount'] ?? $_GET['amount'] ?? 0);
    if ($amount <= 0) throw new RuntimeException('amount required');

    $source=trim((string)($_POST['source'] ?? $_GET['source'] ?? 'manual'));
    $ref=trim((string)($_POST['ref'] ?? $_GET['ref'] ?? ''));
    $currency=trim((string)($_POST['currency'] ?? $_GET['currency'] ?? 'vUSDT'));

    $platformRate=0.25; $poolRate=0.15;
    $platformShare=$amount*$platformRate;
    $rewardPool=$platformShare*$poolRate;
    $uid=ev991_rev_uid();

    $st=$pdo->prepare("INSERT INTO ev991_revenue_events
        (event_uid,source_type,source_ref,gross_amount,platform_rate,pool_rate,platform_share,reward_pool_amount,currency,meta_json)
        VALUES (?,?,?,?,?,?,?,?,?,?)");
    $st->execute([$uid,$source,$ref,$amount,$platformRate,$poolRate,$platformShare,$rewardPool,$currency,json_encode([
        'ip'=>$_SERVER['REMOTE_ADDR'] ?? '',
        'ua'=>$_SERVER['HTTP_USER_AGENT'] ?? ''
    ], JSON_UNESCAPED_UNICODE)]);

    echo json_encode([
        'ok'=>true,
        'event_uid'=>$uid,
        'gross_amount'=>round($amount,8),
        'platform_share'=>round($platformShare,8),
        'reward_pool_amount'=>round($rewardPool,8),
        'formula'=>'gross × 0.25 × 0.15'
    ], JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
} catch(Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok'=>false,'error'=>$e->getMessage()], JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
}


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
