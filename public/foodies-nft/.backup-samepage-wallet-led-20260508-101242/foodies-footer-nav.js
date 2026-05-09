(function(){
  'use strict';
  if(window.__FOODIES_SAME_PAGE_WALLET_LED__) return;
  window.__FOODIES_SAME_PAGE_WALLET_LED__=true;

  var manifestUrl = 'https://expressvisa.one/tonconnect-manifest.json';
  var tonUI = null;
  var connecting = false;

  function killGlobalBars(){
    document.querySelectorAll('#evBottomBar,#adgBottomNav,#visaBottomBar,.ev-bottom-bar,.adg-bottom-nav,.visa-bottom-bar,.bottom-bar-991,[class*="991-bottom"],[id*="991-bottom"]').forEach(function(el){
      if(!el.classList.contains('foodies-footer-nav')) el.remove();
    });
  }

  function shortAddr(w){
    if(!w) return '';
    return w.length>16 ? w.slice(0,6)+'...'+w.slice(-6) : w;
  }

  function saveWallet(addr){
    if(!addr) return;
    localStorage.setItem('ton_wallet', addr);
    localStorage.setItem('wallet_address', addr);
    localStorage.setItem('connected_wallet', addr);
    localStorage.setItem('foodiesIssuerWallet', addr);
    window.__991WalletAddress = addr;
    window.FOODIES_WALLET = addr;
    var owner=document.getElementById('ownerWallet');
    var issuer=document.getElementById('issuerWallet');
    if(owner) owner.value=addr;
    if(issuer) issuer.value=addr;
  }

  function readWallet(){
    var fromTon = '';
    try{
      if(tonUI && tonUI.wallet && tonUI.wallet.account && tonUI.wallet.account.address){
        fromTon = tonUI.wallet.account.address;
      }
    }catch(e){}
    var w = (
      fromTon ||
      window.__991WalletAddress ||
      window.FOODIES_WALLET ||
      localStorage.getItem('ton_wallet') ||
      localStorage.getItem('wallet_address') ||
      localStorage.getItem('connected_wallet') ||
      localStorage.getItem('foodiesIssuerWallet') ||
      ''
    ).trim();
    if(w) saveWallet(w);
    return w;
  }

  function initTonConnect(){
    if(tonUI) return tonUI;

    var mount=document.getElementById('foodiesTonConnectMount');
    if(!mount){
      mount=document.createElement('div');
      mount.id='foodiesTonConnectMount';
      mount.style.cssText='position:fixed;left:-9999px;top:-9999px;width:1px;height:1px;overflow:hidden';
      document.body.appendChild(mount);
    }

    if(window.TonConnectUI && window.TonConnectUI.TonConnectUI){
      tonUI = new window.TonConnectUI.TonConnectUI({
        manifestUrl: manifestUrl,
        buttonRootId: 'foodiesTonConnectMount'
      });
    }else if(window.TON_CONNECT_UI && window.TON_CONNECT_UI.TonConnectUI){
      tonUI = new window.TON_CONNECT_UI.TonConnectUI({
        manifestUrl: manifestUrl,
        buttonRootId: 'foodiesTonConnectMount'
      });
    }else if(window.tonConnectUI){
      tonUI = window.tonConnectUI;
    }

    if(tonUI){
      window.tonConnectUI = tonUI;
      window.__991TonConnectUI = tonUI;
      try{
        tonUI.onStatusChange(function(wallet){
          var addr = wallet && wallet.account && wallet.account.address ? wallet.account.address : '';
          if(addr) saveWallet(addr);
          connecting=false;
          render();
          window.dispatchEvent(new CustomEvent('foodies-wallet-status',{detail:{wallet:addr}}));
        });
      }catch(e){}
    }

    return tonUI;
  }

  async function connectSamePage(e){
    if(e){e.preventDefault();e.stopPropagation();}
    connecting=true;
    render();

    try{
      var ui=initTonConnect();

      if(ui && typeof ui.openModal==='function'){
        await ui.openModal();
        return false;
      }

      if(window.tonConnectUI && typeof window.tonConnectUI.openModal==='function'){
        await window.tonConnectUI.openModal();
        return false;
      }

      window.dispatchEvent(new CustomEvent('991-connect-wallet'));
      window.dispatchEvent(new CustomEvent('foodies-connect-wallet'));

      setTimeout(function(){
        connecting=false;
        render();
      },1800);

    }catch(err){
      console.error('Foodies wallet connect failed',err);
      connecting=false;
      render();
    }

    return false;
  }

  async function disconnectSamePage(e){
    if(e){e.preventDefault();e.stopPropagation();}
    try{
      var ui=initTonConnect();
      if(ui && typeof ui.disconnect==='function') await ui.disconnect();
    }catch(err){}
    ['ton_wallet','wallet_address','connected_wallet','foodiesIssuerWallet'].forEach(function(k){localStorage.removeItem(k);sessionStorage.removeItem(k);});
    window.__991WalletAddress='';
    window.FOODIES_WALLET='';
    render();
  }

  function mode(){
    return new URLSearchParams(location.search).get('mode') || '';
  }

  function render(){
    killGlobalBars();
    document.querySelectorAll('.foodies-footer-nav').forEach(function(el){el.remove();});

    var m=mode();
    var w=readWallet();
    var ledClass = connecting ? 'loading' : (w ? 'on' : '');
    var walletLabel = connecting ? 'Connecting...' : (w ? shortAddr(w) : 'Connect Wallet');
    var walletIcon = connecting ? '…' : (w ? '✓' : '⌁');

    var nav=document.createElement('nav');
    nav.className='foodies-footer-nav';
    nav.innerHTML =
      '<div class="foodies-footer-row">' +
        '<a class="foodies-footer-btn" href="https://expressvisa.one/"><span class="foodies-footer-icon">⌂</span><span>Home</span></a>' +
        '<a class="foodies-footer-btn '+(!m?'is-active':'')+'" href="/foodies-nft/"><span class="foodies-footer-icon">◎</span><span>Launcher</span></a>' +
        '<a class="foodies-footer-btn '+(m==="issue"?'is-active':'')+'" href="/foodies-nft/?mode=issue"><span class="foodies-footer-icon">★</span><span>Issue</span></a>' +
        '<a class="foodies-footer-btn '+((m==="reissue"||m==="list")?'is-active':'')+'" href="/foodies-nft/?mode=reissue"><span class="foodies-footer-icon">♻</span><span>Re-Issue</span></a>' +
        '<a class="foodies-footer-btn '+(w?'is-active':'')+'" href="#" id="foodiesWalletBtn"><span class="foodies-footer-icon">'+walletIcon+'</span><span>'+walletLabel+'</span></a>' +
      '</div>' +
      '<div class="foodies-footer-status"><span class="foodies-wallet-led '+ledClass+'"></span>' +
      (connecting ? 'Wallet Connecting...' : (w ? 'Wallet Connected: '+shortAddr(w)+' · Tap to disconnect' : 'Wallet Not Connected · Tap Connect Wallet')) +
      '</div>';

    document.body.appendChild(nav);

    var btn=document.getElementById('foodiesWalletBtn');
    if(btn){
      btn.addEventListener('click',function(e){
        if(readWallet()) disconnectSamePage(e);
        else connectSamePage(e);
        return false;
      });
    }
  }

  window.foodiesConnectWallet = connectSamePage;
  window.foodiesDisconnectWallet = disconnectSamePage;
  window.foodiesWalletAddress = readWallet;

  if(document.readyState==='loading'){
    document.addEventListener('DOMContentLoaded',function(){
      initTonConnect();
      render();
    });
  }else{
    initTonConnect();
    render();
  }

  window.addEventListener('storage',render);
  window.addEventListener('991-wallet-updated',render);
  window.addEventListener('ton-wallet-connected',function(e){
    if(e && e.detail && e.detail.wallet) saveWallet(e.detail.wallet);
    render();
  });

  setTimeout(function(){initTonConnect();render();},500);
  setInterval(function(){
    var old=readWallet();
    if(old) render();
  },5000);
})();
