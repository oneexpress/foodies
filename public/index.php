<?php
declare(strict_types=1);
header("Content-Type: text/html; charset=UTF-8");

$countries = [
  ["name"=>"Indonesia","flag"=>"id","tag"=>"nat-indonesia"],
  ["name"=>"Bangladesh","flag"=>"bd","tag"=>"nat-bangladesh"],
  ["name"=>"Nepal","flag"=>"np","tag"=>"nat-nepal"],
  ["name"=>"Pakistan","flag"=>"pk","tag"=>"nat-pakistan"],
  ["name"=>"Myanmar","flag"=>"mm","tag"=>"nat-myanmar"],
  ["name"=>"India","flag"=>"in","tag"=>"nat-india"],
  ["name"=>"Philippines","flag"=>"ph","tag"=>"nat-philippines"],
  ["name"=>"Cambodia","flag"=>"kh","tag"=>"nat-cambodia"],
  ["name"=>"Vietnam","flag"=>"vn","tag"=>"nat-vietnam"],
  ["name"=>"Thailand","flag"=>"th","tag"=>"nat-thailand"],
];

$locations = [
  "Kuala Lumpur" => [
    ["name"=>"KLCC","tag"=>"area-klcc"],
    ["name"=>"Bukit Bintang","tag"=>"area-bukit-bintang"],
    ["name"=>"Mont Kiara","tag"=>"area-mont-kiara"],
    ["name"=>"Cheras","tag"=>"area-cheras"],
    ["name"=>"Setapak","tag"=>"area-setapak"],
    ["name"=>"Wangsa Maju","tag"=>"area-wangsa-maju"],
    ["name"=>"Ampang","tag"=>"area-ampang"],
    ["name"=>"Sri Petaling","tag"=>"area-sri-petaling"],
    ["name"=>"Bangsar","tag"=>"area-bangsar"],
    ["name"=>"Kepong","tag"=>"area-kepong"],
  ],
  "Selangor" => [
    ["name"=>"Petaling Jaya","tag"=>"area-petaling-jaya"],
    ["name"=>"Subang Jaya","tag"=>"area-subang-jaya"],
    ["name"=>"Puchong","tag"=>"area-puchong"],
    ["name"=>"Shah Alam","tag"=>"area-shah-alam"],
    ["name"=>"Klang","tag"=>"area-klang"],
    ["name"=>"Cyberjaya","tag"=>"area-cyberjaya"],
    ["name"=>"Kajang","tag"=>"area-kajang"],
    ["name"=>"Seri Kembangan","tag"=>"area-seri-kembangan"],
    ["name"=>"Rawang","tag"=>"area-rawang"],
    ["name"=>"Sungai Buloh","tag"=>"area-sungai-buloh"],
  ],
  "Johor Bahru" => [
    ["name"=>"Johor Bahru City","tag"=>"area-johor-bahru-city"],
    ["name"=>"Skudai","tag"=>"area-skudai"],
    ["name"=>"Tebrau","tag"=>"area-tebrau"],
    ["name"=>"Mount Austin","tag"=>"area-mount-austin"],
    ["name"=>"Permas Jaya","tag"=>"area-permas-jaya"],
    ["name"=>"Pasir Gudang","tag"=>"area-pasir-gudang"],
    ["name"=>"Iskandar Puteri","tag"=>"area-iskandar-puteri"],
    ["name"=>"Bukit Indah","tag"=>"area-bukit-indah"],
    ["name"=>"Kulai","tag"=>"area-kulai"],
    ["name"=>"Senai","tag"=>"area-senai"],
  ],
  "Other Cities" => [
    ["name"=>"Penang / George Town","tag"=>"area-george-town"],
    ["name"=>"Ipoh","tag"=>"loc-ipoh"],
    ["name"=>"Melaka","tag"=>"loc-melaka"],
    ["name"=>"Kota Kinabalu","tag"=>"loc-kota-kinabalu"],
    ["name"=>"Kuching","tag"=>"loc-kuching"],
  ],
];

