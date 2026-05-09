(function(){
  const API = '/verify/api/check-vendor.php';

  function textOf(el){
    return (el && el.textContent ? el.textContent : '').trim();
  }

  function vendorNameFromCard(card){
    const selectors = [
      '.name a',
      '.caption .name',
      '.product-name',
      '.product-thumb .name',
      'h4 a',
      'h4',
      '.title',
      'a'
    ];
    for(const s of selectors){
      const el = card.querySelector(s);
      const t = textOf(el);
      if(t && t.length > 1) return t;
    }
    return '';
  }

  async function applyBadges(){
    const cards = document.querySelectorAll('.product-thumb, .product-layout, .product-item, .main-products .product-grid-item');
    for(const card of cards){
      if(card.dataset.evCertChecked === '1') continue;
      card.dataset.evCertChecked = '1';

      const name = vendorNameFromCard(card);
      if(!name) continue;

      try{
        const r = await fetch(API + '?vendor=' + encodeURIComponent(name), {cache:'no-store'});
        const j = await r.json();
        if(!j.ok || !j.cert || j.cert.status !== 'active') continue;

        const c = j.cert;
        const a = document.createElement('a');
        a.className = 'ev-cert-badge';
        a.href = '/verify/?c=' + encodeURIComponent(c.public_code);
        a.target = '_blank';
        a.rel = 'noopener';
        a.innerHTML = '✔ Certified Vendor <small>' + Number(c.avg_rating).toFixed(1) + '★</small>';

        const wrap = document.createElement('div');
        wrap.className = 'ev-cert-badge-wrap';
        wrap.appendChild(a);

        const target = card.querySelector('.caption') || card.querySelector('.name') || card;
        target.appendChild(wrap);
      }catch(e){}
    }
  }

  document.addEventListener('DOMContentLoaded', applyBadges);
  setTimeout(applyBadges, 800);
  setTimeout(applyBadges, 1800);
})();
