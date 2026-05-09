<?php
declare(strict_types=1); ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Foodies NFT Launcher</title>
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<link rel="stylesheet" href="/foodies-nft/assets/foodies-nft.css?v=launcher-v1">
<link rel="stylesheet" href="/foodies-nft/assets/foodies-local-bottom-nav.css?v=launcher-v1">
<style>
body{background:#f7f8fa;font-family:Arial,sans-serif;margin:0;color:#151515}
.wrap{max-width:1180px;margin:auto;padding:18px 14px 96px}
.hero{background:linear-gradient(135deg,#111,#320006);color:white;border-radius:28px;padding:22px;display:flex;justify-content:space-between;gap:16px;align-items:center;flex-wrap:wrap}
.eyebrow{font-weight:900;color:#ffcc00;letter-spacing:2px}
.hero h1{margin:.25rem 0;font-size:34px}
.hero-actions{display:flex;gap:10px;flex-wrap:wrap}
.hero-actions a,.hero-actions button{border:0;border-radius:14px;padding:11px 15px;font-weight:900;text-decoration:none;background:white;color:#111}
.launcher{display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-top:18px}
.card{background:#fff;border:1px solid #e5e7eb;border-radius:26px;padding:20px;box-shadow:0 10px 28px #0001}
.card h2{margin-top:0}
.action{background:#E60012;color:#fff;border:0;border-radius:16px;padding:14px 18px;font-weight:1000;font-size:16px;cursor:pointer}
.grid{display:grid;grid-template-columns:1.05fr .95fr;gap:18px;margin-top:18px}
.form{display:grid;gap:14px}
.row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
label{font-weight:900;font-size:13px}
input,select,textarea{width:100%;border:1px solid #ddd;border-radius:14px;padding:12px;font-size:15px;margin-top:6px;box-sizing:border-box}
textarea{min-height:86px}
.tiers,.issue-types{display:grid;grid-template-columns:repeat(3,1fr);gap:10px}
.issue-types{grid-template-columns:repeat(2,1fr)}
.tier,.issue-type{border:2px solid #ddd;border-radius:18px;padding:14px;text-align:center;cursor:pointer;font-weight:900}
.tier input,.issue-type input{display:none}.tier:has(input:checked),.issue-type:has(input:checked){border-color:#E60012;background:#fff1f2}
.pricebox{border:2px solid #E60012;background:#fff1f2;border-radius:18px;padding:14px;font-weight:1000}
.submit{background:#E60012;color:white;border:0;border-radius:16px;padding:15px 18px;font-weight:1000;font-size:16px}
.note{color:#666;font-size:13px;line-height:1.45}.hidden{display:none}
.issued-list{display:grid;gap:10px;max-height:380px;overflow:auto}
.issued-item{border:1px solid #ddd;border-radius:16px;padding:12px;background:#fafafa;cursor:pointer}
.issued-item:hover{border-color:#E60012;background:#fff1f2}
.issued-item b{display:block}
@media(max-width:860px){.launcher,.grid,.row,.tiers,.issue-types{grid-template-columns:1fr}}
</style>
<link rel="stylesheet" href="/foodies-nft/assets/foodies-footer-nav.css?v=footer-only-final">
</head>
<body>
<main class="wrap">

</main>

<script>
const prices={1:100,3:300,5:500};
const form=document.getElementById('issueForm');
const msg=document.getElementById('statusMsg');
const result=document.getElementById('resultBox');
const priceText=document.getElementById('priceText');
const reissueFields=document.getElementById('reissueFields');

function currentWallet(){
  return (window.__991WalletAddress||localStorage.getItem('ton_wallet')||localStorage.getItem('wallet_address')||localStorage.getItem('connected_wallet')||'').trim();
}
function refresh(){
  const star=form.querySelector('[name="star_class"]:checked')?.value || '1';
  const issue=form.querySelector('[name="issue_type"]:checked')?.value || 'new';
  priceText.textContent=prices[star];
  reissueFields.classList.toggle('hidden', issue!=='reissue');
  form.parent_cert_uid.required = issue==='reissue';
  document.getElementById('ownerWallet').value=currentWallet();
}
form.querySelectorAll('[name="star_class"],[name="issue_type"]').forEach(x=>x.addEventListener('change',refresh));
refresh();

async function loadIssuedFoodies(){
  const w=currentWallet();
  const box=document.getElementById('issuedList');
  if(!w){box.textContent='Connect wallet first.';return;}
  box.textContent='Loading issued Foodies NFT...';
  const r=await fetch('/foodies-nft/api/list-issued.php?wallet='+encodeURIComponent(w));
  const j=await r.json().catch(()=>({ok:false,rows:[]}));
  if(!j.ok){box.textContent='Failed to load issued NFT.';return;}
  if(!j.rows.length){box.textContent='No issued Foodies NFT found for this wallet.';return;}
  box.innerHTML=j.rows.map(row=>`
    <div class="issued-item" data-uid="${row.cert_uid}">
      <b>${row.cert_uid} · ${row.star_class} STAR · ${row.status}</b>
      ${row.chef_name||''} / ${row.brand_name||''}<br>
      ${row.food_title||''}
    </div>
  `).join('');
  box.querySelectorAll('.issued-item').forEach(el=>{
    el.onclick=()=>{
      const uid=el.dataset.uid;
      document.getElementById('parentCertUid').value=uid;
      document.querySelector('[name="issue_type"][value="reissue"]').click();
      form.scrollIntoView({behavior:'smooth'});
    };
  });
}
window.loadIssuedFoodies=loadIssuedFoodies;

form.addEventListener('submit',async(e)=>{
  e.preventDefault();
  const w=currentWallet();
  document.getElementById('ownerWallet').value=w;
  if(!w){msg.textContent='Connect wallet required.';alert('Please CONNECT WALLET before issuing Foodies NFT.');return;}
  msg.textContent='Submitting...';
  const fd=new FormData(form);
  const r=await fetch('/foodies-nft/api/submit-cert.php',{method:'POST',body:fd});
  const j=await r.json().catch(()=>({ok:false,error:'Bad JSON'}));
  if(!j.ok){msg.textContent='Error: '+(j.error||'submit failed');return;}
  msg.textContent=(j.issue_type==='reissue'?'Reissue':'New issue')+' created: '+j.cert_uid+' · '+j.vshare_price+' vSHARE';
  result.innerHTML='<b>Cert UID:</b> '+j.cert_uid+'<br><b>Issue Type:</b> '+j.issue_type+'<br><b>Price:</b> '+j.vshare_price+' vSHARE<br><b>Verify:</b> <a href="'+j.verify_url+'" target="_blank">'+j.verify_url+'</a>';
});
</script>

<script src="/foodies-nft/assets/foodies-nft.js?v=1"></script>
<script src="/assets/js/991-wallet-sync.js?v=991-unified"></script>
<script src="/assets/991-balance-sync.js?v=991-balance-sync-ux-v1" defer></script>
<script src="/foodies-nft/assets/foodies-local-bottom-nav.js?v=launcher-v1" defer></script>
<script src="/foodies-nft/assets/foodies-footer-nav.js?v=footer-only-final" defer></script>
</body>
</html>
