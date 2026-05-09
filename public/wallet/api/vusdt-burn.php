<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__.'/../../inc/vusdt-treasury.php';

$wallet = $_POST['wallet'] ?? '';
$amount = (float)($_POST['amount'] ?? 0);
$ref    = $_POST['ref'] ?? '';

if (!$wallet || $amount <= 0) {
    echo json_encode(['ok'=>false,'error'=>'invalid_input']); exit;
}

$pdo = ev_vusdt_pdo();
$pdo->beginTransaction();

try {

    // 1. Check balance
    $bal = (float)$pdo->query("
        SELECT COALESCE(SUM(
            CASE WHEN type='credit' THEN amount ELSE -amount END
        ),0) FROM ev_wallet_ledger
        WHERE wallet = ".$pdo->quote($wallet)
    )->fetchColumn();

    if ($bal < $amount) {
        throw new Exception('insufficient_balance');
    }

    // 2. Burn event
    ev_vusdt_audit_event(
        'burn',
        $amount,
        0,
        $wallet,
        '',
        $ref,
        ['reason'=>'spend']
    );

    // 3. Debit wallet
    $stmt = $pdo->prepare("
        INSERT INTO ev_wallet_ledger
        (wallet, type, amount, ref, meta_json)
        VALUES (?, 'debit', ?, ?, ?)
    ");
    $stmt->execute([
        $wallet,
        number_format($amount,6,'.',''),
        $ref ?: 'BURN',
        json_encode(['type'=>'burn'])
    ]);

    $pdo->commit();

    echo json_encode([
        'ok'=>true,
        'burned'=>$amount,
        'wallet'=>$wallet
    ]);

} catch(Throwable $e) {
    $pdo->rollBack();
    echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
}


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
