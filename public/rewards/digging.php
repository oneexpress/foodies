<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/rewards-engine.php';
$wallet = ev_active_wallet();
$vshare = ev_vshare_balance($wallet);
?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Start Digging | Foodies Rewards Engine</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
body{margin:0;font-family:system-ui;background:linear-gradient(135deg,#fff,#fff1f2);color:#111827}
.wrap{max-width:820px;margin:auto;padding:24px 16px 100px}
.card{background:#fff;border:1px solid #fecdd3;border-radius:28px;padding:24px;box-shadow:0 18px 45px rgba(230,0,18,.12)}
.logo{width:72px;height:72px;object-fit:contain;border-radius:18px;border:1px solid #fecdd3;padding:8px;background:#fff}
h1{font-size:42px;line-height:1;margin:14px 0;color:#E60012}
.v{font-size:34px;font-weight:1000;color:#E60012;margin:14px 0}
.btn{border:0;border-radius:999px;padding:14px 18px;font-weight:1000;cursor:pointer;text-decoration:none;display:inline-flex}
.start{background:#E60012;color:#fff}.stop{background:#111827;color:#fff}.ghost{background:#fff;color:#E60012;border:1px solid #fecdd3}
.row{display:flex;flex-wrap:wrap;gap:10px;margin-top:14px}
.small{color:#6b7280;font-size:13px;overflow-wrap:anywhere}
.pulse,.stats{margin-top:14px;padding:14px;border-radius:18px;background:#fff1f2;border:1px dashed #fecdd3}
.stats{display:grid;grid-template-columns:repeat(2,1fr);gap:10px;background:#fff;border-style:solid}
.stat b{display:block;color:#E60012;font-size:18px}
@media(max-width:640px){.stats{grid-template-columns:1fr}.btn{width:100%;justify-content:center}}
</style>
</head>
<body>
<main class="wrap">
<section class="card">
<img class="logo" src="/metadata/991_vshare_logo.png" alt="vShare">
<h1>Start Digging</h1>
<p>Foodies Rewards Engine starts at <b>0.3300 vShare every 10 seconds</b>. Participation Boost uses the locked rule: 1 vUSDT = +0.003 multiplier, capped at 30x. Daily streak may also increase the live rate.</p>

<div class="v" id="vshare">vShare: <?= htmlspecialchars($vshare) ?></div>
<div class="small">Wallet: <span id="wallet"><?= $wallet ? htmlspecialchars($wallet) : 'Not connected' ?></span></div>

<div class="stats">
  <div class="stat"><b id="rate">0.3300 / 10s</b>Current Rate</div>
  <div class="stat"><b id="multi">1.0000x</b>Total Multiplier</div>
  <div class="stat"><b id="boost">1.0000x</b>Participation Boost</div>
  <div class="stat"><b id="streak">0 days</b>Daily Streak</div>
</div>

<div class="row">
<button class="btn start" onclick="startDig()">Start Digging</button>
<button class="btn stop" onclick="stopDig()">Stop</button>
<button class="btn ghost" onclick="testBoost()">Test Linear Boost</button>
<a class="btn ghost" href="/wallet/">Wallet</a>
</div>

<div class="pulse" id="status">Idle</div>
</section>
</main>

<script>
let timer=null;
async function api(action){
  const fd=new FormData();
  fd.append('action', action);
  const r=await fetch('/rewards/api/digging.php',{method:'POST',body:fd});
  return await r.json();
}
function render(j){
  if(j.vShare) document.getElementById('vshare').textContent='vShare: '+j.vShare;
  if(j.rate_text) document.getElementById('rate').textContent=j.rate_text;
  if(j.total_multiplier) document.getElementById('multi').textContent=j.total_multiplier+'x';
  if(j.boost_multiplier) document.getElementById('boost').textContent=j.boost_multiplier+'x';
  if(j.streak_days !== undefined) document.getElementById('streak').textContent=j.streak_days+' days';
}
function setStatus(t){document.getElementById('status').textContent=t;}
async function startDig(){
  const j=await api('start'); render(j);
  setStatus(j.ok ? 'Digging active. Next credit every 10 seconds.' : JSON.stringify(j));
  if(timer) clearInterval(timer);
  timer=setInterval(tick,10000);
}
async function tick(){
  const j=await api('tick'); render(j);
  if(j.status==='credited') setStatus('+'+j.credited+' vShare credited.');
  else if(j.status==='cooldown') setStatus('Cooling down: '+j.next_in+'s');
  else setStatus(JSON.stringify(j));
}
async function stopDig(){
  if(timer) clearInterval(timer);
  timer=null;
  const j=await api('stop'); render(j);
  setStatus('Stopped.');
}
async function testBoost(){
  const r=await fetch('/rewards/api/add-boost.php?amount=100');
  const j=await r.json();
  setStatus(j.ok ? 'Linear vUSDT boost activated for 24h.' : JSON.stringify(j));
  const s=await api('status'); render(s);
}
api('status').then(render);
</script>
<script src="/assets/js/991-wallet-sync.js?v=991-unified"></script>
<script src="/assets/991-balance-sync.js?v=991-balance-sync-ux-v1" defer></script>
<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
</body>
</html>
