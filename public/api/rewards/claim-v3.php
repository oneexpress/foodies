<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../revenue/_bootstrap.php';

try {
    $pdo = ev991_pdo();
    ev991_revenue_bootstrap($pdo);

    $wallet = ev991_current_wallet();
    if ($wallet === '') throw new RuntimeException('Wallet session required');

    $pdo->beginTransaction();

    $state = $pdo->prepare("SELECT * FROM ev991_user_state WHERE wallet=? FOR UPDATE");
    $state->execute([$wallet]);
    $row = $state->fetch();

    if (!$row) {
        $ins = $pdo->prepare("INSERT INTO ev991_user_state (wallet,last_claim_at) VALUES (?,NULL)");
        $ins->execute([$wallet]);
        $row = ['last_claim_at'=>null,'total_earned'=>0,'activity_score'=>0];
    }

    if (!empty($row['last_claim_at']) && (time() - strtotime((string)$row['last_claim_at'])) < 10) {
        $pdo->rollBack();
        echo json_encode(['ok'=>false,'error'=>'Anti-bot throttle: wait 10 seconds'], JSON_PRETTY_PRINT);
        exit;
    }

    $nftWeight = 0;
    $wjson = @file_get_contents('https://expressvisa.one/api/foodies-nft/weight.php?wallet=' . rawurlencode($wallet));
    $wdata = json_decode($wjson ?: '{}', true);
    $nftWeight = (int)($wdata['total_weight'] ?? 0);

    $today = $pdo->prepare("SELECT COALESCE(SUM(reward_amount),0) FROM ev991_reward_claims WHERE wallet=? AND DATE(created_at)=UTC_DATE()");
    $today->execute([$wallet]);
    $earnedToday = (float)$today->fetchColumn();

    $dailyCap = 10.0;
    if ($earnedToday >= $dailyCap) {
        $pdo->rollBack();
        echo json_encode(['ok'=>false,'error'=>'Daily cap reached','earned_today'=>$earnedToday,'daily_cap'=>$dailyCap], JSON_PRETTY_PRINT);
        exit;
    }

    $vusdt = max(0.0, (float)($_POST['vusdt'] ?? $_GET['vusdt'] ?? 0));
    $base = 0.003;
    $boost = $vusdt * 0.003;
    $factor = $nftWeight * 0.01;
    $reward = ($base + $boost) * (1 + $factor);

    if (($earnedToday + $reward) > $dailyCap) {
        $reward = max(0, $dailyCap - $earnedToday);
    }

    $secret = ev991_env(['APP_SECRET','EV991_SECRET'], 'ev991-local-secret');
    $ts = time();
    $sessionHash = hash('sha256', session_id() . '|' . $wallet);
    $ipHash = hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? '') . '|991');
    $proof = hash('sha256', $wallet . '|' . $ts . '|' . $reward . '|' . $secret);

    $st = $pdo->prepare("
        INSERT INTO ev991_reward_claims
        (wallet,reward_amount,base_reward,vusdt_boost,nft_weight,nft_factor,proof_hash,session_hash,ip_hash)
        VALUES (?,?,?,?,?,?,?,?,?)
    ");
    $st->execute([$wallet,$reward,$base,$boost,$nftWeight,$factor,$proof,$sessionHash,$ipHash]);

    $up = $pdo->prepare("
        UPDATE ev991_user_state
        SET last_claim_at=UTC_TIMESTAMP(),
            total_earned=total_earned+?,
            nft_weight_snapshot=?,
            activity_score=activity_score+1
        WHERE wallet=?
    ");
    $up->execute([$reward,$nftWeight,$wallet]);

    $pdo->commit();

    echo json_encode([
        'ok'=>true,
        'version'=>'Anti-bot Proof v3',
        'reward_amount'=>round($reward,8),
        'earned_today'=>round($earnedToday + $reward,8),
        'daily_cap'=>$dailyCap,
        'nft_weight'=>$nftWeight,
        'nft_factor'=>$factor,
        'proof_hash'=>$proof,
        'next_claim_seconds'=>10
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (Throwable $e) {
    if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['ok'=>false,'error'=>$e->getMessage()], JSON_PRETTY_PRINT);
}


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
