(function(){
  if(window.__EV991_REWARDS_LIVE__) return;
  window.__EV991_REWARDS_LIVE__ = true;

  const $ = id => document.getElementById(id);
  const dict = {
    en:{
      eyebrow:'ExpressVisa Rewards Engine',
      title:'Digging Reward Engine',
      subtitle:'0.003 vSHARE reward / 10s · vUSDT speed boost · 10 vSHARE daily cap',
      guard:'✅ TON / Unified login verified · Digging enabled',
      microcopy:'vSHARE = 微股 · vUSDT = 微定币',
      wallet:'Wallet',
      vshareBalance:'vSHARE Balance',
      vshareDesc:'Foodies Rewards participation token',
      score:'Score',
      scoreDesc:'Participation reputation layer',
      dailyCap:'Daily RPM Cap',
      dailyCapDesc:'Progress toward 10 vSHARE daily capacity',
      cycle:'Digging Cycle',
      cycleDesc:'10-second repeating reward charge',
      booster:'Booster Slider',
      boosterDesc:'Minimum 1 vUSDT. Boost increases charging speed only.',
      vusdtBalance:'vUSDT Offchain Balance',
      proof:'Proof Hash Stream',
      proofDesc:'Live nonce hash proof every reward cycle',
      collect:'COLLECT REWARD',
      ledger:'Reward Ledger',
      openWallet:'Open Wallet',
      noReward:'No reward to collect yet.',
      collected:'Reward collected. vSHARE / 微股 credited.',
      loginRequired:'TON login required. Redirecting to signup...'
    },
    zh:{
      eyebrow:'ExpressVisa 奖励引擎',
      title:'Digging 挖矿奖励引擎',
      subtitle:'每 10 秒 0.003 vSHARE / 微股 · vUSDT 微定币加速 · 每日上限 10 vSHARE',
      guard:'✅ TON / 统一登录已验证 · 可开始 Digging',
      microcopy:'vSHARE = 微股 · vUSDT = 微定币',
      wallet:'钱包',
      vshareBalance:'vSHARE / 微股余额',
      vshareDesc:'Foodies Rewards 参与权益通证',
      score:'积分',
      scoreDesc:'参与信誉层',
      dailyCap:'每日 RPM 上限',
      dailyCapDesc:'每日 10 vSHARE / 微股容量进度',
      cycle:'Digging 周期',
      cycleDesc:'每 10 秒循环充能奖励',
      booster:'Booster 加速器',
      boosterDesc:'最低 1 vUSDT / 微定币。加速只影响充能速度。',
      vusdtBalance:'vUSDT / 微定币链下余额',
      proof:'证明哈希流',
      proofDesc:'每个奖励周期生成实时 nonce hash proof',
      collect:'领取奖励',
      ledger:'奖励账本',
      openWallet:'打开钱包',
      noReward:'暂无可领取奖励。',
      collected:'领取成功，vSHARE / 微股已入账。',
      loginRequired:'需要 TON 登录，即将跳转到统一登录...'
    }
  };

  let lang = localStorage.getItem('ev991_lang') || 'en';
  let state = {
    vshare:0, score:0, dailyEarned:0, dailyCap:10,
    pending:0, rewardPerCycle:0.003, boostMultiplier:1,
    vusdtOffchain:0, cycle:10, nonce:'Waiting for rewards proof ...',
    ledger:[]
  };
  let timer = null;

  function n(v){ const x = Number(v); return Number.isFinite(x) ? x : 0; }
  function f(v){ return n(v).toFixed(6); }
  function t(k){ return (dict[lang] && dict[lang][k]) || dict.en[k] || k; }
  function setText(id, val){ const el=$(id); if(el) el.textContent = val; }

  function applyLang(){
    document.documentElement.lang = lang === 'zh' ? 'zh' : 'en';
    document.querySelectorAll('[data-i18n]').forEach(el=>{
      const key=el.dataset.i18n;
      if(dict[lang] && dict[lang][key]) el.textContent=dict[lang][key];
    });
    document.querySelectorAll('[data-lang]').forEach(btn=>btn.classList.toggle('active', btn.dataset.lang===lang));
  }

  function renderLedger(){
    const box = $('ledgerRows');
    if(!box) return;
    if(!state.ledger || !state.ledger.length){
      box.innerHTML = '<div class="hash-line">No reward ledger yet.</div>';
      return;
    }
    box.innerHTML = state.ledger.slice(0,12).map(r=>{
      const amt = f(r.amount || r.credit || 0);
      const at = r.created_at || r.time || '';
      return '<div class="hash-line">+'+amt+' vSHARE / 微股 · '+at+'</div>';
    }).join('');
  }

  function proofLine(){
    const h = state.nonce || ('991-'+Date.now().toString(16)+'-'+Math.random().toString(16).slice(2,10));
    setText('proofHash','Nonce: '+h);
    setText('proofStatus','LIVE · '+new Date().toLocaleTimeString());
    const stream = $('hashStream');
    if(stream){
      const div=document.createElement('div');
      div.className='hash-line';
      div.textContent=h;
      stream.prepend(div);
      while(stream.children.length>8) stream.removeChild(stream.lastChild);
    }
  }

  function render(){
    applyLang();

    const totalDaily = Math.min(state.dailyCap, state.dailyEarned + state.pending);
    const pct = state.dailyCap > 0 ? Math.min(100, Math.round((totalDaily/state.dailyCap)*100)) : 0;

    setText('vshare', f(state.vshare));
    setText('score', f(state.score));
    setText('dailyEarned', f(totalDaily));
    setText('rpmPercent', pct+'%');
    setText('capStatus', pct >= 100 ? 'Daily cap reached' : 'Charging');
    setText('timeToFull', pct >= 100 ? 'Full' : 'Full in --');
    setText('rewardRate', '+'+f(state.rewardPerCycle)+' reward / 10s');
    setText('hourRate', f(state.rewardPerCycle*360)+' / hour');
    setText('boostMultiplier', n(state.boostMultiplier).toFixed(3)+'x');
    setText('boostMax', Math.floor(state.vusdtOffchain)+' vUSDT');
    setText('boostTier', state.vusdtOffchain >= 1 ? 'Boost available' : 'Need minimum 1 vUSDT');
    setText('boostSelected', String(Math.floor(state.vusdtOffchain)));
    setText('vusdtOffchainBalance', f(state.vusdtOffchain));

    const ring = $('rpmRing'); if(ring) ring.style.setProperty('--rpm', pct+'%');
    const fill = $('cycleFill'); if(fill) fill.style.height = Math.max(0, Math.min(100, ((10-state.cycle)/10)*100)) + '%';
    setText('cycleCountdown', state.cycle+'s');

    const slider = $('boostSlider');
    const boostFill = $('boostFill');
    if(slider){
      const max = Math.max(1, Math.floor(state.vusdtOffchain));
      slider.max = max;
      slider.disabled = max < 1;
      slider.value = max;
    }
    if(boostFill){
      const max = Math.max(1, Math.floor(state.vusdtOffchain));
      boostFill.style.width = state.vusdtOffchain >= 1 ? '100%' : '0%';
    }

    renderLedger();
  }

  async function api(path, body){
    const opt = {credentials:'same-origin'};
    if(body){
      opt.method='POST';
      opt.headers={'Content-Type':'application/json'};
      opt.body=JSON.stringify(body);
    }
    const r = await fetch(path + (path.includes('?')?'&':'?') + 'v=' + Date.now(), opt);
    const j = await r.json().catch(()=>({ok:false,error:'Invalid JSON'}));
    if(!j.ok) throw new Error(j.error || j.message || 'API failed');
    return j;
  }

  window.__EV991_REWARDS_RELOAD__ = function(){ try{return load();}catch(e){} };
  async function load(){
    try{
      const j = await api('/rewards/api/status.php');
      state.vshare = n(j.vshare);
      state.score = n(j.score);
      state.dailyEarned = n(j.daily_earned);
      state.dailyCap = n(j.daily_cap || 10);
      state.pending = n(j.pending_reward);
      state.rewardPerCycle = n(j.reward_per_cycle || 0.003);
      state.boostMultiplier = n(j.boost_multiplier || 1);
      state.vusdtOffchain = n(j.vusdt_offchain);
      state.ledger = j.ledger || [];
      state.nonce = j.proof_nonce || state.nonce;
      setText('msg','Boost affects speed only. Daily reward remains capped at 10 vSHARE.');
      render();
    }catch(e){
      setText('msg', t('loginRequired'));
      setTimeout(()=>{ location.href='/signup/?next=/rewards/&reason=ton_required'; }, 800);
    }
  }

  function startCycle(){
    if(timer) clearInterval(timer);
    timer = setInterval(()=>{
      state.cycle -= 1;
      if(state.cycle <= 0){
        state.cycle = 10;
        const next = Math.min(state.dailyCap - state.dailyEarned, state.pending + state.rewardPerCycle);
        state.pending = Math.max(0, next);
        proofLine();
      }
      render();
    },1000);
  }

  async function collect(){
    const btn = $('collectBtn');
    if(state.pending <= 0){
      setText('msg', t('noReward'));
      return;
    }
    if(btn) btn.disabled = true;
    try{
      const j = await api('/rewards/api/collect.php',{amount:state.pending});
      state.pending = 0;
      state.vshare = n(j.vshare);
      state.dailyEarned = n(j.daily_earned);
      state.ledger = j.ledger || state.ledger;
      setText('msg', t('collected'));
      await load();
    }catch(e){
      setText('msg', 'Collect failed: '+e.message);
    }
    if(btn) btn.disabled = false;
    render();
  }

  document.addEventListener('DOMContentLoaded', ()=>{
    document.querySelectorAll('[data-lang]').forEach(btn=>{
      btn.addEventListener('click', ()=>{
        lang = btn.dataset.lang || 'en';
        localStorage.setItem('ev991_lang', lang);
        render();
      });
    });
    const btn = $('collectBtn');
    if(btn) btn.addEventListener('click', collect);
    load();
    startCycle();
    setInterval(load, 30000);
  });
})();

