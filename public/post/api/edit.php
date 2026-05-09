<?php
declare(strict_types=1);

ini_set('display_errors', '0');
error_reporting(E_ALL);
header('Content-Type: application/json; charset=UTF-8');

function out(array $a, int $code = 200): void {
  http_response_code($code);
  echo json_encode($a, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  exit;
}

set_exception_handler(function(Throwable $e): void {
  error_log('[free-post-submit] '.$e->getMessage().' in '.$e->getFile().':'.$e->getLine());
  out(['ok'=>false,'error'=>'server_error'], 500);
});

function postv(string $key, string $default = ''): string {
  return trim((string)($_POST[$key] ?? $default));
}

$cfgFile = '/var/www/secure/visa-free-post-db.php';
if (!is_file($cfgFile)) out(['ok'=>false,'error'=>'db_config_missing'], 500);
$cfg = require $cfgFile;

$db = @new mysqli($cfg['host'], $cfg['user'], $cfg['pass'], $cfg['db']);
if ($db->connect_errno) {
  error_log('[free-post-submit] DB connect failed: '.$db->connect_error);
  out(['ok'=>false,'error'=>'db_failed'], 500);
}
$db->set_charset('utf8mb4');

$target = postv('target');
$listingType = postv('listing_type', 'product');

$titleZh = postv('title_zh');
$titleEn = postv('title_en');
$descZh = postv('description_zh');
$descEn = postv('description_en');

$title = $titleZh !== '' ? $titleZh : $titleEn;
$description = $descZh !== '' ? $descZh : $descEn;

$priceRaw = postv('price', '0');
$price = is_numeric($priceRaw) ? (float)$priceRaw : 0.00;

$whatsapp = postv('whatsapp', postv('wa'));
$whatsapp = preg_replace('/[^\d+]/', '', $whatsapp) ?? '';

$audience = postv('nationality_slug');
$category = postv('category_slug');
$location = postv('location_slug');
$subloc = postv('sub_location_slug');

if ($target === '' || $title === '' || $description === '' || $whatsapp === '') {
  out([
    'ok'=>false,
    'error'=>'missing_required',
    'required'=>['target','title_zh/title_en','description_zh/description_en','whatsapp']
  ], 422);
}

$route = [
  'target' => $target,
  'audience_slug' => $audience,
  'category_slug' => $category,
  'location_slug' => $location,
  'sub_location_slug' => $subloc,
  'audience_zh' => postv('nationality_zh'),
  'audience_en' => postv('nationality_en'),
  'category_zh' => postv('category_zh'),
  'category_en' => postv('category_en'),
  'location_zh' => postv('location_zh'),
  'location_en' => postv('location_en'),
  'sub_location_zh' => postv('sub_location_zh'),
  'sub_location_en' => postv('sub_location_en'),
];

$postRef = 'EP' . date('YmdHis') . strtoupper(substr(bin2hex(random_bytes(6)), 0, 12));
$syncMessage = json_encode(['route'=>$route], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$imagePath = '';

$sql = "INSERT INTO visa_free_posts
(post_ref,target,listing_type,title,description,price,whatsapp,image_path,sync_status,sync_message,created_at,updated_at)
VALUES
(?,?,?,?,?,?,?,?, 'pending', ?, NOW(), NOW())";

$stmt = $db->prepare($sql);
if (!$stmt) {
  error_log('[free-post-submit] Prepare failed: '.$db->error);
  out(['ok'=>false,'error'=>'prepare_failed'], 500);
}

$stmt->bind_param(
  'sssssdsss',
  $postRef,
  $target,
  $listingType,
  $title,
  $description,
  $price,
  $whatsapp,
  $imagePath,
  $syncMessage
);

if (!$stmt->execute()) {
  error_log('[free-post-submit] Insert failed: '.$stmt->error);
  out(['ok'=>false,'error'=>'insert_failed'], 500);
}

out([
  'ok'=>true,
  'post_ref'=>$postRef,
  'status'=>'pending',
  'route'=>$route
]);


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
