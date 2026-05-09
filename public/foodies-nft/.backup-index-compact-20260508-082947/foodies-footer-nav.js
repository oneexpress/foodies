(function(){
  'use strict';
  if(window.__FOODIES_FOOTER_NAV_MARKET_LOGIN__) return;
  window.__FOODIES_FOOTER_NAV_MARKET_LOGIN__ = true;

  function killRedBars(){
    document.querySelectorAll(
      '#evBottomBar,#adgBottomNav,#visaBottomBar,.ev-bottom-bar,.adg-bottom-nav,.visa-bottom-bar,.bottom-bar-991,.ev-991-bottom-nav,[class*="991-bottom"],[id*="991-bottom"],[id*="-bottom-bar"]'
    ).forEach(function(el){
      if(!el.classList.contains('foodies-footer-nav')) el.remove();
    });
  }

  function getWallet(){
    var keys=['foodies_wallet','foodiesIssuerWallet','ton_wallet','wallet_address','ev_wallet','connected_wallet'];
    for(var i=0;i<keys.length;i++){
      var v=localStorage.getItem(keys[i]) || sessionStorage.getItem(keys[i]);
      if(v && v.length>12) return v;
    }
    return window.FOODIES_WALLET || window.connectedWallet || '';
  }

  function shortWallet(w){
    return w && w.length>16 ? w.slice(0,6)+'...'+w.slice(-6) : w;
  }

  function render(){
    killRedBars();

    document.querySelectorAll('.foodies-footer-nav').forEach(function(el){ el.remove(); });

    var w = getWallet();
    var mode = new URLSearchParams(location.search).get('mode') || '';

    var nav = document.createElement('nav');
    nav.className = 'foodies-footer-nav';
    nav.innerHTML =
      '<div class="foodies-footer-row">' +
      '<a class="foodies-footer-btn '+(!mode?'is-active':'')+'" href="/foodies-nft/"><span class="foodies-footer-icon">🏠</span><span>Launcher</span></a>' +
      '<a class="foodies-footer-btn '+(mode==="issue"?'is-active':'')+'" href="/foodies-nft/?mode=issue"><span class="foodies-footer-icon">⭐</span><span>Issue</span></a>' +
      '<a class="foodies-footer-btn '+(mode==="reissue"?'is-active':'')+'" href="/foodies-nft/?mode=reissue"><span class="foodies-footer-icon">♻️</span><span>Re-Issue</span></a>' +
      '<a class="foodies-footer-btn '+(mode==="list"?'is-active':'')+'" href="/foodies-nft/?mode=list"><span class="foodies-footer-icon">📜</span><span>Issued</span></a>' +
      '<a class="foodies-footer-btn" href="https://getgems.io/foodies" target="_blank" rel="noopener"><span class="foodies-footer-icon">💎</span><span>Market</span></a>' +
      '<a class="foodies-footer-btn" href="/foodies-nft/?connect=1"><span class="foodies-footer-icon">'+(w?'✅':'🔐')+'</span><span>'+(w?'Wallet':'Login')+'</span></a>' +
      '</div>' +
      '<div class="foodies-footer-status"><span>🍔 Foodies NFT</span><span class="foodies-wallet-pill">'+(w?'Connected: '+shortWallet(w):'Not connected')+'</span></div>';

    document.body.appendChild(nav);
  }

  if(document.readyState === 'loading') document.addEventListener('DOMContentLoaded', render);
  else render();

  setTimeout(render,250);
  setTimeout(killRedBars,800);
})();
