(function(){
  'use strict';

  let lang = localStorage.getItem('ev_foodies_lang') || 'en';
  let state = { profile:{total_vshare:'0',total_score:'0'}, tiers:[], records:[] };

  function $(id){ return document.getElementById(id); }
  function n(v){ return Number(v || 0).toLocaleString(undefined,{minimumFractionDigits:6,maximumFractionDigits:6}); }
  function esc(s){ return String(s || '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }
  async function api(path,opt={}){ const r = await fetch(path,Object.assign({credentials:'same-origin',cache:'no-store'},opt)); return await r.json(); }

  function applyLang(){
    document.documentElement.lang = lang === 'zh' ? 'zh' : 'en';
    document.querySelectorAll('[data-en][data-zh]').forEach(el => el.textContent = el.dataset[lang]);
    $('langBtn').textContent = lang === 'en' ? '中文' : 'EN';
  }

  function tierName(t){ return lang === 'zh' ? t.name_zh : t.name_en; }

  function render(){
    $('balVshare').textContent = n(state.profile.total_vshare);
    $('balScore').textContent = n(state.profile.total_score);
    $('totalWeight').textContent = String(state.total_weight || 0);

    const vshare = Number(state.profile.total_vshare || 0);
    const score = Number(state.profile.total_score || 0);

    $('tierGrid').innerHTML = state.tiers.map(t => {
      const ok = vshare >= Number(t.vshare) && score >= Number(t.score);
      return `
      <article class="glass tier-card">
        <div class="tier-body">
          <div class="tier-icon">${esc(t.icon)}</div>
          <div class="tier-name">${esc(t.name_en)}</div>
          <div class="tier-zh">${esc(t.name_zh)}</div>
          <div class="stars">${esc(t.stars)}</div>
          <div class="req">
            <div><span>vSHARE</span><b>${esc(t.vshare)}</b></div>
            <div><span>${lang === 'zh' ? '积分' : 'Score'}</span><b>${esc(t.score)}</b></div>
          </div>
          <div class="weight">${lang === 'zh' ? '权重' : 'Weight'}: ${esc(t.weight)}</div>
          <button class="redeem-btn" data-tier="${t.tier_no}" ${ok ? '' : 'disabled'}>
            ${ok ? (lang === 'zh' ? '兑换 NFT' : 'Redeem NFT') : (lang === 'zh' ? '资格不足' : 'Not Eligible')}
          </button>
        </div>
      </article>`;
    }).join('');

    document.querySelectorAll('.redeem-btn[data-tier]').forEach(btn => {
      btn.addEventListener('click', () => redeem(btn.dataset.tier));
    });

    const records = state.records || [];
    $('records').innerHTML = records.length ? records.map(r => `
      <div class="row">
        <div><b>${esc(lang === 'zh' ? r.tier_name_zh : r.tier_name_en)}</b><br><small>${esc(r.redeem_uid)} · ${esc(r.status)}</small></div>
        <small>${esc(r.created_at)}</small>
      </div>
    `).join('') : (lang === 'zh' ? '暂无记录。' : 'No records yet.');
  }

  async function load(){
    const j = await api('/foodies-nft/api/status.php?ts=' + Date.now());
    if(!j.ok) return;
    state = j;
    render();
  }

  async function redeem(tier){
    $('statusMsg').textContent = lang === 'zh' ? '处理中...' : 'Processing...';
    const j = await api('/foodies-nft/api/redeem.php', {
      method:'POST',
      headers:{'Content-Type':'application/json'},
      body:JSON.stringify({tier_no:Number(tier)})
    });
    $('statusMsg').textContent = j.ok ? (lang === 'zh' ? '兑换申请已创建' : 'Redeem request created') : (j.error || 'Failed');
    await load();
  }

  $('langBtn').addEventListener('click', () => {
    lang = lang === 'en' ? 'zh' : 'en';
    localStorage.setItem('ev_foodies_lang', lang);
    applyLang();
    render();
  });

  applyLang();
  load();
})();
