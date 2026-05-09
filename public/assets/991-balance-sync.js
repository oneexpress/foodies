(function(){
  if(window.__EV991_BALANCE_SYNC__) return;
  window.__EV991_BALANCE_SYNC__ = true;

  function txt(el,v){ if(el) el.textContent = v; }

  async function load991Balances(){
    try{
      const r = await fetch('/api/auth/status.php', {credentials:'include', cache:'no-store'});
      const j = await r.json();
      if(!j || !j.ok) return;

      window.__EV991_AUTH_STATUS__ = j;

      document.querySelectorAll('[data-wallet-vusdt]').forEach(el => txt(el, j.balance_vusdt || '0.00'));
      document.querySelectorAll('[data-wallet-vshare]').forEach(el => txt(el, j.balance_vshare || '0.000000'));
      document.querySelectorAll('[data-wallet-score]').forEach(el => txt(el, String(parseInt(j.score || 0, 10))));
      document.querySelectorAll('[data-wallet-usdt-ton]').forEach(el => txt(el, j.balance_usdt_ton || '0.000000'));
      document.querySelectorAll('[data-wallet-ton]').forEach(el => txt(el, j.balance_ton || '0.000000'));

      const cap = document.getElementById('ev-merged-wallet-capsule');
      if(cap){
        const led = cap.querySelector('.ev-wallet-led');
        const bal = cap.querySelector('.ev-wallet-balance');
        const st  = cap.querySelector('.ev-wallet-status');
        if(led){
          led.classList.toggle('on', !!j.wallet_connected);
          led.classList.toggle('off', !j.wallet_connected);
        }
        if(bal) bal.textContent = (j.balance_vusdt || '0.00') + ' vUSDT';
        if(st) st.textContent = j.wallet_connected ? (j.short_wallet || 'CONNECTED') : 'CONNECT WALLET';
      }

      ['vshare','score'].forEach(function(id){
        const el = document.getElementById(id);
        if(!el) return;
        if(id === 'vshare') el.textContent = j.balance_vshare || '0.000000';
        if(id === 'score') el.textContent = String(parseInt(j.score || 0, 10));
      });

      window.dispatchEvent(new CustomEvent('ev991:balance', {detail:j}));
    }catch(e){}
  }

  if(document.readyState === 'loading') document.addEventListener('DOMContentLoaded', load991Balances);
  else load991Balances();

  window.__EV991_BALANCE_RELOAD__ = load991Balances;
  setInterval(load991Balances, 5000);
})();
