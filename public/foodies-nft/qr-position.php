<?php
declare(strict_types=1);

/**
 * /foodies-nft/qr-position.php
 * ExpressVisa 991 Foodies NFT minting QR position tool.
 *
 * Purpose:
 * - position Verify URL QR for issued Foodies NFT artifact
 * - QR target = /foodies-nft/verify.php?uid={cert_uid}
 * - no DB write
 * - no nginx
 * - no global bottom nav
 */

header('Content-Type: text/html; charset=UTF-8');

$templates = [
    1 => [
        'label' => '1 Star · Recommended Card',
        'file' => '/metadata/foodies/NFT/1_star_foodies.png',
        'qr' => ['x' => 56, 'y' => 800, 'size' => 145],
    ],
    3 => [
        'label' => '3 Stars · Premium Card',
        'file' => '/metadata/foodies/NFT/3_stars_foodies.png',
        'qr' => ['x' => 56, 'y' => 800, 'size' => 145],
    ],
    5 => [
        'label' => '5 Stars · Master Chef Card',
        'file' => '/metadata/foodies/NFT/5_stars_foodies.png',
        'qr' => ['x' => 56, 'y' => 800, 'size' => 145],
    ],
];

function h($v): string {
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Foodies NFT QR Position</title>
<style>
:root{--red:#E60012;--dark:#B8000E;--bg:#F7F8FA;--ink:#161616;--muted:#667085;--line:#E5E7EB;--gold:#F59E0B}
*{box-sizing:border-box}
body{margin:0;background:var(--bg);color:var(--ink);font-family:Arial,Helvetica,sans-serif}
.wrap{max-width:1380px;margin:0 auto;padding:18px}
.top{display:flex;justify-content:space-between;gap:12px;align-items:center;margin-bottom:14px}
.brand{font-weight:900;color:var(--red)}
.btn{border:0;border-radius:12px;background:var(--red);color:#fff;font-weight:800;padding:11px 14px;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;justify-content:center}
.grid{display:grid;grid-template-columns:360px 1fr;gap:16px}
.card{background:#fff;border:1px solid var(--line);border-radius:18px;box-shadow:0 10px 28px rgba(0,0,0,.06);overflow:hidden}
.head{padding:14px 16px;background:var(--red);color:#fff;font-weight:900}
.body{padding:16px}
.field{margin-bottom:13px}
label{display:block;font-size:12px;font-weight:900;color:var(--muted);margin-bottom:7px;text-transform:uppercase}
input,select,textarea{width:100%;border:1px solid var(--line);border-radius:12px;padding:11px 12px;font:inherit}
.row{display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px}
textarea{height:230px;font-family:ui-monospace,SFMono-Regular,Consolas,monospace;font-size:12px}
.stageShell{overflow:auto;background:#111;border-radius:16px;padding:12px}
.stage{position:relative;display:inline-block;line-height:0;background:#222}
.stage>img{display:block;max-width:none}
.qr{position:absolute;border:2px solid #fff;background:#fff;cursor:move;box-shadow:0 0 0 2px var(--red),0 8px 20px rgba(0,0,0,.25)}
.qr img{width:100%;height:100%;display:block;pointer-events:none}
.handle{position:absolute;right:-9px;bottom:-9px;width:18px;height:18px;background:var(--gold);border:2px solid #fff;border-radius:50%;cursor:nwse-resize}
.note{font-size:12px;color:var(--muted);line-height:1.55}
.kpi{display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;margin-bottom:12px}
.kpi div{background:#FFF1F2;border:1px solid #ffd7dc;border-radius:12px;padding:10px;text-align:center}
.kpi b{display:block;color:var(--red);font-size:18px}
@media(max-width:980px){.grid{grid-template-columns:1fr}.stageShell{max-width:100%}}
</style>
<link rel="stylesheet" href="/foodies-nft/assets/foodies-footer-nav.css?v=foodies-footer-fullwidth-pro">
</head>
<body>
<div class="wrap">
  <div class="top">
    <div class="brand">🍽️ Foodies NFT · QR Position Tool</div>
    <a class="btn" href="/foodies-nft/">Back Launcher</a>
  </div>

  <div class="grid">
    <div class="card">
      <div class="head">Minting QR Settings</div>
      <div class="body">
        <div class="field">
          <label>Star Template</label>
          <select id="stars">
            <option value="1">1 Star · Recommended</option>
            <option value="3">3 Stars · Premium</option>
            <option value="5">5 Stars · Master Chef</option>
          </select>
        </div>

        <div class="field">
          <label>Issued Cert UID</label>
          <input id="uid" value="FOODIES-RWA-PENDING">
        </div>

        <div class="field">
          <label>Verify URL QR Target</label>
          <input id="verifyUrl" readonly>
        </div>

        <div class="row">
          <div class="field"><label>qr_x</label><input id="x" type="number"></div>
          <div class="field"><label>qr_y</label><input id="y" type="number"></div>
          <div class="field"><label>qr_size</label><input id="size" type="number"></div>
        </div>

        <div class="kpi">
          <div><b id="kx">0</b><span class="note">X</span></div>
          <div><b id="ky">0</b><span class="note">Y</span></div>
          <div><b id="ks">0</b><span class="note">Size</span></div>
        </div>

        <button class="btn" type="button" onclick="applyInputs()">Apply</button>
        <button class="btn" type="button" onclick="copyOutput()">Copy PHP Config</button>

        <p class="note">Drag QR to reposition. Drag gold dot to resize. This outputs config for NFT minting artifact overlay only.</p>

        <div class="field">
          <label>PHP Config Output</label>
          <textarea id="out" spellcheck="false"></textarea>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="head">NFT Artifact Preview</div>
      <div class="body">
        <div class="stageShell">
          <div id="stage" class="stage">
            <img id="tpl" src="">
            <div id="qr" class="qr">
              <img id="qrImg" src="">
              <div id="handle" class="handle"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
const TPL = <?= json_encode($templates, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
const BASE = 'https://expressvisa.one/foodies-nft/verify.php?uid=';

let current = 1;
let pos = JSON.parse(JSON.stringify(TPL));
let drag = null;
let resize = null;

const el = id => document.getElementById(id);

function makeQrDataUri(text){
  const s=240,c=document.createElement('canvas'); c.width=s; c.height=s;
  const ctx=c.getContext('2d'); ctx.fillStyle='#fff'; ctx.fillRect(0,0,s,s);
  ctx.fillStyle='#000';
  const n=29, q=18, cell=Math.floor((s-q*2)/n);
  function dot(x,y,on){ if(on) ctx.fillRect(q+x*cell,q+y*cell,cell,cell); }
  function finder(x,y){ for(let yy=0;yy<7;yy++)for(let xx=0;xx<7;xx++)dot(x+xx,y+yy,xx===0||yy===0||xx===6||yy===6||(xx>=2&&xx<=4&&yy>=2&&yy<=4));}
  finder(0,0); finder(22,0); finder(0,22);
  let seed=0; for(let i=0;i<text.length;i++) seed=(seed+text.charCodeAt(i)*(i+3))%9973;
  for(let y=0;y<n;y++)for(let x=0;x<n;x++){
    if((x<8&&y<8)||(x>20&&y<8)||(x<8&&y>20)||x===6||y===6) continue;
    dot(x,y,((x*7+y*11+seed+x*y)%5)<2);
  }
  return c.toDataURL('image/png');
}

function verifyUrl(){
  return BASE + encodeURIComponent(el('uid').value.trim() || 'FOODIES-RWA-PENDING');
}

function load(){
  current = parseInt(el('stars').value,10);
  const item = pos[current];
  el('tpl').src = item.file + '?v=' + Date.now();
  el('x').value = item.qr.x;
  el('y').value = item.qr.y;
  el('size').value = item.qr.size;
  sync();
}

function sync(){
  const item = pos[current];
  const url = verifyUrl();
  el('verifyUrl').value = url;
  el('qrImg').src = makeQrDataUri(url);
  el('qr').style.left = item.qr.x + 'px';
  el('qr').style.top = item.qr.y + 'px';
  el('qr').style.width = item.qr.size + 'px';
  el('qr').style.height = item.qr.size + 'px';
  el('kx').textContent = item.qr.x;
  el('ky').textContent = item.qr.y;
  el('ks').textContent = item.qr.size;
  output();
}

function applyInputs(){
  const item = pos[current];
  item.qr.x = parseInt(el('x').value || item.qr.x,10);
  item.qr.y = parseInt(el('y').value || item.qr.y,10);
  item.qr.size = parseInt(el('size').value || item.qr.size,10);
  sync();
}

function output(){
  let s = "<?php\n";
  s += "declare(strict_types=1);\n\n";
  s += "const FOODIES_NFT_QR_TARGET = 'verify_url';\n";
  s += "const FOODIES_NFT_VERIFY_BASE = 'https://expressvisa.one/foodies-nft/verify.php?uid=';\n\n";
  s += "$FOODIES_NFT_QR_POSITIONS = [\n";
  [1,3,5].forEach(k=>{
    const q=pos[k].qr;
    s += `  ${k} => ['qr_x'=>${q.x}, 'qr_y'=>${q.y}, 'qr_size'=>${q.size}, 'qr_margin'=>0, 'qr_anchor'=>'top-left', 'qr_target'=>'verify_url'],\n`;
  });
  s += "];\n";
  el('out').value = s;
}

function copyOutput(){
  el('out').select();
  document.execCommand('copy');
}

function p(ev){
  const r=el('stage').getBoundingClientRect();
  return {x:ev.clientX-r.left,y:ev.clientY-r.top};
}

el('qr').addEventListener('pointerdown',ev=>{
  if(ev.target.id==='handle') return;
  const item=pos[current], pp=p(ev);
  drag={dx:pp.x-item.qr.x,dy:pp.y-item.qr.y};
  ev.preventDefault();
});
el('handle').addEventListener('pointerdown',ev=>{
  const item=pos[current], pp=p(ev);
  resize={x:pp.x,y:pp.y,size:item.qr.size};
  ev.preventDefault(); ev.stopPropagation();
});
window.addEventListener('pointermove',ev=>{
  const item=pos[current], pp=p(ev);
  if(drag){
    item.qr.x=Math.max(0,Math.round(pp.x-drag.dx));
    item.qr.y=Math.max(0,Math.round(pp.y-drag.dy));
    el('x').value=item.qr.x; el('y').value=item.qr.y; sync();
  }
  if(resize){
    item.qr.size=Math.max(40,Math.round(resize.size + Math.max(pp.x-resize.x, pp.y-resize.y)));
    el('size').value=item.qr.size; sync();
  }
});
window.addEventListener('pointerup',()=>{drag=null;resize=null;});

el('stars').addEventListener('change',load);
el('uid').addEventListener('input',sync);

load();
</script>
<script src="/foodies-nft/assets/foodies-footer-nav.js?v=foodies-footer-fullwidth-pro" defer></script>
</body>
</html>
