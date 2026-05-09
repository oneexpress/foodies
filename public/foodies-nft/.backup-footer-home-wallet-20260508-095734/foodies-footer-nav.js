(function(){
  'use strict';
  if(window.__FOODIES_ONE_ROW_FOOTER_V2__) return;
  window.__FOODIES_ONE_ROW_FOOTER_V2__=true;

  function wallet(){
    var keys=['foodies_wallet','foodiesIssuerWallet','ton_wallet','wallet_address','ev_wallet','connected_wallet'];
    for(var i=0;i<keys.length;i++){var v=localStorage.getItem(keys[i])||sessionStorage.getItem(keys[i]);if(v&&v.length>12)return v;}
    return window.FOODIES_WALLET||window.connectedWallet||window.__991WalletAddress||'';
  }
  function kill(){
    document.querySelectorAll('#evBottomBar,#adgBottomNav,#visaBottomBar,.ev-bottom-bar,.adg-bottom-nav,.visa-bottom-bar,.bottom-bar-991,[class*="991-bottom"],[id*="991-bottom"]').forEach(function(el){if(!el.classList.contains('foodies-footer-nav'))el.remove();});
  }
  function render(){
    kill();
    document.querySelectorAll('.foodies-footer-nav').forEach(function(el){el.remove();});
    var w=wallet();
    var mode=new URLSearchParams(location.search).get('mode')||'';
    var nav=document.createElement('nav');
    nav.className='foodies-footer-nav';
    nav.innerHTML='<div class="foodies-footer-row">'+
      '<a class="foodies-footer-btn" href="/"><span class="foodies-footer-icon">🏠</span><span>Home</span></a>'+
      '<a class="foodies-footer-btn '+(!mode?'is-active':'')+'" href="/foodies-nft/"><span class="foodies-footer-icon">🍔</span><span>Launcher</span></a>'+
      '<a class="foodies-footer-btn '+(mode==='issue'?'is-active':'')+'" href="/foodies-nft/?mode=issue"><span class="foodies-footer-icon">⭐</span><span>Issue</span></a>'+
      '<a class="foodies-footer-btn '+(mode==='reissue'||mode==='list'?'is-active':'')+'" href="/foodies-nft/?mode=reissue"><span class="foodies-footer-icon">♻️</span><span>Re-Issue</span></a>'+
      '<a class="foodies-footer-btn" href="https://getgems.io/foodies" target="_blank" rel="noopener"><span class="foodies-footer-icon">💎</span><span>Market</span></a>'+
      '<a class="foodies-footer-btn" href="/foodies-nft/?connect=1"><span class="foodies-footer-icon">'+(w?'✅':'🔐')+'</span><span>'+(w?'Wallet':'Login')+'</span></a>'+
      '</div>';
    document.body.appendChild(nav);
  }
  if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',render);else render();
  setTimeout(render,300);setTimeout(kill,900);
})();
