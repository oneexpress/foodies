<?php
declare(strict_types=1);
session_start();

function ev_has_rewards_login(): bool {
    return !empty($_SESSION['ev_account_id'])
        || !empty($_SESSION['ev_ton_address'])
        || !empty($_SESSION['ton_wallet'])
        || !empty($_SESSION['wallet_address'])
        || !empty($_COOKIE['ev_ton_wallet'])
        || !empty($_COOKIE['ev_wallet_user']);
}

if (!ev_has_rewards_login()) {
    header('Location: /signup/?next=/rewards/&reason=ton_required');
    exit;
}

$ver = 'final-ultimate-20260505-live-' . time();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Digging Reward Engine</title>
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<link rel="stylesheet" href="/rewards/assets/rewards.css?v=<?php echo htmlspecialchars($ver, ENT_QUOTES); ?>">
<style>
.ev991-lang-switch{display:flex;gap:8px;flex-wrap:wrap;align-items:center;justify-content:flex-end;margin-top:10px}
.ev991-lang-switch button{border:1px solid rgba(255,255,255,.18);background:rgba(255,255,255,.08);color:#fff;border-radius:999px;padding:9px 13px;font-weight:950;cursor:pointer}
.ev991-lang-switch button.active{background:#E60012;border-color:#E60012}
.ev991-guard-pill{display:inline-flex;align-items:center;gap:7px;margin-top:8px;border-radius:999px;padding:8px 12px;background:rgba(22,242,165,.12);border:1px solid rgba(22,242,165,.22);font-size:12px;font-weight:900;color:#b8ffe3}
.ev991-microcopy{font-size:12px;opacity:.76;margin-top:6px}
</style>

<style id="ev991-rewards-hero-layout-fix">
.hero{
  display:flex!important;
  align-items:flex-start!important;
  justify-content:space-between!important;
  gap:18px!important;
}
.hero > div:first-child{
  flex:1 1 auto!important;
  min-width:0!important;
}
.hero > div:last-child{
  display:flex!important;
  flex-direction:row!important;
  align-items:center!important;
  justify-content:flex-end!important;
  gap:10px!important;
  flex:0 0 auto!important;
  margin-top:0!important;
}
.wallet-btn{
  height:54px!important;
  min-width:112px!important;
  padding:0 22px!important;
  display:inline-flex!important;
  align-items:center!important;
  justify-content:center!important;
  border-radius:999px!important;
  white-space:nowrap!important;
}
.ev991-lang-switch{
  display:flex!important;
  flex-direction:row!important;
  gap:8px!important;
  margin-top:0!important;
}
.ev991-lang-switch button{
  height:54px!important;
  min-width:86px!important;
  padding:0 18px!important;
  display:inline-flex!important;
  align-items:center!important;
  justify-content:center!important;
}
@media(max-width:720px){
  .hero{
    flex-direction:column!important;
  }
  .hero > div:last-child{
    width:100%!important;
    justify-content:flex-start!important;
    flex-wrap:wrap!important;
  }
  .wallet-btn,
  .ev991-lang-switch button{
    height:48px!important;
  }
}
</style>


<style id="ev991-wallet-top-style">
.ev991-wallet-top{
  display:flex;
  justify-content:flex-end;
  margin:10px 0 6px;
}
.ev991-wallet-top .wallet-btn{
  height:48px;
  padding:0 18px;
  border-radius:999px;
}
@media(max-width:720px){
  .ev991-wallet-top{
    justify-content:flex-start;
  }
}
</style>


<style id="ev991-connect-wallet-style">
.ev991-wallet-top{display:flex;justify-content:flex-end;align-items:center;gap:10px;margin:10px 0 8px;flex-wrap:wrap}
.ev991-connect-wallet,.ev991-disconnect-wallet{
  min-height:48px!important;
  padding:0 20px!important;
  border:0!important;
  border-radius:999px!important;
  font-weight:1000!important;
  cursor:pointer!important;
  background:linear-gradient(135deg,#ff8a00,#ffb703)!important;
  color:#210f00!important;
  box-shadow:0 14px 34px rgba(255,138,0,.35)!important;
}
.ev991-disconnect-wallet{background:linear-gradient(135deg,#111827,#374151)!important;color:#fff!important}
.ev991-connect-wallet.connected{background:linear-gradient(135deg,#0e8965,#15e6a3)!important;color:#04120c!important}
@media(max-width:720px){.ev991-wallet-top{justify-content:flex-start}.ev991-connect-wallet,.ev991-disconnect-wallet{width:auto!important}}
</style>

<style id="ev991-simple-sfx-css">
#ev991SoundSwitch{
  position:fixed;
  top:14px;
  right:14px;
  z-index:999999;
}
#ev991SoundBtn{
  border:1px solid rgba(255,255,255,.18);
  background:#ffffff;
  color:#111827;
  border-radius:999px;
  min-height:38px;
  padding:0 14px;
  font-size:12px;
  font-weight:1000;
  cursor:pointer;
  box-shadow:0 8px 24px rgba(0,0,0,.24);
}
#ev991SoundBtn.is-off{
  background:#2b2f3a;
  color:#d1d5db;
}
</style>

</head>
<body>
<main class="wrap">
  <section class="hero">
    <div>
      <div class="eyebrow" data-i18n="eyebrow">ExpressVisa Rewards Engine</div>
      <h1 data-i18n="title">Digging Reward Engine</h1>
      <p data-i18n="subtitle">0.003 vSHARE reward / 10s · vUSDT speed boost · 10 vSHARE daily cap</p>
      <div class="ev991-guard-pill" data-i18n="guard">✅ TON / Unified login verified · Digging enabled</div>
      <div class="ev991-microcopy" data-i18n="microcopy">vSHARE = 微股 · vUSDT = 微定币</div>
    </div>
    <div>
      
      <div class="ev991-lang-switch">
        <button type="button" data-lang="en" class="active">EN</button>
        <button type="button" data-lang="zh">中文</button>
      </div>
    </div>
  </section>

  
<div class="ev991-wallet-top">
</div>

<section class="top-grid">
    <article class="glass balance-card">
      <span data-i18n="vshareBalance">vSHARE Balance</span>
      <strong id="vshare">0.000000</strong>
      <small data-i18n="vshareDesc">Foodies Rewards participation token</small>
    </article>
    <article class="glass balance-card">
      <span data-i18n="score">Score</span>
      <strong id="score">0.000000</strong>
      <small data-i18n="scoreDesc">Participation reputation layer</small>
    </article>
  </section>

  <section class="engine-grid">
    <article class="glass rpm-card">
      <div class="card-head">
        <div>
          <h2 data-i18n="dailyCap">Daily RPM Cap</h2>
          <p data-i18n="dailyCapDesc">Progress toward 10 vSHARE daily capacity</p>
        </div>
        <b id="rpmPercent">0%</b>
      </div>
      <div class="rpm-ring" id="rpmRing" style="--rpm:0%">
        <div class="rpm-core">
          <strong id="dailyEarned">0.000000</strong>
          <span>/ 10 vSHARE</span>
        </div>
      </div>
      <div class="meta">
        <span id="capStatus">Charging</span>
        <span id="timeToFull">Full in --</span>
      </div>
    </article>

    <article class="glass cycle-card">
      <div class="card-head">
        <div>
          <h2 data-i18n="cycle">Digging Cycle</h2>
          <p data-i18n="cycleDesc">10-second repeating reward charge</p>
        </div>
        <b id="cycleCountdown">10s</b>
      </div>
      <div class="vertical-battery">
        <div class="vertical-battery-fill" id="cycleFill"></div>
        <div class="scan-line"></div>
      </div>
      <div class="meta">
        <span id="rewardRate">+0.003000 reward / 10s</span>
        <span id="hourRate">1.080000 / hour</span>
      </div>
    </article>
  </section>

  <section class="glass boost-card">
    <div class="card-head">
      <div>
        <h2 data-i18n="booster">Booster Slider</h2>
        <p data-i18n="boosterDesc">Minimum 1 vUSDT. Boost increases charging speed only.</p>
      </div>
      <b id="boostMultiplier">1.000x</b>
    </div>

    <div class="slider-row">
      <span>1</span>
      <input id="boostSlider" type="range" min="1" max="1" value="1" step="1" disabled>
      <span id="boostMax">0 vUSDT</span>
    </div>

    <div class="bar-track">
      <div class="bar-fill boost-fill" id="boostFill"></div>
    </div>

    <div class="meta">
      <span id="boostTier">Need minimum 1 vUSDT</span>
      <span><b id="boostSelected">0</b> vUSDT selected</span>
    </div>
    <div class="meta vusdt-balance-line">
      <span data-i18n="vusdtBalance">vUSDT Offchain Balance</span>
      <span><b id="vusdtOffchainBalance">0.000000</b> vUSDT</span>
    </div>
  </section>

  <section class="glass proof-card">
    <div class="card-head">
      <div>
        <h2 data-i18n="proof">Proof Hash Stream</h2>
        <p data-i18n="proofDesc">Live nonce hash proof every reward cycle</p>
      </div>
      <b>LIVE</b>
    </div>
    <div class="hash-stream" id="hashStream">
      <div class="hash-line">Waiting for rewards proof ...</div>
    </div>
    <div class="meta">
      <span id="proofHash">Nonce: Waiting for rewards proof ...</span>
      <span id="proofStatus">Waiting for rewards proof ...</span>
    </div>
  </section>

  <section class="glass action-card">
    <button id="collectBtn" type="button" data-i18n="collect">COLLECT REWARD</button>
    <div id="msg">Boost affects speed only. Daily reward remains capped at 10 vSHARE.</div>
  </section>

  <section class="glass ledger">
    <div class="card-head">
      <h2 data-i18n="ledger">Reward Ledger</h2>
      <a href="/wallet/" data-i18n="openWallet">Open Wallet</a>
    </div>
    <div id="ledgerRows">Loading...</div>
  </section>
</main>

<script src="/rewards/assets/rewards.js?v=<?php echo htmlspecialchars($ver, ENT_QUOTES); ?>"></script>
<script src="https://unpkg.com/@tonconnect/ui@latest/dist/tonconnect-ui.min.js"></script>

<script id="ev991-connect-wallet-js">
(function(){
  if(window.__EV991_REWARDS_CONNECT_WALLET__) return;
  window.__EV991_REWARDS_CONNECT_WALLET__ = true;

  let tonUI = null;
  let currentAddress = '';

  function shortAddr(a){return a && a.length > 14 ? a.slice(0,6)+'...'+a.slice(-6) : a;}
  function btn(){return document.getElementById('connectWalletBtn');}
  function disBtn(){return document.getElementById('disconnectWalletBtn');}
  function msg(t){var el=document.getElementById('msg'); if(el) el.textContent=t;}

  async function saveWallet(address){
    const r = await fetch('/rewards/api/connect-wallet.php', {
      method:'POST',
      credentials:'same-origin',
      headers:{'Content-Type':'application/json'},
      body:JSON.stringify({address})
    });
    const j = await r.json();
    if(!j.ok) throw new Error(j.error || 'Wallet session failed');
    return j;
  }

  async function clearWallet(){
    await fetch('/rewards/api/disconnect-wallet.php', {method:'POST', credentials:'same-origin'});
  }

  function render(){
    const b=btn(), d=disBtn();
    if(!b) return;
    if(currentAddress){
      b.textContent='CONNECTED · '+shortAddr(currentAddress);
      b.classList.add('connected');
      if(d) d.style.display='';
    }else{
      b.classList.remove('connected');
      if(d) d.style.display='none';
    }
  }

  async function init(){
    const b=btn(), d=disBtn();
    if(!b) return;

    if(!window.TON_CONNECT_UI){
      b.onclick=function(){ location.href='/signup/?next=/rewards/&reason=ton_required'; };
      msg('TON Connect loading failed. Use signup unified login.');
      return;
    }

    try{
      tonUI = new TON_CONNECT_UI.TonConnectUI({
        manifestUrl: location.origin + '/tonconnect-manifest.json',
        buttonRootId: 'ton-connect'
      });

      tonUI.onStatusChange(async function(wallet){
        try{
          currentAddress = wallet && wallet.account && wallet.account.address ? wallet.account.address : '';
          if(currentAddress){
            await saveWallet(currentAddress);
            msg('TON wallet connected. Digging enabled.');
            if(typeof window.__EV991_REWARDS_RELOAD__ === 'function') window.__EV991_REWARDS_RELOAD__();
          }
          render();
        }catch(e){ msg('Wallet session failed: '+e.message); }
      });

      if(tonUI.wallet && tonUI.wallet.account && tonUI.wallet.account.address){
        currentAddress = tonUI.wallet.account.address;
        await saveWallet(currentAddress);
      }

      b.onclick = async function(){
        if(currentAddress){
          location.href='/wallet/';
          return;
        }
        try{ await tonUI.openModal(); }catch(e){ msg('Connect failed: '+e.message); }
      };

      if(d){
        d.onclick = async function(){
          try{
            if(tonUI) await tonUI.disconnect();
            await clearWallet();
            currentAddress='';
            render();
            msg('Wallet disconnected.');
          }catch(e){ msg('Disconnect failed: '+e.message); }
        };
      }

      render();
    }catch(e){
      msg('TON Connect init failed: '+e.message);
      b.onclick=function(){ location.href='/signup/?next=/rewards/&reason=ton_required'; };
    }
  }

  document.addEventListener('DOMContentLoaded', init);
})();
</script>




<script src="/assets/js/991-wallet-sync.js?v=991-unified"></script>
<script src="/assets/991-balance-sync.js?v=991-balance-sync-ux-v1" defer></script>


<div id="ev991SoundSwitch">
  <button id="ev991SoundBtn" type="button" aria-pressed="true">Sound ON</button>
</div>

<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
</body>
</html>

<script id="991-live-rewards-sync">
(async function(){

async function syncRewards(){

  try{

    const r = await fetch('/api/auth/status.php?t=' + Date.now(),{
      cache:'no-store'
    });

    const j = await r.json();

    if(!j.ok){ return; }

    const vs = document.querySelectorAll(
      '.vshare-balance,#vshare-balance,[data-vshare]'
    );

    vs.forEach(el=>{
      el.textContent = j.balance_vshare + ' vSHARE';
    });

    const sc = document.querySelectorAll(
      '.score-balance,#score-balance,[data-score]'
    );

    sc.forEach(el=>{
      el.textContent = j.score;
    });

  }catch(e){
    console.log('rewards sync err',e);
  }
}

syncRewards();
setInterval(syncRewards,3000);

})();
</script>
