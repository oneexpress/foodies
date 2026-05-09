(function(){
  if (window.__EV991OffchainSync) return;
  window.__EV991OffchainSync = true;

  const API = '/wallet/api/offchain.php';

  function getWallet(){
    return (
      localStorage.getItem('ev_ton_wallet') ||
      localStorage.getItem('ton_wallet') ||
      localStorage.getItem('wallet') ||
      localStorage.getItem('connected_wallet') ||
      ''
    ).trim();
  }

  function setText(sel, val){
    document.querySelectorAll(sel).forEach(el => { el.textContent = val; });
  }

  async function sync(){
    const wallet = getWallet();
    document.documentElement.dataset.walletConnected = wallet ? '1' : '0';

    setText('[data-ev-wallet-status]', wallet ? 'Connected' : 'Connect Wallet');
    setText('[data-ev-wallet-short]', wallet ? wallet.slice(0,6)+'...'+wallet.slice(-4) : 'Not Connected');

    if (!wallet) return null;

    try {
      const r = await fetch(API + '?wallet=' + encodeURIComponent(wallet) + '&t=' + Date.now(), {cache:'no-store'});
      const j = await r.json();
      if (!j || !j.ok) return null;

      const o = j.offchain || {};
      setText('[data-offchain-vusdt]', Number(o.vusdt || 0).toFixed(6));
      setText('[data-offchain-vshare]', Number(o.vshare || 0).toFixed(6));
      setText('[data-score]', Number(o.score || 0).toFixed(6));
      setText('[data-daily-vshare]', Number(j.daily_vshare || 0).toFixed(6));
      setText('[data-daily-cap]', Number(j.daily_cap || 10).toFixed(0));
      setText('[data-last-sync]', j.last_sync || new Date().toISOString());

      window.dispatchEvent(new CustomEvent('ev991:offchain-sync', {detail:j}));
      return j;
    } catch(e) {
      return null;
    }
  }

  window.EV991OffchainSync = {sync, getWallet};
  document.addEventListener('DOMContentLoaded', sync);
  window.addEventListener('storage', sync);
  window.addEventListener('ev991:wallet-connected', sync);
  setInterval(sync, 30000);
})();
