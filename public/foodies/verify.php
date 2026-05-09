<?php
declare(strict_types=1);

/**
 * Foodies RWA Reputation Cert Verify Page
 * Path: /var/www/html/visa/public/foodies/verify.php
 * DB: visa_db
 */

function h($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

function envv(string $k, string $d=''): string {
  static $env = null;
  if ($env === null) {
    $env = [];
    $file = '/var/www/secure/.env';
    if (is_file($file)) {
      foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) continue;
        [$key,$val] = explode('=', $line, 2);
        $env[trim($key)] = trim(trim($val), "\"'");
      }
    }
  }
  return $env[$k] ?? getenv($k) ?: $d;
}

function pdo(): PDO {
  $host = envv('DB_HOST', '127.0.0.1');
  $db   = envv('VISA_DB_NAME', 'visa_db');
  $user = envv('VISA_DB_USER', envv('DB_USER', 'root'));
  $pass = envv('VISA_DB_PASS', envv('DB_PASS', ''));
  return new PDO("mysql:host={$host};dbname={$db};charset=utf8mb4", $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
}

function abs_url(string $path): string {
  $path = trim($path);
  if ($path === '') return '';
  if (preg_match('~^https?://~i', $path)) return $path;
  return 'https://expressvisa.one' . (str_starts_with($path, '/') ? $path : '/' . $path);
}

function map_link(array $r): string {
  $lat = trim((string)($r['latitude'] ?? ''));
  $lng = trim((string)($r['longitude'] ?? ''));
  if ($lat !== '' && $lng !== '') return "https://www.google.com/maps/search/?api=1&query=" . rawurlencode($lat . ',' . $lng);

  $loc = trim((string)($r['location'] ?? $r['address'] ?? ''));
  $brand = trim((string)($r['brand_name'] ?? ''));
  $q = trim($brand . ' ' . $loc);
  return $q !== '' ? "https://www.google.com/maps/search/?api=1&query=" . rawurlencode($q) : '';
}

function qr_url(string $data, int $size=220): string {
  return 'https://api.qrserver.com/v1/create-qr-code/?size=' . $size . 'x' . $size . '&data=' . rawurlencode($data);
}

$uid = trim((string)($_GET['uid'] ?? $_GET['cert_uid'] ?? ''));
if ($uid === '') {
  http_response_code(400);
  echo 'Missing cert UID.';
  exit;
}

$row = null;
try {
  $pdo = pdo();

  $tables = ['foodies_rwa_certs', 'foodies_reputation_certs'];
  foreach ($tables as $table) {
    $chk = $pdo->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?");
    $chk->execute([$table]);
    if ((int)$chk->fetchColumn() < 1) continue;

    $sql = "SELECT * FROM {$table} WHERE cert_uid = :uid OR uid = :uid LIMIT 1";
    $st = $pdo->prepare($sql);
    $st->execute([':uid' => $uid]);
    $row = $st->fetch();
    if ($row) break;
  }
} catch (Throwable $e) {
  http_response_code(500);
  echo 'Verify database error.';
  exit;
}

if (!$row) {
  http_response_code(404);
  echo 'Foodies RWA Reputation Cert not found.';
  exit;
}

$status = strtoupper(trim((string)($row['status'] ?? 'VALID')));
$isValid = !in_array($status, ['REVOKED','FAILED','CANCELLED','VOID'], true);

$tier = trim((string)($row['tier'] ?? $row['star_tier'] ?? $row['card_tier'] ?? ''));
$title = trim((string)($row['card_title'] ?? ''));
$stars = (int)($row['stars'] ?? 0);

if ($stars <= 0) {
  if (str_contains(strtolower($tier), '5')) $stars = 5;
  elseif (str_contains(strtolower($tier), '3')) $stars = 3;
  else $stars = 1;
}

if ($title === '') {
  $title = $stars === 5 ? 'MASTER CHEF CARD' : ($stars === 3 ? 'PREMIUM CARD' : 'RECOMMENDED CARD');
}

$scoreText = trim((string)($row['score_text'] ?? ''));
if ($scoreText === '') {
  $scoreText = $stars === 5 ? 'AVERAGE SCORE: 4.0 – 5.0' : ($stars === 3 ? 'AVERAGE SCORE: 2.0 – 3.9' : 'AVERAGE SCORES: 1.0 - 1.9');
}

$chef = trim((string)($row['chef_real_name'] ?? $row['chef_name'] ?? 'Certified Chef'));
$brand = trim((string)($row['brand_name'] ?? $row['restaurant_name'] ?? 'Foodies Brand'));
$dish = trim((string)($row['signature_dish'] ?? $row['dish_name'] ?? 'Signature Dish'));
$location = trim((string)($row['location'] ?? $row['address'] ?? ''));
$whatsapp = preg_replace('/\D+/', '', (string)($row['whatsapp'] ?? ''));
$social = trim((string)($row['social_url'] ?? $row['social_link'] ?? ''));
$nftImage = abs_url((string)($row['nft_image'] ?? $row['nft_image_path'] ?? $row['card_image'] ?? ''));
$foodImage = abs_url((string)($row['food_image'] ?? $row['main_food_image'] ?? ''));
$logoImage = abs_url((string)($row['brand_logo'] ?? $row['logo_image'] ?? ''));
$pdfUrl = abs_url((string)($row['pdf_url'] ?? $row['pdf_path'] ?? "/foodies/pdf.php?uid=" . rawurlencode($uid)));
$metadataUrl = abs_url((string)($row['metadata_url'] ?? $row['metadata_path'] ?? ''));
$nftUrl = abs_url((string)($row['nft_url'] ?? $row['ton_nft_url'] ?? ''));
$mapUrl = map_link($row);
$verifyUrl = 'https://expressvisa.one/foodies/verify.php?uid=' . rawurlencode($uid);

$themeClass = $stars === 5 ? 'gold' : ($stars === 3 ? 'silver' : 'green');
$starText = str_repeat('★', max(1, $stars));
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Foodies RWA Verify — <?=h($uid)?></title>
<style>
:root{--red:#E60012;--dark:#090909;--white:#fff;--muted:#777;--line:#ececec}
*{box-sizing:border-box}body{margin:0;font-family:Inter,Arial,sans-serif;background:#f7f8fa;color:#171717}
.wrap{max-width:1180px;margin:0 auto;padding:22px 14px 100px}
.hero{border-radius:28px;padding:22px;background:linear-gradient(135deg,#111,#2b0003);color:white;box-shadow:0 18px 45px rgba(0,0,0,.18)}
.top{display:flex;gap:18px;justify-content:space-between;align-items:center;flex-wrap:wrap}
.brand{display:flex;gap:12px;align-items:center}.logo{width:58px;height:58px;border-radius:50%;background:white;object-fit:cover}
.badge{padding:9px 14px;border-radius:999px;font-weight:900;background:#12b76a;color:white}.bad{background:#d92d20}
.grid{display:grid;grid-template-columns:390px 1fr;gap:22px;margin-top:22px}
.cardbox{background:white;border-radius:28px;padding:16px;box-shadow:0 14px 38px rgba(0,0,0,.12)}
.nft{width:100%;border-radius:22px;background:#111;display:block}
.placeholder{aspect-ratio:723/999;border-radius:22px;display:grid;place-items:center;text-align:center;padding:28px;font-weight:900;color:white}
.green{background:linear-gradient(160deg,#063,#111 70%)}.silver{background:linear-gradient(160deg,#d7d7d7,#111 70%)}.gold{background:linear-gradient(160deg,#d99b16,#111 70%)}
.info{display:grid;gap:14px}
.panel{background:white;border-radius:24px;padding:18px;border:1px solid var(--line);box-shadow:0 10px 24px rgba(0,0,0,.06)}
h1{margin:8px 0 4px;font-size:34px;line-height:1.05}.sub{opacity:.82}.stars{font-size:30px;letter-spacing:4px;color:#ffbf00;text-shadow:0 0 12px rgba(255,191,0,.35)}
.kv{display:grid;grid-template-columns:180px 1fr;gap:8px 14px;font-size:15px}.k{color:#777;font-weight:800}.v{font-weight:800}
.actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:16px}.btn{border:0;border-radius:14px;padding:12px 16px;font-weight:900;text-decoration:none;display:inline-flex;align-items:center;gap:8px}
.btn.red{background:var(--red);color:white}.btn.black{background:#111;color:white}.btn.white{background:white;color:#111;border:1px solid #ddd}
.gallery{display:grid;grid-template-columns:1fr 220px;gap:16px;align-items:start}
.food{width:100%;border-radius:18px;display:block;background:#eee;min-height:220px;object-fit:cover}
.qr{width:220px;height:220px;background:white;border:8px solid white;border-radius:16px;box-shadow:0 8px 24px rgba(0,0,0,.1)}
.metrics{display:grid;grid-template-columns:repeat(5,1fr);gap:10px}.metric{background:#fafafa;border:1px solid #eee;border-radius:16px;padding:12px;text-align:center}.metric b{display:block;font-size:20px}
.footer{margin-top:18px;text-align:center;color:#777;font-size:12px}
@media(max-width:860px){.grid{grid-template-columns:1fr}.kv{grid-template-columns:1fr}.gallery{grid-template-columns:1fr}.metrics{grid-template-columns:1fr 1fr}.qr{width:200px;height:200px}}
</style>
</head>
<body>
<div class="wrap">
  <section class="hero">
    <div class="top">
      <div class="brand">
        <img class="logo" src="/metadata/991_visa_logo_only.png" alt="991">
        <div>
          <div style="font-weight:1000;font-size:22px">@foodies RWA Reputation Cert</div>
          <div class="sub">Verified chef, brand, dish, location and reputation certificate</div>
        </div>
      </div>
      <div class="badge <?=$isValid?'':'bad'?>"><?=$isValid?'VALID CERT':'INVALID / REVOKED'?></div>
    </div>

    <h1><?=h($title)?></h1>
    <div class="stars"><?=h($starText)?></div>
    <div class="sub"><?=h($scoreText)?> · UID <?=h($uid)?></div>
  </section>

  <main class="grid">
    <aside class="cardbox">
      <?php if ($nftImage): ?>
        <img class="nft" src="<?=h($nftImage)?>" alt="Foodies NFT Card">
      <?php else: ?>
        <div class="placeholder <?=$themeClass?>">
          <div>
            <div style="font-size:28px">@foodies</div>
            <div style="font-size:44px;margin:12px 0"><?=h($starText)?></div>
            <div><?=h($title)?></div>
            <div style="font-size:13px;margin-top:12px"><?=h($scoreText)?></div>
          </div>
        </div>
      <?php endif; ?>

      <div class="actions">
        <a class="btn red" href="<?=h($pdfUrl)?>" target="_blank">📄 Reputation PDF</a>
        <?php if ($nftUrl): ?><a class="btn black" href="<?=h($nftUrl)?>" target="_blank">💎 View NFT</a><?php endif; ?>
      </div>
    </aside>

    <section class="info">
      <div class="panel">
        <h2>Certified Identity</h2>
        <div class="kv">
          <div class="k">Chef Real Name</div><div class="v"><?=h($chef)?></div>
          <div class="k">Brand / Restaurant</div><div class="v"><?=h($brand)?></div>
          <div class="k">Signature Dish</div><div class="v"><?=h($dish)?></div>
          <div class="k">Location</div><div class="v"><?=h($location ?: '—')?></div>
          <div class="k">Status</div><div class="v"><?=h($status)?></div>
          <div class="k">Issued At</div><div class="v"><?=h($row['issued_at'] ?? $row['created_at'] ?? '—')?></div>
        </div>

        <div class="actions">
          <?php if ($mapUrl): ?><a class="btn black" href="<?=h($mapUrl)?>" target="_blank">📍 Open Google Map</a><?php endif; ?>
          <?php if ($whatsapp): ?><a class="btn red" href="https://wa.me/<?=h($whatsapp)?>?text=<?=rawurlencode('I found your Foodies RWA Reputation Cert: '.$verifyUrl)?>" target="_blank">💬 WhatsApp</a><?php endif; ?>
          <?php if ($social): ?><a class="btn white" href="<?=h(abs_url($social))?>" target="_blank">🔗 Social</a><?php endif; ?>
        </div>
      </div>

      <div class="panel">
        <h2>Food Image + Location QR</h2>
        <div class="gallery">
          <?php if ($foodImage): ?>
            <img class="food" src="<?=h($foodImage)?>" alt="Food Image">
          <?php else: ?>
            <div class="food" style="display:grid;place-items:center;color:#777;font-weight:900">Food image pending</div>
          <?php endif; ?>

          <div>
            <img class="qr" src="<?=h(qr_url($mapUrl ?: $verifyUrl, 220))?>" alt="Google Map QR">
            <div style="font-size:12px;color:#777;text-align:center;margin-top:8px">Google Map QR</div>
          </div>
        </div>
      </div>

      <div class="panel">
        <h2>Reputation Metrics</h2>
        <div class="metrics">
          <div class="metric"><b><?=h($row['food_quality_score'] ?? '-')?></b>Food</div>
          <div class="metric"><b><?=h($row['branding_score'] ?? '-')?></b>Branding</div>
          <div class="metric"><b><?=h($row['hygiene_score'] ?? '-')?></b>Hygiene</div>
          <div class="metric"><b><?=h($row['presentation_score'] ?? '-')?></b>Present</div>
          <div class="metric"><b><?=h($row['community_score'] ?? '-')?></b>Community</div>
        </div>
      </div>

      <div class="panel">
        <h2>Verification QR</h2>
        <img class="qr" src="<?=h(qr_url($verifyUrl, 220))?>" alt="Verify QR">
        <div class="actions">
          <a class="btn red" href="<?=h($verifyUrl)?>">✅ Verify Link</a>
          <?php if ($metadataUrl): ?><a class="btn black" href="<?=h($metadataUrl)?>" target="_blank">{} Metadata</a><?php endif; ?>
        </div>
      </div>
    </section>
  </main>

  <div class="footer">
    © 2026 ExpressVisa 991 · Foodies RWA Reputation Cert
  </div>
</div>
</body>
</html>
