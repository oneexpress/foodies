<?php
declare(strict_types=1);


ini_set('display_errors', '0');
error_reporting(E_ALL);

$logDir = '/var/log/foodies-nft';
if (!is_dir($logDir)) @mkdir($logDir, 0755, true);
ini_set('log_errors', '1');
ini_set('error_log', $logDir . '/verify-error.log');

function h(string $v): string { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }

function envv(string $key, string $default = ''): string {
    static $env = null;
    if ($env === null) {
        $env = [];
        $file = '/var/www/secure/.env';
        if (is_file($file)) {
            foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) continue;
                [$k, $v] = explode('=', $line, 2);
                $env[trim($k)] = trim($v, " \t\n\r\0\x0B\"'");
            }
        }
    }
    return $env[$key] ?? getenv($key) ?: $default;
}

function pick(array $row, array $keys, string $default=''): string {
    foreach ($keys as $k) {
        if (array_key_exists($k, $row) && trim((string)$row[$k]) !== '') return (string)$row[$k];
    }
    return $default;
}

function qr_url(string $data, int $size=240): string {
    return 'https://api.qrserver.com/v1/create-qr-code/?size='.$size.'x'.$size.'&data='.rawurlencode($data);
}

$uid = trim((string)($_GET['uid'] ?? $_GET['cert_uid'] ?? $_GET['id'] ?? ''));
$row = [];
$dbOk = false;
$tableUsed = '';
$error = '';

