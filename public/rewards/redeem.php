<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/rewards-engine.php';
$wallet = ev_active_wallet();
$vshare = ev_vshare_balance($wallet);
?><!doctype html>
<html><head><meta charset="utf-8"><title>Redeem Rewards</title><meta name="viewport" content="width=device-width,initial-scale=1">
<style>
body{margin:0;font-family:system-ui;background:#fff1f2;color:#111827}.wrap{max-width:760px;margin:auto;padding:24px 16px 100px}.card{background:#fff;border:1px solid #fecdd3;border-radius:24px;padding:22px;box-shadow:0 18px 45px rgba(230,0,18,.12)}h1{color:#E60012}.v{font-size:30px;font-weight:1000;color:#E60012}.row{display:grid;gap:10px}button{border:0;border-radius:999px;padding:13px 16px;background:#E60012;color:#fff;font-weight:900}select,input{width:100%;padding:12px;border-radius:14px;border:1px solid #fecdd3}
</style></head>
<body><main class="wrap"><section class="card">
<h1>Redeem Rewards</h1>
<div class="v">vShare: <?= htmlspecialchars($vshare) ?></div>
<p><b>Wallet:</b> <?= $wallet ? htmlspecialchars($wallet) : 'Not connected' ?></p>
<form class="row" onsubmit="redeem(event)">
<input name="wallet" value="<?= htmlspecialchars($wallet) ?>" placeholder="Wallet">
<select name="type">
<option value="food_tasting">Food Tasting Meal Set</option>
<option value="foodies_rwa_nft">Foodies RWA NFT</option>
<option value="participation_boost">Participation Boost</option>
</select>
<input name="amount" value="10.0000" inputmode="decimal">
<button>Redeem Rewards</button>
</form>
<pre id="out"></pre>
</section></main>
<script>
async function redeem(e){
 e.preventDefault();
 const fd=new FormData(e.target);
 const r=await fetch('/rewards/api/redeem.php',{method:'POST',body:fd});
 document.getElementById('out').textContent=JSON.stringify(await r.json(),null,2);
}
</script>
<script src="/assets/js/991-wallet-sync.js?v=991-unified"></script>
<script src="/assets/991-balance-sync.js?v=991-balance-sync-ux-v1" defer></script>
<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
</body></html>
