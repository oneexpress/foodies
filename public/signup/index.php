<?php
declare(strict_types=1);
session_start();
$next = $_GET['next'] ?? '/post/';
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login / Signup · ExpressVisa</title>
<script src="https://unpkg.com/@tonconnect/ui@latest/dist/tonconnect-ui.min.js"></script>
<style>
body{margin:0;font-family:Arial,"Microsoft YaHei",sans-serif;background:#fff7f7;color:#111}
.wrap{max-width:920px;margin:auto;padding:28px}
.lang{display:flex;gap:10px;justify-content:flex-end;margin-bottom:14px}
.lang button{width:auto;height:44px;padding:0 22px;margin:0;border:0;border-radius:999px;background:#e60023;color:#fff;font-weight:900}
.hero{background:#e60023;color:#fff;border-radius:28px;padding:26px;display:flex;gap:18px;align-items:center}
.hero img{width:72px;background:#fff;border-radius:16px;padding:8px}
.card{margin-top:20px;background:#fff;border-radius:24px;padding:24px;box-shadow:0 18px 50px rgba(0,0,0,.08)}
.tabs{display:flex;gap:10px;margin-bottom:18px}
.tab{flex:1;height:50px;border-radius:999px;border:1px solid #fecdd3;background:#fff1f2;color:#b1b;font-weight:900;cursor:pointer}
.tab.active{background:#e60023;color:#fff}
.panel{display:none}.panel.active{display:block}
label{font-weight:900;display:block;margin:14px 0 6px}
input{width:100%;height:52px;border:1px solid #ddd;border-radius:14px;padding:0 12px;box-sizing:border-box}
button.main{width:100%;height:54px;margin-top:16px;border:0;border-radius:999px;background:#e60023;color:#fff;font-weight:900;cursor:pointer}
.tonBtn{background:#0098ea!important}
.walletBox{border:1px solid #ddd;border-radius:16px;padding:12px;background:#fafafa}
.ok{color:#15803d;font-weight:900}.bad{color:#b91c1c;font-weight:900}
.orLine{display:flex;align-items:center;gap:12px;margin:22px 0;color:#b1b;font-weight:900}
.orLine:before,.orLine:after{content:"";height:1px;background:#fecdd3;flex:1}
.orLine span{background:#fff;padding:0 10px}
</style>
</head>
<body>
<div class="wrap">

  <div class="lang">
    <button type="button" onclick="setLang('zh')">中</button>
    <button type="button" onclick="setLang('en')">EN</button>
  </div>

  <div class="hero">
    <div>
      <h1 data-zh="ExpressVisa 账户" data-en="ExpressVisa Account">ExpressVisa Account</h1>
      <p data-zh="登录或创建统一 ExpressVisa 账户" data-en="Login or create unified ExpressVisa account">Login or create unified ExpressVisa account</p>
    </div>
  </div>

  <div class="card">
    <div class="tabs">
      <button type="button" class="tab active" onclick="tab(event,'login')" data-zh="登录" data-en="Login">Login</button>
      <button type="button" class="tab" onclick="tab(event,'signup')" data-zh="注册" data-en="Signup">Signup</button>
    </div>

    <form id="login" class="panel active" method="post" action="/signup/api/login.php">
      <input type="hidden" name="next" value="<?=htmlspecialchars($next,ENT_QUOTES,'UTF-8')?>">
      <label data-zh="邮箱 / 用户名" data-en="Email / Username">Email / Username</label>
      <input name="identity" required>

      <label data-zh="密码" data-en="Password">Password</label>
      <input type="password" name="password" required>

      <button class="main" type="submit" data-zh="登录" data-en="Login">Login</button>

      <div class="orLine"><span>or</span></div>

      <button class="main tonBtn" type="button" onclick="tonLogin()" data-zh="使用 TON 钱包登录" data-en="Login with TON Wallet">Login with TON Wallet</button>
      <div id="tonLoginStatus" class="bad" style="margin-top:10px" data-zh="TON 钱包未连接" data-en="TON wallet not connected">TON wallet not connected</div>
    </form>

    <form id="signup" class="panel" method="post" action="/signup/api/submit.php" onsubmit="return validateSignup()">
      <input type="hidden" name="next" value="<?=htmlspecialchars($next,ENT_QUOTES,'UTF-8')?>">

      <label data-zh="用户名" data-en="Username">Username</label>
      <input name="username" required>

      <label data-zh="邮箱" data-en="Email">Email</label>
      <input name="email" type="email" required>

      <label data-zh="WhatsApp 联系" data-en="WhatsApp">WhatsApp</label>
      <input name="whatsapp" required>

      <label data-zh="密码" data-en="Password">Password</label>
      <input type="password" id="p1" name="password" required>

      <label data-zh="再次输入密码" data-en="Retype Password">Retype Password</label>
      <input type="password" id="p2" name="password2" required>

      <label data-zh="TON 钱包" data-en="TON Wallet">TON Wallet</label>
      <div class="walletBox">
        <button class="main tonBtn" type="button" onclick="connectSignupTon()" data-zh="连接 TON 钱包" data-en="Connect TON Wallet">Connect TON Wallet</button>
        <input type="hidden" id="ton" name="ton_address">
        <div id="walletStatus" class="bad" style="margin-top:10px" data-zh="未连接" data-en="Not connected">Not connected</div>
      </div>

      <button class="main" type="submit" data-zh="创建账户" data-en="Create Account">Create Account</button>
    </form>
  </div>
</div>

<script>
function applyLang(){
  const lang=localStorage.getItem("ev_lang") || "en";
  document.querySelectorAll("[data-zh][data-en]").forEach(el=>{
    el.textContent = lang === "zh" ? el.dataset.zh : el.dataset.en;
  });
}
function setLang(lang){
  localStorage.setItem("ev_lang",lang);
  applyLang();
}
function tab(ev,id){
  document.querySelectorAll('.tab').forEach(x=>x.classList.remove('active'));
  document.querySelectorAll('.panel').forEach(x=>x.classList.remove('active'));
  document.getElementById(id).classList.add('active');
  ev.currentTarget.classList.add('active');
}

const tonUI = new TON_CONNECT_UI.TonConnectUI({
  manifestUrl: "https://expressvisa.one/tonconnect-manifest.json"
});

async function connectWalletOnce(){
  const current = tonUI.wallet;
  if(current && current.account && current.account.address) return current.account.address;
  const wallet = await tonUI.connectWallet();
  if(wallet && wallet.account && wallet.account.address) return wallet.account.address;
  throw new Error("Wallet not connected");
}

async function tonLogin(){
  const status=document.getElementById('tonLoginStatus');
  try{
    status.textContent="Connecting...";
    status.className="bad";
    const wallet=await connectWalletOnce();
    status.textContent="Connected. Logging in...";
    status.className="ok";

    const r=await fetch('/signup/api/ton-login.php',{
      method:'POST',
      headers:{'Content-Type':'application/json'},
      body:JSON.stringify({ton_address:wallet,next:"<?=htmlspecialchars($next,ENT_QUOTES,'UTF-8')?>"})
    });
    const j=await r.json();
    if(j.ok){ window.location.href=j.redirect || '/post/'; return; }
    alert(j.message || 'TON login failed');
    if(j.need_signup){ document.querySelectorAll('.tab')[1].click(); }
  }catch(e){
    status.textContent="TON login failed";
    status.className="bad";
    alert(e.message || "TON login failed");
  }
}

async function connectSignupTon(){
  const status=document.getElementById('walletStatus');
  try{
    status.textContent="Connecting...";
    status.className="bad";
    const wallet=await connectWalletOnce();
    document.getElementById('ton').value=wallet;
    status.textContent="Connected: "+wallet.slice(0,8)+"...";
    status.className="ok";
  }catch(e){
    status.textContent="Not connected";
    status.className="bad";
    alert(e.message || "Connect TON wallet failed");
  }
}

function validateSignup(){
  if(document.getElementById('p1').value !== document.getElementById('p2').value){
    alert("Password mismatch"); return false;
  }
  if(!document.getElementById('ton').value){
    alert("Connect TON wallet"); return false;
  }
  return true;
}

applyLang();
</script>

<div style="margin-top:16px;padding:14px;border-radius:16px;background:#fff1f2;border:1px solid #fecdd3;color:#7f1d1d">
  <b>vUSDT Wallet</b><br>
  Create or open your ExpressVisa settlement wallet.
  <br><br><a href="/wallet/" style="display:inline-block;background:#e60012;color:white;padding:10px 14px;border-radius:12px;text-decoration:none;font-weight:800">Open Wallet</a>
</div>
<script src="/assets/js/991-wallet-sync.js?v=991-unified"></script>
<script src="/assets/991-balance-sync.js?v=991-balance-sync-ux-v1" defer></script>
<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
</body>
</html>
