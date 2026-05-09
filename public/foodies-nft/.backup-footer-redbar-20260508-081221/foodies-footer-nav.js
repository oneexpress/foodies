(function(){
  'use strict';
  if (window.__FOODIES_FOOTER_NAV_FINAL__) return;
  window.__FOODIES_FOOTER_NAV_FINAL__ = true;

  function killBadBars(){
    document.querySelectorAll('#evBottomBar,#adgBottomNav,.ev-bottom-bar,.adg-bottom-nav,.visa-bottom-bar,.bottom-bar-991,[class*="991-bottom"],[id*="991-bottom"],[class*="-bottom-bar"],[id*="-bottom-bar"]').forEach(function(el){
      if (!el.classList.contains('foodies-footer-nav')) el.remove();
    });
  }

  function wallet(){
    var keys=['foodies_wallet','foodiesIssuerWallet','ton_wallet','wallet_address','ev_wallet','connected_wallet'];
    for(var i=0;i<keys.length;i++){
      var v=localStorage.getItem(keys[i]) || sessionStorage.getItem(keys[i]);
      if(v && v.length>12) return v;
    }
    return window.FOODIES_WALLET || window.connectedWallet || '';
  }

  function short(w){return w && w.length>16 ? w.slice(0,6)+'...'+w.slice(-6) : w;}

  function render(){
    killBadBars();
    document.querySelectorAll('.foodies-footer-nav').forEach(function(el,i){ if(i>0) el.remove(); });
    if(document.querySelector('.foodies-footer-nav')) return;

    var w=wallet();
    var path=location.pathname;
    var nav=document.createElement('nav');
    nav.className='foodies-footer-nav';
    nav.innerHTML =
      '<div class="foodies-footer-row">' +
      '<a class="foodies-footer-btn '+((path==='/foodies-nft/'||path.endsWith('/foodies-nft/index.php'))?'is-active':'')+'" href="/foodies-nft/"><span class="foodies-footer-icon">🏠</span><span>Launcher</span></a>' +
      '<a class="foodies-footer-btn" href="/foodies-nft/?mode=issue"><span class="foodies-footer-icon">⭐</span><span>Issue</span></a>' +
      '<a class="foodies-footer-btn" href="/foodies-nft/?mode=reissue"><span class="foodies-footer-icon">♻️</span><span>Re-Issue</span></a>' +
      '<a class="foodies-footer-btn" href="/foodies-nft/?mode=list"><span class="foodies-footer-icon">📜</span><span>Issued</span></a>' +
      '<a class="foodies-footer-btn" href="/wallet/"><span class="foodies-footer-icon">'+(w?'✅':'🔐')+'</span><span>'+(w?'Wallet':'Login')+'</span></a>' +
      '</div>' +
      '<div class="foodies-footer-status"><span>🍔 Foodies NFT</span><span class="foodies-wallet-pill">'+(w?'Connected: '+short(w):'Not connected')+'</span></div>';
    document.body.appendChild(nav);
  }

  if(document.readyState==='loading') document.addEventListener('DOMContentLoaded', render);
  else render();

  setTimeout(render,300);
  setTimeout(killBadBars,800);
})();
