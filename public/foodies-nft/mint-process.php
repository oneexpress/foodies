<?php
declare(strict_types=1);
header('Content-Type: text/html; charset=UTF-8');

$uid = trim((string)($_GET['uid'] ?? $_GET['mint_uid'] ?? 'FOODIES-RWA-PENDING'));
$stars = (int)($_GET['stars'] ?? $_GET['star'] ?? 1);
if (!in_array($stars, [1,3,5], true)) $stars = 1;

function h($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

$safe = preg_replace('/[^A-Za-z0-9._-]/', '-', $uid);
$rawBase = 'https://raw.githubusercontent.com/oneexpress/foodies/main/public/metadata/foodies';
$verify = 'https://expressvisa.one/foodies-nft/verify.php?uid=' . rawurlencode($uid);

$localArtifact = '/metadata/foodies/generated/' . $safe . '-' . $stars . 'star.png';
$rawArtifact = $rawBase . '/generated/' . rawurlencode($safe) . '-' . $stars . 'star.png';
$rawMetadata = $rawBase . '/items/' . rawurlencode($safe) . '.json';

$artifactApi = '/foodies-nft/api/nft-artifact-mint.php?uid=' . rawurlencode($uid) . '&stars=' . $stars;
$metaApi = '/foodies-nft/api/nft-metadata.php?uid=' . rawurlencode($uid) . '&stars=' . $stars;
$payloadApi = '/foodies-nft/api/build-mint-payload.php?uid=' . rawurlencode($uid) . '&stars=' . $stars;
$templateFallback = $stars === 5 ? '/metadata/foodies/NFT/5_stars_foodies.png' : ($stars === 3 ? '/metadata/foodies/NFT/3_stars_foodies.png' : '/metadata/foodies/NFT/1_star_foodies.png');
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<title>Foodies NFT Mint Process</title>
<link rel="stylesheet" href="/foodies-nft/assets/foodies-footer-nav.css?v=foodies-mint-ultimate">
<script src="https://unpkg.com/@tonconnect/ui@latest/dist/tonconnect-ui.min.js"></script>
<style>
:root{
  --red:#E60012;--dark:#B8000E;--green:#15803d;--soft:#FFF1F2;--bg:#F7F8FA;
  --ink:#101828;--muted:#667085;--line:#E5E7EB;--gold:#f59e0b;--blue:#2563eb;
}
*{box-sizing:border-box}
body{margin:0;background:radial-gradient(circle at top,#fff 0,#fff3f4 30%,var(--bg) 66%);font-family:Arial,Helvetica,sans-serif;color:var(--ink);padding-bottom:128px}
.wrap{max-width:1280px;margin:0 auto;padding:18px}
.top{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:14px}
.back{background:#111;color:#fff;text-decoration:none;border-radius:999px;padding:11px 15px;font-weight:1000}
.lang{display:flex;gap:8px}
.lang button{border:1px solid var(--line);background:#fff;border-radius:999px;padding:9px 13px;font-weight:1000;cursor:pointer}
.lang .active{background:var(--red);color:#fff}
.hero{background:linear-gradient(135deg,var(--red),var(--dark));border-radius:30px;color:#fff;padding:24px;box-shadow:0 20px 52px rgba(230,0,18,.25);overflow:hidden;position:relative}
.hero h1{margin:0 0 8px;font-size:31px}
.hero p{margin:0;line-height:1.55;opacity:.94;max-width:860px}
.heroGrid{display:grid;grid-template-columns:1fr auto;gap:16px;align-items:end}
.wallet{margin-top:14px;display:flex;align-items:center;gap:10px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.24);border-radius:18px;padding:12px 14px;font-weight:1000}
.led{width:12px;height:12px;border-radius:50%;background:#ef4444;box-shadow:0 0 0 5px rgba(239,68,68,.13),0 0 16px rgba(239,68,68,.75)}
.led.on{background:#22c55e;box-shadow:0 0 0 5px rgba(34,197,94,.16),0 0 18px rgba(34,197,94,.95)}
.pillbar{display:flex;gap:8px;flex-wrap:wrap;margin-top:14px}
.pill{border:1px solid rgba(255,255,255,.35);background:rgba(255,255,255,.14);border-radius:999px;padding:8px 11px;font-size:12px;font-weight:900}
.grid{display:grid;grid-template-columns:minmax(0,450px) minmax(0,1fr);gap:16px;margin-top:16px;max-width:100%;overflow:hidden}
.card{background:#fff;border:1px solid var(--line);border-radius:24px;padding:18px;box-shadow:0 12px 30px rgba(0,0,0,.07);min-width:0;max-width:100%}
.card h2{margin:0 0 12px;font-size:20px}
.preview{background:#111;border-radius:22px;overflow:hidden;display:flex;align-items:center;justify-content:center;min-height:540px;position:relative}
.preview img{max-width:100%;height:auto;display:block}
.badge{position:absolute;top:14px;left:14px;background:rgba(21,128,61,.94);color:#fff;border-radius:999px;padding:8px 11px;font-size:12px;font-weight:1000}
.meta{font-size:13px;color:#475467;line-height:1.75;word-break:break-word}
.meta b{color:#111}
.steps{display:grid;gap:10px}
.step{display:grid;grid-template-columns:34px 1fr auto;gap:10px;align-items:center;border:1px solid var(--line);border-radius:18px;padding:13px;background:#fff;transition:.2s}
.num{width:34px;height:34px;border-radius:13px;background:#fee2e2;color:#b91c1c;font-weight:1000;display:flex;align-items:center;justify-content:center}
.step.ready{border-color:#bbf7d0;background:#f0fdf4}.step.ready .num{background:#dcfce7;color:#15803d}
.step.wait{border-color:#fde68a;background:#fffbeb}.step.wait .num{background:#fef3c7;color:#92400e}
.step.run{border-color:#bfdbfe;background:#eff6ff}.step.run .num{background:#dbeafe;color:#1d4ed8}
.step h3{margin:0;font-size:15px}.step p{margin:3px 0 0;color:var(--muted);font-size:12px}
.status{font-size:11px;font-weight:1000;border-radius:999px;padding:7px 9px;background:#fee2e2;color:#b91c1c}
.step.ready .status{background:#dcfce7;color:#15803d}.step.wait .status{background:#fef3c7;color:#92400e}.step.run .status{background:#dbeafe;color:#1d4ed8}
.actions{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-top:14px}
.btn{border:0;border-radius:16px;background:var(--red);color:#fff;font-weight:1000;padding:14px 12px;text-decoration:none;text-align:center;cursor:pointer}
.btn.dark{background:#111}.btn.green{background:var(--green)}.btn.gold{background:var(--gold)}.btn.blue{background:var(--blue)}
.btn:disabled{opacity:.55;cursor:not-allowed}
pre{white-space:pre-wrap;word-break:break-word;overflow-wrap:anywhere;background:#101828;color:#e5e7eb;border-radius:18px;padding:14px;font-size:11px;max-height:330px;overflow:auto;max-width:100%;width:100%}
.small{font-size:12px;color:var(--muted);line-height:1.55}
#tonConnectMount{min-height:36px;margin-bottom:10px}
@media(max-width:930px){.grid{grid-template-columns:1fr}.preview{min-height:auto}.actions{grid-template-columns:1fr}.heroGrid{grid-template-columns:1fr}.hero h1{font-size:24px}}
</style>
</head>
<body>
<div class="wrap">
  <div class="top">
    <a class="back" href="/foodies-nft/mint.php">← Mint Factory</a>
    <div class="lang"><button id="langEN" class="active">EN</button><button id="langZH">中文</button></div>
  </div>

  <section class="hero">
    <div class="heroGrid">
      <div>
        <h1 data-i18n="title">🍽️ Foodies NFT Mint Process</h1>
        <p data-i18n="desc">Preflight cert, CDN metadata, QR-merged NFT artifact, TON payload, wallet connection, treasury contribution and final mint transaction before sending to Tonkeeper.</p>
        <div class="pillbar">
          <span class="pill">Raw GitHub CDN</span>
          <span class="pill">QR Artifact Ready</span>
          <span class="pill">TonConnect UI</span>
          <span class="pill">Getgems Metadata</span>
        </div>
      </div>
      <div class="wallet"><span id="walletLed" class="led"></span><span id="walletText">Wallet: Not connected</span></div>
    </div>
  </section>

  <div class="grid">
    <div class="card">
      <h2 data-i18n="artifact">NFT Artifact · QR Merged Ready</h2>
      <div class="preview">
        <span class="badge" id="artifactBadge">CHECKING...</span>
        <img id="artifactImg" loading="eager" decoding="sync" src="<?= h($rawArtifact) ?>?v=<?= time() ?>" alt="Ready QR NFT Artifact"
          onerror="this.onerror=null;this.src='<?= h($localArtifact) ?>?v=<?= time() ?>';">
      </div>
      <div class="actions">
        <a class="btn dark" href="<?= h($verify) ?>" target="_blank">Verify</a>
        <a class="btn dark" href="<?= h($rawMetadata) ?>" target="_blank">CDN Metadata</a>
        <button class="btn blue" id="refreshBtn">Refresh</button>
      </div>
      <p class="small">Primary preview uses raw GitHub CDN. If unavailable, local QR artifact fallback is used automatically.</p>
    </div>

    <div>
      <div class="card">
        <h2 data-i18n="mintInfo">Mint Information</h2>
        <div class="meta">
          <b>Cert UID:</b> <?= h($uid) ?><br>
          <b>Star Rating:</b> <?= h($stars) ?><br>
          <b>Verify URL:</b> <?= h($verify) ?><br>
          <b>CDN Artifact:</b> <?= h($rawArtifact) ?><br>
          <b>Local Artifact:</b> https://expressvisa.one<?= h($localArtifact) ?><br>
          <b>CDN Metadata:</b> <?= h($rawMetadata) ?><br>
          <b>Payload API:</b> https://expressvisa.one<?= h($payloadApi) ?>
        </div>
      </div>

      <div class="card" style="margin-top:14px">
        <h2 data-i18n="process">Step-by-step Mint Status</h2>
        <div id="steps" class="steps"></div>
        <div class="actions">
          <button class="btn green" id="connectBtn" data-i18n="connect">Connect Wallet</button>
          <button class="btn blue" id="precheckBtn">Run Preflight</button>
          <button class="btn green" id="payloadBtn" data-i18n="payload">Prepare Payload</button>
          <button class="btn gold" id="treasuryBtn" type="button">0.50 TON Treasury</button>
          <button class="btn green" id="mintNowBtn">Mint via Tonkeeper</button>
          <a class="btn dark" href="https://getgems.io/foodies" target="_blank">Getgems</a>
        </div>
      </div>

      <div class="card" style="margin-top:14px">
        <h2>TonConnect / Payload / JSON</h2>
        <div id="tonConnectMount"></div>
        <pre id="jsonBox">Initializing...</pre>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
const UID=<?= json_encode($uid) ?>;
const STARS=<?= json_encode((string)$stars) ?>;
const RAW_ARTIFACT=<?= json_encode($rawArtifact) ?>;
const LOCAL_ARTIFACT=<?= json_encode($localArtifact) ?>;
const RAW_METADATA=<?= json_encode($rawMetadata) ?>;

const I18N={
 en:{title:'🍽️ Foodies NFT Mint Process',desc:'Preflight cert, CDN metadata, QR-merged NFT artifact, TON payload, wallet connection, treasury contribution and final mint transaction before sending to Tonkeeper.',artifact:'NFT Artifact · QR Merged Ready',mintInfo:'Mint Information',process:'Step-by-step Mint Status',connect:'Connect Wallet',payload:'Prepare Payload',walletOff:'Wallet: Not connected',walletOn:'Wallet Connected: '},
 zh:{title:'🍽️ Foodies NFT 铸造流程',desc:'铸造前逐步检查证书、CDN 元数据、已合成 QR 的 NFT 图片、TON Payload、钱包连接、0.50 TON 平台贡献和最终 Tonkeeper 铸造交易。',artifact:'NFT 图片 · 已合成 QR',mintInfo:'铸造资料',process:'铸造状态步骤',connect:'连接钱包',payload:'准备 Payload',walletOff:'钱包：未连接',walletOn:'钱包已连接：'}
};

const state={wallet:false,precheck:false,artifact:false,metadata:false,payload:false,treasury:false,ready:false,lastPayload:null,ton:null};
const $=id=>document.getElementById(id);

function lang(){return localStorage.getItem('foodies_lang')==='zh'?'zh':'en'}
function t(k){return (I18N[lang()]||I18N.en)[k]||k}
function log(v){$('jsonBox').textContent=typeof v==='string'?v:JSON.stringify(v,null,2)}
function short(a){return a&&a.length>14?a.slice(0,7)+'...'+a.slice(-6):a}
function setStep(key,val){state[key]=!!val; renderSteps()}
function detectWallet(){
 const acct=state.ton?.account;
 if(acct?.address)return acct.address;
 for(const k of ['foodies_wallet','ton_wallet','wallet_address','connected_wallet','wallet']){
   const v=localStorage.getItem(k)||sessionStorage.getItem(k);
   if(v&&v.trim())return v.trim();
 }
 return '';
}
function syncWallet(){
 const w=detectWallet();
 const on=!!w;
 state.wallet=on;
 $('walletLed').classList.toggle('on',on);
 $('walletText').textContent=on?t('walletOn')+short(w):t('walletOff');
 renderSteps();
}
function syncLang(){
 document.documentElement.lang=lang()==='zh'?'zh-CN':'en';
 $('langEN').classList.toggle('active',lang()==='en');
 $('langZH').classList.toggle('active',lang()==='zh');
 document.querySelectorAll('[data-i18n]').forEach(el=>{el.textContent=t(el.dataset.i18n)});
 syncWallet();
}
function ton(){
 if(state.ton)return state.ton;
 if(!window.TON_CONNECT_UI?.TonConnectUI)throw new Error('TonConnect UI library not loaded');
 state.ton=new TON_CONNECT_UI.TonConnectUI({
   manifestUrl:'https://expressvisa.one/tonconnect-manifest.json',
   buttonRootId:'tonConnectMount'
 });
 state.ton.onStatusChange(()=>syncWallet());
 return state.ton;
}
function renderSteps(){
 const rows=[
  [1,'Certificate UID loaded',UID,'precheck'],
  [2,'Wallet connected','TonConnect UI active','wallet'],
  [3,'CDN metadata reachable',RAW_METADATA,'metadata'],
  [4,'NFT artifact with Verify QR ready','CDN + local fallback checked','artifact'],
  [5,'Mint payload prepared','Friendly EQ address only','payload'],
  [6,'0.50 TON treasury contribution prepared','Required before public mint','treasury'],
  [7,'Ready to mint','Send transaction via Tonkeeper','ready']
 ];
 $('steps').innerHTML=rows.map(([n,label,sub,key])=>{
   const ok=!!state[key];
   const cls=ok?'ready':(key==='wallet'||key==='treasury'?'wait':'run');
   const st=ok?'READY':(cls==='wait'?'WAITING':'CHECK');
   return `<div class="step ${cls}"><div class="num">${n}</div><div><h3>${label}</h3><p>${sub}</p></div><span class="status">${st}</span></div>`;
 }).join('');
 $('mintNowBtn').disabled=!(state.wallet&&state.payload);
}
async function headOk(url){
 try{
   const r=await fetch(url+(url.includes('?')?'&':'?')+'v='+Date.now(),{cache:'no-store',method:'GET'});
   return r.ok;
 }catch(e){return false}
}
async function precheck(){
 log('Running preflight checks...');
 setStep('precheck',true);
 const [metaOk,artifactOk]=await Promise.all([headOk(RAW_METADATA),headOk(RAW_ARTIFACT)]);
 setStep('metadata',metaOk);
 setStep('artifact',artifactOk);
 $('artifactBadge').textContent=artifactOk?'CDN QR ARTIFACT READY':'LOCAL FALLBACK';
 $('artifactBadge').style.background=artifactOk?'rgba(21,128,61,.94)':'rgba(245,158,11,.94)';
 let mintCheck=null;
 try{
   const r=await fetch('/foodies-nft/api/mint-check.php?uid='+encodeURIComponent(UID)+'&stars='+encodeURIComponent(STARS),{cache:'no-store'});
   const txt=await r.text();
   try{mintCheck=JSON.parse(txt)}catch(e){mintCheck={raw:txt}}
 }catch(e){mintCheck={warning:String(e)}}
 log({ok:true,uid:UID,stars:STARS,cdn_metadata:metaOk,cdn_artifact:artifactOk,raw_metadata:RAW_METADATA,raw_artifact:RAW_ARTIFACT,local_artifact:LOCAL_ARTIFACT,mint_check:mintCheck});
}
async function connect(){
 try{await ton().openModal();syncWallet()}catch(e){log('Connect error: '+(e.message||e))}
}
async function payload(){
 if(!detectWallet()){await connect(); if(!detectWallet())throw new Error('Wallet not connected');}
 log('Preparing mint payload...');
 const r=await fetch('/foodies-nft/api/build-mint-payload.php?fresh=1&uid='+encodeURIComponent(UID)+'&stars='+encodeURIComponent(STARS)+'&to='+encodeURIComponent(detectWallet()),{cache:'no-store'});
 const txt=await r.text();
 let j; try{j=JSON.parse(txt)}catch(e){throw new Error('Payload API returned non-JSON: '+txt.slice(0,180))}
 if(!j.ok)throw new Error(j.error||'payload_error');
 if(String(j.messages?.[0]?.address||'').startsWith('0:'))throw new Error('Raw 0: address blocked');
 state.lastPayload=j;
 setStep('payload',true);
 state.ready=state.wallet&&state.payload;
 renderSteps();
 log(j);
 return j;
}
async function treasuryContribution(){
 if(!detectWallet()){await connect(); if(!detectWallet())return;}
 const fd=new FormData();
 fd.append('action','mint_treasury_contribution');
 fd.append('uid',UID);fd.append('stars',STARS);fd.append('wallet',detectWallet());fd.append('amount_ton','0.50');
 try{
   const res=await fetch('/foodies-nft/api/ton-withdraw.php',{method:'POST',body:fd});
   const txt=await res.text();
   let j; try{j=JSON.parse(txt)}catch(e){j={raw:txt}}
   setStep('treasury',true);
   log(j);
 }catch(e){
   log('Treasury preparation failed: '+(e.message||e));
 }
}
async function mintNow(){
 try{
   const p=state.lastPayload||await payload();
   const tx=p.tonconnect || {validUntil:Math.floor(Date.now()/1000)+600,messages:p.messages};
   const result=await ton().sendTransaction(tx);
   log({ok:true,status:'MINT_TX_SENT',payload:p,result});
 }catch(e){log('Mint failed: '+(e.message||e))}
}
$('langEN').onclick=()=>{localStorage.setItem('foodies_lang','en');syncLang()};
$('langZH').onclick=()=>{localStorage.setItem('foodies_lang','zh');syncLang()};
$('connectBtn').onclick=connect;
$('precheckBtn').onclick=precheck;
$('payloadBtn').onclick=()=>payload().catch(e=>log('Payload error: '+(e.message||e)));
$('treasuryBtn').onclick=treasuryContribution;
$('mintNowBtn').onclick=mintNow;
$('refreshBtn').onclick=()=>{$('artifactImg').src=RAW_ARTIFACT+'?v='+Date.now();precheck()};
window.addEventListener('storage',syncWallet);
try{ton()}catch(e){log('TonConnect init warning: '+(e.message||e))}
syncLang();
precheck();
})();
</script>
<script src="/foodies-nft/assets/foodies-footer-nav.js?v=foodies-mint-ultimate" defer></script>
</body>
</html>
