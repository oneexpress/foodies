<?php
declare(strict_types=1);

function reward_credit(PDO $pdo, string $wallet, float $vshare, string $eventRef): array {

    $wallet = trim($wallet);

    if ($wallet === '') {
        return ['ok'=>false,'error'=>'wallet_empty'];
    }

    $check = $pdo->prepare("
        SELECT id
        FROM ev_reward_ledger
        WHERE event_ref=?
        LIMIT 1
    ");

    $check->execute([$eventRef]);

    if ($check->fetch()) {
        return ['ok'=>true,'duplicate'=>true];
    }

    $pdo->beginTransaction();

    try {

        $st = $pdo->prepare("
            INSERT INTO ev_reward_ledger
            (
                wallet,
                event_ref,
                event_type,
                score_delta,
                vshare_delta,
                memo
            )
            VALUES (?,?,?,?,?,?)
        ");

        $st->execute([
            $wallet,
            $eventRef,
            'digging',
            1,
            $vshare,
            '991 digging reward'
        ]);

        $st = $pdo->prepare("
            INSERT INTO ev_wallet_ledger
            (
                wallet_address,
                token,
                type,
                amount,
                meta_json
            )
            VALUES (?,?,?,?,?)
        ");

        $st->execute([
            $wallet,
            'vSHARE',
            'digging_reward',
            $vshare,
            json_encode([
                'event_ref'=>$eventRef,
                'source'=>'991-digging'
            ], JSON_UNESCAPED_UNICODE)
        ]);

        $st = $pdo->prepare("
            INSERT INTO ev_rewards_claims
            (
                wallet_address,
                token,
                amount,
                proof_nonce
            )
            VALUES (?,?,?,?)
        ");

        $st->execute([
            $wallet,
            'vSHARE',
            $vshare,
            $eventRef
        ]);

        $st = $pdo->prepare("
            INSERT INTO ev_reward_profiles
            (
                wallet,
                total_score,
                total_vshare
            )
            VALUES (?,1,?)
            ON DUPLICATE KEY UPDATE
                total_score = total_score + 1,
                total_vshare = total_vshare + VALUES(total_vshare)
        ");

        $st->execute([
            $wallet,
            $vshare
        ]);

        $pdo->commit();

        return [
            'ok'=>true,
            'credited'=>true
        ];

    } catch(Throwable $e){

        if($pdo->inTransaction()){
            $pdo->rollBack();
        }

        return [
            'ok'=>false,
            'error'=>$e->getMessage()
        ];
    }
}

<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
