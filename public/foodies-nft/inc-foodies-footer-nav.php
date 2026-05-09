<?php
declare(strict_types=1);
$active = $active ?? '';
?>
<style>
.foodies-footer-nav{
  position:fixed!important;
  left:50%!important;
  bottom:12px!important;
  transform:translateX(-50%)!important;
  width:min(760px,calc(100vw - 18px))!important;
  z-index:99999!important;
  background:#fff!important;
  border:1px solid #E5E7EB!important;
  border-radius:24px!important;
  box-shadow:0 14px 34px rgba(0,0,0,.18)!important;
  padding:8px!important;
  display:grid!important;
  grid-template-columns:repeat(5,1fr)!important;
  gap:7px!important;
  overflow:hidden!important;
}
.foodies-footer-nav a,.foodies-footer-wallet{
  min-width:0!important;
  height:58px!important;
  border-radius:16px!important;
  border:0!important;
  background:#fff!important;
  color:#111!important;
  text-decoration:none!important;
  display:flex!important;
  flex-direction:column!important;
  align-items:center!important;
  justify-content:center!important;
  gap:3px!important;
  font-weight:900!important;
  font-size:11px!important;
  line-height:1.05!important;
  cursor:pointer!important;
  white-space:normal!important;
  text-align:center!important;
  padding:4px!important;
}
.foodies-footer-nav .ico{
  width:30px!important;
  height:30px!important;
  min-width:30px!important;
  border-radius:12px!important;
  background:#E60012!important;
  color:#fff!important;
  display:flex!important;
  align-items:center!important;
  justify-content:center!important;
  font-size:15px!important;
  line-height:1!important;
}
.foodies-footer-nav a.active,.foodies-footer-wallet.active{
  background:#E60012!important;
  color:#fff!important;
}
.foodies-footer-nav a.active .ico,.foodies-footer-wallet.active .ico{
  background:#B8000E!important;
}
.foodies-wallet-led{
  width:9px!important;
  height:9px!important;
  border-radius:50%!important;
  background:#bbb!important;
  display:block!important;
}
.foodies-wallet-led.on{
  background:#22c55e!important;
  box-shadow:0 0 0 4px rgba(34,197,94,.15),0 0 14px rgba(34,197,94,.8)!important;
}
.foodies-wallet-sub{display:none!important}
body{padding-bottom:86px!important}
@media(max-width:520px){
  .foodies-footer-nav{
    width:calc(100vw - 10px)!important;
    bottom:6px!important;
    border-radius:20px!important;
    gap:4px!important;
    padding:6px!important;
  }
  .foodies-footer-nav a,.foodies-footer-wallet{
    height:54px!important;
    font-size:10px!important;
    border-radius:14px!important;
  }
  .foodies-footer-nav .ico{
    width:27px!important;
    height:27px!important;
    min-width:27px!important;
    border-radius:10px!important;
    font-size:13px!important;
  }
}
</style>
<nav class="foodies-footer-nav" aria-label="Foodies NFT footer nav">
  <a class="<?= $active==='home'?'active':'' ?>" href="https://expressvisa.one/"><span class="ico">⌂</span><span>Home</span></a>
  <a class="<?= $active==='launcher'?'active':'' ?>" href="/foodies-nft/"><span class="ico">◎</span><span>Launcher</span></a>
  <a class="<?= $active==='verify'?'active':'' ?>" href="/foodies-nft/verify.php?uid=FOODIES-RWA-PENDING"><span class="ico">✓</span><span>Verify</span></a>
  <a class="<?= $active==='mint'?'active':'' ?>" href="/foodies-nft/mint.php"><span class="ico">◆</span><span>Mint NFT</span></a>
  <button class="foodies-footer-wallet <?= $active==='wallet'?'active':'' ?>" id="foodiesWalletBtn" type="button">
    <span class="ico"><span id="foodiesWalletLed" class="foodies-wallet-led"></span></span>
    <span id="foodiesWalletText">Connect</span>
    <span id="foodiesWalletSub" class="foodies-wallet-sub">Tap</span>
  </button>
</nav>
<script>
(function(){
  const btn=document.getElementById('foodiesWalletBtn');
  const led=document.getElementById('foodiesWalletLed');
  const txt=document.getElementById('foodiesWalletText');
  const short=a=>a&&a.length>12?a.slice(0,4)+'...'+a.slice(-4):a;
  function getWallet(){
    return localStorage.getItem('foodies_wallet') ||
           localStorage.getItem('ton_wallet') ||
           localStorage.getItem('wallet') ||
           sessionStorage.getItem('foodies_wallet') || '';
  }
  function setState(w){
    if(w){
      led && led.classList.add('on');
      if(txt) txt.textContent=short(w);
      btn && btn.classList.add('active');
    }else{
      led && led.classList.remove('on');
      if(txt) txt.textContent='Connect';
      btn && btn.classList.remove('active');
    }
    window.FOODIES_WALLET=w;
  }
  function connect(){
    const old=getWallet();
    if(old && confirm('Disconnect wallet '+short(old)+' ?')){
      ['foodies_wallet','ton_wallet','wallet'].forEach(k=>{localStorage.removeItem(k);sessionStorage.removeItem(k);});
      setState('');
      return;
    }
    const w=prompt('Paste TON wallet address');
    if(w && w.trim()){
      localStorage.setItem('foodies_wallet',w.trim());
      localStorage.setItem('ton_wallet',w.trim());
      setState(w.trim());
    }
  }
  btn && btn.addEventListener('click',connect);
  setState(getWallet());
})();
</script>
