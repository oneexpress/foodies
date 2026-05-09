<?php
declare(strict_types=1); ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Foodies NFT Launcher</title>
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<link rel="stylesheet" href="/foodies-nft/assets/foodies-footer-nav.css?v=compact-row-v1">
<style>
:root{--red:#E60012;--dark:#111;--soft:#fff1f2;--line:#e5e7eb}
*{box-sizing:border-box}
body{margin:0;background:#f7f8fa;color:#111;font-family:Arial,system-ui,sans-serif;padding-bottom:108px}
.wrap{max-width:1120px;margin:auto;padding:16px}
.hero{background:linear-gradient(135deg,#111,#3a0008);color:#fff;border-radius:24px;padding:18px;display:flex;justify-content:space-between;gap:14px;align-items:center;flex-wrap:wrap}
.hero h1{margin:4px 0;font-size:28px}.eyebrow{color:#ffd54a;font-weight:900;letter-spacing:1.5px}.muted{color:#666;font-size:13px;line-height:1.4}
.tabs{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin:14px 0}
.tab{border:2px solid var(--line);background:#fff;border-radius:18px;padding:14px;font-weight:1000;cursor:pointer;text-align:center}
.tab.active{border-color:var(--red);background:var(--soft);color:var(--red)}
.grid{display:grid;grid-template-columns:1.05fr .95fr;gap:16px}.card{background:#fff;border:1px solid var(--line);border-radius:22px;padding:18px;box-shadow:0 10px 26px #0001}
h2{margin:0 0 12px;font-size:24px}.form{display:grid;gap:12px}.row{display:grid;grid-template-columns:1fr 1fr;gap:10px}
label{font-weight:900;font-size:13px}input,select,textarea{width:100%;border:1px solid #ddd;border-radius:14px;padding:11px;font-size:15px;margin-top:5px}textarea{min-height:68px}
.tiers{display:grid;grid-template-columns:repeat(3,1fr);gap:8px}.tier{border:2px solid #ddd;border-radius:16px;padding:12px;text-align:center;font-weight:1000;cursor:pointer;background:#fff}.tier input{display:none}.tier:has(input:checked){border-color:var(--red);background:var(--soft);color:var(--red)}
.submit,.btn{background:var(--red);color:#fff;border:0;border-radius:15px;padding:13px 16px;font-weight:1000;font-size:15px;cursor:pointer;text-decoration:none;display:inline-block;text-align:center}
.output{border:2px solid var(--red);background:var(--soft);border-radius:16px;padding:12px;font-weight:900;margin:8px 0}.hidden{display:none!important}
.issued-list{display:grid;gap:8px;max-height:300px;overflow:auto}.issued-item{border:1px solid #ddd;border-radius:14px;padding:10px;background:#fafafa;cursor:pointer}.issued-item:hover,.issued-item.selected{border-color:var(--red);background:var(--soft)}
.compact-note{font-size:12px;color:#777}.status{font-weight:900;color:var(--red)}
@media(max-width:880px){.grid,.row,.tiers{grid-template-columns:1fr}.hero h1{font-size:24px}}
</style>
</head>
<body>
<main class="wrap">
  <section class="hero">
    <div>
      <div class="eyebrow">EXPRESSVISA 991 · FOODIES NFT</div>
      <h1>Foodies RWA Reputation Card</h1>
      <div>Issue new Foodies NFT or re-issue an existing cert from the same wallet.</div>
    </div>
    <div>
      <a class="btn" href="https://getgems.io/foodies" target="_blank" rel="noopener">💎 NFT Marketplace</a>
    </div>
  </section>

  <div class="tabs">
    <button class="tab active" id="tabIssue" type="button">🆕 Issue New</button>
    <button class="tab" id="tabReissue" type="button">♻️ Re-Issue Existing</button>
  </div>

  <section class="grid">
    <div class="card">
      <h2 id="formTitle">Issue New Foodies NFT</h2>

      <form id="issueForm" class="form" enctype="multipart/form-data">
        <input type="hidden" name="owner_wallet" id="ownerWallet">
        <input type="hidden" name="issuer_wallet" id="issuerWallet">
        <input type="hidden" name="issue_type" id="issueType" value="new">
        <input type="hidden" name="parent_cert_uid" id="parentCertUid">

        <div id="newFields">
          <div class="row">
            <label>Chef Real Name *
              <input name="chef_name" placeholder="Chef real name">
            </label>
            <label>Brand / Restaurant Name *
              <input name="brand_name" placeholder="Brand name">
            </label>
          </div>

          <div class="row">
            <label>Food / Signature Dish *
              <input name="food_title" placeholder="Signature dish">
            </label>
            <label>Business Type *
              <select name="business_type">
                <option>HomeChise Unit</option>
                <option>Hawker Stall Unit</option>
                <option>Foodcourt F&B Unit</option>
                <option>Foodtruck Unit</option>
                <option>Cloud Kitchen Unit</option>
              </select>
            </label>
          </div>

          <div class="row">
            <label>Location / Address *
              <input name="location_text" placeholder="Food location / address">
            </label>
            <label>Google Maps Link
              <input name="google_maps_url" placeholder="Auto/manual Google Maps link">
            </label>
          </div>

          <label>Signature Food Review
            <textarea name="signature_food_review" maxlength="300" placeholder="Signature food review, maximum 300 characters"></textarea>
            <div class="compact-note"><span id="reviewCount">0</span>/300 characters</div>
          </label>

          <div class="row">
            <label>Signature Food Upload
              <input type="file" name="signature_food_upload" accept="image/*">
            </label>
            <label>Social Link Type
              <select name="social_link_type">
                <option value="">Select social link type</option>
                <option value="fb">Facebook / FB</option>
                <option value="tiktok">TikTok</option>
                <option value="xhs">XHS / 小红书</option>
              </select>
            </label>
          </div>

          <label>Social Link URL
            <input name="social_link_url" placeholder="Paste FB, TikTok, or XHS link">
          </label>
        </div>

        <div id="reissueFields" class="hidden">
          <p class="compact-note">Re-issue mode only needs: select issued cert + choose star rating.</p>
          <button type="button" class="btn" onclick="loadIssuedFoodies()">Load My Issued Certs</button>
          <div id="issuedList" class="issued-list"></div>
          <div class="output">Selected Cert: <span id="selectedCertText">None</span></div>
        </div>

        <h2>Star Rating</h2>
        <div class="tiers">
          <label class="tier">⭐<br>1 Star<br>100 vSHARE<input type="radio" name="star_class" value="1" checked></label>
          <label class="tier">⭐⭐⭐<br>3 Stars<br>300 vSHARE<input type="radio" name="star_class" value="3"></label>
          <label class="tier">⭐⭐⭐⭐⭐<br>5 Stars<br>500 vSHARE<input type="radio" name="star_class" value="5"></label>
        </div>

        <div class="output">Price: <span id="priceText">100</span> vSHARE</div>
        <button class="submit" type="submit" id="submitBtn">Issue Foodies NFT</button>
        <div id="statusMsg" class="status"></div>
      </form>
    </div>

    <aside class="card">
      <h2>Production Output</h2>
      <p class="muted">Wallet required. Cert UID, verify URL, Google Maps link, PDF cert, and NFT metadata are generated after submit.</p>
      <div class="output">⭐ 1 Star = 100 vSHARE</div>
      <div class="output">⭐⭐⭐ 3 Stars = 300 vSHARE</div>
      <div class="output">⭐⭐⭐⭐⭐ 5 Stars = 500 vSHARE</div>
      <div id="resultBox" class="muted"></div>
    </aside>
  </section>
</main>

<script>
const prices={1:100,3:300,5:500};
const form=document.getElementById('issueForm');
const msg=document.getElementById('statusMsg');
const result=document.getElementById('resultBox');
const priceText=document.getElementById('priceText');
const issueType=document.getElementById('issueType');

function currentWallet(){
  return (window.__991WalletAddress||localStorage.getItem('ton_wallet')||localStorage.getItem('wallet_address')||localStorage.getItem('connected_wallet')||'').trim();
}
function setMode(mode){
  issueType.value=mode;
  document.getElementById('tabIssue').classList.toggle('active',mode==='new');
  document.getElementById('tabReissue').classList.toggle('active',mode==='reissue');
  document.getElementById('newFields').classList.toggle('hidden',mode==='reissue');
  document.getElementById('reissueFields').classList.toggle('hidden',mode!=='reissue');
  document.getElementById('formTitle').textContent=mode==='reissue'?'Re-Issue Existing Foodies NFT':'Issue New Foodies NFT';
  document.getElementById('submitBtn').textContent=mode==='reissue'?'Re-Issue Foodies NFT':'Issue Foodies NFT';
}
function refresh(){
  const star=form.querySelector('[name="star_class"]:checked')?.value || '1';
  priceText.textContent=prices[star];
  const w=currentWallet();
  document.getElementById('ownerWallet').value=w;
  document.getElementById('issuerWallet').value=w;
}
document.getElementById('tabIssue').onclick=()=>setMode('new');
document.getElementById('tabReissue').onclick=()=>{setMode('reissue');loadIssuedFoodies();};
form.querySelectorAll('[name="star_class"]').forEach(x=>x.addEventListener('change',refresh));
refresh();

const review=document.querySelector('[name="signature_food_review"]');
if(review){
  const count=document.getElementById('reviewCount');
  const syncReviewCount=()=>{ if(count) count.textContent=String(review.value.length); };
  review.addEventListener('input',syncReviewCount);
  syncReviewCount();
}

async function loadIssuedFoodies(){
  const w=currentWallet();
  const box=document.getElementById('issuedList');
  if(!w){box.textContent='Connect wallet first.';return;}
  box.textContent='Loading...';
  const r=await fetch('/foodies-nft/api/list-issued.php?wallet='+encodeURIComponent(w));
  const j=await r.json().catch(()=>({ok:false,rows:[]}));
  if(!j.ok){box.textContent='Failed to load issued certs.';return;}
  if(!j.rows.length){box.textContent='No issued cert found for this wallet.';return;}
  box.innerHTML=j.rows.map(row=>`
    <div class="issued-item" data-uid="${row.cert_uid||''}">
      <b>${row.cert_uid||''}</b>
      ${(row.star_class||'')} STAR · ${(row.chef_name||'')} / ${(row.brand_name||'')}
    </div>`).join('');
  box.querySelectorAll('.issued-item').forEach(el=>{
    el.onclick=()=>{
      box.querySelectorAll('.issued-item').forEach(x=>x.classList.remove('selected'));
      el.classList.add('selected');
      document.getElementById('parentCertUid').value=el.dataset.uid;
      document.getElementById('selectedCertText').textContent=el.dataset.uid;
    };
  });
}
window.loadIssuedFoodies=loadIssuedFoodies;

form.addEventListener('submit',async(e)=>{
  e.preventDefault();
  refresh();
  const w=currentWallet();
  if(!w){msg.textContent='Connect wallet required.';alert('Please connect wallet first.');return;}
  if(issueType.value==='reissue' && !document.getElementById('parentCertUid').value){
    msg.textContent='Please select issued cert first.';return;
  }
  msg.textContent='Submitting...';
  const fd=new FormData(form);
  const r=await fetch('/foodies-nft/api/submit-cert.php',{method:'POST',body:fd});
  const j=await r.json().catch(()=>({ok:false,error:'Bad JSON'}));
  if(!j.ok){msg.textContent='Error: '+(j.error||'submit failed');return;}
  msg.textContent='Created: '+j.cert_uid+' · '+j.vshare_price+' vSHARE';
  result.innerHTML='<b>Cert UID:</b> '+j.cert_uid+'<br><b>Price:</b> '+j.vshare_price+' vSHARE<br><b>Verify:</b> <a href="'+j.verify_url+'" target="_blank">'+j.verify_url+'</a>';
});
const urlMode=new URLSearchParams(location.search).get('mode');
if(urlMode==='reissue'||urlMode==='list') setMode('reissue'); else setMode('new');
</script>

<script src="/assets/js/991-wallet-sync.js?v=991-unified"></script>
<script src="/foodies-nft/assets/foodies-footer-nav.js?v=wallet-home-v2" defer></script>
</body>
</html>