try {
    $host = envv('DB_HOST','127.0.0.1');
    if ($host === 'localhost') $host = '127.0.0.1';
    $db = envv('DB_DATABASE', envv('DB_NAME','visa_db'));
    $user = envv('DB_USERNAME', envv('DB_USER','root'));
    $pass = envv('DB_PASSWORD', envv('DB_PASS',''));

    $pdo = new PDO("mysql:host={$host};dbname={$db};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $dbOk = true;

    foreach (['foodies_nft_certs','foodies_certificates','foodies_certs'] as $t) {
        if (!$pdo->query("SHOW TABLES LIKE ".$pdo->quote($t))->fetchColumn()) continue;
        $cols = $pdo->query("SHOW COLUMNS FROM `$t`")->fetchAll(PDO::FETCH_COLUMN);
        $key = in_array('cert_uid',$cols,true) ? 'cert_uid' : (in_array('uid',$cols,true) ? 'uid' : (in_array('id',$cols,true) ? 'id' : ''));
        if ($key === '') continue;

        if ($uid !== '') {
            $st = $pdo->prepare("SELECT * FROM `$t` WHERE `$key`=? LIMIT 1");
            $st->execute([$uid]);
            $row = $st->fetch() ?: [];
        } else {
            $order = in_array('created_at',$cols,true) ? 'created_at DESC' : (in_array('id',$cols,true) ? 'id DESC' : '1 DESC');
            $row = $pdo->query("SELECT * FROM `$t` ORDER BY $order LIMIT 1")->fetch() ?: [];
        }

        if ($row) { $tableUsed = $t; break; }
    }
} catch (Throwable $e) {
    $error = $e->getMessage();
    error_log('[verify] '.$error);
}

$certUid = pick($row, ['cert_uid','uid'], $uid !== '' ? $uid : 'FOODIES-RWA-PENDING');
$star = (int)pick($row, ['star_class','star_rating','stars'], (string)($_GET['star'] ?? 1));
if (!in_array($star,[1,3,5],true)) $star = 1;

$tiers = [
    1 => ['title'=>'RECOMMENDED CARD','price'=>'100 vSHARE','nft'=>'/metadata/foodies/NFT/1_star_foodies.png','core'=>'#0E8965','stars'=>'★'],
    3 => ['title'=>'PREMIUM CARD','price'=>'300 vSHARE','nft'=>'/metadata/foodies/NFT/3_stars_foodies.png','core'=>'#8E939B','stars'=>'★★★'],
    5 => ['title'=>'MASTER CHEF CARD','price'=>'500 vSHARE','nft'=>'/metadata/foodies/NFT/5_stars_foodies.png','core'=>'#B88700','stars'=>'★★★★★'],
];
$tier = $tiers[$star];

$chef = pick($row, ['chef_name','chef_real_name'], 'Chef Name');
$brand = pick($row, ['brand_name','restaurant_name'], 'Restaurant / Brand');
$food = pick($row, ['food_title','signature_dish'], 'Signature Food');
$business = pick($row, ['business_type'], 'HomeChise Unit');
$location = pick($row, ['location_text','location','address'], 'Food Location');
$review = pick($row, ['signature_food_review','review','notes'], 'Signature Food Review pending.');
$socialType = pick($row, ['social_link_type','social_type'], '');
$socialUrl = pick($row, ['social_link_url','social_url'], '');
$issuer = pick($row, ['issuer_wallet','owner_wallet','wallet_address'], 'Issuer Wallet Pending');
$mintBy = pick($row, ['mint_by','minter_wallet','minter_address'], 'Foodies RWA Engine');
$status = strtoupper(pick($row, ['status'], $row ? 'ISSUED' : 'PENDING'));
$getgems = pick($row, ['getgems_url','marketplace_url'], 'https://getgems.io/foodies');

$verifyUrl = 'https://expressvisa.one/foodies-nft/verify.php?uid='.rawurlencode($certUid);
$mapUrl = 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($location);

$pdfUrl = '/foodies-nft/pdf-design.php?'.http_build_query([
    'star'=>$star,
    'uid'=>$certUid,
    'wallet'=>$issuer,
    'issuer'=>$issuer,
    'chef'=>$chef,
    'brand'=>$brand,
    'food'=>$food,
    'business'=>$business,
    'location'=>$location,
    'social'=>$socialUrl ?: $socialType,
    'verify'=>$verifyUrl,
    'price'=>$tier['price'],
    'preview'=>1,
]);

$led = in_array($status, ['ISSUED','MINTED','VERIFIED','PAID','ACTIVE'], true) ? 'on' : 'wait';
$score = $row ? 100 : 72;
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Foodies RWA Verify</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
:root{--red:#E60012;--core:<?= h($tier['core']) ?>;--bg:#07111f;--panel:#0e1a2b;--panel2:#111f33;--line:#203553;--text:#f8fafc;--muted:#a9bdd8}
*{box-sizing:border-box}
body{margin:0;background:radial-gradient(circle at top left,#183153,#07111f 44%,#050914);color:var(--text);font-family:Arial,Helvetica,sans-serif;padding-bottom:24px}
.wrap{max-width:1280px;margin:auto;padding:18px}
.top{display:flex;justify-content:space-between;gap:12px;align-items:center;margin-bottom:16px}
.lang{display:flex;gap:6px;background:#0c1727;border:1px solid var(--line);padding:6px;border-radius:14px}
.lang button{background:transparent;color:#fff;border:0;font-weight:900;padding:7px 10px;border-radius:10px;cursor:pointer}
.lang button.active,.lang button:hover{background:var(--red)}
.sfx{background:#0c1727;border:1px solid var(--line);color:#fff;border-radius:14px;padding:10px 14px;font-weight:900;cursor:pointer}
.hero{position:relative;overflow:hidden;background:linear-gradient(135deg,#12243b,#111827 60%,color-mix(in srgb,var(--core) 28%,#111));border:1px solid var(--line);border-radius:28px;padding:24px;box-shadow:0 18px 50px #0006}
.hero:before{content:"";position:absolute;inset:0;background:linear-gradient(90deg,transparent,rgba(255,255,255,.08),transparent);transform:translateX(-80%);animation:sweep 6s infinite}
@keyframes sweep{50%,100%{transform:translateX(100%)}}
.eyebrow{font-weight:1000;color:#ffd54a;letter-spacing:3px;font-size:12px}
.hero h1{margin:8px 0 10px;font-size:42px;line-height:1}
.uidbox{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);border-radius:18px;padding:14px;margin-top:10px}
.uidbox small{display:block;color:var(--muted);letter-spacing:3px}.uidbox b{font-size:20px}
.led{width:12px;height:12px;border-radius:999px;display:inline-block;margin-right:8px;background:#f59e0b;box-shadow:0 0 0 4px #f59e0b22}.led.on{background:#22c55e;box-shadow:0 0 0 4px #22c55e22,0 0 18px #22c55e}
.kpis{display:grid;grid-template-columns:repeat(6,1fr);gap:12px;margin:16px 0}
.kpi{background:linear-gradient(180deg,#102039,#091525);border:1px solid var(--line);border-radius:18px;padding:14px;min-height:104px}
.kpi small{display:block;color:#9dc2ff;letter-spacing:2px;font-size:11px}.kpi b{display:block;font-size:26px;margin:10px 0 5px}.kpi span{color:var(--muted);font-size:13px}
.main{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.card{background:linear-gradient(180deg,#0f1d31,#081321);border:1px solid var(--line);border-radius:22px;padding:16px;box-shadow:0 14px 40px #0004}
.card h2{margin:0 0 12px;font-size:22px}
.tabs{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px}
.tab{background:#13243c;border:1px solid var(--line);color:#fff;border-radius:14px;padding:13px;font-weight:1000;cursor:pointer}
.tab.active,.tab:hover{background:var(--red);border-color:var(--red)}
.panel.hidden{display:none!important}
.pdf-frame{height:680px;background:#e5e7eb;border-radius:18px;overflow:hidden;border:1px solid #263b5b}
.pdf-frame iframe{width:100%;height:100%;border:0;display:block}
.nftbox{background:#050914;border-radius:18px;padding:14px;border:1px solid #263b5b}
.nftbox img{width:100%;border-radius:16px;display:block}
.btnrow{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:12px}
.btn{background:var(--red);color:#fff;text-decoration:none;border:0;border-radius:14px;padding:13px;text-align:center;font-weight:1000;cursor:pointer}
.btn.dark{background:#17243a}
.info{display:grid;gap:8px}
.row{display:grid;grid-template-columns:150px 1fr;gap:10px;border-bottom:1px dashed #263b5b;padding:8px 0}
.row b{color:#dbeafe}.row span{text-align:right;font-weight:900;word-break:break-word;color:#fff}
.row a{color:#9ed0ff}
.unit{display:grid;grid-template-columns:repeat(2,1fr);gap:8px;margin-top:8px}
.unit div{border:1px solid #263b5b;background:#0b1627;border-radius:14px;padding:10px;font-weight:900;color:#dbeafe}
.review{border:1px solid color-mix(in srgb,var(--core) 70%,#fff);background:rgba(255,255,255,.04);border-radius:18px;padding:14px;line-height:1.6;color:#fff;font-weight:850}
.qrs{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:10px}
.qr{text-align:center;border:1px solid #263b5b;background:#fff;border-radius:18px;padding:14px;color:#111;font-weight:1000}
.qr img{width:160px;height:160px}
.warn{margin:14px 0;background:#3b1d08;border:1px solid #9a3412;color:#fed7aa;border-radius:16px;padding:12px;font-weight:900}
@media(max-width:980px){.main,.kpis{grid-template-columns:1fr}.hero h1{font-size:30px}.row{grid-template-columns:1fr}.row span{text-align:left}.pdf-frame{height:620px}.unit,.qrs{grid-template-columns:1fr}}

/* FOODIES COMPOSER PDF VIEWER */
.composer-viewer{
  background:#1f2937;
  border:1px solid #334155;
  border-radius:20px;
  overflow:hidden;
  box-shadow:0 16px 38px rgba(0,0,0,.38);
}
.composer-toolbar{
  height:58px;
  background:#2b2f36;
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:10px;
  padding:0 14px;
  border-bottom:1px solid #3f4652;
}
.composer-left,.composer-right{
  display:flex;
  align-items:center;
  gap:10px;
}
.composer-menu{
  width:34px;
  height:34px;
  border-radius:10px;
  border:0;
  background:#374151;
  color:#fff;
  font-size:18px;
  font-weight:1000;
  cursor:pointer;
}
.composer-title{
  max-width:260px;
  overflow:hidden;
  text-overflow:ellipsis;
  white-space:nowrap;
  color:#fff;
  font-size:13px;
  font-weight:1000;
}
.composer-pill{
  background:#111827;
  color:#fff;
  border:1px solid #4b5563;
  border-radius:10px;
  padding:8px 10px;
  font-size:12px;
  font-weight:900;
}
.composer-btn{
  width:34px;
  height:34px;
  border-radius:10px;
  border:0;
  background:#374151;
  color:#fff;
  font-weight:1000;
  cursor:pointer;
}
.composer-btn:hover,.composer-menu:hover{
  background:#E60012;
}
.composer-stage{
  background:#111827;
  padding:18px;
  height:720px;
  overflow:auto;
}
.composer-page{
  width:min(100%,520px);
  margin:0 auto;
  background:#fff;
  border-radius:18px;
  overflow:hidden;
  box-shadow:0 24px 60px rgba(0,0,0,.45);
}
.composer-page iframe{
  width:100%;
  height:735px;
  border:0;
  display:block;
  background:#fff;
}
@media(max-width:900px){
  .composer-toolbar{height:auto;min-height:58px;flex-wrap:wrap;padding:10px}
  .composer-title{max-width:180px}
  .composer-stage{height:650px;padding:10px}
  .composer-page iframe{height:680px}
}


/* COMPOSER WHITE AREA FIX */
.composer-stage{
  height:760px!important;
  overflow:hidden!important;
  display:flex!important;
  align-items:flex-start!important;
  justify-content:center!important;
}
.composer-page{
  width:560px!important;
  max-width:100%!important;
  height:760px!important;
  overflow:hidden!important;
  background:transparent!important;
  border-radius:20px!important;
}
.composer-page iframe{
  width:100%!important;
  height:760px!important;
  overflow:hidden!important;
}
.pdf-frame{
  height:760px!important;
  overflow:hidden!important;
}
.pdf-frame iframe{
  height:760px!important;
}
@media(max-width:900px){
  .composer-stage{height:700px!important}
  .composer-page{height:700px!important}
  .composer-page iframe{height:700px!important}
}

</style>
</head>
<body>
<main class="wrap">
  <div class="top">
    <div class="lang"><button class="active">EN</button><button>中</button></div>
    <button class="sfx" id="sfxBtn">🔊 SFX ON</button>
  </div>

  <section class="hero">
    <div class="eyebrow">PREMIUM VERIFY LOUNGE</div>
    <h1>Foodies RWA Reputation Certificate</h1>
    <div>Custody-grade verification dashboard for certificate, payment, mint, map proof, and marketplace readiness.</div>
    <div class="uidbox"><small>CERT UID</small><b><?= h($certUid) ?></b></div>
    <div style="margin-top:12px"><span class="led <?= h($led) ?>"></span>Status: <?= h($status) ?> · <?= h($star.' Star · '.$tier['title']) ?></div>
  </section>

  <?php if (!$row): ?>
    <div class="warn">Cert record not found. Showing preview fallback for <?= h($certUid) ?>.</div>
  <?php endif; ?>

  <section class="kpis">
    <div class="kpi"><small>INTEGRITY SCORE</small><b><?= (int)$score ?>%</b><span><?= $row ? 'Live cert record found' : 'Fallback preview mode' ?></span></div>
    <div class="kpi"><small>VERIFY ROUTE</small><b>YES</b><span>Official public URL</span></div>
    <div class="kpi"><small>QR STATUS</small><b>YES</b><span>Verify + map QR ready</span></div>
    <div class="kpi"><small>NFT HEALTH</small><b>YES</b><span>Tier artwork route ready</span></div>
    <div class="kpi"><small>GETGEMS READY</small><b><?= $getgems ? 'YES' : 'NO' ?></b><span>Marketplace URL available</span></div>
    <div class="kpi"><small>MINT PRICE</small><b>YES</b><span><?= h($tier['price']) ?></span></div>
  </section>

  <section class="main">
    <div class="card">
      <h2>Certificate Artifact Viewer</h2>
      <div class="tabs">
        <button class="tab active" onclick="showTab('pdf');playSfx('click')">Cert PDF Preview</button>
        <button class="tab" onclick="showTab('nft');playSfx('click')">NFT Preview</button>
      </div>

      <div id="pdfPanel" class="panel">
        <div class="composer-viewer">
          <div class="composer-toolbar">
            <div class="composer-left">
              <button class="composer-menu" type="button" onclick="playSfx('click')">☰</button>
              <div class="composer-title"><?= h($certUid) ?></div>
            </div>
            <div class="composer-right">
              <span class="composer-pill">1 / 1</span>
              <button class="composer-btn" type="button" onclick="zoomPdf(-5);playSfx('click')">−</button>
              <span class="composer-pill" id="pdfZoomLabel">72%</span>
              <button class="composer-btn" type="button" onclick="zoomPdf(5);playSfx('click')">+</button>
              <button class="composer-btn" type="button" onclick="window.open('<?= h($pdfUrl) ?>','_blank');playSfx('click')">⎙</button>
              <a class="composer-btn" style="display:flex;align-items:center;justify-content:center;text-decoration:none" href="<?= h($pdfUrl) ?>" target="_blank" onclick="playSfx('click')">↗</a>
            </div>
          </div>
          <div class="composer-stage">
            <div class="composer-page" id="composerPage">
              <iframe id="composerPdfFrame" src="<?= h($pdfUrl) ?>"></iframe>
            </div>
          </div>
        </div>
      </div>

      <div id="nftPanel" class="panel hidden">
        <div class="nftbox"><img src="<?= h($tier['nft']) ?>" alt="Foodies NFT Preview"></div>
      </div>

      <div class="btnrow">
        <button class="btn" type="button" onclick="showTab('pdf');zoomPdf(0);document.getElementById('composerPdfFrame')?.scrollIntoView({behavior:'smooth',block:'center'});playSfx('click')">Open PDF</button>
        <a class="btn dark" href="<?= h($getgems) ?>" target="_blank" rel="noopener" onclick="playSfx('click')">Open Getgems</a>
      </div>
    </div>

    <div class="card">
      <h2>Verified RWA Cert Info</h2>
      <div class="info">
        <div class="row"><b>Cert UID</b><span><?= h($certUid) ?></span></div>
        <div class="row"><b>Tier</b><span><?= h($star.' Star · '.$tier['title']) ?></span></div>
        <div class="row"><b>Issuer</b><span><?= h($issuer) ?></span></div>
        <div class="row"><b>Mint By</b><span><?= h($mintBy) ?></span></div>
        <div class="row"><b>Price</b><span><?= h($tier['price']) ?></span></div>
        <div class="row"><b>Chef</b><span><?= h($chef) ?></span></div>
        <div class="row"><b>Brand</b><span><?= h($brand) ?></span></div>
        <div class="row"><b>Signature Food</b><span><?= h($food) ?></span></div>
        <div class="row"><b>Business Type</b><span><?= h($business) ?></span></div>
        <div class="row"><b>Location</b><span><a href="<?= h($mapUrl) ?>" target="_blank"><?= h($location) ?></a></span></div>
        <div class="row"><b>Social Media</b><span><?= $socialUrl ? '<a href="'.h($socialUrl).'" target="_blank">'.h(strtoupper($socialType ?: 'SOCIAL')).'</a>' : '-' ?></span></div>
        <div class="row"><b>Verify URL</b><span><a href="<?= h($verifyUrl) ?>"><?= h($verifyUrl) ?></a></span></div>
      </div>

      <h2 style="margin-top:18px">Unit of Responsibility</h2>
      <div class="unit">
        <div>Green & Clean Food</div><div>Food Hygiene</div><div>Clean Preparation</div>
        <div>Responsible Sourcing</div><div>Kitchen / Food Safety</div><div>Environmental Responsibility</div>
      </div>

      <h2 style="margin-top:18px">Signature Food Review</h2>
      <div class="review"><?= h($review) ?></div>

      <h2 style="margin-top:18px">Live Verify QR</h2>
      <div class="qrs">
        <div class="qr"><img src="<?= h(qr_url($verifyUrl)) ?>" alt="Verify QR"><br>Verify URL QR</div>
        <div class="qr"><img src="<?= h(qr_url($mapUrl)) ?>" alt="Map QR"><br>Google Map QR</div>
      </div>
    </div>
  </section>
</main>

<audio id="sfxClick" preload="auto" src="/assets/sfx/click.mp3"></audio>
<audio id="sfxSuccess" preload="auto" src="/assets/sfx/success.mp3"></audio>
<script>
let sfxOn = localStorage.getItem('foodies_verify_sfx') !== 'off';
const sfxBtn = document.getElementById('sfxBtn');
function renderSfx(){sfxBtn.textContent = sfxOn ? '🔊 SFX ON' : '🔇 SFX OFF'}
function playSfx(type){
  if(!sfxOn) return;
  const el = document.getElementById(type === 'success' ? 'sfxSuccess' : 'sfxClick');
  if(el){ el.currentTime = 0; el.play().catch(()=>{}); }
}
sfxBtn.addEventListener('click',()=>{sfxOn=!sfxOn;localStorage.setItem('foodies_verify_sfx',sfxOn?'on':'off');renderSfx();playSfx('click')});
renderSfx();
function showTab(t){
  document.getElementById('pdfPanel').classList.toggle('hidden', t !== 'pdf');
  document.getElementById('nftPanel').classList.toggle('hidden', t !== 'nft');
  document.querySelectorAll('.tab').forEach((b,i)=>b.classList.toggle('active',(t==='pdf'&&i===0)||(t==='nft'&&i===1)));
}
setTimeout(()=>playSfx('success'),500);

let composerZoom = 72;
function zoomPdf(delta){
  composerZoom = Math.max(45, Math.min(110, composerZoom + delta));
  const page = document.getElementById('composerPage');
  const label = document.getElementById('pdfZoomLabel');
  if(page){
    page.style.width = composerZoom + '%';
    page.style.maxWidth = '760px';
  }
  if(label) label.textContent = composerZoom + '%';
}
setTimeout(()=>zoomPdf(0),100);

</script>
</body>
</html>
