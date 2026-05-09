<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__.'/_bootstrap.php';
try {
    $pdo=ev991_pdo(); ev991_revenue_bootstrap($pdo);
    $events=$pdo->query("SELECT event_uid,source_type,source_ref,gross_amount,reward_pool_amount,currency,status,created_at FROM ev991_revenue_events ORDER BY id DESC LIMIT 20")->fetchAll();
    echo json_encode(['ok'=>true,'db'=>ev991_env('DB_DATABASE'),'events'=>$events], JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
} catch(Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok'=>false,'error'=>$e->getMessage()], JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
}


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
