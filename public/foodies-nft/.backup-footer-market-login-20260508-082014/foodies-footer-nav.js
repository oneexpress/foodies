(function(){
  'use strict';
  if(window.__FOODIES_FOOTER_NAV_RED_FIX__) return;
  window.__FOODIES_FOOTER_NAV_RED_FIX__ = true;

  function killRedBars(){
    document.querySelectorAll(
      '#evBottomBar,#adgBottomNav,#visaBottomBar,.ev-bottom-bar,.adg-bottom-nav,.visa-bottom-bar,.bottom-bar-991,.ev-991-bottom-nav,[class*="991-bottom"],[id*="991-bottom"],[id*="-bottom-bar"]'
    ).forEach(function(el){
      if(!el.classList.contains('foodies-footer-nav')) el.remove();
    });

    document.querySelectorAll('body > nav, body > div').forEach(function(el){
      if(el.classList.contains('foodies-footer-nav')) return;
      var st = getComputedStyle(el);
      var txt = (el.textContent || '').trim();
      if(st.position === 'fixed' && (st.bottom === '0px' || parseInt(st.bottom || '999',10) < 40)){
        if(/Foodies NFT|Issue New|Re-Issue|Verify|Wallet|CONNECT WALLET|LOGIN: OFF/i.test(txt)){
          el.remove();
        }
      }
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

    document.querySelectorAll('.foodies-footer-nav').forEach(function(el,i){
      if(i>0) el.remove();
    });

    if(document.querySelector('.foodies-footer-nav')) return;

    var w = getWallet();
    var mode = new URLSearchParams(location.search).get('mode') || '';

    var nav = document.createElement('nav');
    nav.className = 'foodies-footer-nav';
    nav.innerHTML =
      '<div class="foodies-footer-row">' +
      '<a class="foodies-footer-btn '+(!mode?'is-active':'')+'" href="/foodies-nft/"><span class="foodies-footer-icon">🏠</span><span>Launcher</span></a>' +
      '<a class="foodies-footer-btn '+(mode==='issue'?'is-active':'')+'" href="/foodies-nft/?mode=issue"><span class="foodies-footer-icon">⭐</span><span>Issue</span></a>' +
      '<a class="foodies-footer-btn '+(mode==='reissue'?'is-active':'')+'" href="/foodies-nft/?mode=reissue"><span class="foodies-footer-icon">♻️</span><span>Re-Issue</span></a>' +
      '<a class="foodies-footer-btn '+(mode==='list'?'is-active':'')+'" href="/foodies-nft/?mode=list"><span class="foodies-footer-icon">📜</span><span>Issued</span></a>' +
      '<a class="foodies-footer-btn" href="/wallet/"><span class="foodies-footer-icon">'+(w?'✅':'🔐')+'</span><span>'+(w?'Wallet':'Login')+'</span></a>' +
      '</div>' +
      '<div class="foodies-footer-status"><span>🍔 Foodies NFT</span><span class="foodies-wallet-pill">'+(w?'Connected: '+shortWallet(w):'Not connected')+'</span></div>';

    document.body.appendChild(nav);
  }

  if(document.readyState === 'loading') document.addEventListener('DOMContentLoaded', render);
  else render();

  setTimeout(render,200);
  setTimeout(killRedBars,600);
  setTimeout(killRedBars,1200);
})();
