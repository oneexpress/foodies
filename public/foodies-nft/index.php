<?php
declare(strict_types=1); ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Foodies NFT Launcher</title>
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
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

.foodies-langbar{display:flex;gap:8px;align-items:center;justify-content:flex-end;margin:0 0 12px}
.foodies-langbtn{border:1px solid #e5e7eb;background:#fff;color:#111;border-radius:999px;padding:8px 12px;font-weight:1000;cursor:pointer}
.foodies-langbtn.active,.foodies-langbtn:hover{background:#E60012;color:#fff;border-color:#E60012}


.pdf-preview-card{position:sticky;top:14px}
.pdf-preview-frame{
  width:100%;
  min-height:520px;
  border:2px solid #E60012;
  border-radius:20px;
  background:#fff;
  overflow:hidden;
  box-shadow:0 10px 26px rgba(0,0,0,.08);
}
.pdf-preview-doc{
  aspect-ratio:0.707/1;
  width:100%;
  min-height:520px;
  background:linear-gradient(180deg,#fff,#fff7f8);
  padding:22px;
  display:flex;
  flex-direction:column;
  justify-content:space-between;
}
.pdf-cert-head{text-align:center;border-bottom:3px solid #E60012;padding-bottom:14px}
.pdf-cert-kicker{font-weight:1000;color:#E60012;letter-spacing:1.5px;font-size:12px}
.pdf-cert-title{font-size:26px;font-weight:1000;margin:8px 0;color:#111}
.pdf-cert-sub{font-size:12px;color:#666;font-weight:900}
.pdf-cert-body{display:grid;gap:10px;margin:18px 0}
.pdf-cert-row{display:flex;justify-content:space-between;gap:12px;border-bottom:1px dashed #ddd;padding-bottom:7px;font-size:13px}
.pdf-cert-row b{color:#333}.pdf-cert-row span{text-align:right;font-weight:900;color:#111}
.pdf-cert-stars{text-align:center;font-size:28px;color:#E60012;font-weight:1000;margin:10px 0}
.pdf-cert-review{border:1px solid #eee;border-radius:16px;padding:12px;background:#fff;font-size:13px;min-height:70px;color:#333}
.pdf-cert-footer{display:grid;grid-template-columns:90px 1fr;gap:12px;align-items:end;border-top:3px solid #111;padding-top:14px}
.pdf-cert-qr{width:90px;height:90px;border:2px solid #111;background:#fff;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:1000;color:#111}
.pdf-cert-meta{font-size:11px;color:#555;font-weight:900;line-height:1.45}
.pdf-actions{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:10px}
.pdf-actions a,.pdf-actions button{border:0;border-radius:14px;background:#E60012;color:#fff;font-weight:1000;padding:11px;text-align:center;text-decoration:none;cursor:pointer}
.pdf-actions button.secondary{background:#111}
@media(max-width:880px){.pdf-preview-card{position:static}.pdf-preview-doc{min-height:480px}.pdf-preview-frame{min-height:480px}}


.pdf-preview-doc{position:relative;overflow:hidden}
.pdf-preview-doc.tier-1{
  background:linear-gradient(180deg,#f7fff9,#ffffff)!important;
  border:6px solid #0E8965!important;
}
.pdf-preview-doc.tier-3{
  background:linear-gradient(180deg,#f8fafc,#ffffff)!important;
  border:6px solid #9ca3af!important;
}
.pdf-preview-doc.tier-5{
  background:linear-gradient(180deg,#fff8dc,#ffffff)!important;
  border:6px solid #d4af37!important;
}
.pdf-cert-seal{
  position:absolute;
  right:22px;
  top:92px;
  width:92px;
  height:92px;
  border-radius:999px;
  border:4px double currentColor;
  display:flex;
  align-items:center;
  justify-content:center;
  text-align:center;
  font-size:11px;
  font-weight:1000;
  line-height:1.1;
  transform:rotate(-12deg);
  opacity:.92;
}
.pdf-preview-doc.tier-1 .pdf-cert-seal,.pdf-preview-doc.tier-1 .pdf-cert-title,.pdf-preview-doc.tier-1 .pdf-cert-kicker,.pdf-preview-doc.tier-1 .pdf-cert-stars{color:#0E8965!important}
.pdf-preview-doc.tier-3 .pdf-cert-seal,.pdf-preview-doc.tier-3 .pdf-cert-title,.pdf-preview-doc.tier-3 .pdf-cert-kicker,.pdf-preview-doc.tier-3 .pdf-cert-stars{color:#6b7280!important}
.pdf-preview-doc.tier-5 .pdf-cert-seal,.pdf-preview-doc.tier-5 .pdf-cert-title,.pdf-preview-doc.tier-5 .pdf-cert-kicker,.pdf-preview-doc.tier-5 .pdf-cert-stars{color:#b8860b!important}
.pdf-barcode{
  height:52px;
  margin-top:8px;
  background:repeating-linear-gradient(90deg,#111 0 2px,#fff 2px 4px,#111 4px 5px,#fff 5px 9px,#111 9px 12px,#fff 12px 15px);
  border:1px solid #111;
  border-radius:8px;
}
.pdf-barcode-text{
  font-size:10px;
  letter-spacing:2px;
  text-align:center;
  font-weight:1000;
  margin-top:4px;
}
.pdf-tier-badge{
  display:inline-block;
  padding:6px 12px;
  border-radius:999px;
  font-size:11px;
  font-weight:1000;
  background:#111;
  color:#fff;
  margin-top:8px;
}
.pdf-preview-doc.tier-1 .pdf-tier-badge{background:#0E8965}
.pdf-preview-doc.tier-3 .pdf-tier-badge{background:#6b7280}
.pdf-preview-doc.tier-5 .pdf-tier-badge{background:#b8860b}
@media print{
  body *{visibility:hidden!important}
  #pdfPreviewDoc,#pdfPreviewDoc *{visibility:visible!important}
  #pdfPreviewDoc{
    position:fixed!important;
    left:0!important;
    top:0!important;
    width:100%!important;
    min-height:100vh!important;
    box-shadow:none!important;
  }
}


.preview-tabs{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin:8px 0 12px}
.preview-tab-btn{border:2px solid #e5e7eb;background:#fff;color:#111;border-radius:14px;padding:11px 8px;font-weight:1000;cursor:pointer}
.preview-tab-btn.active,.preview-tab-btn:hover{background:#E60012;color:#fff;border-color:#E60012}
.preview-panel.hidden{display:none!important}
.nft-preview-frame{border:2px solid #E60012;border-radius:20px;background:#111;padding:14px;box-shadow:0 10px 26px rgba(0,0,0,.12)}
.nft-preview-img{width:100%;display:block;border-radius:16px;background:#fff;aspect-ratio:723.61/998.93;object-fit:cover}
.nft-preview-meta{margin-top:10px;color:#fff;font-size:12px;font-weight:900;line-height:1.5}


/* ULTIMATE MICHELIN-STYLE RWA PDF PREVIEW */
.pdf-preview-frame{
  background:#f8f8f8!important;
  padding:14px!important;
  border:0!important;
  box-shadow:none!important;
}
.pdf-preview-doc{
  position:relative!important;
  min-height:720px!important;
  padding:34px 34px 28px!important;
  border-radius:18px!important;
  background:#fff!important;
  box-shadow:0 20px 60px rgba(0,0,0,.12)!important;
  border-width:8px!important;
  border-style:solid!important;
  overflow:hidden!important;
}
.pdf-preview-doc:before{
  content:"";
  position:absolute;
  inset:18px;
  border:1.5px solid currentColor;
  border-radius:12px;
  opacity:.38;
  pointer-events:none;
}
.pdf-preview-doc.tier-1{color:#0E8965!important;border-color:#0E8965!important;background:#f7fffb!important}
.pdf-preview-doc.tier-3{color:#8E939B!important;border-color:#8E939B!important;background:#fbfcfd!important}
.pdf-preview-doc.tier-5{color:#B88700!important;border-color:#B88700!important;background:#fffaf0!important}

.pdf-cert-head{
  border-bottom:2px solid currentColor!important;
  padding:6px 96px 20px 96px!important;
  margin-bottom:18px!important;
}
.pdf-cert-kicker{
  color:currentColor!important;
  font-size:12px!important;
  letter-spacing:2.5px!important;
}
.pdf-cert-title{
  color:currentColor!important;
  font-size:30px!important;
  letter-spacing:.5px!important;
}
.pdf-cert-sub{
  color:currentColor!important;
  opacity:.86!important;
}
.pdf-tier-badge{
  background:currentColor!important;
  color:#fff!important;
  margin-top:12px!important;
}
.pdf-cert-stars{
  color:currentColor!important;
  margin:16px 0!important;
  text-shadow:none!important;
}
.pdf-cert-row{
  border-bottom:1px dotted currentColor!important;
  color:#1f2937!important;
}
.pdf-cert-row b{color:#1f2937!important}
.pdf-cert-row span{color:#111!important}
.pdf-cert-review{
  border:1px solid currentColor!important;
  color:#111!important;
  background:rgba(255,255,255,.72)!important;
  min-height:76px!important;
}
.pdf-food-image{
  width:100%;
  min-height:155px;
  border:2px dashed currentColor;
  border-radius:18px;
  display:flex;
  align-items:center;
  justify-content:center;
  text-align:center;
  font-weight:1000;
  color:currentColor;
  background:rgba(255,255,255,.55);
  margin:12px 0;
  overflow:hidden;
}
.pdf-food-image img{
  width:100%;
  height:155px;
  object-fit:cover;
  display:block;
}
.pdf-cert-seal{
  top:auto!important;
  right:28px!important;
  bottom:118px!important;
  color:currentColor!important;
  background:rgba(255,255,255,.9)!important;
}
.pdf-cert-footer{
  border-top:2px solid currentColor!important;
  grid-template-columns:96px 1fr 86px!important;
  color:#111!important;
}
.pdf-cert-qr{
  border:2px solid currentColor!important;
  color:currentColor!important;
}
.pdf-barcode{
  border:1px solid currentColor!important;
}
.pdf-composer-badge{
  position:absolute;
  left:34px;
  bottom:18px;
  font-size:10px;
  font-weight:1000;
  color:currentColor;
  letter-spacing:1.2px;
}
.pdf-actions button{
  background:currentColor!important;
  color:#fff!important;
}
.pdf-actions button.secondary{
  background:#111!important;
}


/* FINAL INDEX PDF-DESIGN PREVIEW FORMAT */
.pdf-preview-card{position:sticky!important;top:14px!important}
.pdf-design-live-frame{
  width:100%;
  height:760px;
  border:0;
  border-radius:22px;
  background:#fff;
  box-shadow:0 12px 30px rgba(0,0,0,.12);
}
.pdf-preview-frame{
  padding:0!important;
  border:0!important;
  background:transparent!important;
  box-shadow:none!important;
  min-height:auto!important;
}
.pdf-actions{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:10px}
.pdf-actions a,.pdf-actions button{
  border:0;border-radius:14px;background:#E60012;color:#fff;
  font-weight:1000;padding:11px;text-align:center;text-decoration:none;cursor:pointer
}
.pdf-actions button.secondary{background:#111}
@media(max-width:880px){
  .pdf-preview-card{position:static!important}
  .pdf-design-live-frame{height:680px}
}


/* CLEAN PDF IFRAME PREVIEW FIX */
.pdf-preview-card{overflow:hidden!important}
.pdf-preview-frame{
  width:100%!important;
  height:auto!important;
  max-height:none!important;
  overflow:hidden!important;
  background:linear-gradient(180deg,#f8fafc,#fff)!important;
  border-radius:22px!important;
  padding:10px!important;
}
.pdf-design-live-frame{
  width:100%!important;
  height:640px!important;
  border:0!important;
  border-radius:20px!important;
  background:transparent!important;
  overflow:hidden!important;
  display:block!important;
}
.preview-panel{overflow:hidden!important}
.pdf-actions{position:sticky;bottom:0;background:#fff;padding-top:8px;z-index:3}
@media(max-width:880px){
  .pdf-design-live-frame{height:620px!important}
}


.foodies-market-btn{
  position:absolute;
  right:22px;
  top:22px;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  gap:8px;

  min-width:220px;
  height:54px;
  padding:0 22px;

  border-radius:999px;
  background:#E60012;
  color:#fff!important;

  text-decoration:none;
  font-weight:1000;
  font-size:16px;
  letter-spacing:.3px;

  box-shadow:0 10px 24px rgba(230,0,18,.28);
  transition:.18s ease;
  z-index:20;
}

.foodies-market-btn:hover{
  background:#fff;
  color:#E60012!important;
  transform:translateY(-1px);
}

@media(max-width:700px){
  .foodies-market-btn{
    position:relative;
    top:auto;
    right:auto;
    margin-top:16px;
    width:100%;
    min-width:0;
    font-size:15px;
  }
}


/* FORCE VISIBLE NFT MARKETPLACE CTA */
.hero{position:relative!important;padding-right:280px!important}
#foodiesMarketBtn.foodies-market-btn{
  position:absolute!important;
  right:22px!important;
  top:50%!important;
  transform:translateY(-50%)!important;
  width:auto!important;
  min-width:220px!important;
  max-width:none!important;
  height:54px!important;
  padding:0 22px!important;
  border-radius:999px!important;
  display:inline-flex!important;
  align-items:center!important;
  justify-content:center!important;
  gap:8px!important;
  overflow:visible!important;
  white-space:nowrap!important;
  background:#E60012!important;
  color:#fff!important;
  text-decoration:none!important;
  font-size:16px!important;
  font-weight:1000!important;
  line-height:1!important;
  text-indent:0!important;
  letter-spacing:.2px!important;
  opacity:1!important;
  z-index:50!important;
  box-shadow:0 10px 24px rgba(230,0,18,.28)!important;
}
#foodiesMarketBtn.foodies-market-btn:hover{
  background:#fff!important;
  color:#E60012!important;
}
@media(max-width:760px){
  .hero{padding-right:18px!important}
  #foodiesMarketBtn.foodies-market-btn{
    position:relative!important;
    right:auto!important;
    top:auto!important;
    transform:none!important;
    margin-top:14px!important;
    width:100%!important;
    min-width:0!important;
  }
}


/* FOODIES FORM CLEAN PRO UI */
#issueForm.form{gap:16px!important}
#newFields{display:grid!important;gap:16px!important}
#newFields .row{
  display:grid!important;
  grid-template-columns:1fr 1fr!important;
  gap:14px!important;
  align-items:start!important;
}
#newFields label,
#issueForm label{
  display:block!important;
  position:relative!important;
  font-size:0!important;
  font-weight:900!important;
  margin:0!important;
  color:#111!important;
}
.field-label{
  display:block!important;
  font-size:13px!important;
  line-height:1.25!important;
  margin:0 0 7px!important;
  font-weight:1000!important;
  color:#111827!important;
}
#issueForm input,
#issueForm select,
#issueForm textarea{
  margin:0!important;
  width:100%!important;
  min-height:48px!important;
  border:1px solid #d9dde5!important;
  border-radius:16px!important;
  background:#fff!important;
  padding:12px 14px!important;
  font-size:15px!important;
  line-height:1.35!important;
  color:#111!important;
  outline:none!important;
  box-shadow:0 1px 0 rgba(15,23,42,.03)!important;
}
#issueForm input:focus,
#issueForm select:focus,
#issueForm textarea:focus{
  border-color:#E60012!important;
  box-shadow:0 0 0 4px rgba(230,0,18,.10)!important;
}
#issueForm textarea{
  min-height:96px!important;
  resize:vertical!important;
}
#reviewCount{
  font-weight:1000!important;
  color:#E60012!important;
}
#issueForm .compact-note{
  margin-top:6px!important;
  font-size:12px!important;
  line-height:1.25!important;
  color:#667085!important;
}
#issueForm input[type="file"]{
  padding:10px!important;
  display:flex!important;
  align-items:center!important;
}
#issueForm input[type="file"]::file-selector-button{
  border:0!important;
  background:#E60012!important;
  color:#fff!important;
  border-radius:12px!important;
  padding:9px 12px!important;
  margin-right:10px!important;
  font-weight:900!important;
  cursor:pointer!important;
}
#issueForm .output{
  border:2px solid #E60012!important;
  background:#fff7f8!important;
  border-radius:18px!important;
}
#issueForm h2{
  margin:6px 0 0!important;
  font-size:22px!important;
}
@media(max-width:760px){
  #newFields .row{grid-template-columns:1fr!important}
}


/* FIX STAR RATING TEXT */
.tiers .tier{
  min-height:92px!important;
  display:flex!important;
  flex-direction:column!important;
  align-items:center!important;
  justify-content:center!important;
  gap:5px!important;
  font-size:13px!important;
}
.tier-stars{font-size:20px!important;line-height:1!important}
.tier-title{display:block!important;font-size:15px!important;font-weight:1000!important;color:#111!important}
.tier-price{display:block!important;font-size:12px!important;font-weight:900!important;color:#667085!important}
.tier:has(input:checked) .tier-title,
.tier:has(input:checked) .tier-price{color:#E60012!important}


/* COMPACT PLATFORM REISSUE */
#reissueFields{
  border:1px solid #e5e7eb!important;
  border-radius:18px!important;
  padding:14px!important;
  background:#fafafa!important;
}
#reissueFields .compact-note{
  margin:0 0 10px!important;
  font-size:13px!important;
  color:#667085!important;
}
#reissueFields .btn{
  width:100%!important;
  min-height:46px!important;
  border-radius:14px!important;
}
.selected-mini{
  margin-top:10px!important;
  padding:10px 12px!important;
  border-radius:14px!important;
  background:#fff7f8!important;
  border:1px solid rgba(230,0,18,.25)!important;
  color:#111!important;
  font-size:13px!important;
  font-weight:900!important;
}
#issuedList.issued-list{
  margin-top:10px!important;
  max-height:240px!important;
  overflow:auto!important;
  gap:7px!important;
}
#issuedList .issued-item{
  padding:9px 10px!important;
  border-radius:12px!important;
  display:grid!important;
  gap:3px!important;
  background:#fff!important;
  font-size:12px!important;
  line-height:1.25!important;
}
#issuedList .issued-item b{
  font-size:13px!important;
  color:#E60012!important;
}
#issuedList .issued-item.selected{
  border-color:#E60012!important;
  background:#fff1f2!important;
}
body.foodies-reissue-mode #newFields{
  display:none!important;
}
body.foodies-reissue-mode #formTitle{
  font-size:24px!important;
}
body.foodies-reissue-mode #issueForm{
  gap:14px!important;
}
body.foodies-reissue-mode .tiers{
  margin-top:0!important;
}

</style>
<script src="https://unpkg.com/@tonconnect/ui@latest/dist/tonconnect-ui.min.js"></script>
<link rel="stylesheet" href="/foodies-nft/assets/foodies-footer-nav.css?v=foodies-footer-fullwidth-pro">
</head>
<body>
<div id="foodiesTonConnectMount" style="position:fixed;left:-9999px;top:-9999px;width:1px;height:1px;overflow:hidden"></div>

<main class="wrap">
  <div class="foodies-langbar">
    <button type="button" class="foodies-langbtn active" id="foodiesLangEN">EN</button>
    <button type="button" class="foodies-langbtn" id="foodiesLangZH">中文</button>
  </div>
  <section class="hero">
    <div>
      <div class="eyebrow">EXPRESSVISA 991 · FOODIES NFT</div>
      <h1>Foodies RWA Reputation Card</h1>
      <div>Issue new Foodies NFT or Mint NFT an existing cert from the same wallet.</div>
    </div>
    <div>
      <a id="foodiesMarketBtn" class="foodies-market-btn" href="https://getgems.io/foodies" target="_blank" rel="noopener">💎 NFT Marketplace</a>
    </div>
  </section>

  <div class="tabs">
    <button class="tab active" id="tabIssue" type="button">🆕 Issue New</button>
    <button class="tab" id="tabReissue" type="button">♻️ Mint NFT Existing</button>
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
            <label><span class="field-label">Chef Real Name *</span>
              <input name="chef_name" placeholder="Chef real name">
            </label>
            <label><span class="field-label">Brand / Restaurant Name *</span>
              <input name="brand_name" placeholder="Brand name">
            </label>
          </div>

          <div class="row">
            <label><span class="field-label">Food / Signature Dish *</span>
              <input name="food_title" placeholder="Signature dish">
            </label>
            <label><span class="field-label">Business Type *</span>
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
            <label><span class="field-label">Location / Address *</span>
              <input name="location_text" placeholder="Food location / address">
            </label>
            <label><span class="field-label">Google Maps Link</span>
              <input name="google_maps_url" placeholder="Auto/manual Google Maps link">
            </label>
          </div>

          <label><span class="field-label">Signature Food Review</span>
              <textarea name="signature_food_review" maxlength="300" placeholder="Signature food review, maximum 300 characters"></textarea>
            <div class="compact-note"><span id="reviewCount">0</span>/300 characters</div>
          </label>

          <div class="row">
            <label><span class="field-label">Signature Food Upload</span>
              <input type="file" name="signature_food_upload" accept="image/*">
            </label>
            <label><span class="field-label">Social Link Type</span>
              <select name="social_link_type">
                <option value="">Select social link type</option>
                <option value="fb">Facebook / FB</option>
                <option value="tiktok">TikTok</option>
                <option value="xhs">XHS / 小红书</option>
              </select>
            </label>
          </div>

          <label><span class="field-label">Social Link URL</span>
              <input name="social_link_url" placeholder="Paste FB, TikTok, or XHS link">
          </label>
        </div>

        <div id="reissueFields" class="hidden">
          <p class="compact-note">Select any issued Foodies NFT cert on the platform, then choose star rating only.</p>
          <button type="button" class="btn" onclick="loadIssuedFoodies()">Load Platform Certs</button>
          <div id="issuedList" class="issued-list"></div>
          <div class="selected-mini">Selected: <b id="selectedCertText">None</b></div>
        </div>

        <h2>Star Rating</h2>
        <div class="tiers">
          <label class="tier"><input type="radio" name="star_class" value="1" checked><span class="tier-stars">⭐</span><span class="tier-title">1 Star</span><span class="tier-price">100 vSHARE</span></label>
          <label class="tier"><input type="radio" name="star_class" value="3"><span class="tier-stars">⭐⭐⭐</span><span class="tier-title">3 Stars</span><span class="tier-price">300 vSHARE</span></label>
          <label class="tier"><input type="radio" name="star_class" value="5"><span class="tier-stars">⭐⭐⭐⭐⭐</span><span class="tier-title">5 Stars</span><span class="tier-price">500 vSHARE</span></label>
        </div>

        <div class="output">Price: <span id="priceText">100</span> vSHARE</div>
        <button class="submit" type="submit" id="submitBtn">Issue Foodies NFT</button>
        <div id="statusMsg" class="status"></div>
      </form>
    </div>

    <aside class="card pdf-preview-card">
      <h2>Foodies RWA Live Preview</h2>
      <div class="preview-tabs">
        <button type="button" class="preview-tab-btn active" id="pdfPreviewTabBtn" onclick="setFoodiesPreviewTab('pdf')">Live Preview</button>
        <button type="button" class="preview-tab-btn" id="nftPreviewTabBtn" onclick="setFoodiesPreviewTab('nft')">RWA NFT Preview</button>
      </div>
      <p class="muted">Live preview uses the final A4 <b>pdf-design.php</b> certificate format.</p>

      <div class="preview-panel" id="pdfPreviewPanel">
        <div class="pdf-preview-frame">
          <iframe id="pdfDesignFrame" class="pdf-design-live-frame" src="/foodies-nft/pdf-design.php?star=1&preview=1"></iframe>
        </div>
      </div>

      <div class="preview-panel hidden" id="nftPreviewPanel">
        <div class="nft-preview-frame">
          <img id="nftPreviewImg" class="nft-preview-img" src="/metadata/foodies/NFT/1_star_foodies.png" alt="Foodies NFT Preview">
          <div class="nft-preview-meta">
            NFT Preview: <span id="nftPreviewTier">1 Star Recommended Card</span><br>
            Source: /metadata/foodies/NFT/
          </div>
        </div>
      </div>

      <div class="pdf-actions">
        <a id="openPdfDesignBtn" href="/foodies-nft/pdf-design.php?star=1" target="_blank">Open Live Preview</a>
        <button type="button" class="secondary" onclick="syncPdfPreview();setFoodiesPreviewTab('nft')">NFT Preview</button>
      </div>

      <div id="resultBox" class="muted" style="margin-top:10px"></div>
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
  document.body.classList.toggle('foodies-reissue-mode', mode==='reissue');
  document.getElementById('tabIssue').classList.toggle('active',mode==='new');
  document.getElementById('tabReissue').classList.toggle('active',mode==='reissue');
  document.getElementById('newFields').classList.toggle('hidden',mode==='reissue');
  document.getElementById('reissueFields').classList.toggle('hidden',mode!=='reissue');
  document.getElementById('formTitle').textContent=mode==='reissue'?'Mint NFT Existing Foodies NFT':'Issue New Foodies NFT';
  document.getElementById('submitBtn').textContent=mode==='reissue'?'Mint NFT Foodies NFT':'Issue Foodies NFT';
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




function setFoodiesPreviewTab(tab){
  const pdf=document.getElementById('pdfPreviewPanel');
  const nft=document.getElementById('nftPreviewPanel');
  const pdfBtn=document.getElementById('pdfPreviewTabBtn');
  const nftBtn=document.getElementById('nftPreviewTabBtn');
  if(pdf) pdf.classList.toggle('hidden',tab!=='pdf');
  if(nft) nft.classList.toggle('hidden',tab!=='nft');
  if(pdfBtn) pdfBtn.classList.toggle('active',tab==='pdf');
  if(nftBtn) nftBtn.classList.toggle('active',tab==='nft');
}
function syncNftPreview(){
  const star=form.querySelector('[name="star_class"]:checked')?.value || '1';
  const img=document.getElementById('nftPreviewImg');
  const tier=document.getElementById('nftPreviewTier');
  const src = star==='5'
    ? '/metadata/foodies/NFT/5_stars_foodies.png'
    : (star==='3' ? '/metadata/foodies/NFT/3_stars_foodies.png' : '/metadata/foodies/NFT/1_star_foodies.png');
  if(img) img.src=src+'?v=foodies-final';
  if(tier) tier.textContent = star==='5'
    ? '5 Stars Master Chef Card'
    : (star==='3' ? '3 Stars Premium Card' : '1 Star Recommended Card');
}
function valByName(name){
  const el=document.querySelector('[name="'+name+'"]');
  return el ? (el.value||'').trim() : '';
}
function buildPdfDesignUrl(previewMode=false){
  const star=form.querySelector('[name="star_class"]:checked')?.value || '1';
  const params=new URLSearchParams();
  params.set('star',star);
  if(previewMode) params.set('preview','1');
  params.set('uid',document.getElementById('parentCertUid')?.value || 'FOODIES-RWA-PENDING');
  params.set('wallet',currentWallet() || 'Wallet Not Connected');
  params.set('issuer',currentWallet() || 'Issuer Wallet Pending');
  params.set('chef',valByName('chef_name') || 'Chef Name');
  params.set('brand',valByName('brand_name') || 'Restaurant / Brand');
  params.set('food',valByName('food_title') || 'Signature Food');
  params.set('business',valByName('business_type') || 'HomeChise Unit');
  params.set('location',valByName('location_text') || 'Food Location');
  params.set('social',valByName('social_link_url') || 'Official Social Link');
  params.set('chain','TON Mainnet');
  params.set('verify','Verify URL Pending');
  params.set('price', prices[star] + ' vSHARE');
  return '/foodies-nft/pdf-design.php?' + params.toString();
}
function syncPdfPreview(){
  const url=buildPdfDesignUrl(true);
  const fullUrl=buildPdfDesignUrl(false);
  const frame=document.getElementById('pdfDesignFrame');
  const open=document.getElementById('openPdfDesignBtn');
  if(frame) frame.src=url;
  if(open) open.href=fullUrl;
  syncNftPreview();
}
document.querySelectorAll('#issueForm input,#issueForm select,#issueForm textarea').forEach(el=>{
  el.addEventListener('input',syncPdfPreview);
  el.addEventListener('change',syncPdfPreview);
});
setTimeout(syncPdfPreview,120);


const review=document.querySelector('[name="signature_food_review"]');
if(review){
  const count=document.getElementById('reviewCount');
  const syncReviewCount=()=>{ if(count) count.textContent=String(review.value.length); };
  review.addEventListener('input',syncReviewCount);
  syncReviewCount();
}

async function loadIssuedFoodies(){
  const box=document.getElementById('issuedList');
  box.textContent='Loading all platform issued certs...';
  const r=await fetch('/foodies-nft/api/list-issued.php?scope=all');
  const j=await r.json().catch(()=>({ok:false,rows:[]}));
  if(!j.ok){box.textContent='Failed to load issued certs.';return;}
  if(!j.rows.length){box.textContent='No issued Foodies NFT cert found on platform.';return;}
  box.innerHTML=j.rows.map(row=>`
    <div class="issued-item" data-uid="${row.cert_uid||''}">
      <b>${row.cert_uid||''}</b>
      <span>${(row.chef_name||'Chef')} · ${(row.brand_name||'Brand')}</span>
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
    msg.textContent='Please select a platform cert first.';return;
  }
  msg.textContent='Submitting...';
  const fd=new FormData(form);
  const r=await fetch('/foodies-nft/api/submit-cert.php',{method:'POST',body:fd});
  const j=await r.json().catch(()=>({ok:false,error:'Bad JSON'}));
  if(!j.ok){msg.textContent='Error: '+(j.error||'submit failed');return;}
  msg.textContent='Created: '+j.cert_uid+' · '+j.vshare_price+' vSHARE';
  result.innerHTML='<b>Cert UID:</b> '+j.cert_uid+'<br><b>Price:</b> '+j.vshare_price+' vSHARE<br><b>Verify:</b> <a href="'+j.verify_url+'" target="_blank">'+j.verify_url+'</a>'; document.getElementById('parentCertUid').value=j.cert_uid; syncPdfPreview();; if(pv) pv.textContent=j.cert_uid; syncPdfPreview();
});
const urlMode=new URLSearchParams(location.search).get('mode');
if(urlMode==='reissue'||urlMode==='list') setMode('reissue'); else setMode('new');
</script>


<script>
(function(){
  const dict={
    en:{
      eyebrow:'EXPRESSVISA 991 · FOODIES NFT',
      title:'Foodies RWA Reputation Card',
      hero:'Issue new Foodies NFT or Mint NFT an existing cert from the same wallet.',
      marketplace:'💎 NFT Marketplace',
      issueTab:'🆕 Issue New',
      reissueTab:'♻️ Mint NFT Existing',
      issueTitle:'Issue New Foodies NFT',
      reissueTitle:'Mint NFT Existing Foodies NFT',
      chef:'Chef Real Name *',
      brand:'Brand / Restaurant Name *',
      food:'Food / Signature Dish *',
      business:'Business Type *',
      location:'Location / Address *',
      maps:'Google Maps Link',
      review:'Signature Food Review',
      upload:'Signature Food Upload',
      socialType:'Social Link Type',
      socialUrl:'Social Link URL',
      reissueNote:'Select any issued Foodies NFT cert on the platform, then choose star rating only.',
      loadCerts:'Load Platform Certs',
      selected:'Selected Cert:',
      star:'Star Rating',
      submitIssue:'Issue Foodies NFT',
      submitReissue:'Mint NFT Foodies NFT',
      output:'Production Output',
      outputText:'Wallet required. Cert UID, verify URL, Google Maps link, PDF cert, and NFT metadata are generated after submit.'
    },
    zh:{
      eyebrow:'EXPRESSVISA 991 · 美食 NFT',
      title:'Foodies RWA 美食信誉卡',
      hero:'发行新的 Foodies NFT，或重新发行已存在的美食证书。',
      marketplace:'💎 NFT Marketplace',
      issueTab:'🆕 发行新 NFT',
      reissueTab:'♻️ 重新发行',
      issueTitle:'发行新的 Foodies NFT',
      reissueTitle:'重新发行 Foodies NFT',
      chef:'厨师真实姓名 *',
      brand:'品牌 / 餐厅名称 *',
      food:'招牌美食 / 菜品 *',
      business:'业务类型 *',
      location:'地点 / 地址 *',
      maps:'Google 地图链接',
      review:'招牌美食评价',
      upload:'上传招牌美食图片',
      socialType:'社交平台类型',
      socialUrl:'社交平台链接',
      reissueNote:'重新发行会加载平台所有已发行 Foodies NFT 证书。只需选择证书 + 选择星级。',
      loadCerts:'加载平台已发行证书',
      selected:'已选择证书:',
      star:'星级评分',
      submitIssue:'发行 Foodies NFT',
      submitReissue:'重新发行 Foodies NFT',
      output:'生产输出',
      outputText:'需要连接钱包。提交后生成 Cert UID、验证链接、Google 地图链接、PDF 证书和 NFT metadata。'
    }
  };

  function setText(selector,text){
    const el=document.querySelector(selector);
    if(el) el.textContent=text;
  }

  function applyLang(lang){
    const t=dict[lang]||dict.en;
    localStorage.setItem('foodies_lang',lang);
    document.documentElement.lang=lang==='zh'?'zh-CN':'en';

    document.getElementById('foodiesLangEN')?.classList.toggle('active',lang==='en');
    document.getElementById('foodiesLangZH')?.classList.toggle('active',lang==='zh');

    setText('.eyebrow',t.eyebrow);
    setText('.hero h1',t.title);
    const heroText=document.querySelector('.hero h1 + div');
    if(heroText) heroText.textContent=t.hero;
    const market=document.querySelector('.hero .btn');
    if(market) market.textContent=t.market;

    setText('#tabIssue',t.issueTab);
    setText('#tabReissue',t.reissueTab);

    const setFieldLabel=(name,text)=>{
      const el=document.querySelector('label:has([name="'+name+'"]) .field-label');
      if(el) el.textContent=text;
    };
    setFieldLabel('chef_name',t.chef);
    setFieldLabel('brand_name',t.brand);
    setFieldLabel('food_title',t.food);
    setFieldLabel('business_type',t.business);
    setFieldLabel('location_text',t.location);
    setFieldLabel('google_maps_url',t.maps);
    setFieldLabel('signature_food_review',t.review);
    setFieldLabel('signature_food_upload',t.upload);
    setFieldLabel('social_link_type',t.socialType);
    setFieldLabel('social_link_url',t.socialUrl);

    const note=document.querySelector('#reissueFields .compact-note');
    if(note) note.textContent=t.reissueNote;
    const loadBtn=document.querySelector('#reissueFields .btn');
    if(loadBtn) loadBtn.textContent=t.loadCerts;
    const selected=document.querySelector('#reissueFields .output');
    if(selected && selected.firstChild) selected.firstChild.textContent=t.selected+' ';

    const h2s=[...document.querySelectorAll('h2')];
    const starH=h2s.find(x=>x.textContent.includes('Star')||x.textContent.includes('星级'));
    if(starH) starH.textContent=t.star;
    const outH=h2s.find(x=>x.textContent.includes('Production')||x.textContent.includes('生产'));
    if(outH) outH.textContent=t.output;
    const outP=document.querySelector('aside.card .muted');
    if(outP) outP.textContent=t.outputText;

    if(typeof issueType!=='undefined'){
      const reissue=issueType.value==='reissue';
      setText('#formTitle',reissue?t.reissueTitle:t.issueTitle);
      setText('#submitBtn',reissue?t.submitReissue:t.submitIssue);
    }
  }

  const oldSetMode=window.setMode || null;
  const tryPatchSetMode=()=>{
    if(typeof setMode==='function' && !setMode.__foodiesLangPatched){
      const original=setMode;
      window.setMode=setMode=function(mode){
        original(mode);
        applyLang(localStorage.getItem('foodies_lang')||'en');
      };
      window.setMode.__foodiesLangPatched=true;
    }
  };

  document.getElementById('foodiesLangEN')?.addEventListener('click',()=>applyLang('en'));
  document.getElementById('foodiesLangZH')?.addEventListener('click',()=>applyLang('zh'));

  setTimeout(()=>{tryPatchSetMode();applyLang(localStorage.getItem('foodies_lang')||'en');},50);
})();
</script>

<script src="/assets/js/991-wallet-sync.js?v=991-unified"></script>
<script>
(function(){
  function fixFoodiesMarketBtn(){
    const btn=document.getElementById('foodiesMarketBtn') || document.querySelector('.hero a[href="https://getgems.io/foodies"]');
    if(!btn) return;
    btn.id='foodiesMarketBtn';
    btn.className='foodies-market-btn';
    btn.href='https://getgems.io/foodies';
    btn.target='_blank';
    btn.rel='noopener';
    btn.textContent='💎 NFT Marketplace';
  }
  document.addEventListener('DOMContentLoaded',fixFoodiesMarketBtn);
  setTimeout(fixFoodiesMarketBtn,50);
  setTimeout(fixFoodiesMarketBtn,300);
  setTimeout(fixFoodiesMarketBtn,900);
  window.addEventListener('storage',fixFoodiesMarketBtn);
})();
</script>



<script src="/foodies-nft/assets/foodies-footer-nav.js?v=foodies-footer-fullwidth-pro" defer></script>
</body>
</html>
