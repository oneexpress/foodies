<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__.'/_bootstrap.php';
try {
    $pdo=ev991_pdo(); ev991_revenue_bootstrap($pdo);
    $wallet=ev991_current_wallet();

    $pool=(float)$pdo->query("SELECT COALESCE(SUM(reward_pool_amount),0) FROM ev991_revenue_events WHERE status='confirmed'")->fetchColumn();
    $distributed=(float)$pdo->query("SELECT COALESCE(SUM(share_amount),0) FROM ev991_revenue_distributions WHERE status='credited'")->fetchColumn();

    $mine=0.0;
    if ($wallet !== '') {
        $st=$pdo->prepare("SELECT COALESCE(SUM(share_amount),0) FROM ev991_revenue_distributions WHERE wallet=? AND status='credited'");
        $st->execute([$wallet]);
        $mine=(float)$st->fetchColumn();
    }

    echo json_encode([
        'ok'=>true,
        'version'=>'revenue-v2',
        'formula'=>'reward_pool = total_revenue × 0.25 × 0.15',
        'reward_pool_total'=>round($pool,8),
        'distributed_total'=>round($distributed,8),
        'available_pool'=>round(max(0,$pool-$distributed),8),
        'my_revenue_earned'=>round($mine,8),
        'wallet'=>$wallet,
        'db'=>ev991_env('DB_DATABASE')
    ], JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
} catch(Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok'=>false,'error'=>$e->getMessage()], JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
}


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
