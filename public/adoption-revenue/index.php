<?php
declare(strict_types=1);
?>
<!doctype html><html lang="en"><head>
<meta charset="utf-8"><title>Adoption Revenue Dashboard</title>
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<style>
*{box-sizing:border-box}body{margin:0;background:radial-gradient(circle at top left,rgba(255,135,0,.24),transparent 36%),#030406;color:#fff;font-family:Inter,Arial,sans-serif;padding:18px 14px 130px}.wrap{max-width:980px;margin:auto}.hero,.card{border:1px solid rgba(255,255,255,.13);border-radius:28px;background:linear-gradient(135deg,rgba(255,135,0,.18),rgba(255,255,255,.055));padding:22px;margin-bottom:14px;box-shadow:0 20px 58px rgba(0,0,0,.38)}h1{margin:0;font-size:30px}.muted{color:#aab;font-weight:800}.grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}.label{font-size:12px;color:#aab;font-weight:950;text-transform:uppercase}.value{font-size:34px;color:#fff2b8;font-weight:950;margin-top:8px}.btn{display:inline-block;border:0;border-radius:999px;padding:12px 16px;background:#ff8700;color:#241000;font-weight:950;text-decoration:none;cursor:pointer;margin:6px 6px 0 0}pre{white-space:pre-wrap;overflow:auto;background:rgba(0,0,0,.36);padding:14px;border-radius:16px;color:#fff2b8}@media(max-width:760px){.grid{grid-template-columns:1fr}h1{font-size:24px}}
</style></head><body><main class="wrap">
<section class="hero"><h1>Adoption Revenue Dashboard</h1><p class="muted">Revenue pool · distribution · marketplace adoption engine</p><a class="btn" href="/wallet/">Wallet</a><a class="btn" href="/rewards/">Rewards</a><a class="btn" href="/foodies-nft/">Foodies NFT</a><button class="btn" onclick="loadDash()">Refresh</button></section>
<section class="grid">
<div class="card"><div class="label">Reward Pool</div><div class="value" id="poolTotal">0</div></div>
<div class="card"><div class="label">Available</div><div class="value" id="poolAvailable">0</div></div>
<div class="card"><div class="label">Distributed</div><div class="value" id="distributed">0</div></div>
<div class="card"><div class="label">My Revenue</div><div class="value" id="mine">0</div></div>
<div class="card"><div class="label">NFT Weight</div><div class="value" id="weight">0</div></div>
<div class="card"><div class="label">Engine</div><div class="value">v2</div></div>
</section>
<section class="card"><div class="label">Live API Snapshot</div><pre id="out">Loading...</pre></section>
</main>
<script>
async function loadDash(){
 const pool=await fetch('/api/revenue/pool.php',{credentials:'same-origin'}).then(r=>r.json()).catch(e=>({ok:false,error:e.message}));
 const weight=await fetch('/api/foodies-nft/weight.php',{credentials:'same-origin'}).then(r=>r.json()).catch(e=>({ok:false,error:e.message}));
 if(pool.ok){poolTotal.textContent=pool.reward_pool_total||0;poolAvailable.textContent=pool.available_pool||0;distributed.textContent=pool.distributed_total||0;mine.textContent=pool.my_revenue_earned||0;}
 if(weight.ok){document.getElementById('weight').textContent=weight.total_weight||0;}
 out.textContent=JSON.stringify({pool,weight},null,2);
}
loadDash();
</script>
<script src="/assets/js/991-wallet-sync.js?v=991-unified"></script>
<script src="/assets/991-balance-sync.js?v=991-balance-sync-ux-v1" defer></script>
<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
</body></html>
