<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__.'/_bootstrap.php';

try {
    $pdo=ev991_pdo(); ev991_revenue_bootstrap($pdo);

    $wallet=ev991_current_wallet();
    if ($wallet === '') throw new RuntimeException('wallet required');

    $eventUid=trim((string)($_POST['event_uid'] ?? $_GET['event_uid'] ?? ''));
    if ($eventUid === '') {
        $eventUid=(string)$pdo->query("SELECT event_uid FROM ev991_revenue_events WHERE status='confirmed' ORDER BY id DESC LIMIT 1")->fetchColumn();
    }
    if ($eventUid === '') throw new RuntimeException('no revenue event');

    $st=$pdo->prepare("SELECT reward_pool_amount FROM ev991_revenue_events WHERE event_uid=? AND status='confirmed'");
    $st->execute([$eventUid]);
    $pool=(float)$st->fetchColumn();
    if ($pool <= 0) throw new RuntimeException('event pool empty');

    $weight=max(1, (float)($_POST['weight'] ?? $_GET['weight'] ?? 1));
    $share=min($pool, $pool * min($weight,100) / 100);

    $ins=$pdo->prepare("INSERT IGNORE INTO ev991_revenue_distributions (wallet,event_uid,share_weight,share_amount) VALUES (?,?,?,?)");
    $ins->execute([$wallet,$eventUid,$weight,$share]);

    echo json_encode(['ok'=>true,'event_uid'=>$eventUid,'wallet'=>$wallet,'share_weight'=>$weight,'share_amount'=>round($share,8)], JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
} catch(Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok'=>false,'error'=>$e->getMessage()], JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
}


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
