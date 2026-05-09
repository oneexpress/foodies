<?php
declare(strict_types=1);


$star = (int)($_GET['star'] ?? 1);
if (!in_array($star, [1,3,5], true)) $star = 1;

$tiers = [
  1 => ['class'=>'tier-green','title'=>'RECOMMENDED CARD','sub'=>'1 STAR FOOD REPUTATION','stars'=>'★'],
  3 => ['class'=>'tier-silver','title'=>'PREMIUM CARD','sub'=>'3 STARS FOOD REPUTATION','stars'=>'★★★'],
  5 => ['class'=>'tier-gold','title'=>'MASTER CHEF CARD','sub'=>'5 STARS FOOD REPUTATION','stars'=>'★★★★★'],
];

$tier = $tiers[$star];

function h(string $v): string {
  return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
}

$chef = h((string)($_GET['chef'] ?? 'Chef Name'));
$brand = h((string)($_GET['brand'] ?? 'Restaurant / Brand'));
$food = h((string)($_GET['food'] ?? 'Signature Food'));
$business = h((string)($_GET['business'] ?? 'HomeChise Unit'));
$location = h((string)($_GET['location'] ?? 'Food Location'));
$social = h((string)($_GET['social'] ?? 'Official Social Link'));
$review = h((string)($_GET['review'] ?? 'Signature Food Review preview will appear here. Maximum 300 characters.'));
$uid = h((string)($_GET['uid'] ?? 'FOODIES-RWA-PENDING'));
$wallet = h((string)($_GET['wallet'] ?? 'Wallet Not Connected'));
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Foodies RWA Cert PDF Design</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
@page{size:A4;margin:0}
*{box-sizing:border-box}
body{
  margin:0;
  background:#e5e7eb;
  font-family:Arial,Helvetica,sans-serif;
  color:#111;
}

.sheet{
  width:210mm;
  height:297mm;
  margin:0 auto;
  background:#fff;
  padding:8mm;
  overflow:hidden;
}


.cert{
  --core:#0E8965;
  --core-dark:#075F47;
  --core-soft:#EFFFF8;

  position:relative;
  height:281mm;
  border-radius:8mm;
  padding:8mm;
  overflow:hidden;

  background:
    linear-gradient(135deg,var(--core-dark),var(--core),var(--core-dark));

  box-shadow:0 10px 35px rgba(0,0,0,.15);
}

