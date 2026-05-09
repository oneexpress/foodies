<?php
declare(strict_types=1);
require_once __DIR__ . '/rewards-engine.php';

function ev_rewards_after_vusdt_payment(string $wallet, string $ref, float $paidAmount = 0.0, array $meta = []): void {
    $wallet = trim($wallet);
    $ref = trim($ref);
    if ($wallet === '' || $ref === '') return;

    // Public rule: vUSDT purchase boosts Foodies Rewards Engine.
    // Default: 2 vShare + 1% of paid amount as participation boost.
    $base = 2.0000;
    $bonus = max(0.0, $paidAmount * 0.01);
    $vshare = $base + $bonus;

    ev_reward_vshare($wallet, $vshare, 'vusdt_payment_activity', $ref, array_merge($meta, [
        'engine' => 'Foodies Rewards Engine',
        'display' => 'vUSDT Purchase → vShare'
    ]));
}


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
