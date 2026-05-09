<?php
declare(strict_types=1);
header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
$ver = 'wallet-lang-final-' . time();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>ExpressVisa Wallet</title>
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<style>
:root{--bg:#030406;--line:rgba(255,255,255,.1);--text:#f9fafb;--muted:#9ca3af;--red:#e60012;--green:#16f2a5;--gold:#ffb703}
*{box-sizing:border-box}
body{margin:0;font-family:Inter,Arial,sans-serif;background:radial-gradient(circle at top left,rgba(230,0,18,.22),transparent 32%),radial-gradient(circle at bottom right,rgba(22,242,165,.13),transparent 32%),var(--bg);color:var(--text);padding-bottom:150px!important}
.wrap{max-width:980px;margin:auto;padding:20px 14px 130px}
.top{display:flex;justify-content:space-between;gap:14px;align-items:center;margin-bottom:16px}
.brand h1{margin:0;font-size:26px;letter-spacing:-.05em}
.brand p{margin:7px 0 0;color:var(--muted);font-size:13px;font-weight:700}
.lang-switch{display:flex;gap:6px;margin-top:10px}
.lang-btn{border:1px solid rgba(255,255,255,.14);background:rgba(255,255,255,.06);color:#fff;border-radius:999px;padding:8px 12px;font-weight:950;cursor:pointer}
.lang-btn.active{background:var(--red);border-color:var(--red)}
.row{display:grid;gap:14px;margin-top:14px}
.split{grid-template-columns:1fr 1fr}
.card{position:relative;overflow:hidden;border:1px solid var(--line);border-radius:28px;background:linear-gradient(145deg,rgba(255,255,255,.08),rgba(255,255,255,.03));box-shadow:0 20px 60px rgba(0,0,0,.35)}
.card:before{content:"";position:absolute;inset:-1px;background:linear-gradient(135deg,rgba(230,0,18,.2),transparent,rgba(22,242,165,.12));pointer-events:none}
.inner{position:relative;padding:24px}
.token-head{display:flex;align-items:center;justify-content:space-between;gap:14px}
.token-left{display:flex;align-items:center;gap:14px}
.logo{width:58px;height:58px;border-radius:18px;object-fit:contain;background:#fff;padding:7px;border:1px solid rgba(255,255,255,.16)}
.logo.big{width:68px;height:68px;border-radius:22px}
.symbol{font-size:22px;font-weight:950;letter-spacing:-.04em}
.type{font-size:12px;color:var(--muted);margin-top:5px;line-height:1.35;font-weight:700}
.badge{font-size:11px;border:1px solid rgba(255,255,255,.12);color:#fff;border-radius:999px;padding:7px 10px;background:rgba(255,255,255,.06);white-space:nowrap;font-weight:900}
.balance{margin-top:24px;font-size:56px;line-height:.95;font-weight:950;letter-spacing:-.08em}
.balance.medium{font-size:42px}
.unit{font-size:13px;color:var(--muted);margin-top:8px;font-weight:800}
.mode-switch{display:flex;gap:6px;margin-top:14px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:999px;padding:5px;width:max-content}
.mode-btn{border:0;border-radius:999px;padding:8px 12px;background:transparent;color:#9ca3af;font-size:11px;font-weight:950;cursor:pointer}
.mode-btn.active{background:var(--red);color:#fff}
.foodbank-card{margin-top:14px}
.foodbank-icon{width:58px;height:58px;border-radius:18px;display:grid;place-items:center;background:var(--red);color:#fff;font-size:28px;font-weight:950;box-shadow:0 12px 34px rgba(230,0,18,.38)}
.actions{margin-top:16px;display:flex;gap:10px;flex-wrap:wrap}
.btn{border:1px solid rgba(255,255,255,.12);background:#11141a;color:#fff;border-radius:999px;padding:12px 15px;font-weight:900;text-decoration:none;font-size:13px;cursor:pointer}
.btn.red{background:var(--red);border-color:var(--red)}
.donate-input{width:220px;max-width:100%;border:1px solid rgba(255,255,255,.12);background:#050505;color:#fff;border-radius:999px;padding:12px 14px;font-weight:800;outline:none}
.small,.status{margin-top:12px;color:var(--muted);font-size:12px}
.status{text-align:center;margin-top:16px}
.digging-cta{display:flex;align-items:center;justify-content:space-between;gap:14px;width:max-content;margin-top:18px;padding:15px 18px 15px 22px;border-radius:999px;text-decoration:none;background:linear-gradient(135deg,#ff7a00,#ffb200);color:#2b1200;font-weight:950;box-shadow:0 14px 40px rgba(255,122,0,.35)}
.digging-cta .cta-text{display:flex;align-items:center;gap:6px;font-size:14px;letter-spacing:.035em;white-space:nowrap}
.digging-cta .cta-text b{font-size:14px;color:#2b1200}
.digging-cta .cta-icon{width:42px;height:42px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:#fff;color:#ff7a00;font-size:19px;box-shadow:0 6px 16px rgba(0,0,0,.25);flex:0 0 auto}
.ev991-engine-btn{background:linear-gradient(135deg,#ff8700,#ffb703)!important;color:#211000!important;border:0!important}
.wallet-connect-panel{display:flex;align-items:center;justify-content:flex-end;gap:10px;min-height:46px}
.connect-btn{border:0;border-radius:999px;padding:14px 22px;background:linear-gradient(135deg,#e60012,#ff7a00);color:#fff;font-weight:950;cursor:pointer;box-shadow:0 12px 32px rgba(230,0,18,.35);letter-spacing:.02em}
.donation-balance-box{margin-top:22px;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.06);border-radius:24px;padding:18px}
.donation-label{font-size:12px;color:var(--muted);font-weight:900;text-transform:uppercase;letter-spacing:.08em}
.donation-balance{margin-top:8px;font-size:46px;line-height:.95;font-weight:950;letter-spacing:-.06em;color:#fff}
.donation-unit{margin-top:8px;color:var(--muted);font-size:13px;font-weight:800}
.connected-wallet{display:none;align-items:center;gap:8px;background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.12);border-radius:999px;padding:7px 8px 7px 13px}
.connected-wallet.show{display:flex}
.wallet-dot{width:9px;height:9px;border-radius:50%;background:var(--green)}
.wallet-short{font-size:13px;font-weight:950;color:#fff}
.wallet-mini-btn{border:0;border-radius:999px;background:rgba(255,255,255,.12);color:#fff;padding:8px 10px;font-size:12px;font-weight:900;cursor:pointer}
.wallet-mini-btn.disconnect{background:rgba(230,0,18,.9)}
#ton-connect{position:absolute;left:-9999px;top:auto;width:1px;height:1px;overflow:hidden}
@media(max-width:720px){.top{display:block}.wallet-connect-panel{justify-content:flex-start;margin-top:14px}.split{grid-template-columns:1fr}.balance{font-size:44px}.balance.medium{font-size:36px}.digging-cta{width:100%;justify-content:space-between}.token-head{align-items:flex-start}.badge{margin-top:4px}body{padding-bottom:170px!important}.wrap{padding-bottom:155px!important}}
</style>
</head>
<body>
<main class="wrap">
  <section class="top">
    <div class="brand">
      <h1 data-i18n="title">ExpressVisa Wallet</h1>
      <p data-i18n="subtitle">Rewards Engine · vUSDT settlement · TON assets</p>
      <div class="lang-switch">
        <button class="lang-btn active" data-lang-btn="en" type="button">EN</button>
        <button class="lang-btn" data-lang-btn="zh" type="button">中文</button>
      </div>
    </div>
    <div class="wallet-connect-panel">
      <div id="ton-connect"></div>
      <button class="connect-btn" id="connectWalletBtn" type="button" data-i18n="connect">🔐 CONNECT WALLET</button>
      <div class="connected-wallet" id="connectedWalletBox">
        <span class="wallet-dot"></span>
        <span class="wallet-short" id="walletShortAddr">Not connected</span>
        <button class="wallet-mini-btn" id="copyWalletBtn" type="button" data-i18n="copy">Copy</button>
        <button class="wallet-mini-btn disconnect" id="disconnectWalletBtn" type="button" data-i18n="disconnect">Disconnect</button>
      </div>
    </div>
  </section>

  <section class="row">
    <article class="card primary">
      <div class="inner">
        <div class="token-head">
          <div class="token-left">
            <img class="logo big" src="/metadata/991_vshare_logo.png" alt="vSHARE">
            <div><div class="symbol">vSHARE</div><div class="type" data-i18n="vshareDesc">Foodies Rewards Engine / participation token</div></div>
          </div>
          <div class="badge" data-i18n="participation">Participation</div>
        </div>
        <div class="mode-switch" data-token="vSHARE">
          <button class="mode-btn" data-mode="offchain" type="button">OFF-CHAIN</button>
          <button class="mode-btn active" data-mode="onchain" type="button">ON-CHAIN</button>
        </div>
        <div class="balance" id="bal-vshare" data-wallet-vshare>0.00</div>
        <div class="unit" id="unit-vshare">vSHARE off-chain balance</div>
        <a href="/rewards/?from=wallet" class="digging-cta">
          <span class="cta-text"><span data-i18n="startDigging">START DIGGING</span> → <b data-i18n="earnVshare">Earn vSHARE</b></span>
          <span class="cta-icon">⛏️</span>
        </a>
      </div>
    </article>
  </section>

<section class="wallet-card score-card" style="margin-top:18px;">
  <div style="display:flex;align-items:center;gap:14px;">
    <div style="width:64px;height:64px;border-radius:20px;background:#fff;display:flex;align-items:center;justify-content:center;font-size:34px;">🏆</div>
    <div>
      <h2 style="margin:0;color:#fff;">SCORE</h2>
      <p style="margin:6px 0 0;color:#b8c7d9;font-weight:800;">Participation reputation balance</p>
    </div>
  </div>
  <div style="font-size:58px;font-weight:1000;color:#fff;margin-top:28px;line-height:1;" data-wallet-score>0</div>
  <div style="color:#b8c7d9;font-weight:800;margin-top:10px;">1 reward awarded = 1 Score</div>
</section>


  <section class="row">
    <article class="card secondary">
      <div class="inner">
        <div class="token-head">
          <div class="token-left">
            <div><div class="symbol">vUSDT</div><div class="type" data-i18n="vusdtDesc">ExpressVisa internal settlement token</div></div>
          </div>
          <div class="badge" data-i18n="settlement">Settlement</div>
        </div>
        <div class="mode-switch" data-token="vUSDT">
          <button class="mode-btn" data-mode="offchain" type="button">OFF-CHAIN</button>
          <button class="mode-btn active" data-mode="onchain" type="button">ON-CHAIN</button>
        </div>
        <div class="balance" id="bal-vusdt">0.00</div>
        <div class="unit" id="unit-vusdt">vUSDT off-chain balance</div>
      </div>
    </article>
  </section>

  <section class="row split">
    <article class="card mini">
      <div class="inner">
        <div class="token-head">
          <div class="token-left">
            <img class="logo" src="/metadata/usdt_ton.png" alt="USDT-TON">
            <div><div class="symbol">USDT-TON</div><div class="type" data-i18n="usdtTonDesc">external on-chain TON asset</div></div>
          </div>
        </div>
        <div class="balance medium" id="bal-usdt-ton">0.00</div>
        <div class="unit" data-i18n="usdtTonUnit">USDT-TON balance</div>
      </div>
    </article>

    <article class="card mini">
      <div class="inner">
        <div class="token-head">
          <div class="token-left">
            <img class="logo" src="/metadata/ton.png" alt="Native TON">
            <div><div class="symbol">Native TON</div><div class="type" data-i18n="tonDesc">native TON gas/token asset</div></div>
          </div>
        </div>
        <div class="balance medium" id="bal-ton">0.00</div>
        <div class="unit" data-i18n="tonUnit">TON balance</div>
      </div>
    </article>
  </section>

  <section class="card foodbank-card">
    <div class="inner">
      <div class="token-head">
        <div class="token-left">
          <div class="foodbank-icon">♥</div>
          <div><div class="symbol" data-i18n="foodbank">FoodBank Donation</div><div class="type" data-i18n="foodbankDesc">Donate vUSDT to support FoodBank / F&B community assistance</div></div>
        </div>
        <div class="badge">vUSDT</div>
      </div>
      <div class="donation-balance-box">
        <div class="donation-label" data-i18n="donationBalance">Donation Balance</div>
        <div class="donation-balance" id="bal-donation-vusdt">0.00</div>
        <div class="donation-unit">vUSDT</div>
      </div>
      <div class="actions">
        <input id="foodbankAmount" class="donate-input" type="number" min="0.01" step="0.01" placeholder="Amount vUSDT">
        <button class="btn red" id="foodbankDonateBtn" type="button" data-i18n="donate">Donate vUSDT</button>
      </div>
      <div class="small" id="foodbankMsg" data-i18n="donationNote">Donation uses vUSDT Offchain Balance.</div>
    </div>
  </section>

  <section class="actions">
    <a class="btn red" href="/wallet/" data-i18n="refresh">Refresh Wallet</a>
    <a class="btn" href="/wallet/api/tokens.php" target="_blank">Token API</a>
    <a class="btn" href="/foodies-nft/">Foodies NFT</a>
    <a class="btn ev991-engine-btn" href="/engine-hub/">⚡ Engine Hub</a>
  </section>

  <div class="status" id="walletStatus">Wallet UI loaded · 4 canonical token rows active</div>
</main>

<script src="https://unpkg.com/@tonconnect/ui@latest/dist/tonconnect-ui.min.js"></script>
<script>
(function(){
  const $ = (id)=>document.getElementById(id);

  const dict = {
    en:{
      vshareDesc:'Foodies Rewards Engine / participation token',participation:'Participation',startDigging:'START DIGGING',earnVshare:'Earn vSHARE',
      vusdtDesc:'ExpressVisa internal settlement token',settlement:'Settlement',usdtTonDesc:'external on-chain TON asset',tonDesc:'native TON gas/token asset',
      usdtTonUnit:'USDT-TON balance',tonUnit:'TON balance',foodbank:'FoodBank Donation',foodbankDesc:'Donate vUSDT to support FoodBank / F&B community assistance',
      donate:'Donate vUSDT',donationNote:'Donation uses vUSDT Offchain Balance.',refresh:'Refresh Wallet'
    },
    zh:{
      title:'ExpressVisa 钱包',subtitle:'奖励引擎 · vUSDT 结算 · TON 资产',connect:'🔐 连接钱包',copy:'复制',disconnect:'断开连接',
      vshareDesc:'美食奖励引擎 / 参与通证',participation:'参与奖励',startDigging:'开始 DIGGING',earnVshare:'赚取 vSHARE',
      vusdtDesc:'ExpressVisa 内部结算通证',settlement:'结算资产',usdtTonDesc:'TON 链上外部资产',tonDesc:'TON 原生 Gas / 通证资产',
      usdtTonUnit:'USDT-TON 余额',tonUnit:'TON 余额',foodbank:'FoodBank 捐赠',foodbankDesc:'捐赠 vUSDT 支持 FoodBank / 餐饮社区援助',
      donate:'捐赠 vUSDT',donationNote:'捐赠使用 vUSDT 链下余额。',refresh:'刷新钱包'
    }
  };

  let lang = localStorage.getItem('ev991_lang') || 'en';
  let tonConnectUI = null;
  let connectedAddress = '';

  const modeState = { vSHARE:'onchain', vUSDT:'onchain' };
  const balances = { vSHARE:{offchain:0,onchain:0}, vUSDT:{offchain:0,onchain:0}, usdtTon:0, ton:0, donationVusdt:0 };

  function num(v){ const n=Number(v); return Number.isFinite(n)?n:0; }
  function fmt(v){ return num(v).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:6}); }
  function shortAddress(addr){ return addr && addr.length>14 ? addr.slice(0,6)+'...'+addr.slice(-6) : (addr || 'Not connected'); }

  function applyLang(){
    document.documentElement.lang = lang === 'zh' ? 'zh' : 'en';
    document.querySelectorAll('[data-i18n]').forEach(el=>{
      const key = el.dataset.i18n;
      if(dict[lang] && dict[lang][key]) el.textContent = dict[lang][key];
    });
    document.querySelectorAll('[data-lang-btn]').forEach(btn=>btn.classList.toggle('active', btn.dataset.langBtn === lang));
    render();
  }

  function unitText(token){
    const mode = modeState[token];
    if(token === 'vSHARE') return mode === 'offchain' ? (lang==='zh'?'vSHARE 链下余额':'vSHARE off-chain balance') : (lang==='zh'?'vSHARE 链上余额':'vSHARE on-chain balance');
    return mode === 'offchain' ? (lang==='zh'?'vUSDT 链下余额':'vUSDT off-chain balance') : (lang==='zh'?'vUSDT 链上余额':'vUSDT on-chain balance');
  }

  function render(){
    $('bal-vshare').textContent = fmt(balances.vSHARE[modeState.vSHARE]);
    $('unit-vshare').textContent = unitText('vSHARE');
    $('bal-vusdt').textContent = fmt(balances.vUSDT[modeState.vUSDT]);
    $('unit-vusdt').textContent = unitText('vUSDT');
    $('bal-usdt-ton').textContent = fmt(balances.usdtTon);
    $('bal-ton').textContent = fmt(balances.ton);
    if($('bal-donation-vusdt')) $('bal-donation-vusdt').textContent = fmt(balances.donationVusdt);
  }

  async function loadTokens(){
    const status = $('walletStatus');
    try{
      const addrParam = connectedAddress ? '&address=' + encodeURIComponent(connectedAddress) + '&wallet=' + encodeURIComponent(connectedAddress) : '';
      const r = await fetch('/wallet/api/tokens.php?v=' + Date.now() + addrParam, {credentials:'same-origin'});
      const j = await r.json();
      if(!j || j.ok === false) throw new Error((j && (j.error || j.message)) || 'Token API unavailable');

      const data = j.tokens || j.data || j;
      const b = j.balances || {};
      balances.vSHARE.offchain = num(data.vSHARE?.offchain ?? b.vshare_offchain ?? data.vshare_offchain ?? data.vshare ?? 0);
      balances.vSHARE.onchain  = num(data.vSHARE?.onchain  ?? b.vshare_onchain  ?? data.vshare_onchain ?? 0);
      balances.vUSDT.offchain  = num(data.vUSDT?.offchain  ?? b.vusdt_offchain  ?? data.vusdt_offchain ?? data.vusdt ?? 0);
      balances.vUSDT.onchain   = num(data.vUSDT?.onchain   ?? b.vusdt_onchain   ?? data.vusdt_onchain ?? 0);
      balances.usdtTon = num(data.USDT_TON?.balance ?? data.USDT_TON?.onchain ?? b.usdt_ton ?? data.usdt_ton ?? data.usdtTon ?? 0);
      balances.ton = num(data.TON?.balance ?? data.TON?.onchain ?? b.native_ton ?? data.ton ?? 0);
      balances.donationVusdt = num(data.DONATION?.vUSDT ?? data.FOODBANK?.vUSDT ?? b.donation_vusdt ?? b.foodbank_vusdt ?? data.donation_vusdt ?? data.foodbank_vusdt ?? 0);

      status.textContent = lang === 'zh' ? '钱包已同步 · 4 个标准资产行已启用' : 'Wallet synced · 4 canonical token rows active';
    }catch(e){
      status.textContent = (lang === 'zh' ? '钱包 API 暂不可用: ' : 'Wallet API pending: ') + e.message;
    }
    render();
  }

  function setWalletState(wallet){
    const connectBtn = $('connectWalletBtn'), box = $('connectedWalletBox'), addrEl = $('walletShortAddr');
    connectedAddress = wallet && wallet.account && wallet.account.address ? wallet.account.address : '';
    if(connectedAddress){
      connectBtn.style.display='none'; box.classList.add('show'); addrEl.textContent=shortAddress(connectedAddress);
      $('walletStatus').textContent = (lang==='zh'?'钱包已连接 · ':'Wallet connected · ') + shortAddress(connectedAddress);
    }else{
      connectBtn.style.display=''; box.classList.remove('show'); addrEl.textContent='Not connected';
    }
  }

  async function initWalletConnect(){
    const status=$('walletStatus'), connectBtn=$('connectWalletBtn'), copyBtn=$('copyWalletBtn'), disconnectBtn=$('disconnectWalletBtn');

    if(!window.TON_CONNECT_UI){
      status.textContent = 'TON Connect library pending';
      connectBtn.onclick = ()=>{ status.textContent = 'TON Connect library not loaded. Please refresh.'; };
      return;
    }

    try{
      tonConnectUI = new TON_CONNECT_UI.TonConnectUI({manifestUrl:location.origin+'/tonconnect-manifest.json',buttonRootId:'ton-connect'});
      tonConnectUI.onStatusChange(function(wallet){ setWalletState(wallet); loadTokens(); });
      setWalletState(tonConnectUI.wallet);

      connectBtn.onclick = async()=>{ try{ await tonConnectUI.openModal(); }catch(e){ status.textContent='Connect wallet failed: '+e.message; } };
      disconnectBtn.onclick = async()=>{ try{ await tonConnectUI.disconnect(); setWalletState(null); await loadTokens(); }catch(e){ status.textContent='Disconnect failed: '+e.message; } };
      copyBtn.onclick = async()=>{ if(!connectedAddress)return; try{ await navigator.clipboard.writeText(connectedAddress); status.textContent='Wallet copied · '+shortAddress(connectedAddress); }catch(e){ status.textContent=connectedAddress; } };
    }catch(e){ status.textContent='TON Connect init failed: '+e.message; }
  }

  document.querySelectorAll('[data-lang-btn]').forEach(btn=>btn.onclick=()=>{ lang=btn.dataset.langBtn; localStorage.setItem('ev991_lang',lang); applyLang(); });
  document.querySelectorAll('.mode-switch').forEach(sw=>{
    sw.querySelectorAll('.mode-btn').forEach(btn=>{
      btn.onclick=()=>{ const token=sw.dataset.token; modeState[token]=btn.dataset.mode; sw.querySelectorAll('.mode-btn').forEach(b=>b.classList.remove('active')); btn.classList.add('active'); render(); };
    });
  });

  const donateBtn=$('foodbankDonateBtn');
  if(donateBtn){
    donateBtn.onclick=async()=>{
      const msg=$('foodbankMsg'), amount=num($('foodbankAmount').value);
      if(amount<=0){ msg.textContent=lang==='zh'?'请输入捐赠金额。':'Enter donation amount.'; return; }
      msg.textContent=lang==='zh'?'正在提交捐赠...':'Submitting donation...';
      try{
        const r=await fetch('/wallet/api/foodbank-donate.php',{method:'POST',headers:{'Content-Type':'application/json'},credentials:'same-origin',body:JSON.stringify({amount})});
        const j=await r.json();
        if(!j.ok) throw new Error(j.error || 'Donation failed');
        msg.textContent=lang==='zh'?'捐赠成功，谢谢支持。':'Donation successful. Thank you.';
        await loadTokens();
      }catch(e){ msg.textContent=(lang==='zh'?'捐赠 API 暂不可用: ':'Donation API pending: ')+e.message; }
    };
  }

  applyLang();
  initWalletConnect();
  loadTokens();
  setInterval(loadTokens,30000);
})();
</script>


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
</body>
</html>
