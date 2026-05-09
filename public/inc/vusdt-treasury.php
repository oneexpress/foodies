<?php
declare(strict_types=1);

function ev_vusdt_pdo(): PDO {
    return new PDO(
        'mysql:host=localhost;dbname=visa_db;charset=utf8mb4',
        'root',
        '',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
}

function ev_vusdt_audit_summary(): array {
    $pdo = ev_vusdt_pdo();

    $q = fn($sql) => (float)$pdo->query($sql)->fetchColumn();

    $initial = $q("SELECT COALESCE(SUM(amount_vusdt),0) FROM ev_vusdt_treasury_audit WHERE event_type='initial_supply'");
    $backed  = $q("SELECT COALESCE(SUM(amount_usdt_ton),0) FROM ev_vusdt_treasury_audit WHERE event_type='backing_deposit'");
    $minted  = $q("SELECT COALESCE(SUM(amount_vusdt),0) FROM ev_vusdt_treasury_audit WHERE event_type='mint'");
    $burned  = $q("SELECT COALESCE(SUM(amount_vusdt),0) FROM ev_vusdt_treasury_audit WHERE event_type='burn'");

    $circulating = max(0, $minted - $burned);
    $reserve = max(0, $initial + $minted - $burned - $circulating);
    $backingGap = $backed - $circulating;

    return [
        'initial_supply_vusdt' => number_format($initial, 6, '.', ''),
        'backed_usdt_ton' => number_format($backed, 6, '.', ''),
        'minted_vusdt' => number_format($minted, 6, '.', ''),
        'burned_vusdt' => number_format($burned, 6, '.', ''),
        'circulating_vusdt' => number_format($circulating, 6, '.', ''),
        'treasury_reserve_vusdt' => number_format($reserve, 6, '.', ''),
        'backing_gap_usdt_ton_minus_circulating' => number_format($backingGap, 6, '.', ''),
        'is_fully_backed' => $backingGap >= 0,
        'peg_rule' => '1 vUSDT = 1 USDT-TON reference peg',
        'supply_rule' => 'Initial 10,000,000 vUSDT, mintable, no max supply; circulating supply must be backed 1:1.'
    ];
}

function ev_vusdt_audit_event(string $event, float $vusdt, float $usdtTon=0.0, string $wallet='', string $tx='', string $ref='', array $meta=[]): bool {
    $pdo = ev_vusdt_pdo();
    $st = $pdo->prepare("
        INSERT IGNORE INTO ev_vusdt_treasury_audit
        (event_type,wallet,amount_vusdt,amount_usdt_ton,tx_hash,ref,meta_json)
        VALUES (?,?,?,?,?,?,?)
    ");
    return $st->execute([
        $event,
        $wallet ?: null,
        number_format($vusdt,6,'.',''),
        number_format($usdtTon,6,'.',''),
        $tx ?: null,
        $ref ?: null,
        $meta ? json_encode($meta, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) : null
    ]);
}


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