$chinaLocations = [
  "吉隆坡" => [
    ["name"=>"吉隆坡","tag"=>"loc-kuala-lumpur"],
    ["name"=>"吉隆坡市中心","tag"=>"area-klcc"],
    ["name"=>"武吉免登","tag"=>"area-bukit-bintang"],
    ["name"=>"满家乐","tag"=>"area-mont-kiara"],
    ["name"=>"蕉赖","tag"=>"area-cheras"],
    ["name"=>"文良港","tag"=>"area-setapak"],
    ["name"=>"旺沙玛朱","tag"=>"area-wangsa-maju"],
    ["name"=>"安邦","tag"=>"area-ampang"],
    ["name"=>"斯里八打灵","tag"=>"area-sri-petaling"],
    ["name"=>"孟沙","tag"=>"area-bangsar"],
    ["name"=>"甲洞","tag"=>"area-kepong"],
  ],
  "雪兰莪" => [
    ["name"=>"雪兰莪","tag"=>"loc-selangor"],
    ["name"=>"八打灵再也","tag"=>"area-petaling-jaya"],
    ["name"=>"梳邦再也","tag"=>"area-subang-jaya"],
    ["name"=>"蒲种","tag"=>"area-puchong"],
    ["name"=>"莎阿南","tag"=>"area-shah-alam"],
    ["name"=>"巴生","tag"=>"area-klang"],
    ["name"=>"赛城","tag"=>"area-cyberjaya"],
    ["name"=>"加影","tag"=>"area-kajang"],
    ["name"=>"史里肯邦安","tag"=>"area-seri-kembangan"],
    ["name"=>"万挠","tag"=>"area-rawang"],
    ["name"=>"双溪毛糯","tag"=>"area-sungai-buloh"],
  ],
  "新山" => [
    ["name"=>"新山","tag"=>"loc-johor-bahru"],
    ["name"=>"新山市区","tag"=>"area-johor-bahru-city"],
    ["name"=>"士古来","tag"=>"area-skudai"],
    ["name"=>"地不佬","tag"=>"area-tebrau"],
    ["name"=>"奥斯汀山","tag"=>"area-mount-austin"],
    ["name"=>"百万镇","tag"=>"area-permas-jaya"],
    ["name"=>"巴西古当","tag"=>"area-pasir-gudang"],
    ["name"=>"依斯干达公主城","tag"=>"area-iskandar-puteri"],
    ["name"=>"武吉英达","tag"=>"area-bukit-indah"],
    ["name"=>"古来","tag"=>"area-kulai"],
    ["name"=>"士乃","tag"=>"area-senai"],
  ],
  "其他城市" => [
    ["name"=>"其他城市","tag"=>"loc-other-cities"],
    ["name"=>"槟城乔治市","tag"=>"area-penang-george-town"],
    ["name"=>"怡保","tag"=>"area-ipoh"],
    ["name"=>"马六甲","tag"=>"area-melaka"],
    ["name"=>"亚庇","tag"=>"area-kota-kinabalu"],
    ["name"=>"古晋","tag"=>"area-kuching"],
  ],
];

