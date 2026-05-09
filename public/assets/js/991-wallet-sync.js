(function(){
  if(window.__EV991_WALLET_SYNC__) return;
  window.__EV991_WALLET_SYNC__ = true;

  async function refresh991WalletState(){
    try{
      const r = await fetch('/api/auth/status.php?_=' + Date.now(), {
        credentials:'include'
      });

      const j = await r.json();

      const bubbles = document.querySelectorAll(
        '.ev-wallet-bubble,.ev-connect-bubble'
      );

      bubbles.forEach(function(el){

        const bal = el.querySelector('.ev-balance');
        const stat = el.querySelector('.ev-status');
        const led = el.querySelector('.ev-led');

        if(bal){
          bal.textContent =
            (j.balance_vusdt || '0.00') + ' vUSDT';
        }

        if(stat){
          stat.textContent =
            j.wallet_connected
              ? 'CONNECTED'
              : 'CONNECT WALLET';
        }

        if(led){
          led.style.background =
            j.wallet_connected
              ? '#00ff66'
              : '#ff3344';
        }

        el.classList.toggle(
          'connected',
          !!j.wallet_connected
        );
      });

      window.__EV991_AUTH__ = j;

    }catch(e){}
  }

  setInterval(refresh991WalletState, 4000);
  refresh991WalletState();

})();
