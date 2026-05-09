<?php
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>vUSDT Wallet · ExpressVisa</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
:root{--red:#E60012;--dark:#B8000E;--soft:#FFF1F2;--bg:#F7F8FA;--text:#202124;--line:#E5E7EB}
*{box-sizing:border-box}
body{margin:0;font-family:Inter,Arial,sans-serif;background:var(--bg);color:var(--text)}
.wrap{max-width:960px;margin:0 auto;padding:28px 16px 90px}
.hero{background:linear-gradient(135deg,var(--red),var(--dark));color:#fff;border-radius:26px;padding:28px;box-shadow:0 18px 42px rgba(230,0,18,.22)}
.logo{height:58px;background:#fff;border-radius:18px;padding:10px;margin-bottom:18px}
h1{margin:0;font-size:30px}
p{line-height:1.65}
.card{background:#fff;border:1px solid var(--line);border-radius:22px;padding:22px;margin-top:18px;box-shadow:0 8px 24px rgba(0,0,0,.05)}
.balance{font-size:42px;font-weight:900;color:var(--red)}
.ref{font-family:ui-monospace,Consolas,monospace;background:var(--soft);color:var(--dark);padding:12px;border-radius:14px;word-break:break-all}
.btn{display:inline-flex;align-items:center;justify-content:center;background:var(--red);color:#fff;text-decoration:none;border-radius:14px;padding:13px 18px;font-weight:900;margin:6px 8px 0 0}
.btn.secondary{background:#fff;color:var(--red);border:1px solid rgba(230,0,18,.25)}
.small{font-size:13px;color:#6b7280}
table{width:100%;border-collapse:collapse;margin-top:10px}
td,th{padding:10px;border-bottom:1px solid var(--line);text-align:left;font-size:14px}
th{color:var(--dark)}
</style>
</head>
<body>
<div class="wrap">
  <section class="hero">
    <h1>vUSDT Off-chain Wallet</h1>
    <p>Use vUSDT as an internal ExpressVisa settlement balance. This is an off-chain ledger wallet, not an on-chain crypto wallet.</p>
  </section>

  <section class="card">
    <div class="small">Available Balance</div>
    <div class="balance" id="bal">Loading...</div>
    <div class="small">Wallet Ref</div>
    <div class="ref" id="ref">Loading...</div>
    <br>
    <a class="btn" href="/booking/">Book Appointment</a>
    <a class="btn secondary" href="/post/">Free Post</a>
    <a class="btn secondary" href="/status/">Check Status</a>
  </section>

  <section class="card">
    <h2>Recent Ledger</h2>
    <table>
      <thead><tr><th>Type</th><th>Amount</th><th>Note</th><th>Date</th></tr></thead>
      <tbody id="ledger"><tr><td colspan="4">Loading...</td></tr></tbody>
    </table>
  </section>
</div>

<script>
async function loadWallet(){
  const userKey = localStorage.getItem('ev_user_key') || ('browser:' + crypto.randomUUID());
  localStorage.setItem('ev_user_key', userKey);

  const r = await fetch('/vusdt/api/wallet.php?user_key=' + encodeURIComponent(userKey));
  const j = await r.json();

  if(!j.ok){ alert('Wallet load failed'); return; }

  document.getElementById('bal').textContent = Number(j.wallet.balance).toFixed(6) + ' vUSDT';
  document.getElementById('ref').textContent = j.wallet.wallet_ref + ' · ' + j.wallet.status;

  const rows = j.ledger.length ? j.ledger.map(x => `
    <tr>
      <td>${x.tx_type}</td>
      <td>${Number(x.amount).toFixed(6)}</td>
      <td>${x.note || '-'}</td>
      <td>${x.created_at}</td>
    </tr>
  `).join('') : '<tr><td colspan="4">No transactions yet.</td></tr>';

  document.getElementById('ledger').innerHTML = rows;
}
loadWallet();
</script>
<script src="/assets/js/991-wallet-sync.js?v=991-unified"></script>
<script src="/assets/991-balance-sync.js?v=991-balance-sync-ux-v1" defer></script>
<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
</body>
</html>
