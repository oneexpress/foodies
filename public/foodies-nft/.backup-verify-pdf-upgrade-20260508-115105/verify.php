<?php
declare(strict_types=1);

$uid = trim((string)($_GET['uid'] ?? ''));
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Foodies NFT Verify</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="/foodies-nft/assets/foodies-nft.css?v=verify-1">
<style>
.verify-card{margin-top:14px}
.verify-big{font-size:28px;font-weight:950;margin-top:8px}
.status-ok{color:#16f2a5}.status-bad{color:#ffb703}
.code{font-family:ui-monospace,SFMono-Regular,Menlo,monospace;word-break:break-all;color:#9ca3af}
</style>
</head>
<body>
<main class="wrap">
  <section class="hero">
    <div>
      <div class="eyebrow">Foodies NFT</div>
      <h1>Verify NFT</h1>
      <p>Check Foodies NFT redeem and mint status.</p>
    </div>
    <div class="hero-actions">
      <a href="/foodies-nft/">Redeem</a>
      <a href="/wallet/">Wallet</a>
    </div>
  </section>

  <section class="glass verify-card">
    <span class="code">UID: <?=htmlspecialchars($uid ?: 'missing')?></span>
    <div id="result" class="verify-big">Loading...</div>
    <div id="details"></div>
  </section>
</main>

<script>
(async function(){
  const uid = <?=json_encode($uid)?>;
  const result = document.getElementById('result');
  const details = document.getElementById('details');
  if(!uid){ result.textContent='UID required'; result.className='verify-big status-bad'; return; }
  try{
    const r = await fetch('/foodies-nft/api/verify.php?uid=' + encodeURIComponent(uid), {cache:'no-store'});
    const j = await r.json();
    if(!j.ok){ result.textContent='Not found'; result.className='verify-big status-bad'; return; }
    const n = j.nft;
    result.textContent = j.valid ? 'VALID FOODIES NFT' : 'PENDING FOODIES NFT';
    result.className = 'verify-big ' + (j.valid ? 'status-ok' : 'status-bad');
    details.innerHTML = `
      <p><b>${n.tier_name_en}</b> / ${n.tier_name_zh}</p>
      <p>${n.stars} · Weight ${n.weight}</p>
      <p>Status: ${n.status}</p>
      <p class="code">Redeem UID: ${n.redeem_uid}</p>
      <p class="code">NFT UID: ${n.nft_uid || ''}</p>
    `;
  }catch(e){
    result.textContent='Verify failed';
    result.className='verify-big status-bad';
  }
})();
</script>
<script src="/assets/js/991-wallet-sync.js?v=991-unified"></script>
<script src="/assets/991-balance-sync.js?v=991-balance-sync-ux-v1" defer></script>
</body>
</html>
