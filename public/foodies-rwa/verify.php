<?php
declare(strict_types=1);

ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', '/var/log/foodies-rwa/verify-error.log');

function h($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

function envv(string $k, string $d=''): string {
    static $cfg = null;
    if ($cfg === null) {
        $cfg = [];
        $f = '/var/www/secure/.env';
        if (is_file($f)) {
            foreach (file($f, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) continue;
                [$key,$val] = explode('=', $line, 2);
                $cfg[trim($key)] = trim(trim($val), "\"'");
            }
        }
    }
    return $cfg[$k] ?? getenv($k) ?: $d;
}

function pdo_conn(): PDO {
    $host = envv('VISA_DB_HOST', envv('DB_HOST', '127.0.0.1'));
    if ($host === 'localhost') $host = '127.0.0.1';

    $db = envv('VISA_DB_NAME', envv('DB_NAME', 'visa_db'));
    $user = envv('VISA_DB_USER', envv('DB_USER', 'root'));
    $pass = envv('VISA_DB_PASS', envv('DB_PASS', ''));

    return new PDO("mysql:host={$host};dbname={$db};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}

function table_exists(PDO $pdo, string $table): bool {
    $s = $pdo->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema=DATABASE() AND table_name=?");
    $s->execute([$table]);
    return (int)$s->fetchColumn() > 0;
}

function abs_url(?string $path): string {
    $path = trim((string)$path);
    if ($path === '') return '';
    if (preg_match('~^https?://~i', $path)) return $path;
    return 'https://expressvisa.one' . (str_starts_with($path, '/') ? $path : '/' . $path);
}

function qr_url(string $data, int $size=220): string {
    return 'https://api.qrserver.com/v1/create-qr-code/?size='.$size.'x'.$size.'&data='.rawurlencode($data);
}

$uid = trim((string)($_GET['uid'] ?? $_GET['cert_uid'] ?? ''));
if ($uid === '' || $uid === 'YOUR_CERT_UID') {
    $uid = '';
}

$cert = null;
$dbError = '';

try {
    $pdo = pdo_conn();

    foreach (['foodies_rwa_certs','foodies_rwa_certificates'] as $table) {
        if (!table_exists($pdo, $table)) continue;

        if ($uid !== '') {
            $sql = $table === 'foodies_rwa_certs'
                ? "SELECT *, cert_uid AS _cert_uid, chef_name AS _chef, brand_name AS _brand, food_title AS _food, location_name AS _loc, nft_image_path AS _nft, food_image_path AS _foodimg FROM foodies_rwa_certs WHERE cert_uid=:uid OR nft_uid=:uid LIMIT 1"
                : "SELECT *, cert_uid AS _cert_uid, chef_realname AS _chef, chef_brand AS _brand, food_title AS _food, location_text AS _loc, nft_png_path AS _nft, food_image AS _foodimg FROM foodies_rwa_certificates WHERE cert_uid=:uid OR nft_uid=:uid LIMIT 1";
            $st = $pdo->prepare($sql);
            $st->execute(['uid'=>$uid]);
            $cert = $st->fetch();
        } else {
            $sql = $table === 'foodies_rwa_certs'
                ? "SELECT *, cert_uid AS _cert_uid, chef_name AS _chef, brand_name AS _brand, food_title AS _food, location_name AS _loc, nft_image_path AS _nft, food_image_path AS _foodimg FROM foodies_rwa_certs ORDER BY id DESC LIMIT 1"
                : "SELECT *, cert_uid AS _cert_uid, chef_realname AS _chef, chef_brand AS _brand, food_title AS _food, location_text AS _loc, nft_png_path AS _nft, food_image AS _foodimg FROM foodies_rwa_certificates ORDER BY id DESC LIMIT 1";
            $cert = $pdo->query($sql)->fetch();
        }

        if ($cert) break;
    }
} catch (Throwable $e) {
    $dbError = $e->getMessage();
    error_log('[Foodies Verify DB] '.$dbError);
}

if (!$cert) {
    http_response_code($dbError ? 500 : 404);
    ?>
    <!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Foodies RWA Verify</title>
    <style>body{font-family:Arial,sans-serif;background:#f7f8fa;margin:0;padding:24px}.box{max-width:760px;margin:auto;background:#fff;border-radius:20px;padding:24px;box-shadow:0 10px 30px #0001}.bad{color:#E60012;font-weight:900}.btn{display:inline-block;background:#E60012;color:#fff;padding:12px 16px;border-radius:12px;text-decoration:none;font-weight:900}</style>
    </head><body><div class="box">
    <h1 class="bad"><?= $dbError ? 'Verify database error fixed-log mode' : 'Foodies RWA cert not found' ?></h1>
    <p>Use a real UID, for example: <b>/foodies-rwa/verify.php?uid=FD1-20260508-0001</b></p>
    <?php if ($dbError): ?><p><b>Server log:</b> /var/log/foodies-rwa/verify-error.log</p><?php endif; ?>
    <a class="btn" href="/foodies-rwa/verify.php">Open Latest Cert</a>
    </div></body></html>
    <?php
    exit;
}

$certUid = (string)($cert['_cert_uid'] ?? $cert['cert_uid'] ?? '');
$star = (int)($cert['star_class'] ?? $cert['stars'] ?? 1);
if (!in_array($star, [1,3,5], true)) $star = 1;

$title = $star === 5 ? 'MASTER CHEF CARD' : ($star === 3 ? 'PREMIUM CARD' : 'RECOMMENDED CARD');
$scoreRange = $star === 5 ? 'AVERAGE SCORE: 4.0 – 5.0' : ($star === 3 ? 'AVERAGE SCORE: 2.0 – 3.9' : 'AVERAGE SCORES: 1.0 - 1.9');
$status = strtolower((string)($cert['status'] ?? 'draft'));
$isValid = in_array($status, ['approved','minted','public_verified'], true);
$verifyUrl = 'https://expressvisa.one/foodies-rwa/verify.php?uid=' . rawurlencode($certUid);

$mapUrl = trim((string)($cert['google_maps_url'] ?? $cert['google_map_link'] ?? ''));
if ($mapUrl === '') {
    $lat = trim((string)($cert['latitude'] ?? ''));
    $lng = trim((string)($cert['longitude'] ?? ''));
    $q = ($lat && $lng) ? "$lat,$lng" : trim((string)($cert['_loc'] ?? '').' '.(string)($cert['address_text'] ?? ''));
    $mapUrl = $q ? 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($q) : '';
}

$nftImg = abs_url($cert['_nft'] ?? '');
$foodImg = abs_url($cert['_foodimg'] ?? '');
$pdfUrl = abs_url($cert['pdf_path'] ?? '');
$metaUrl = abs_url($cert['metadata_path'] ?? '');
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Foodies RWA Verify — <?=h($certUid)?></title>
<style>
body{margin:0;font-family:Arial,sans-serif;background:#f5f6f8;color:#151515}.wrap{max-width:1120px;margin:auto;padding:18px 14px 80px}.hero{border-radius:28px;padding:22px;background:linear-gradient(135deg,#111,#2a0003);color:#fff}.head{display:flex;justify-content:space-between;gap:12px;align-items:center;flex-wrap:wrap}.logo{width:58px;height:58px;border-radius:50%;background:#fff}.brand{display:flex;gap:12px;align-items:center}.badge{padding:10px 16px;border-radius:999px;background:#12b76a;font-weight:900}.bad{background:#d92d20}.stars{font-size:34px;letter-spacing:5px;color:#ffbf00}.grid{display:grid;grid-template-columns:380px 1fr;gap:20px;margin-top:20px}.panel{background:#fff;border-radius:24px;padding:18px;box-shadow:0 10px 28px #0001;border:1px solid #eee}.nft{width:100%;border-radius:20px;background:#111}.ph{aspect-ratio:723/999;border-radius:20px;display:grid;place-items:center;text-align:center;background:#111;color:#fff;font-weight:900}.kv{display:grid;grid-template-columns:180px 1fr;gap:10px}.k{color:#777;font-weight:900}.v{font-weight:900}.btn{display:inline-flex;margin:10px 8px 0 0;padding:12px 15px;border-radius:14px;text-decoration:none;font-weight:900}.red{background:#E60012;color:white}.black{background:#111;color:white}.qr{width:210px;height:210px;border:8px solid #fff;border-radius:16px;box-shadow:0 8px 22px #0002}.gc{border:2px solid #0b7a4b;background:#f0fff8}@media(max-width:850px){.grid{grid-template-columns:1fr}.kv{grid-template-columns:1fr}}
</style>
</head>
<body><div class="wrap">
<section class="hero"><div class="head"><div class="brand"><img class="logo" src="/metadata/991_visa_logo_only.png"><div><div style="font-size:22px;font-weight:900">@foodies RWA Reputation Verify</div><div>Food Reputation Identity · Green & Clean Food · TON NFT</div></div></div><div class="badge <?=$isValid?'':'bad'?>"><?=$isValid?'VALID CERT':h(strtoupper($status))?></div></div><h1><?=h($title)?></h1><div class="stars"><?=h(str_repeat('★',$star))?></div><div><?=h($scoreRange)?> · UID <?=h($certUid)?></div></section>
<main class="grid">
<aside class="panel"><?php if($nftImg): ?><img class="nft" src="<?=h($nftImg)?>"><?php else: ?><div class="ph"><div><div>@foodies</div><div style="font-size:42px"><?=h(str_repeat('★',$star))?></div><div><?=h($title)?></div></div></div><?php endif; ?><?php if($pdfUrl): ?><a class="btn red" href="<?=h($pdfUrl)?>" target="_blank">📄 PDF Cert</a><?php endif; ?><?php if($metaUrl): ?><a class="btn black" href="<?=h($metaUrl)?>" target="_blank">{} Metadata</a><?php endif; ?></aside>
<section style="display:grid;gap:18px">
<div class="panel"><h2>Certified Chef Identity</h2><div class="kv"><div class="k">Chef Real Name</div><div class="v"><?=h($cert['_chef'] ?? '')?></div><div class="k">Brand Name</div><div class="v"><?=h($cert['_brand'] ?? '—')?></div><div class="k">Food / Dish</div><div class="v"><?=h($cert['_food'] ?? '')?></div><div class="k">Location</div><div class="v"><?=h($cert['_loc'] ?? '')?></div><div class="k">Owner Wallet</div><div class="v"><?=h($cert['owner_wallet'] ?? '—')?></div><div class="k">TON NFT</div><div class="v"><?=h($cert['ton_nft_item_address'] ?? $cert['ton_nft_address'] ?? '—')?></div></div><?php if($mapUrl): ?><a class="btn black" href="<?=h($mapUrl)?>" target="_blank">📍 Google Map</a><?php endif; ?><a class="btn red" href="<?=h($verifyUrl)?>">✅ Verify Link</a></div>
<div class="panel gc"><h2>Green & Clean Food Responsibility Unit</h2><p><b>Verified responsibility layer:</b> food hygiene, clean preparation, responsible sourcing, food safety and environmental responsibility.</p></div>
<div class="panel"><h2>QR Verification</h2><img class="qr" src="<?=h(qr_url($verifyUrl))?>"> <img class="qr" src="<?=h(qr_url($mapUrl ?: $verifyUrl))?>"><p>Verify QR + Google Map QR</p></div>
</section>
</main></div></body></html>
