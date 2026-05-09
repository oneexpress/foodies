<?php
require_once __DIR__ . '/../_db.php';
$pdo = ev_pdo();
$wallet = ev_wallet();
$st = $pdo->prepare("SELECT event_type,score_delta,vshare_delta,vusdt_delta,memo,created_at FROM ev_reward_ledger WHERE wallet=? ORDER BY id DESC LIMIT 30");
$st->execute([$wallet]);
ev_json(['ok'=>true,'rows'=>$st->fetchAll()]);


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
