<?php
declare(strict_types=1);
function calc_boost(float $v): float { return min(30.0, 1 + ($v * 0.003)); }
$amount = isset($_GET['amount']) ? (float)$_GET['amount'] : 10000;
$boost = calc_boost($amount);
$rate = 0.33 * $boost;
?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>vShare Boost Calculator</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
body{margin:0;font-family:system-ui;background:#fff1f2;color:#111827}.wrap{max-width:760px;margin:auto;padding:24px 16px 100px}
.card{background:#fff;border:1px solid #fecdd3;border-radius:28px;padding:24px;box-shadow:0 18px 45px rgba(230,0,18,.12)}
h1{color:#E60012}.big{font-size:36px;font-weight:1000;color:#E60012}input{width:100%;padding:14px;border:1px solid #fecdd3;border-radius:16px}button{margin-top:10px;border:0;border-radius:999px;padding:12px 16px;background:#E60012;color:#fff;font-weight:900}
</style>
</head>
<body><main class="wrap"><section class="card">
<h1>vShare Boost Calculator</h1>
<p>Locked rule: <b>1 vUSDT = +0.003 multiplier</b>, capped at <b>30x</b>.</p>
<form>
<input name="amount" value="<?=htmlspecialchars((string)$amount)?>" inputmode="decimal">
<button>Calculate</button>
</form>
<p class="big"><?=number_format($boost,4)?>x</p>
<p>Digging rate: <b><?=number_format($rate,4)?> vShare / 10s</b></p>
<script src="/assets/js/991-wallet-sync.js?v=991-unified"></script>
<script src="/assets/991-balance-sync.js?v=991-balance-sync-ux-v1" defer></script>
<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
</body></html>
