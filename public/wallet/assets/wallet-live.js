(function(){
  async function loadBalances(){
    const wallet =
      localStorage.getItem('ev_ton_wallet') ||
      document.body.getAttribute('data-ton-wallet') ||
      '';

    if(!wallet) return;

    const box = document.querySelector('[data-wallet-balances]');
    if(!box) return;

    try{
      const res = await fetch('/wallet/api/balances.php?wallet=' + encodeURIComponent(wallet), {cache:'no-store'});
      const data = await res.json();
      if(!data.ok) return;

      data.tokens.forEach(function(t){
        const row = box.querySelector('[data-token-row="'+t.symbol+'"]');
        if(row){
          const bal = row.querySelector('[data-token-balance]');
          if(bal) bal.textContent = t.balance;
        }
      });
    }catch(e){}
  }

  window.evLoadWalletBalances = loadBalances;
  document.addEventListener('DOMContentLoaded', loadBalances);
})();
