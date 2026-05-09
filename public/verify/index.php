<?php
declare(strict_types=1);
header("Content-Type: text/html; charset=UTF-8");
$code = strtoupper(trim($_GET['c'] ?? ''));
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Vendor Quality Cert Verify · ExpressVisa</title>
<style>
:root{--red:#E60012;--dark:#B8000E;--soft:#FFF1F2;--bg:#F7F8FA;--ink:#111827;--muted:#667085;--line:#E5E7EB}
*{box-sizing:border-box}
body{margin:0;font-family:Arial,"Microsoft YaHei",sans-serif;background:linear-gradient(180deg,#fff7f7,#f7f8fa);color:var(--ink)}
.wrap{max-width:920px;margin:auto;padding:28px 16px 90px}
.hero{background:linear-gradient(135deg,var(--red),var(--dark));color:#fff;border-radius:28px;padding:28px;box-shadow:0 24px 60px rgba(230,0,18,.22)}
.logo{height:58px;background:#fff;border-radius:16px;padding:10px;margin-bottom:16px}
h1{margin:0;font-size:32px}
p{line-height:1.6}
.card{margin-top:18px;background:#fff;border:1px solid var(--line);border-radius:24px;padding:22px;box-shadow:0 12px 32px rgba(15,23,42,.08)}
.row{display:grid;grid-template-columns:1fr auto;gap:12px}
input{width:100%;border:1px solid var(--line);border-radius:16px;padding:14px;font-size:16px;text-transform:uppercase}
button{border:0;border-radius:16px;background:var(--red);color:#fff;font-weight:900;padding:14px 18px;cursor:pointer}
.badge{display:inline-flex;background:var(--soft);color:var(--dark);border:1px solid rgba(230,0,18,.2);border-radius:999px;padding:8px 12px;font-weight:900}
.cert{display:none}
.cert.active{display:block}
.code{font-family:ui-monospace,Consolas,monospace;background:#111827;color:#fff;border-radius:16px;padding:14px;word-break:break-all}
.grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-top:14px}
.stat{background:#FFF1F2;border:1px solid rgba(230,0,18,.16);border-radius:18px;padding:14px}
.stat b{display:block;color:var(--red);font-size:22px}
.err{color:#B8000E;font-weight:900}
.small{color:var(--muted);font-size:13px}
@media(max-width:700px){.row{grid-template-columns:1fr}.grid{grid-template-columns:1fr}h1{font-size:26px}}

.langbar{
  display:flex;
  justify-content:flex-end;
  gap:8px;
  margin-bottom:14px;
}
.langbtn{
  border:1px solid var(--line);
  background:#fff;
  color:var(--ink);
  border-radius:999px;
  padding:9px 14px;
  font-weight:900;
  cursor:pointer;
}
.langbtn.active,.langbtn:hover{
  background:var(--red);
  color:#fff;
  border-color:var(--red);
}

</style>
</head>
<body>
<div class="wrap">
  <div class="langbar"><button type="button" class="langbtn active" id="langEN">EN</button><button type="button" class="langbtn" id="langZH">中文</button></div>
  <section class="hero">
    <h1 data-i18n="title">Vendor Quality Cert Verification</h1>
    <p data-i18n="hero">Verify a certified mobility F&B vendor using the public cert code.</p>
  </section>

  <section class="card">
    <div class="row">
      <input id="code" value="<?=htmlspecialchars($code,ENT_QUOTES,'UTF-8')?>" placeholder="Example: ONE-KLFOODTRUCK-A7F3" data-ph-en="Example: ONE-KLFOODTRUCK-A7F3" data-ph-zh="示例：ONE-KLFOODTRUCK-A7F3" data-ph-en="Example: ONE-KLFOODTRUCK-A7F3" data-ph-zh="示例：ONE-KLFOODTRUCK-A7F3">
      <button onclick="verifyCert()" data-i18n="verifyBtn">Verify</button>
    </div>
    <p class="small" data-i18n="format">Public cert format: ONE-BrandName-HASH4</p>
    <div id="msg"></div>
  </section>

  <section class="card cert" id="certBox">
    <span class="badge" data-i18n="badge">✔ Certified Vendor</span>
    <h2 id="vendorName"></h2>
    <div class="code" id="publicCode"></div>

    <div class="grid">
      <div class="stat"><span data-i18n="status">Status</span><b id="status"></b></div>
      <div class="stat"><span data-i18n="rating">Rating</span><b id="rating"></b></div>
      <div class="stat"><span data-i18n="visits">Verified Visits</span><b id="visits"></b></div>
    </div>

    <p><b data-i18n="mobType">Mobility F&B Type:</b> <span id="mobType"></span></p>
    <p><b data-i18n="location">Location:</b> <span id="locationName"></span></p>
    <p><b data-i18n="issuedAt">Issued At:</b> <span id="issuedAt"></span></p>
  </section>
</div>

<script>
const I18N={
  en:{
    title:'Vendor Quality Cert Verification',
    hero:'Verify a certified mobility F&B vendor using the public cert code.',
    verifyBtn:'Verify',
    format:'Public cert format: ONE-BrandName-HASH4',
    badge:'✔ Certified Vendor',
    status:'Status',
    rating:'Rating',
    visits:'Verified Visits',
    mobType:'Mobility F&B Type:',
    location:'Location:',
    issuedAt:'Issued At:',
    enter:'Please enter a cert code.',
    notFound:'Cert not found or inactive.',
    loading:'Verifying...',
    ratingSuffix:' / 5'
  },
  zh:{
    title:'供应商品质证书验证',
    hero:'使用公开证书编号验证已认证的流动餐饮供应商。',
    verifyBtn:'验证',
    format:'公开证书格式：ONE-BrandName-HASH4',
    badge:'✔ 已认证供应商',
    status:'状态',
    rating:'评分',
    visits:'已验证到访',
    mobType:'流动餐饮类型：',
    location:'地点：',
    issuedAt:'签发时间：',
    enter:'请输入证书编号。',
    notFound:'找不到证书或证书未启用。',
    loading:'验证中...',
    ratingSuffix:' / 5'
  }
};

let LANG=localStorage.getItem('vendor_verify_lang') || 'en';

function applyLang(lang){
  LANG=lang;
  localStorage.setItem('vendor_verify_lang',lang);
  document.documentElement.lang=lang==='zh'?'zh-CN':'en';

  document.getElementById('langEN')?.classList.toggle('active',lang==='en');
  document.getElementById('langZH')?.classList.toggle('active',lang==='zh');

  document.querySelectorAll('[data-i18n]').forEach(el=>{
    const k=el.getAttribute('data-i18n');
    if(I18N[lang][k]) el.textContent=I18N[lang][k];
  });

  const code=document.getElementById('code');
  if(code) code.placeholder=code.getAttribute(lang==='zh'?'data-ph-zh':'data-ph-en') || code.placeholder;
}

document.getElementById('langEN')?.addEventListener('click',()=>applyLang('en'));
document.getElementById('langZH')?.addEventListener('click',()=>applyLang('zh'));

async function verifyCert(){
  const t=I18N[LANG] || I18N.en;
  const code = document.getElementById('code').value.trim().toUpperCase();
  const msg = document.getElementById('msg');
  const box = document.getElementById('certBox');
  box.classList.remove('active');
  msg.innerHTML = '';

  if(!code){
    msg.innerHTML = '<p class="err">'+t.enter+'</p>';
    return;
  }

  msg.innerHTML='<p class="small">'+t.loading+'</p>';

  try{
    const r = await fetch('/verify/api/check.php?c=' + encodeURIComponent(code), {cache:'no-store'});
    const j = await r.json();

    if(!j.ok){
      msg.innerHTML = '<p class="err">'+t.notFound+'</p>';
      return;
    }

    const c = j.cert;
    document.getElementById('vendorName').textContent = c.vendor_name;
    document.getElementById('publicCode').textContent = c.public_code;
    document.getElementById('status').textContent = c.status;
    document.getElementById('rating').textContent = Number(c.avg_rating).toFixed(2) + t.ratingSuffix;
    document.getElementById('visits').textContent = c.verified_visits;
    document.getElementById('mobType').textContent = c.mob_type;
    document.getElementById('locationName').textContent = c.location_name || '-';
    document.getElementById('issuedAt').textContent = c.issued_at;
    msg.innerHTML='';
    box.classList.add('active');
  }catch(e){
    msg.innerHTML = '<p class="err">'+t.notFound+'</p>';
  }
}

applyLang(LANG);
if(document.getElementById('code').value.trim()){ verifyCert(); }
</script>
<script src="/assets/js/991-wallet-sync.js?v=991-unified"></script>
<script src="/assets/991-balance-sync.js?v=991-balance-sync-ux-v1" defer></script>
<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
</body>
</html>