function h(string $s): string { return htmlspecialchars($s, ENT_QUOTES, "UTF-8"); }
function tag_url(string $slug): string { return "/community/t/" . rawurlencode($slug); }
function china_tag_url(string $slug): string { return "/china/community/t/" . rawurlencode($slug); }
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>ExpressVisa One | Visa, FoodTruck Marketplace, Jobs & Community</title>
<meta name="description" content="ExpressVisa One connects China community, foreign worker community, marketplace, jobs posting, visa and permit services in Malaysia.">
<link rel="canonical" href="https://expressvisa.one/">
<style>
:root{--red:#e60023;--red2:#b8001c;--ink:#070707;--muted:#64707d;--line:rgba(230,0,35,.14)}
*{box-sizing:border-box}
body{margin:0;font-family:Arial,"Helvetica Neue",sans-serif;background:linear-gradient(180deg,#fff7f7 0%,#f5f7fb 44%,#fff 100%);color:var(--ink)}
.wrap{max-width:1280px;margin:0 auto;padding:30px 22px 42px}
.hero{display:grid;grid-template-columns:310px 1px 1fr;gap:42px;align-items:center;background:#fff;border-radius:34px;padding:38px;box-shadow:0 30px 90px rgba(20,25,40,.10)}
.logoBox{text-align:center}.logoBox img{width:255px;max-width:100%;height:auto;filter:drop-shadow(0 18px 26px rgba(230,0,35,.12))}
.sep{height:315px;background:linear-gradient(180deg,transparent,var(--red),transparent)}
h1{margin:0;font-size:clamp(44px,6vw,80px);line-height:.98;letter-spacing:-.055em;font-weight:1000}
.brand{display:block;color:var(--red);font-size:clamp(30px,4vw,56px);margin-bottom:14px;letter-spacing:-.035em}
.my{display:block;color:var(--red);font-size:clamp(34px,4vw,52px);margin-top:12px}
.hero p{max-width:850px;margin:22px 0 24px;font-size:20px;line-height:1.45;color:#202832}
.statusBlock{max-width:980px;margin:24px auto}
.statusCard{background:#fff;border:1px solid var(--line);border-radius:24px;box-shadow:0 20px 60px rgba(20,25,40,.08);overflow:hidden}
.statusRow,.bookingRow{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:16px 20px}
.statusRow{border-bottom:1px solid rgba(230,0,35,.12)}
.statusLeft{display:flex;align-items:center;gap:12px;font-weight:1000;color:#162033}
.liveDot{width:12px;height:12px;border-radius:50%;background:#16a34a;box-shadow:0 0 0 7px rgba(22,163,74,.12)}
.statusBtn,.bookBtn{display:inline-flex;align-items:center;justify-content:center;border-radius:15px;text-decoration:none;font-weight:1000;white-space:nowrap}
.statusBtn{background:linear-gradient(180deg,#ef1b2d,#b90018);color:#fff;padding:12px 18px}
.bookBtn{background:#111;color:#fff;padding:12px 18px}
.bookingPills{display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.pill{display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border-radius:999px;background:#fff7f7;border:1px solid rgba(230,0,35,.14);color:#9b1020;font-weight:1000;font-size:14px}
.quick{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px;max-width:900px}
.quick a{min-height:76px;display:flex;align-items:center;justify-content:center;gap:14px;padding:17px 20px;border-radius:18px;background:linear-gradient(180deg,#ef1b2d,#b90018);color:#fff;text-decoration:none;font-weight:1000;font-size:17px;box-shadow:0 18px 34px rgba(230,0,35,.24);transition:.18s ease}
.quick a:hover,.btn:hover,.chinaMini a:hover,.flags a:hover,.locTags a:hover{transform:translateY(-3px)}
.vusdt{margin:24px 0 18px;padding:18px 22px;display:flex;align-items:center;gap:16px;border-radius:22px;background:#fff;border:1px solid var(--line);box-shadow:0 20px 50px rgba(20,25,40,.07)}
.vusdt img{width:58px;height:58px;object-fit:contain}.vusdt strong{display:block;font-size:18px;margin-bottom:4px}.vusdt span{font-size:15px;line-height:1.5;color:#2f3440}
.launcher{display:grid!important;grid-template-columns:repeat(3,minmax(0,1fr))!important;gap:22px!important}
.card{min-height:370px;background:#fff;border:1px solid var(--line);border-radius:28px;padding:28px;text-align:center;box-shadow:0 28px 70px rgba(20,25,40,.09)}
.card h2{margin:8px 0 10px;font-size:30px;line-height:1.12}.card p{margin:0 auto 18px;max-width:330px;color:var(--muted);line-height:1.55}
.mascot{width:auto;height:300px;object-fit:contain;margin:0 auto 14px;display:block;filter:drop-shadow(0 18px 22px rgba(0,0,0,.14))}
.chinaMini{display:grid;grid-template-columns:1fr;gap:9px;margin:16px 0}
.chinaMini a{display:block;padding:10px 12px;border-radius:14px;background:#fff7f7;border:1px solid rgba(230,0,35,.16);color:#b8001c;text-decoration:none;font-weight:1000;font-size:14px;transition:.18s ease}
.flags{display:grid!important;grid-template-columns:repeat(5,58px)!important;justify-content:center!important;gap:12px!important;margin:22px auto!important}
.flags a{display:flex!important;align-items:center!important;justify-content:center!important;width:58px!important;height:42px!important;border-radius:12px!important;background:#fff!important;border:1px solid rgba(230,0,35,.12)!important;box-shadow:0 10px 22px rgba(20,25,40,.10)!important;transition:.18s ease!important}
.flags img{width:48px!important;height:32px!important;object-fit:cover!important;border-radius:7px!important;display:block!important}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:10px;padding:14px 22px;border-radius:14px;background:linear-gradient(180deg,#ef1b2d,#b90018);color:#fff;text-decoration:none;font-weight:1000;box-shadow:0 16px 30px rgba(230,0,35,.22);transition:.18s ease}
.locFooter{margin-top:26px;background:#fff;border:1px solid var(--line);border-radius:28px;padding:24px;box-shadow:0 24px 70px rgba(20,25,40,.08)}
.locFooter h3{margin:0 0 16px;font-size:22px}
.locGroup{margin:16px 0}.locGroup strong{display:block;margin-bottom:10px}
.locTags{display:flex;flex-wrap:wrap;gap:9px}
.locTags a{display:inline-flex;align-items:center;padding:9px 12px;border-radius:999px;background:#fff7f7;border:1px solid rgba(230,0,35,.16);color:#9b1020;text-decoration:none;font-size:13px;font-weight:900;transition:.18s ease}
.footer{text-align:center;margin-top:28px;color:#8a8f99;font-size:13px}
@media(max-width:920px){.wrap{padding:16px 12px 28px}.hero{grid-template-columns:1fr;gap:18px;text-align:center;padding:26px 18px;border-radius:26px}.sep{display:none}.logoBox img{width:175px}h1{font-size:42px}.brand{font-size:28px}.my{font-size:30px}.hero p{font-size:15px;margin:16px auto}.quick{grid-template-columns:1fr}.statusRow,.bookingRow{flex-direction:column;align-items:stretch}.vusdt{align-items:flex-start}.launcher{grid-template-columns:1fr!important}.card{min-height:0}}
</style>
</head>
<body>
<main class="wrap">
  <section class="hero">
    <div class="logoBox"><img src="/metadata/991_visa_logo_only.png?v=20260430" alt="ExpressVisa One"></div>
    <div class="sep"></div>
    <div>
      <h1><span class="brand">ExpressVisa One</span>Visa, Community,<br>FoodTruck Marketplace & Jobs<span class="my">in Malaysia</span></h1>
      <p>ExpressVisa connects China community, foreign worker community, marketplace, jobs posting, visa and permit service routes through one professional Malaysia-focused platform.</p>

      <section class="statusBlock">
        <div class="statusCard">
          <div class="statusRow">
            <div class="statusLeft"><span class="liveDot"></span><span>Live Service Status · Normal Processing</span></div>
            <a class="statusBtn" href="/status/">Check Status</a>
          </div>
          <div class="bookingRow">
            <div class="bookingPills">
              <span class="pill">📅 Booking Appointment</span>
              <span class="pill">🕙 Daily 10:00 AM – 6:00 PM</span>
              <span class="pill">✅ Service Slots Open</span>
            </div>
            <a class="bookBtn" href="/booking/">Book Appointment</a>
          </div>
        </div>
      </section>

      <div class="quick">
        <a href="<?=h(tag_url('svc-visa-permit'))?>">VISA & PERMIT</a>
        <a href="/marketplace/">🚐 FOODTRUCK</a>
        <a href="<?=h(tag_url('svc-jobs-posting'))?>">JOBS POSTING</a>
        <a href="/china/community/t/car-rental">豪华MPV租车</a>
        <a href="/china/community/t/premium-homestay">高级民宿</a>
        <a href="/china/community/t/loan-finance">线上贷款申请</a>
      </div>
    </div>
  </section>

  <section class="vusdt">
    <img src="/metadata/991_visa_logo_only.png?v=20260430" alt="vUSDT">
    <div><strong>vUSDT Settlement Notice</strong><span>We accept USDT on any supported network and exchange it into vUSDT at a fixed 1:1 rate for eligible ExpressVisa settlement services.</span></div>
  </section>

  <section class="launcher">
    <article class="card" id="china">
      <img class="mascot" src="/metadata/mascot.png?v=20260430" alt="China Community Mascot">
      <h2>China Community</h2>
      <p>中国人在马来西亚第二家园、商务、贷款、签证与本地生活服务社区。</p>
      <div class="chinaMini">
        <a href="/china/community/t/business-services">商务服务</a>
        <a href="/china/community/t/loan-finance">贷款与金融</a>
        <a href="/china/community/t/local-life">本地生活</a>
      </div>
      <a class="btn" href="/china/community/">进入社区</a>
    </article>

    <article class="card" id="foreign">
      <h2>Foreign Worker<br>Community</h2>
      <p>Country-based visa, permit, marketplace and jobs posting access for foreign worker communities in Malaysia.</p>
      <div class="flags">
        <?php foreach ($countries as $c): ?>
        <a href="<?=h(tag_url($c["tag"]))?>" title="<?=h($c["name"])?>">
          <img src="https://flagcdn.com/w80/<?=h($c["flag"])?>.png" alt="<?=h($c["name"])?>">
        </a>
        <?php endforeach; ?>
      </div>
      <a class="btn" href="/community/">Enter Community</a>
    </article>

    <article class="card" id="agent">
      <h2>Become Online Agent</h2>
      <p>Join ExpressVisa as an online agent for visa, permit, marketplace and jobs posting service referrals.</p>
      <a class="btn" href="<?=h(tag_url('svc-agency-helpdesk'))?>">Apply Now</a>
    </article>
  </section>

  <section class="locFooter">
    <h3>📍 Browse Service Locations</h3>
    <?php foreach ($locations as $city => $areas): ?>
      <div class="locGroup">
        <strong><?=h($city)?></strong>
        <div class="locTags">
          <?php foreach ($areas as $area): ?>
            <a href="<?=h(tag_url($area["tag"]))?>"><?=h($area["name"])?></a>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </section>

  <section class="locFooter">
    <h3>📍 中国社区地区入口</h3>
    <?php foreach ($chinaLocations as $city => $areas): ?>
      <div class="locGroup">
        <strong><?=h($city)?></strong>
        <div class="locTags">
          <?php foreach ($areas as $area): ?>
            <a href="<?=h(china_tag_url($area["tag"]))?>"><?=h($area["name"])?></a>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </section>

  <div class="footer">© 2026 ExpressVisa One · expressvisa.one</div>
</main>
<script src="/assets/js/991-wallet-sync.js?v=991-unified"></script>
<script src="/assets/991-balance-sync.js?v=991-balance-sync-ux-v1" defer></script>
<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
</body>
</html>
