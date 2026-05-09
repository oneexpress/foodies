(function(){
  const KEYS=['foodies_wallet','ton_wallet','wallet_address','connected_wallet','wallet'];
  const MANIFEST='https://expressvisa.one/tonconnect-manifest.json';

  const I18N={
    en:{home:'Home',launcher:'Launcher',verify:'Verify',mint:'Mint NFT',market:'Market',lang:'EN',switch:'Switch',connect:'Connect',connected:'Connected',offline:'Offline',ton:'TON Wallet'},
    zh:{home:'首页',launcher:'启动器',verify:'验证',mint:'铸造NFT',market:'市场',lang:'中文',switch:'切换',connect:'连接钱包',connected:'已连接',offline:'未连接',ton:'TON 钱包'}
  };

  function currentLang(){return localStorage.getItem('foodies_lang')==='zh'?'zh':'en';}
  function t(k){return (I18N[currentLang()]||I18N.en)[k]||k;}
  function short(a){return a&&a.length>14?a.slice(0,6)+'...'+a.slice(-6):(a||'');}

  function getWallet(){
    for(const k of KEYS){
      const v=localStorage.getItem(k)||sessionStorage.getItem(k);
      if(v&&v.trim()) return v.trim();
    }
    return '';
  }

  function setWallet(w){
    if(w){
      localStorage.setItem('foodies_wallet',w);
      localStorage.setItem('ton_wallet',w);
      localStorage.setItem('wallet_address',w);
      window.__991WalletAddress=w;
      window.FOODIES_WALLET=w;
    }else{
      KEYS.forEach(k=>{localStorage.removeItem(k);sessionStorage.removeItem(k);});
      window.__991WalletAddress='';
      window.FOODIES_WALLET='';
    }
    window.dispatchEvent(new CustomEvent('foodies-footer-wallet-changed',{detail:{wallet:w,connected:!!w}}));
  }

  function active(){
    const p=location.pathname;
    if(p.includes('/mint.php')) return 'mint';
    if(p.includes('/verify.php')) return 'verify';
    return 'launcher';
  }

  function cls(k){return active()===k?'active':'';}

  async function ensureTonConnect(){
    if(window.FOODIES_FOOTER_TON_UI) return window.FOODIES_FOOTER_TON_UI;
    if(window.TON_CONNECT_UI && window.TON_CONNECT_UI.TonConnectUI){
      window.FOODIES_FOOTER_TON_UI = new window.TON_CONNECT_UI.TonConnectUI({manifestUrl:MANIFEST});
      window.FOODIES_FOOTER_TON_UI.onStatusChange(function(wallet){
        if(wallet && wallet.account && wallet.account.address) setWallet(wallet.account.address);
        else render();
      });
      return window.FOODIES_FOOTER_TON_UI;
    }
    return null;
  }

  async function connectWallet(){
    const old=getWallet();
    if(old){
      if(confirm((currentLang()==='zh'?'钱包已连接：':'Wallet connected: ')+short(old)+'\n\n'+(currentLang()==='zh'?'是否断开？':'Disconnect this wallet?'))){
        const ui=await ensureTonConnect();
        try{ if(ui&&ui.disconnect) await ui.disconnect(); }catch(e){}
        setWallet('');
        render();
      }
      return;
    }

    const ui=await ensureTonConnect();
    if(ui && ui.openModal){
      try{ await ui.openModal(); return; }catch(e){}
    }

    const w=prompt(currentLang()==='zh'?'粘贴 TON 钱包地址':'Paste TON wallet address');
    if(w&&w.trim()){ setWallet(w.trim()); render(); }
  }

  function toggleLang(){
    const next=currentLang()==='zh'?'en':'zh';
    localStorage.setItem('foodies_lang',next);
    document.documentElement.lang=next==='zh'?'zh-CN':'en';

    const en=document.getElementById('langEN')||document.getElementById('foodiesLangEN');
    const zh=document.getElementById('langZH')||document.getElementById('foodiesLangZH');
    if(next==='zh' && zh) zh.click();
    if(next==='en' && en) en.click();

    window.dispatchEvent(new CustomEvent('foodies-lang-change',{detail:{lang:next}}));
    render();
  }

  function syncExternalWalletStatus(){
    const w=getWallet(), on=!!w;
    ['topLed','heroLed'].forEach(id=>document.getElementById(id)?.classList.toggle('on',on));
    const top=document.getElementById('connectTopText');
    if(top) top.textContent=on?t('connected'):t('connect');
    const hero=document.getElementById('heroWallet');
    if(hero) hero.textContent=on
      ? (currentLang()==='zh'?'钱包已连接：':'Wallet Connected: ')+short(w)
      : (currentLang()==='zh'?'钱包：未连接':'Wallet: Not connected');
  }

  function render(){
    document.querySelectorAll('.foodies-footer-nav').forEach(x=>x.remove());
    const w=getWallet();
    const connected=!!w;

    const nav=document.createElement('nav');
    nav.className='foodies-footer-nav';
    nav.innerHTML=`
      <a class="${cls('home')}" href="https://expressvisa.one/"><span class="ico">⌂</span><span>${t('home')}</span></a>
      <a class="${cls('launcher')}" href="/foodies-nft/"><span class="ico">◎</span><span>${t('launcher')}</span></a>
      <a class="${cls('verify')}" href="/foodies-nft/verify.php?uid=FOODIES-RWA-PENDING"><span class="ico">✓</span><span>${t('verify')}</span></a>
      <a class="${cls('mint')}" href="/foodies-nft/mint.php"><span class="ico">◆</span><span>${t('mint')}</span></a>
      <a href="https://getgems.io/foodies" target="_blank" rel="noopener"><span class="ico">💎</span><span>${t('market')}</span></a>
      <button id="foodiesFooterLangBtn" type="button"><span class="ico">文</span><span>${t('lang')}</span><span class="foodies-footer-small">${t('switch')}</span></button>
      <button class="${connected?'active':''}" id="foodiesFooterWalletBtn" type="button">
        <span class="ico"><span class="foodies-wallet-led ${connected?'on':''}"></span></span>
        <span>${connected?t('connected'):t('connect')}</span>
        <span class="foodies-footer-small">${connected?short(w):t('offline')+' · '+t('ton')}</span>
      </button>`;
    document.body.appendChild(nav);

    document.getElementById('foodiesFooterWalletBtn')?.addEventListener('click',connectWallet);
    document.getElementById('foodiesFooterLangBtn')?.addEventListener('click',toggleLang);
    syncExternalWalletStatus();
  }

  function loadTonScript(){
    if(window.TON_CONNECT_UI || document.querySelector('script[data-foodies-tonconnect]')) return;
    const s=document.createElement('script');
    s.src='https://unpkg.com/@tonconnect/ui@latest/dist/tonconnect-ui.min.js';
    s.defer=true;
    s.dataset.foodiesTonconnect='1';
    document.head.appendChild(s);
  }

  loadTonScript();
  document.addEventListener('DOMContentLoaded',render);
  window.addEventListener('storage',render);
  window.addEventListener('foodies-wallet-sync',render);
  window.addEventListener('foodies-lang-change',render);
  setInterval(syncExternalWalletStatus,1200);
  setTimeout(render,100);
  setTimeout(render,700);
})();
