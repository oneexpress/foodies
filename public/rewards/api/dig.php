<?php
declare(strict_types=1);
require_once __DIR__ . '/../_db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ev_json(['ok'=>false,'error'=>'POST_REQUIRED'],405);
}

$pdo = ev_pdo();
$wallet = ev_wallet();
$today = date('Y-m-d');

$raw = file_get_contents('php://input');
$body = json_decode(is_string($raw) ? $raw : '', true);
if (!is_array($body)) $body = [];

$boostVusdt = max(0.0, (float)($body['boost_vusdt'] ?? 0));
$multiplier = 1 + ($boostVusdt * 0.003);
$basePerCollect = 0.003;
$vshare = $basePerCollect * $multiplier;
if ($vshare <= 0) $vshare = $basePerCollect;

$pdo->prepare("INSERT IGNORE INTO ev_reward_profiles(wallet) VALUES(?)")->execute([$wallet]);

$pdo->prepare("INSERT IGNORE INTO ev_reward_daily_actions(wallet,action_date,action_key,action_count) VALUES(?,?, 'daily_collect',0)")
    ->execute([$wallet,$today]);

$earnedTodayStmt = $pdo->prepare("
    SELECT COALESCE(SUM(vshare_delta),0)
    FROM ev_reward_ledger
    WHERE wallet=? AND DATE(created_at)=? AND event_type IN ('daily_dig','daily_collect')
");
$earnedTodayStmt->execute([$wallet,$today]);
$earnedToday = (float)$earnedTodayStmt->fetchColumn();

$remaining = max(0.0, 10.0 - $earnedToday);
if ($remaining <= 0) {
    ev_json(['ok'=>false,'error'=>'DAILY_CAP_REACHED']);
}

$vshare = min($vshare, $remaining);
$score = 1.0;
$ref = 'REWARD-' . date('YmdHis') . '-' . bin2hex(random_bytes(4));

$pdo->beginTransaction();
$pdo->prepare("UPDATE ev_reward_daily_actions SET action_count=action_count+1 WHERE wallet=? AND action_date=? AND action_key='daily_collect'")
    ->execute([$wallet,$today]);

$pdo->prepare("
    INSERT INTO ev_reward_ledger(wallet,event_ref,event_type,score_delta,vshare_delta,vusdt_delta,memo)
    VALUES(?,?,?,?,?,?,?)
")->execute([
    $wallet,
    $ref,
    'daily_collect',
    $score,
    number_format($vshare, 6, '.', ''),
    '0.000000',
    'Collected from Digging Reward Engine'
]);

$pdo->prepare("
    UPDATE ev_reward_profiles
    SET total_score=total_score+?,
        total_vshare=total_vshare+?,
        last_action_at=NOW()
    WHERE wallet=?
")->execute([
    $score,
    number_format($vshare, 6, '.', ''),
    $wallet
]);

$pdo->commit();

ev_json([
    'ok'=>true,
    'message'=>'Digging reward collected',
    'event_ref'=>$ref,
    'vshare_delta'=>number_format($vshare, 6, '.', ''),
    'multiplier'=>number_format($multiplier, 6, '.', ''),
    'daily_remaining'=>number_format(max(0, $remaining - $vshare), 6, '.', '')
]);


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