/* 991 Rewards Unified Wallet Sync */
(function(){
  async function syncRewardsWalletStatus(){
    try{
      const r = await fetch('/api/auth/status.php', {credentials:'include', cache:'no-store'});
      const j = await r.json();
      if(!j || !j.ok) return;

      if(!j.wallet_connected){
        const msg = document.getElementById('msg');
        if(msg) msg.textContent = 'TON / Unified login required before digging.';
        document.body.classList.add('ev-digging-locked');
      }else{
        document.body.classList.remove('ev-digging-locked');
      }

      if(typeof window.__EV991_REWARDS_RELOAD__ === 'function'){
        window.__EV991_REWARDS_RELOAD__();
      }
    }catch(e){}
  }

  if(document.readyState === 'loading'){
    document.addEventListener('DOMContentLoaded', syncRewardsWalletStatus);
  }else{
    syncRewardsWalletStatus();
  }

  setInterval(syncRewardsWalletStatus, 10000);
})();

/* 991 Rewards Balance Sync */
(function(){
  async function syncRewardsBalances(){
    try{
      const r = await fetch('/api/auth/status.php', {credentials:'include', cache:'no-store'});
      const j = await r.json();
      if(!j || !j.ok) return;

      const vshare = document.getElementById('vshare');
      const score  = document.getElementById('score');

      if(vshare) vshare.textContent = j.balance_vshare || '0.000000';
      if(score) score.textContent = String(parseInt(j.score || 0, 10));

      document.body.classList.toggle('ev-wallet-connected', !!j.wallet_connected);
      document.body.classList.toggle('ev-wallet-disconnected', !j.wallet_connected);
    }catch(e){}
  }

  if(document.readyState === 'loading'){
    document.addEventListener('DOMContentLoaded', syncRewardsBalances);
  }else{
    syncRewardsBalances();
  }
  setInterval(syncRewardsBalances, 8000);
})();

