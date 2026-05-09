(function(){
  'use strict';

  const ids = {
    'vSHARE':'bal-vshare',
    'vUSDT':'bal-vusdt',
    'USDT-TON':'bal-usdt-ton',
    'Native TON':'bal-ton'
  };

  const units = {
    'vSHARE':'unit-vshare',
    'vUSDT':'unit-vusdt'
  };

  const state = {
    address: '',
    mode: { 'vSHARE':'offchain', 'vUSDT':'offchain' },
    offchain: { 'vSHARE':'0.000000', 'vUSDT':'0.000000' },
    onchain: { 'vSHARE':null, 'vUSDT':null, 'USDT-TON':'0.000000', 'Native TON':'0.000000' },
    configured: { 'vSHARE':false, 'vUSDT':false }
  };

  function setStatus(text){
    const el = document.getElementById('walletStatus');
    if (el) el.textContent = text;
  }

  function setBalance(symbol, balance){
    const el = document.getElementById(ids[symbol]);
    if (el) el.textContent = balance ?? '0.000000';
  }

  function updateToken(symbol){
    if (symbol === 'vSHARE' || symbol === 'vUSDT') {
      const mode = state.mode[symbol];
      const value = mode === 'onchain'
        ? (state.onchain[symbol] ?? 'Not deployed')
        : (state.offchain[symbol] ?? '0.000000');

      setBalance(symbol, value);

      const unit = document.getElementById(units[symbol]);
      if (unit) unit.textContent = symbol + ' ' + mode + ' balance';

      document.querySelectorAll('.mode-switch[data-token="'+symbol+'"] .mode-btn').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.mode === mode);
      });
    }
  }

  async function loadTokens(){
    try{
      const res = await fetch('/wallet/api/tokens.php?ts=' + Date.now(), {cache:'no-store', credentials:'same-origin'});
      const data = await res.json();
      (data.tokens || []).forEach(t => {
        if (t.symbol === 'vSHARE' || t.symbol === 'vUSDT') {
          state.offchain[t.symbol] = t.balance || '0.000000';
          updateToken(t.symbol);
        } else {
          setBalance(t.symbol, t.balance || '0.000000');
        }
      });
    }catch(e){}
  }

  async function loadOnchain(address){
    if (!address) return;
    try{
      setStatus('Loading live on-chain balances...');
      const res = await fetch('/wallet/api/onchain-balances.php?address=' + encodeURIComponent(address) + '&ts=' + Date.now(), {
        cache:'no-store',
        credentials:'same-origin'
      });
      const data = await res.json();

      if (data && data.ok && data.balances) {
        state.onchain = Object.assign(state.onchain, data.balances);
        state.configured = Object.assign(state.configured, data.configured || {});

        setBalance('USDT-TON', data.balances['USDT-TON']);
        setBalance('Native TON', data.balances['Native TON']);
        updateToken('vSHARE');
        updateToken('vUSDT');

        setStatus('Wallet ready · live on-chain balances loaded');
      } else {
        setStatus('Wallet ready · on-chain balance unavailable');
      }
    }catch(e){
      setStatus('Wallet ready · on-chain balance unavailable');
    }
  }

  function bindSwitches(){
    document.querySelectorAll('.mode-switch .mode-btn').forEach(btn => {
      btn.addEventListener('click', function(){
        const box = btn.closest('.mode-switch');
        const symbol = box ? box.dataset.token : '';
        const mode = btn.dataset.mode;

        if (!symbol || !mode) return;

        if (mode === 'onchain' && !state.address) {
          setStatus('Connect TON wallet first to view on-chain ' + symbol + ' balance');
          return;
        }

        state.mode[symbol] = mode;
        updateToken(symbol);

        if (mode === 'onchain') {
          loadOnchain(state.address);
        }
      });
    });
  }

  function initTonConnect(){
    if (!window.TON_CONNECT_UI) return;

    const root = document.getElementById('ton-connect');
    if (!root) return;

    const ui = new TON_CONNECT_UI.TonConnectUI({
      manifestUrl: location.origin + '/tonconnect-manifest.json',
      buttonRootId: 'ton-connect'
    });

    let last = localStorage.getItem('ev_bound_ton') || '';

    ui.onStatusChange(async function(wallet){
      const addr = wallet && wallet.account && wallet.account.address ? wallet.account.address : '';

      if (!addr) {
        state.address = '';
        last = '';
        localStorage.removeItem('ev_bound_ton');
        setStatus('Wallet ready · TON wallet not connected');
        return;
      }

      state.address = addr;
      loadOnchain(addr);

      if (addr === last) return;
      last = addr;
      localStorage.setItem('ev_bound_ton', addr);

      try{
        await fetch('/wallet/api/bind-ton.php', {
          method:'POST',
          headers:{'Content-Type':'application/json'},
          credentials:'same-origin',
          body:JSON.stringify({address:addr})
        });
      }catch(e){}
    });
  }

  document.addEventListener('DOMContentLoaded', function(){
    bindSwitches();
    loadTokens();
    initTonConnect();
  });
})();

// FoodBank Donation — vUSDT offchain debit
document.addEventListener('DOMContentLoaded', function(){
  const btn = document.getElementById('foodbankDonateBtn');
  const amountEl = document.getElementById('foodbankAmount');
  const msg = document.getElementById('foodbankMsg');
  if (!btn || !amountEl) return;

  btn.addEventListener('click', async function(){
    const amount = Number(amountEl.value || 0);
    if (!amount || amount <= 0) {
      if (msg) msg.textContent = 'Enter a valid vUSDT amount.';
      return;
    }

    btn.disabled = true;
    if (msg) msg.textContent = 'Submitting FoodBank donation...';

    try {
      const res = await fetch('/wallet/api/foodbank-donate.php', {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        credentials:'same-origin',
        body:JSON.stringify({
          amount_vusdt: amount,
          memo: 'FoodBank Donation'
        })
      });
      const data = await res.json();
      if (data.ok) {
        if (msg) msg.textContent = 'Donation confirmed: ' + data.amount_vusdt + ' vUSDT';
        amountEl.value = '';
        setTimeout(() => location.reload(), 900);
      } else {
        if (msg) msg.textContent = data.error || 'Donation failed';
      }
    } catch(e) {
      if (msg) msg.textContent = 'Network error';
    }

    btn.disabled = false;
  });
});
