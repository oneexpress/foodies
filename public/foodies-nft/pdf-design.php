<?php
declare(strict_types=1);


$preview = (int)($_GET['preview'] ?? 0) === 1;
$star = (int)($_GET['star'] ?? 1);
if (!in_array($star, [1,3,5], true)) $star = 1;

$tiers = [
  1 => ['class'=>'tier-green','title'=>'RECOMMENDED CARD','sub'=>'1 STAR FOOD REPUTATION','stars'=>'★','price'=>'100 vSHARE'],
  3 => ['class'=>'tier-silver','title'=>'PREMIUM CARD','sub'=>'3 STARS FOOD REPUTATION','stars'=>'★★★','price'=>'300 vSHARE'],
  5 => ['class'=>'tier-gold','title'=>'MASTER CHEF CARD','sub'=>'5 STARS FOOD REPUTATION','stars'=>'★★★★★','price'=>'500 vSHARE'],
];
$tier = $tiers[$star];

function h(string $v): string { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }

$uid       = h((string)($_GET['uid'] ?? 'FOODIES-RWA-PENDING'));
$wallet    = h((string)($_GET['wallet'] ?? 'Wallet Not Connected'));
$issuer    = h((string)($_GET['issuer'] ?? 'Issuer Wallet Pending'));
$price     = h((string)($_GET['price'] ?? $tier['price']));
$chain     = h((string)($_GET['chain'] ?? 'TON Mainnet'));
$verifyUrl = h((string)($_GET['verify'] ?? 'Verify URL Pending'));

