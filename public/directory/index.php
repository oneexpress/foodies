<?php declare(strict_types=1); ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>ExpressVisa One · Service Directory</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    :root{--red:#d0021b;--dark:#111827;--muted:#6b7280;--line:#e5e7eb;--soft:#f8fafc}
    *{box-sizing:border-box}
    body{margin:0;font-family:Arial,Helvetica,sans-serif;background:var(--soft);color:var(--dark)}
    .wrap{max-width:1180px;margin:auto;padding:28px 18px 60px}
    .hero{background:#fff;border:1px solid var(--line);border-radius:24px;padding:28px;box-shadow:0 18px 45px rgba(15,23,42,.06)}
    h1{margin:0 0 8px;font-size:32px}
    .sub{color:var(--muted);line-height:1.6;margin:0 0 22px}
    .filters{display:grid;grid-template-columns:repeat(4,1fr);gap:12px}
    select,input{width:100%;height:48px;border:1px solid var(--line);border-radius:14px;padding:0 14px;background:#fff;font-size:15px}
    .grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-top:22px}
    .card{background:#fff;border:1px solid var(--line);border-radius:20px;padding:18px;box-shadow:0 10px 24px rgba(15,23,42,.04)}
    .badge{display:inline-flex;background:#fff1f2;color:var(--red);border:1px solid #fecdd3;border-radius:999px;padding:6px 10px;font-size:12px;font-weight:700}
    .title{font-size:18px;font-weight:800;margin:12px 0 8px}
    .meta{color:var(--muted);font-size:14px;line-height:1.5}
    .btn{display:inline-flex;margin-top:14px;background:var(--red);color:#fff;text-decoration:none;border-radius:12px;padding:11px 14px;font-weight:800}
    .empty{display:none;margin-top:20px;color:var(--muted);text-align:center}
    @media(max-width:900px){.filters,.grid{grid-template-columns:1fr}.hero{padding:20px}h1{font-size:26px}}
  </style>
</head>
<body>
<div class="wrap">
  <section class="hero">
    <h1>ExpressVisa Service Directory</h1>
    <p class="sub">Search all core services by category, nationality, location and area.</p>

    <div class="filters">
      <select id="service"><option value="">All Services</option></select>
      <select id="nationality"><option value="">All Nationalities</option></select>
      <select id="location"><option value="">All Locations</option></select>
      <select id="area"><option value="">All Areas</option></select>
    </div>

    <div id="results" class="grid"></div>
    <div id="empty" class="empty">No matching directory route found.</div>
  </section>
</div>

<script>
let TAX = null;

function opt(v,t){return `<option value="${v}">${t}</option>`}

async function init(){
  const res = await fetch('/api/visa-directory.php');
  const json = await res.json();
  TAX = json.data;

  service.innerHTML += TAX.services.map(x=>opt(x.key,x.label)).join('');
  nationality.innerHTML += TAX.nationalities.map(x=>opt(x.key,x.name)).join('');
  location.innerHTML += TAX.locations.map(x=>opt(x.key,x.name)).join('');

  [service,nationality,location,area].forEach(el=>el.addEventListener('change', render));
  location.addEventListener('change', syncAreas);
  render();
}

function syncAreas(){
  const loc = TAX.locations.find(x=>x.key===location.value);
  area.innerHTML = '<option value="">All Areas</option>' + (loc ? loc.areas.map(x=>opt(x,x)).join('') : '');
}

function render(){
  const svc = service.value;
  const nat = nationality.value;
  const loc = location.value;
  const ar = area.value;

  let services = TAX.services.filter(x=>!svc || x.key===svc);
  let locs = TAX.locations.filter(x=>!loc || x.key===loc);
  let nats = TAX.nationalities.filter(x=>!nat || x.key===nat);

  const rows = [];
  services.forEach(s=>{
    locs.forEach(l=>{
      const areas = ar ? [ar] : l.areas.slice(0,3);
      areas.forEach(a=>{
        rows.push({s,l,a,n:nats[0] || null});
      });
    });
  });

  results.innerHTML = rows.slice(0,36).map(r=>`
    <article class="card">
      <span class="badge">${r.s.label}</span>
      <div class="title">${r.s.name} · ${r.l.name}</div>
      <div class="meta">
        Area: ${r.a}<br>
        Nationality: ${r.n ? r.n.name : 'All eligible nationalities'}
      </div>
      <a class="btn" href="/marketplace/">Search Service</a>
    </article>
  `).join('');

  empty.style.display = rows.length ? 'none' : 'block';
}

init();
</script>
<script src="/assets/js/991-wallet-sync.js?v=991-unified"></script>
<script src="/assets/991-balance-sync.js?v=991-balance-sync-ux-v1" defer></script>
<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
</body>
</html>