/* 991 immediate balance refresh after rewards actions */
(function(){
  const oldFetch = window.fetch;
  if(window.__EV991_REWARD_FETCH_PATCH__) return;
  window.__EV991_REWARD_FETCH_PATCH__ = true;
  window.fetch = async function(){
    const res = await oldFetch.apply(this, arguments);
    try{
      const url = String(arguments[0] || '');
      if(url.indexOf('/rewards/api/collect.php') >= 0 || url.indexOf('/rewards/api/dig') >= 0){
        setTimeout(function(){
          if(window.__EV991_BALANCE_RELOAD__) window.__EV991_BALANCE_RELOAD__();
          if(window.__EV991_REWARDS_RELOAD__) window.__EV991_REWARDS_RELOAD__();
        }, 300);
      }
    }catch(e){}
    return res;
  };
})();



/* 991 DIGGING SFX REMAP — SIMPLE FINAL */
(function(){
  if(window.__EV991_DIGGING_SFX_SIMPLE__) return;
  window.__EV991_DIGGING_SFX_SIMPLE__ = true;

  const KEY = '991_sfx_enabled';
  const BASE = '/assets/sfx/';
  const FILES = {
    click: 'click.mp3',
    gold: 'click_gold.mp3',
    start: 'mining_start.mp3',
    loop: 'mining_loop.mp3',
    reward: 'mining_done.mp3',
    success: 'success.mp3',
    error: 'error.mp3',
    boot: 'boot.mp3'
  };

  let enabled = localStorage.getItem(KEY);
  enabled = enabled === null ? true : enabled === '1';

  const cache = {};
  let loopAudio = null;
  let rewardTimer = null;

  function isOn(){ return enabled === true; }
  function save(){ localStorage.setItem(KEY, isOn() ? '1' : '0'); }

  function audio(name){
    if(!FILES[name]) return null;
    if(!cache[name]){
      cache[name] = new Audio(BASE + FILES[name]);
      cache[name].preload = 'auto';
      cache[name].volume = name === 'loop' ? 0.20 : 0.72;
    }
    cache[name].muted = !isOn();
    return cache[name];
  }

  function play(name){
    if(!isOn()) return;
    try{
      const a = audio(name);
      if(!a) return;
      a.loop = false;
      a.currentTime = 0;
      a.play().catch(()=>{});
    }catch(e){}
  }

  function stopDiggingSfx(){
    try{
      if(loopAudio){
        loopAudio.pause();
        loopAudio.currentTime = 0;
      }
    }catch(e){}
    loopAudio = null;
    if(rewardTimer){
      clearInterval(rewardTimer);
      rewardTimer = null;
    }
  }

  function startDiggingSfx(){
    if(!isOn()) return;

    play('start');

    setTimeout(function(){
      if(!isOn()) return;
      stopDiggingSfx();
      loopAudio = audio('loop');
      if(loopAudio){
        loopAudio.loop = true;
        loopAudio.currentTime = 0;
        loopAudio.play().catch(()=>{});
      }

      rewardTimer = setInterval(function(){
        if(!isOn()) return;
        play('reward');
        setTimeout(function(){ play('success'); }, 240);
      }, 10000);
    }, 400);
  }

  function renderSwitch(){
    const btn = document.getElementById('ev991SoundBtn');
    if(!btn) return;
    btn.textContent = isOn() ? 'Sound ON' : 'Sound OFF';
    btn.classList.toggle('is-off', !isOn());
    btn.setAttribute('aria-pressed', isOn() ? 'true' : 'false');
  }

  function toggle(){
    enabled = !isOn();
    save();
    renderSwitch();

    if(isOn()){
      play('gold');
    }else{
      stopDiggingSfx();
    }
  }

  window.EV991_REWARDS_SFX = {
    play,
    startMiningSfx: startDiggingSfx,
    stopMiningSfx: stopDiggingSfx,
    isEnabled: isOn,
    setEnabled: function(v){
      enabled = !!v;
      save();
      renderSwitch();
      if(!enabled) stopDiggingSfx();
    }
  };

  window.play991Sound = function(name){
    const key = Object.keys(FILES).find(k => FILES[k] === name) || name;
    play(key);
  };

  document.addEventListener('click', function(e){
    const sw = e.target.closest('#ev991SoundBtn');
    if(sw){
      toggle();
      return;
    }

    const el = e.target.closest('button,a,input[type=range]');
    if(!el || !isOn()) return;

    if(el.id === 'collectBtn'){
      play('gold');
      startDiggingSfx();
      return;
    }

    const txt = (el.textContent || '').toLowerCase();
    if(txt.includes('start digging') || txt.includes('collect reward') || txt.includes('claim')){
      startDiggingSfx();
      return;
    }

    play('click');
  }, true);

  let lastCycle = '';
  setInterval(function(){
    const cd = document.getElementById('cycleCountdown');
    const val = cd ? cd.textContent.trim() : '';
    if(val && val !== lastCycle){
      lastCycle = val;
      if(isOn() && val === '10s'){
        play('reward');
        setTimeout(function(){ play('success'); }, 240);
      }
    }
  }, 700);

  document.addEventListener('DOMContentLoaded', function(){
    renderSwitch();
  });

  renderSwitch();
})();