$chef      = h((string)($_GET['chef'] ?? 'Chef Name'));
$brand     = h((string)($_GET['brand'] ?? 'Restaurant / Brand'));
$food      = h((string)($_GET['food'] ?? 'Signature Food'));
$business  = h((string)($_GET['business'] ?? 'HomeChise Unit'));
$location  = h((string)($_GET['location'] ?? 'Food Location'));
$social    = h((string)($_GET['social'] ?? 'Official Social Link'));
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
body{margin:0;background:#e5e7eb;font-family:Arial,Helvetica,sans-serif;color:#111}
.sheet{width:210mm;height:297mm;margin:0 auto;background:#fff;padding:7mm;overflow:hidden}
.cert{
  --core:#0E8965;--core-dark:#075F47;--core-soft:#EFFFF8;
  height:283mm;border-radius:8mm;padding:7mm;overflow:hidden;
  background:linear-gradient(135deg,var(--core-dark),var(--core),var(--core-dark));
  box-shadow:0 14px 38px rgba(0,0,0,.16)
}
.tier-green{--core:#0E8965;--core-dark:#075F47;--core-soft:#EFFFF8}
.tier-silver{--core:#8E939B;--core-dark:#4B5563;--core-soft:#F8FAFC}
.tier-gold{--core:#B88700;--core-dark:#6B4A00;--core-soft:#FFF6DC}
.inner{
  position:relative;height:100%;border-radius:5mm;padding:7mm;
  background:radial-gradient(circle at top,var(--core-soft),#fff 45%),linear-gradient(180deg,#fff,var(--core-soft));
  border:1mm solid rgba(255,255,255,.85);overflow:hidden
}
.inner:before{content:"";position:absolute;inset:5mm;border:.4mm solid var(--core);border-radius:4mm;opacity:.42;pointer-events:none}
.header{position:relative;text-align:center;padding:2mm 8mm 4mm;border-bottom:.65mm solid var(--core)}
.kicker{color:var(--core);font-size:8.5pt;font-weight:1000;letter-spacing:2.5pt}
.title{margin:3mm 0 1.5mm;color:var(--core-dark);font-size:22pt;line-height:1;font-weight:1000;letter-spacing:.6pt}
.subtitle{color:var(--core-dark);font-size:8.5pt;font-weight:900}
.badge{margin:3mm auto 0;display:inline-block;background:linear-gradient(135deg,var(--core-dark),var(--core));color:#fff;border-radius:999px;padding:2.4mm 7mm;font-size:7.5pt;font-weight:1000}
.stars{text-align:center;color:var(--core);font-size:31pt;letter-spacing:2.1mm;margin:4.2mm 0 4.8mm;font-weight:1000;text-shadow:0 3px 10px rgba(0,0,0,.12)}
.content{position:relative;display:grid;gap:2.4mm}
.rwa-lines{margin:0;padding:0;border:0;background:transparent}
.rwa-line{display:grid;grid-template-columns:33mm 1fr;gap:5mm;padding:1.28mm 0;border-bottom:.24mm dotted var(--core);font-size:8pt;line-height:1.12}
.rwa-line b{color:var(--core-dark);font-weight:1000}
.rwa-line span{color:#111;font-weight:1000;text-align:left;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.food-frame{border:.55mm dashed var(--core);border-radius:5mm;background:linear-gradient(135deg,rgba(255,255,255,.98),var(--core-soft));overflow:hidden}
.food-placeholder{height:47mm;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:2mm;color:var(--core-dark);text-align:center}
.food-placeholder .icon{width:18mm;height:18mm;border-radius:999px;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,var(--core-dark),var(--core));color:#fff;font-size:18pt;font-weight:1000;box-shadow:0 8px 20px rgba(0,0,0,.16)}
.food-placeholder .label{font-size:13pt;font-weight:1000;letter-spacing:1pt}
.food-placeholder .hint{font-size:8pt;font-weight:900;opacity:.72}
.info-card{margin-top:1.5mm;padding:0;background:transparent;border:0}
.row{display:grid;grid-template-columns:36mm 1fr;gap:5mm;border-bottom:.24mm dotted var(--core);padding:1mm 0;font-size:7.8pt;line-height:1.15}
.row b{font-weight:1000;color:#111}.row span{text-align:right;font-weight:1000;color:#111;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.footer{
  position:absolute;left:10mm;right:10mm;bottom:8mm;
  display:grid;grid-template-columns:26mm 1fr 26mm;gap:4mm;align-items:end;
  border-top:.75mm solid var(--core);padding:3mm;border-radius:4mm;
  background:linear-gradient(135deg,rgba(255,255,255,.96),rgba(255,255,255,.82))
}
.qr{width:24mm;height:24mm;border:.65mm solid var(--core);border-radius:2mm;background:#fff;color:var(--core-dark);display:flex;align-items:center;justify-content:center;text-align:center;font-size:7pt;font-weight:1000;line-height:1.1}
.meta{font-size:7.2pt;line-height:1.25;font-weight:900}
.barcode-wrap{margin-top:1.5mm;padding:1mm;border:.4mm solid var(--core);border-radius:2mm;background:#fff}
.barcode{height:9mm;background:repeating-linear-gradient(90deg,#111 0 1px,#fff 1px 2px,#111 2px 4px,#fff 4px 6px,#111 6px 7px,#fff 7px 10px,#111 10px 13px,#fff 13px 15px)}
.barcode-text{margin-top:.7mm;text-align:center;font-size:6.5pt;letter-spacing:1.1pt;color:#111}
.seal{width:24mm;height:24mm;border-radius:999px;background:radial-gradient(circle,#fff 0 48%,var(--core-soft) 49% 63%,#fff 64%);border:1mm double var(--core);color:var(--core-dark);display:flex;align-items:center;justify-content:center;text-align:center;font-weight:1000;font-size:7pt;line-height:1.08;transform:rotate(-8deg);box-shadow:0 8px 22px rgba(0,0,0,.12)}
.seal:before{content:"";position:absolute;width:20mm;height:20mm;border:.32mm solid var(--core);border-radius:999px}
.printbar{width:210mm;margin:10px auto;display:flex;gap:8px}.printbar button,.printbar a{border:0;border-radius:12px;padding:12px 18px;background:#E60012;color:#fff;font-weight:1000;text-decoration:none;cursor:pointer}
@media print{body{background:#fff}.sheet{margin:0;padding:7mm}.printbar{display:none}}

body.preview-mode{background:transparent!important;overflow:hidden!important}
body.preview-mode .printbar{display:none!important}
body.preview-mode .sheet{
  width:100%!important;
  height:760px!important;
  min-height:0!important;
  margin:0!important;
  padding:0!important;
  background:transparent!important;
  display:flex!important;
  justify-content:center!important;
  overflow:hidden!important;
}
body.preview-mode .cert{
  width:100%!important;
  max-width:430px!important;
  height:760px!important;
  min-height:760px!important;
  border-radius:22px!important;
  padding:10px!important;
  box-shadow:none!important;
}
body.preview-mode .inner{
  height:100%!important;
  padding:14px!important;
  border-radius:18px!important;
}
body.preview-mode .header{padding:8px 16px 12px!important}
body.preview-mode .kicker{font-size:8px!important;letter-spacing:3px!important}
body.preview-mode .title{font-size:24px!important}
body.preview-mode .subtitle{font-size:9px!important}
body.preview-mode .badge{font-size:8px!important;padding:7px 14px!important;margin-top:8px!important}
body.preview-mode .stars{font-size:36px!important;letter-spacing:4px!important;margin:13px 0!important;font-weight:1000!important;text-shadow:0 2px 8px rgba(0,0,0,.14)!important}
body.preview-mode .rwa-line{
  grid-template-columns:92px 1fr!important;
  padding:4px 0!important;
  font-size:8px!important;
}
body.preview-mode .food-placeholder{height:118px!important}
body.preview-mode .food-placeholder .icon{width:42px!important;height:42px!important;font-size:18px!important}
body.preview-mode .food-placeholder .label{font-size:13px!important}
body.preview-mode .food-placeholder .hint{font-size:8px!important}
body.preview-mode .row{
  grid-template-columns:95px 1fr!important;
  padding:4px 0!important;
  font-size:8px!important;
}
body.preview-mode .footer{
  left:14px!important;
  right:14px!important;
  bottom:14px!important;
  grid-template-columns:58px 1fr 58px!important;
  gap:8px!important;
  padding:8px!important;
}
body.preview-mode .qr{width:54px!important;height:54px!important;font-size:7px!important}
body.preview-mode .meta{font-size:7px!important}
body.preview-mode .barcode{height:24px!important}
body.preview-mode .barcode-text{font-size:6px!important}
body.preview-mode .seal{width:54px!important;height:54px!important;font-size:7px!important}
body.preview-mode .seal:before{width:44px!important;height:44px!important}

/* CHROME PDF PREVIEW TOOLBAR */
.chrome-print-toolbar{
  width:210mm;
  margin:12px auto;
  padding:10px 16px;
  border-radius:18px;
  background:#202124;
  color:#fff;

  display:flex;
  flex-wrap:nowrap;
  align-items:center;
  justify-content:space-between;
  gap:14px;

  overflow:hidden;
  box-shadow:0 10px 28px rgba(0,0,0,.16);
}
.chrome-print-left{
  display:flex;
  align-items:center;
  gap:10px;
  flex:0 0 auto;
  min-width:0;
}
.chrome-dot{
  width:12px;
  height:12px;
  border-radius:999px;
}
.chrome-dot.red{background:#ff5f57}
.chrome-dot.yellow{background:#febc2e}
.chrome-dot.green{background:#28c840}

.chrome-print-title{
  font-size:13px;
  font-weight:1000;
  letter-spacing:.4px;
}

.chrome-print-actions{
  display:flex;
  align-items:center;
  justify-content:flex-end;
  gap:8px;
  flex-wrap:nowrap;
  flex:1 1 auto;
  overflow-x:auto;
  scrollbar-width:none;
}

.chrome-print-actions::-webkit-scrollbar{
  display:none;
}

.chrome-print-actions button,
.chrome-print-actions a{
  border:0;
  border-radius:12px;
  padding:10px 14px;
  background:#E60012;
  color:#fff;
  font-size:12px;
  font-weight:1000;
  text-decoration:none;
  cursor:pointer;
  transition:.18s ease;

  white-space:nowrap;
  flex:0 0 auto;
}

.chrome-print-actions button:hover,
.chrome-print-actions a:hover{
  background:#111;
}

body.preview-mode .chrome-print-toolbar{
  display:none!important;
}

@media(max-width:900px){
  .chrome-print-toolbar{
    width:100%;
    border-radius:0;
    margin:0;
    padding:10px 12px;
    gap:10px;
  }

  .chrome-print-left{
    min-width:auto;
  }

  .chrome-print-title{
    font-size:11px;
    line-height:1.1;
  }

  .chrome-print-actions{
    gap:6px;
  }

  .chrome-print-actions button,
  .chrome-print-actions a{
    padding:9px 11px;
    font-size:11px;
  }
}

@media print{
  .chrome-print-toolbar{
    display:none!important;
  }

  body{
    background:#fff!important;
  }

  html,body{
    width:210mm;
    height:297mm;
  }
}


/* PRINT ONLY: TRUE FULL A4 */
@media print{
  @page{size:A4;margin:0!important}

  html,body{
    width:210mm!important;
    height:297mm!important;
    margin:0!important;
    padding:0!important;
    background:#fff!important;
    overflow:hidden!important;
  }

  .chrome-print-toolbar,
  .printbar{
    display:none!important;
  }

  .sheet{
    width:210mm!important;
    height:297mm!important;
    margin:0!important;
    padding:0!important;
    background:#fff!important;
    overflow:hidden!important;
  }

  .cert{
    width:210mm!important;
    height:297mm!important;
    min-height:297mm!important;
    margin:0!important;
    padding:8mm!important;
    border-radius:0!important;
    box-shadow:none!important;
    page-break-inside:avoid!important;
    break-inside:avoid!important;
  }

  .inner{
    width:100%!important;
    height:100%!important;
    min-height:0!important;
    border-radius:6mm!important;
    padding:7mm!important;
    overflow:hidden!important;
  }
}


/* FINAL TRUE A4 PRINT + PREVIEW REPAIR */
@media screen{
  body:not(.preview-mode){
    background:#e5e7eb!important;
  }
  body:not(.preview-mode) .sheet{
    width:210mm!important;
    height:297mm!important;
    padding:0!important;
    margin:14px auto!important;
    background:#fff!important;
    overflow:hidden!important;
  }
  body:not(.preview-mode) .cert{
    width:210mm!important;
    height:297mm!important;
    min-height:297mm!important;
    border-radius:0!important;
    padding:8mm!important;
    box-shadow:0 14px 38px rgba(0,0,0,.16)!important;
  }
  body:not(.preview-mode) .inner{
    height:100%!important;
    border-radius:6mm!important;
    padding:7mm!important;
  }
}

@media print{
  @page{
    size:A4 portrait;
    margin:0!important;
  }

  *{
    -webkit-print-color-adjust:exact!important;
    print-color-adjust:exact!important;
  }

  html,body{
    width:210mm!important;
    height:297mm!important;
    min-width:210mm!important;
    min-height:297mm!important;
    margin:0!important;
    padding:0!important;
    background:#fff!important;
    overflow:hidden!important;
  }

  .chrome-print-toolbar,
  .printbar{
    display:none!important;
  }

  .sheet{
    position:fixed!important;
    left:0!important;
    top:0!important;
    width:210mm!important;
    height:297mm!important;
    min-width:210mm!important;
    min-height:297mm!important;
    max-width:210mm!important;
    max-height:297mm!important;
    margin:0!important;
    padding:0!important;
    background:#fff!important;
    overflow:hidden!important;
  }

  .cert{
    position:absolute!important;
    left:0!important;
    top:0!important;
    width:210mm!important;
    height:297mm!important;
    min-width:210mm!important;
    min-height:297mm!important;
    max-width:210mm!important;
    max-height:297mm!important;
    margin:0!important;
    padding:8mm!important;
    border-radius:0!important;
    box-shadow:none!important;
    overflow:hidden!important;
    page-break-before:avoid!important;
    page-break-after:avoid!important;
    page-break-inside:avoid!important;
    break-inside:avoid!important;
  }

  .inner{
    width:100%!important;
    height:100%!important;
    max-height:281mm!important;
    margin:0!important;
    padding:7mm!important;
    border-radius:6mm!important;
    overflow:hidden!important;
  }
}

</style>
</head>
<body class="<?= $preview ? 'preview-mode' : '' ?>">

<div class="chrome-print-toolbar">
  <div class="chrome-print-left">
    <div class="chrome-dot red"></div>
    <div class="chrome-dot yellow"></div>
    <div class="chrome-dot green"></div>
    <div class="chrome-print-title">Foodies PDF</div>
  </div>

  <div class="chrome-print-actions">
    <button type="button" onclick="openChromePdfPreview()">
      Print Preview
    </button>

    <a href="?star=1">1 Star</a>
    <a href="?star=3">3 Stars</a>
    <a href="?star=5">5 Stars</a>
  </div>
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
        <div class="rwa-lines">
          <div class="rwa-line"><b>Cert UID</b><span><?= $uid ?></span></div>
          <div class="rwa-line"><b>Wallet</b><span><?= $wallet ?></span></div>
          <div class="rwa-line"><b>Issuer Wallet</b><span><?= $issuer ?></span></div>
          <div class="rwa-line"><b>Price</b><span><?= $price ?></span></div>
          <div class="rwa-line"><b>Chain</b><span><?= $chain ?></span></div>
          <div class="rwa-line"><b>Verify URL</b><span><?= $verifyUrl ?></span></div>
        </div>

        <div class="food-frame">
          <div class="food-placeholder">
            <div class="icon">🍽</div>
            <div class="label">SIGNATURE FOOD IMAGE</div>
            <div class="hint">Upload premium food photo here</div>
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
      </div>

      <div class="footer">
        <div class="qr">VERIFY<br>URL QR</div>
        <div class="meta">
          Cert UID: <?= $uid ?><br>
          Chain: <?= $chain ?><br>
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

<script>
function openChromePdfPreview(){
  try{
    document.title='Foodies-RWA-Certificate';
    window.focus();
    document.body.classList.remove('preview-mode'); setTimeout(()=>window.print(),80);
  }catch(e){
    console.error(e);
  }
}
</script>

</body>
</html>
