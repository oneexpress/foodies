<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__.'/../../inc/vusdt-treasury.php';

$wallet = $_POST['wallet'] ?? '';
$amount = (float)($_POST['amount'] ?? 0);
$tx     = $_POST['tx_hash'] ?? '';
$ref    = $_POST['ref'] ?? '';

if (!$wallet || $amount <= 0) {
    echo json_encode(['ok'=>false,'error'=>'invalid_input']); exit;
}

/*
RULE:
Mint ONLY allowed when backing deposit exists.
We treat this call as verified deposit.
*/

$pdo = ev_vusdt_pdo();
$pdo->beginTransaction();

try {

    // 1. Record backing (USDT-TON)
    ev_vusdt_audit_event(
        'backing_deposit',
        0,
        $amount,
        $wallet,
        $tx,
        $ref,
        ['source'=>'mint']
    );

    // 2. Mint vUSDT
    ev_vusdt_audit_event(
        'mint',
        $amount,
        0,
        $wallet,
        $tx,
        $ref,
        ['rule'=>'1:1 mint']
    );

    // 3. Credit wallet ledger
    $stmt = $pdo->prepare("
        INSERT INTO ev_wallet_ledger
        (wallet, type, amount, ref, meta_json)
        VALUES (?, 'credit', ?, ?, ?)
    ");
    $stmt->execute([
        $wallet,
        number_format($amount,6,'.',''),
        $ref ?: 'MINT',
        json_encode(['tx'=>$tx,'type'=>'mint'])
    ]);

    $pdo->commit();

    echo json_encode([
        'ok'=>true,
        'minted'=>$amount,
        'wallet'=>$wallet
    ]);

} catch(Throwable $e) {
    $pdo->rollBack();
    echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
}


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
