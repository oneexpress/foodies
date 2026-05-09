<?php
declare(strict_types=1);
header('Content-Type: text/html; charset=UTF-8');

$uid = trim((string)($_GET['uid'] ?? $_GET['mint_uid'] ?? 'FOODIES-MINT-0001'));
$stars = (int)($_GET['stars'] ?? $_GET['star'] ?? 5);
if (!in_array($stars, [1,3,5], true)) $stars = 5;

function h($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

$safe = preg_replace('/[^A-Za-z0-9._-]/', '-', $uid);
$cdnBase = 'https://cdn.jsdelivr.net/gh/oneexpress/foodies@main/public/metadata/foodies';
$verify = 'https://expressvisa.one/foodies-nft/verify.php?uid=' . rawurlencode($uid);

$cdnArtifact = $cdnBase . '/generated/' . $safe . '-' . $stars . 'star.png';
$cdnMeta = $cdnBase . '/items/' . $safe . '.json';
$payloadApi = '/foodies-nft/api/build-mint-payload.php?fresh=1&uid=' . rawurlencode($uid) . '&stars=' . $stars;
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<title>Foodies NFT Mint Process</title>
<link rel="stylesheet" href="/foodies-nft/assets/foodies-footer-nav.css?v=cdn-mint-ready">
<script src="https://unpkg.com/@tonconnect/ui@latest/dist/tonconnect-ui.min.js"></script>
<style>
:root{--red:#E60012;--dark:#B8000E;--bg:#F7F8FA;--ink:#101828;--muted:#667085;--line:#E5E7EB;--green:#22c55e;--gold:#f59e0b}
*{box-sizing:border-box}body{margin:0;background:radial-gradient(circle at top,#fff 0,#fff3f4 28%,var(--bg) 62%);font-family:Arial,Helvetica,sans-serif;color:var(--ink);padding-bottom:128px;overflow-x:hidden}.wrap{width:100%;max-width:1240px;margin:0 auto;padding:18px;overflow:hidden}.top{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:14px}.back{background:#111;color:#fff;text-decoration:none;border-radius:999px;padding:11px 15px;font-weight:1000}.lang{display:flex;gap:8px}.lang button{border:1px solid var(--line);background:#fff;border-radius:999px;padding:9px 13px;font-weight:1000;cursor:pointer}.lang .active{background:var(--red);color:#fff}.hero{background:linear-gradient(135deg,var(--red),var(--dark));border-radius:30px;color:#fff;padding:24px;box-shadow:0 20px 52px rgba(230,0,18,.25)}.hero h1{margin:0 0 8px;font-size:30px}.hero p{margin:0;line-height:1.55;opacity:.92}.wallet{margin-top:14px;display:flex;align-items:center;gap:10px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.24);border-radius:18px;padding:12px 14px;font-weight:1000}.led{width:12px;height:12px;border-radius:50%;background:#ef4444;box-shadow:0 0 0 5px rgba(239,68,68,.13),0 0 16px rgba(239,68,68,.75)}.led.on{background:var(--green);box-shadow:0 0 0 5px rgba(34,197,94,.16),0 0 18px rgba(34,197,94,.95)}.grid{display:grid;grid-template-columns:minmax(0,430px) minmax(0,1fr);gap:16px;margin-top:16px;min-width:0}.card{background:#fff;border:1px solid var(--line);border-radius:24px;padding:18px;box-shadow:0 12px 30px rgba(0,0,0,.07);min-width:0;max-width:100%;overflow:hidden}.preview{background:#111;border-radius:22px;overflow:hidden;display:flex;align-items:center;justify-content:center;min-height:520px}.preview img{max-width:100%;height:auto;display:block}.meta{font-size:13px;color:#475467;line-height:1.75;word-break:break-word;overflow-wrap:anywhere;max-width:100%}.meta b{color:#111}.steps{display:grid;gap:11px}.step{display:grid;grid-template-columns:34px minmax(0,1fr) auto;gap:10px;align-items:center;border:1px solid var(--line);border-radius:18px;padding:13px;background:#fff;min-width:0;max-width:100%;overflow:hidden}.num{width:34px;height:34px;border-radius:13px;background:#fff1f2;color:var(--red);font-weight:1000;display:flex;align-items:center;justify-content:center}.step.ready .num{background:#dcfce7;color:#15803d}.step.wait .num{background:#fef3c7;color:#92400e}.step h3{margin:0;font-size:15px}.step p{margin:3px 0 0;color:var(--muted);font-size:12px;overflow-wrap:anywhere}.status{font-size:11px;font-weight:1000;border-radius:999px;padding:7px 9px;background:#fee2e2;color:#b91c1c}.step.ready .status{background:#dcfce7;color:#15803d}.step.wait .status{background:#fef3c7;color:#92400e}.actions{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-top:14px}.btn{border:0;border-radius:16px;background:var(--red);color:#fff;font-weight:1000;padding:14px 12px;text-decoration:none;text-align:center;cursor:pointer}.btn.dark{background:#111}.btn.green{background:var(--green)}pre{white-space:pre-wrap;word-break:break-word;overflow-wrap:anywhere;background:#101828;color:#e5e7eb;border-radius:18px;padding:14px;font-size:11px;max-height:310px;max-width:100%;overflow:auto}@media(max-width:900px){.grid{grid-template-columns:1fr}.preview{min-height:auto}.actions{grid-template-columns:1fr}.hero h1{font-size:24px}}
</style>
</head>
<body>
<div class="wrap">
  <div class="top">
    <a class="back" href="/foodies-nft/mint.php">← Mint Factory</a>
    <div class="lang"><button id="langEN" class="active">EN</button><button id="langZH">中文</button></div>
  </div>

  <section class="hero">
    <h1 data-i18n="title">🍽️ Foodies NFT Mint Process</h1>
    <p data-i18n="desc">Ready for NFT mint: QR-merged artifact and metadata are loaded from immutable CDN, then TON payload is prepared for Tonkeeper.</p>
    <div class="wallet"><span id="walletLed" class="led"></span><span id="walletText">Wallet: Not connected</span></div>
  </section>

  <div class="grid">
    <div class="card">
      <h2 data-i18n="artifact">Ready QR NFT Artifact</h2>
      <div class="preview"><img id="artifactImg" loading="eager" decoding="sync" src="<?= h($cdnArtifact) ?>?v=<?= time() ?>" alt="Ready QR NFT Artifact"></div>
      <div class="actions">
        <a class="btn dark" href="<?= h($verify) ?>" target="_blank">Verify</a>
        <a class="btn dark" href="<?= h($cdnMeta) ?>" target="_blank">CDN Metadata</a>
        <button class="btn" id="refreshBtn">Refresh</button>
      </div>
    </div>

    <div>
      <div class="card"><h2 data-i18n="mintInfo">Mint Information</h2><div class="meta">
        <b>Cert UID:</b> <?= h($uid) ?><br>
        <b>Star Rating:</b> <?= h($stars) ?><br>
        <b>Verify URL:</b> <?= h($verify) ?><br>
        <b>CDN Artifact:</b> <?= h($cdnArtifact) ?><br>
        <b>CDN Metadata:</b> <?= h($cdnMeta) ?><br>
        <b>Payload API:</b> https://expressvisa.one<?= h($payloadApi) ?>
      </div></div>

      <div class="card" style="margin-top:14px">
        <h2 data-i18n="process">Minting Process</h2>
        <div id="steps" class="steps"></div>
        <div class="actions">
          <button class="btn" id="connectBtn" data-i18n="connect">Connect Wallet</button>
          <button class="btn green" id="payloadBtn" data-i18n="payload">Prepare Mint Payload</button>
          <button class="btn green" id="mintNowBtn">Mint via Tonkeeper</button>
          <a class="btn dark" href="https://getgems.io/foodies" target="_blank">Getgems</a>
        </div>
      </div>

      <div class="card" style="margin-top:14px"><h2>Payload / JSON</h2><pre id="jsonBox">Loading...</pre></div>
    </div>
  </div>
</div>

<script>
(function(){
const UID=<?= json_encode($uid) ?>, STARS=<?= json_encode((string)$stars) ?>;
const CDN_ARTIFACT=<?= json_encode($cdnArtifact) ?>, CDN_META=<?= json_encode($cdnMeta) ?>;
const I18N={en:{title:'🍽️ Foodies NFT Mint Process',desc:'Ready for NFT mint: QR-merged artifact and metadata are loaded from immutable CDN, then TON payload is prepared for Tonkeeper.',artifact:'Ready QR NFT Artifact',mintInfo:'Mint Information',process:'Minting Process',connect:'Connect Wallet',payload:'Prepare Mint Payload',walletOff:'Wallet: Not connected',walletOn:'Wallet Connected: '},zh:{title:'🍽️ Foodies NFT 铸造流程',desc:'NFT 已准备：已合成 QR 的图片与 Metadata 均使用 CDN，随后准备 TON 钱包铸造 Payload。',artifact:'已合成 QR 的 NFT 图片',mintInfo:'铸造资料',process:'铸造流程',connect:'连接钱包',payload:'准备铸造 Payload',walletOff:'钱包：未连接',walletOn:'钱包已连接：'}};
const KEYS=['foodies_wallet','ton_wallet','wallet_address','connected_wallet','wallet'];
function short(a){return a&&a.length>14?a.slice(0,7)+'...'+a.slice(-6):a}
function wallet(){for(const k of KEYS){const v=localStorage.getItem(k)||sessionStorage.getItem(k);if(v&&v.trim())return v.trim()}return ''}
function lang(){return localStorage.getItem('foodies_lang')==='zh'?'zh':'en'}
function t(k){return (I18N[lang()]||I18N.en)[k]||k}
function syncLang(){document.documentElement.lang=lang()==='zh'?'zh-CN':'en';document.getElementById('langEN').classList.toggle('active',lang()==='en');document.getElementById('langZH').classList.toggle('active',lang()==='zh');document.querySelectorAll('[data-i18n]').forEach(el=>{el.textContent=t(el.dataset.i18n)});syncWallet()}
function syncWallet(){const w=wallet(),on=!!w;document.getElementById('walletLed').classList.toggle('on',on);document.getElementById('walletText').textContent=on?t('walletOn')+short(w):t('walletOff')}
async function connect(){const old=wallet();if(old){if(confirm('Disconnect '+short(old)+'?')){KEYS.forEach(k=>localStorage.removeItem(k));syncWallet()}return}const w=prompt('Paste TON wallet address');if(w&&w.trim()){localStorage.setItem('foodies_wallet',w.trim());localStorage.setItem('ton_wallet',w.trim());syncWallet()}}
function renderSteps(extra){const arr=[
{step:1,label:'QR-merged NFT artifact',status:'ready'},
{step:2,label:'CDN item metadata JSON',status:'ready'},
{step:3,label:'TON wallet connection',status:wallet()?'ready':'waiting_wallet'},
{step:4,label:'TonConnect mint payload',status:extra||'waiting_payload'}
];document.getElementById('steps').innerHTML=arr.map(x=>{const cls=x.status==='ready'?'ready':(x.status.indexOf('waiting')>=0?'wait':'');return `<div class="step ${cls}"><div class="num">${x.step}</div><div><h3>${x.label}</h3><p>${x.status}</p></div><span class="status">${x.status}</span></div>`}).join('')}
async function precheck(){renderSteps();try{const [img,meta]=await Promise.all([fetch(CDN_ARTIFACT,{cache:'no-store',method:'HEAD'}),fetch(CDN_META,{cache:'no-store'})]);const j=await meta.json();document.getElementById('jsonBox').textContent=JSON.stringify({ok:true,artifact_status:img.status,cdn_metadata:j},null,2)}catch(e){document.getElementById('jsonBox').textContent='CDN check failed: '+e.message}}
async function payload(){const w=wallet();if(!w){alert('Connect wallet first');connect();renderSteps();return null}const res=await fetch('/foodies-nft/api/build-mint-payload.php?fresh=1&uid='+encodeURIComponent(UID)+'&stars='+encodeURIComponent(STARS)+'&to='+encodeURIComponent(w),{cache:'no-store'});const j=await res.json();document.getElementById('jsonBox').textContent=JSON.stringify(j,null,2);renderSteps(j.ok?'ready':'payload_error');return j}
async function mintNow(){const w=wallet();if(!w){alert('Connect wallet first');connect();return}try{const j=await payload();if(!j||!j.ok||!j.tonconnect)throw new Error(j?.error||'missing tonconnect payload');if(!window.tonConnectUI){alert('Payload ready. TonConnect UI not initialized.');return}const result=await window.tonConnectUI.sendTransaction(j.tonconnect);document.getElementById('jsonBox').textContent=JSON.stringify({payload:j,result},null,2).slice(0,12000)}catch(e){alert('Mint failed: '+e.message)}}
document.getElementById('langEN').onclick=()=>{localStorage.setItem('foodies_lang','en');syncLang()};document.getElementById('langZH').onclick=()=>{localStorage.setItem('foodies_lang','zh');syncLang()};document.getElementById('connectBtn').onclick=connect;document.getElementById('payloadBtn').onclick=payload;document.getElementById('mintNowBtn').onclick=mintNow;document.getElementById('refreshBtn').onclick=()=>{document.getElementById('artifactImg').src=CDN_ARTIFACT+'?v='+Date.now();precheck()};window.addEventListener('storage',syncWallet);syncLang();precheck();
})();
</script>
<script src="/foodies-nft/assets/foodies-footer-nav.js?v=cdn-mint-ready" defer></script>
</body>
</html>
