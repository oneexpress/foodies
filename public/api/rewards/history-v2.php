<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../inc/ev991-db.php';

try {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $pdo = ev991_pdo();
    $wallet = trim((string)($_SESSION['ton_wallet'] ?? $_SESSION['wallet'] ?? $_GET['wallet'] ?? ''));

    if ($wallet === '') {
        echo json_encode(['ok'=>true,'wallet'=>'','items'=>[]], JSON_PRETTY_PRINT);
        exit;
    }

    $st = $pdo->prepare("SELECT reward_amount,nft_weight,proof_hash,status,created_at FROM ev991_reward_claims WHERE wallet=? ORDER BY id DESC LIMIT 20");
    $st->execute([$wallet]);

    echo json_encode(['ok'=>true,'wallet'=>$wallet,'items'=>$st->fetchAll()], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (Throwable $e) {
    echo json_encode(['ok'=>false,'error'=>$e->getMessage()], JSON_PRETTY_PRINT);
}


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
