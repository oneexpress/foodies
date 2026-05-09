<?php
declare(strict_types=1);

require_once __DIR__ . '/../public/inc/vusdt-treasury.php';
require_once __DIR__ . '/../public/inc/expressvisa-ton-env.php';

$pdo = ev_vusdt_pdo();

$usdtMaster = ev_ton_env('USDT_TON_MASTER');
$treasury   = ev_ton_env('VUSDT_TREASURY_TON_WALLET');
$apiKey     = ev_ton_env('TONCENTER_API_KEY');

function fail_deposit(PDO $pdo, int $id, string $reason): void {
    $st = $pdo->prepare("UPDATE ev_vusdt_ton_deposits SET status='rejected', reject_reason=? WHERE id=? AND status='pending'");
    $st->execute([$reason, $id]);
}

function mark_verified(PDO $pdo, int $id): void {
    $st = $pdo->prepare("UPDATE ev_vusdt_ton_deposits SET status='verified', verified_at=NOW() WHERE id=? AND status='pending'");
    $st->execute([$id]);
}

function mint_deposit(PDO $pdo, array $d): void {
    $wallet = (string)$d['user_wallet'];
    $amount = (float)$d['amount_usdt_ton'];
    $tx = (string)$d['tx_hash'];
    $ref = (string)($d['payload_ref'] ?: 'TON-USDT-DEPOSIT');

    $pdo->beginTransaction();
    try {
        ev_vusdt_audit_event('backing_deposit', 0, $amount, $wallet, $tx, $ref, [
            'source'=>'ton_auto_verify',
            'jetton'=>'USDT-TON'
        ]);

        ev_vusdt_audit_event('mint', $amount, 0, $wallet, $tx, $ref, [
            'rule'=>'1 vUSDT = 1 USDT-TON',
            'source'=>'ton_auto_mint'
        ]);

        $st = $pdo->prepare("
          INSERT INTO ev_wallet_ledger (wallet,type,amount,ref,meta_json)
          VALUES (?, 'credit', ?, ?, ?)
        ");
        $st->execute([
            $wallet,
            number_format($amount,6,'.',''),
            $ref,
            json_encode(['tx'=>$tx,'type'=>'ton_usdt_auto_mint'], JSON_UNESCAPED_SLASHES)
        ]);

        $up = $pdo->prepare("UPDATE ev_vusdt_ton_deposits SET status='minted', minted_at=NOW() WHERE id=?");
        $up->execute([(int)$d['id']]);

        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}

/*
Production verification rule:
- jetton master must equal USDT_TON_MASTER
- destination/treasury wallet should match VUSDT_TREASURY_TON_WALLET when configured
- exact amount from chain must match pending amount
- tx_hash cannot be reused due UNIQUE(tx_hash)

This scaffold currently requires config before live verification.
If config is missing, it will not mint.
*/

$rows = $pdo->query("SELECT * FROM ev_vusdt_ton_deposits WHERE status='pending' ORDER BY id ASC LIMIT 20")->fetchAll();

$result = ['ok'=>true,'checked'=>0,'minted'=>0,'rejected'=>0,'blocked'=>false];

if ($usdtMaster === '' || $treasury === '' || $apiKey === '') {
    $result['blocked'] = true;
    $result['error'] = 'Missing USDT_TON_MASTER / VUSDT_TREASURY_TON_WALLET / TONCENTER_API_KEY in /var/www/secure/.env';
    echo json_encode($result, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
    exit;
}

foreach ($rows as $d) {
    $result['checked']++;

    /*
    TODO live chain query:
    - call Toncenter /api/v3 transactions by hash/account
    - parse jetton_transfer
    - confirm sender, receiver, jetton_master, amount

    Until real parser is added, only allow explicit SIM tx for staging.
    */
    if (!str_starts_with((string)$d['tx_hash'], 'SIM-')) {
        fail_deposit($pdo, (int)$d['id'], 'LIVE_TON_PARSER_NOT_ENABLED');
        $result['rejected']++;
        continue;
    }

    mark_verified($pdo, (int)$d['id']);
    $d['status'] = 'verified';
    mint_deposit($pdo, $d);
    $result['minted']++;
}

echo json_encode($result, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