.tier-green{--core:#0E8965;--core-dark:#075F47;--core-soft:#EFFFF8}
.tier-silver{--core:#8E939B;--core-dark:#4B5563;--core-soft:#F8FAFC}
.tier-gold{--core:#B88700;--core-dark:#6B4A00;--core-soft:#FFF6DC}


.inner{
  position:relative;
  height:100%;
  border-radius:5mm;
  padding:7mm;

  background:
    radial-gradient(circle at top,var(--core-soft),#fff 46%),
    linear-gradient(180deg,#fff,var(--core-soft));

  border:1mm solid rgba(255,255,255,.82);
}

.inner:before{
  content:"";
  position:absolute;
  inset:6mm;
  border:0.45mm solid var(--core);
  border-radius:4mm;
  opacity:.45;
  pointer-events:none;
}
.header{
  position:relative;
  text-align:center;
  padding:2mm 8mm 4mm;
  border-bottom:0.6mm solid var(--core);
}
.kicker{
  color:var(--core);
  font-size:9pt;
  font-weight:1000;
  letter-spacing:2.8pt;
}
.title{
  margin:4mm 0 2mm;
  color:var(--core-dark);
  font-size:22pt;
  line-height:1;
  font-weight:1000;
  letter-spacing:.7pt;
}
.subtitle{
  color:var(--core-dark);
  font-size:9pt;
  font-weight:900;
}
.badge{
  margin:4mm auto 0;
  display:inline-block;
  background:linear-gradient(135deg,var(--core-dark),var(--core));
  color:#fff;
  border-radius:999px;
  padding:2.8mm 8mm;
  font-size:8pt;
  font-weight:1000;
}
.stars{
  position:relative;
  text-align:center;
  color:var(--core);
  font-size:20pt;
  letter-spacing:1.2mm;
  margin:3mm 0;
}
.content{
  position:relative;
  display:grid;
  gap:2.8mm;
}
.row{
  display:grid;
  grid-template-columns:42mm 1fr;
  gap:6mm;
  border-bottom:0.28mm dotted var(--core);
  padding:1.2mm 0;
  font-size:8.6pt;
}
.row b{font-weight:1000;color:#111}
.row span{text-align:right;font-weight:900;color:#111}
.review{
  margin-top:4mm;
  min-height:23mm;
  border:0.45mm solid var(--core);
  border-radius:5mm;
  padding:5mm;
  font-size:10pt;
  line-height:1.5;
  background:#fff;
}
.food-placeholder{
  margin-top:5mm;
  height:48mm;
  border:0.65mm dashed var(--core);
  border-radius:5mm;
  background:
    linear-gradient(135deg,rgba(255,255,255,.92),var(--core-soft));
  display:flex;
  flex-direction:column;
  align-items:center;
  justify-content:center;
  gap:2mm;
  color:var(--core-dark);
  text-align:center;
}
.food-placeholder .icon{
  width:18mm;
  height:18mm;
  border-radius:999px;
  display:flex;
  align-items:center;
  justify-content:center;
  background:linear-gradient(135deg,var(--core-dark),var(--core));
  color:#fff;
  font-size:20pt;
  font-weight:1000;
}
.food-placeholder .label{
  font-size:12pt;
  font-weight:1000;
  letter-spacing:.8pt;
}
.food-placeholder .hint{
  font-size:8pt;
  font-weight:900;
  opacity:.72;
}
.footer{
  position:absolute;
  left:12mm;
  right:12mm;
  bottom:11mm;
  display:grid;
  grid-template-columns:24mm 1fr 26mm;
  gap:6mm;
  align-items:end;
  border-top:0.8mm solid var(--core);
  padding-top:5mm;
}
.qr{
  width:22mm;
  height:22mm;
  border:0.7mm solid var(--core);
  border-radius:2mm;
  background:#fff;
  color:var(--core-dark);
  display:flex;
  align-items:center;
  justify-content:center;
  font-size:8pt;
  font-weight:1000;
}
.meta{
  font-size:8.5pt;
  line-height:1.45;
  font-weight:900;
}
.barcode-wrap{
  margin-top:2.5mm;
  padding:1.4mm;
  border:0.45mm solid var(--core);
  border-radius:2.5mm;
  background:#fff;
}
.barcode{
  height:10mm;
  background:
    repeating-linear-gradient(90deg,
      #111 0 1px,#fff 1px 2px,
      #111 2px 4px,#fff 4px 6px,
      #111 6px 7px,#fff 7px 10px,
      #111 10px 13px,#fff 13px 15px);
}
.barcode-text{
  margin-top:1mm;
  text-align:center;
  font-size:7pt;
  letter-spacing:1.4pt;
  color:#111;
}
.seal{
  width:24mm;
  height:24mm;
  border-radius:999px;
  background:
    radial-gradient(circle,#fff 0 48%,var(--core-soft) 49% 63%,#fff 64%);
  border:1.1mm double var(--core);
  color:var(--core-dark);
  display:flex;
  align-items:center;
  justify-content:center;
  text-align:center;
  font-weight:1000;
  font-size:8pt;
  line-height:1.12;
  transform:rotate(-8deg);
  box-shadow:0 8px 22px rgba(0,0,0,.12);
}
.seal:before{
  content:"";
  position:absolute;
  width:25mm;
  height:25mm;
  border:0.35mm solid var(--core);
  border-radius:999px;
}
.printbar{
  width:210mm;
  margin:10px auto;
  display:flex;
  gap:8px;
}
.printbar button,.printbar a{
  border:0;
  border-radius:12px;
  padding:12px 18px;
  background:#E60012;
  color:#fff;
  font-weight:1000;
  text-decoration:none;
  cursor:pointer;
}
@media print{
  body{background:#fff}
  .sheet{margin:0;padding:12mm}
  .printbar{display:none}
}

/* ===== PRO CERT PDF LAYOUT V3 ===== */

.content{
  margin-top:1mm!important;
  gap:2.2mm!important;
}

.food-showcase{
  position:relative;
}

.food-frame{
  position:relative;
  border-radius:6mm;
  overflow:hidden;
  border:0.8mm solid var(--core);
  background:
    linear-gradient(135deg,
      rgba(255,255,255,.96),
      var(--core-soft));
  box-shadow:
    0 10px 28px rgba(0,0,0,.10),
    inset 0 1px 0 rgba(255,255,255,.8);
}

.food-head{
  padding:4mm 5mm 3mm;
  border-bottom:0.45mm solid rgba(0,0,0,.08);
  text-align:center;
  background:
    linear-gradient(135deg,
      rgba(255,255,255,.95),
      rgba(255,255,255,.7));
}

.food-head-title{
  color:var(--core-dark);
  font-size:13pt;
  font-weight:1000;
  letter-spacing:1.8pt;
}

.food-head-sub{
  margin-top:1mm;
  color:var(--core-dark);
  opacity:.7;
  font-size:8pt;
  font-weight:900;
}

.food-placeholder{
  position:relative;
  height:46mm!important;
  border:0!important;
  border-radius:0!important;
  background:
    radial-gradient(circle at top,
      rgba(255,255,255,.98),
      var(--core-soft));
  overflow:hidden;
}

.food-glow{
  position:absolute;
  inset:-20%;
  background:
    radial-gradient(circle,
      rgba(255,255,255,.9),
      transparent 60%);
  animation:foodGlow 6s linear infinite;
  opacity:.55;
}

@keyframes foodGlow{
  from{transform:translateY(-6%) rotate(0deg)}
  to{transform:translateY(6%) rotate(360deg)}
}

.food-placeholder .icon{
  position:relative;
  z-index:2;
  width:16mm!important;
  height:16mm!important;
  font-size:18pt!important;
  background:
    linear-gradient(135deg,
      var(--core-dark),
      var(--core));
  box-shadow:
    0 10px 25px rgba(0,0,0,.16),
    inset 0 2px 0 rgba(255,255,255,.45);
}

.food-placeholder .label{
  position:relative;
  z-index:2;
  font-size:12pt!important;
  letter-spacing:1.5pt!important;
  color:var(--core-dark)!important;
}

.food-placeholder .hint{
  position:relative;
  z-index:2;
  font-size:9pt!important;
  opacity:.75!important;
  color:var(--core-dark)!important;
}

.info-card{
  border:0.45mm solid rgba(0,0,0,.08);
  border-radius:5mm;
  padding:4mm 5mm;
  background:
    linear-gradient(180deg,
      rgba(255,255,255,.98),
      rgba(255,255,255,.82));
  box-shadow:
    inset 0 1px 0 rgba(255,255,255,.8);
}

.review-wrap{
  border-radius:5mm;
  overflow:hidden;
  border:0.6mm solid var(--core);
  background:
    linear-gradient(180deg,
      rgba(255,255,255,.98),
      rgba(255,255,255,.88));
}

.review-title{
  padding:3mm 5mm;
  background:
    linear-gradient(135deg,
      var(--core-dark),
      var(--core));
  color:#fff;
  font-size:10pt;
  font-weight:1000;
  letter-spacing:1pt;
}

.review{
  margin:0!important;
  min-height:18mm!important;
  border:0!important;
  border-radius:0!important;
  background:transparent!important;
  padding:3mm!important;
  font-size:10pt!important;
  line-height:1.75!important;
}

.footer{
  margin-top:2mm!important;
  background:
    linear-gradient(135deg,
      rgba(255,255,255,.98),
      rgba(255,255,255,.88));
  border-radius:5mm;
  padding:5mm!important;
  border-top:0.9mm solid var(--core)!important;
  box-shadow:
    inset 0 1px 0 rgba(255,255,255,.8);
}

.qr{
  background:
    linear-gradient(135deg,
      rgba(255,255,255,.98),
      var(--core-soft));
  box-shadow:
    inset 0 1px 0 rgba(255,255,255,.85);
}

.barcode-wrap{
  background:
    linear-gradient(180deg,
      rgba(255,255,255,.98),
      rgba(255,255,255,.88));
  box-shadow:
    inset 0 1px 0 rgba(255,255,255,.8);
}

.seal{
  box-shadow:
    0 12px 30px rgba(0,0,0,.14),
    inset 0 2px 0 rgba(255,255,255,.6)!important;
}

.title{
  text-shadow:
    0 2px 0 rgba(255,255,255,.5),
    0 8px 24px rgba(0,0,0,.08);
}

.badge{
  box-shadow:
    0 8px 18px rgba(0,0,0,.16),
    inset 0 2px 0 rgba(255,255,255,.25);
}


.cert,.inner,.content,.footer{
  overflow:hidden!important;
}

body{
  overflow-x:hidden!important;
}

</style>
</head>
<body>
<div class="printbar">
  <button onclick="window.print()">Print / Save as PDF</button>
  <a href="?star=1">1 Star</a>
  <a href="?star=3">3 Stars</a>
  <a href="?star=5">5 Stars</a>
</div>

<div class="sheet">
  <section class="cert <?= h($tier['class']) ?>">
    <div class="inner">
      <div class="header">
        <div class="kicker">FOODIES RWA CERTIFICATE</div>
        <div class="title"><?= h($tier['title']) ?></div>
        <div class="subtitle">Green & Clean Food · Proof of Reputation</div>
        <div class="badge"><?= h($tier['sub']) ?></div>
      </div>

      <div class="stars"><?= h($tier['stars']) ?></div>

      
      <div class="content">

        <div class="food-showcase">
          <div class="food-frame">
            <div class="food-head">
              <div class="food-head-title">SIGNATURE FOOD</div>
              <div class="food-head-sub">Premium Food Presentation Area</div>
            </div>

            <div class="food-placeholder">
              <div class="food-glow"></div>
              <div class="icon">🍽</div>
              <div class="label">SIGNATURE FOOD IMAGE</div>
              <div class="hint">Upload premium food photo here</div>
            </div>
          </div>
        </div>

        <div class="info-card">
          <div class="row"><b>Chef</b><span><?= $chef ?></span></div>
          <div class="row"><b>Brand</b><span><?= $brand ?></span></div>
          <div class="row"><b>Signature Food</b><span><?= $food ?></span></div>
          <div class="row"><b>Business Type</b><span><?= $business ?></span></div>
          <div class="row"><b>Location</b><span><?= $location ?></span></div>
          <div class="row"><b>Social</b><span><?= $social ?></span></div>
        </div>

        <div class="review-wrap">
          <div class="review-title">SIGNATURE FOOD REVIEW</div>
          <div class="review"><?= $review ?></div>
        </div>

      </div>


      <div class="footer">
        <div class="qr">MAP QR</div>
        <div class="meta">
          Cert UID: <?= $uid ?><br>
          Wallet: <?= $wallet ?><br>
          Verify URL and PDF generated after submit.
          <div class="barcode-wrap">
            <div class="barcode"></div>
            <div class="barcode-text"><?= $uid ?></div>
          </div>
        </div>
        <div class="seal">FOODIES<br>RWA<br>OFFICIAL<br>SEAL</div>
      </div>
    </div>
  </section>
</div>
</body>
</html>
