<?php
declare(strict_types=1);

session_start();
ini_set('display_errors', '0');
error_reporting(E_ALL);

function h(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
function p(string $k, string $d = ''): string { return trim((string)($_POST[$k] ?? $d)); }

function fail(string $msg, int $code = 422, array $extra = []): never {
  http_response_code($code);
  header('Content-Type: text/html; charset=UTF-8');
  echo "<!doctype html><html><head><meta charset='utf-8'><meta name='viewport' content='width=device-width,initial-scale=1'>";
  echo "<title>Submit Failed</title></head><body style='font-family:Arial,\"Microsoft YaHei\",sans-serif;background:#fff1f2;color:#991b1b;padding:24px'>";
  echo "<div style='max-width:720px;margin:40px auto;background:#fff;border:1px solid #fecdd3;border-radius:24px;padding:24px'>";
  echo "<h1>❌ Submit Failed</h1><p><b>".h($msg)."</b></p>";
  if ($extra) echo "<pre style='white-space:pre-wrap;background:#f8fafc;padding:12px;border-radius:12px'>".h(json_encode($extra, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT))."</pre>";
  echo "<p><a href='/post/' style='display:inline-block;background:#e60023;color:#fff;text-decoration:none;padding:12px 18px;border-radius:999px;font-weight:900'>← Back</a></p>";
  echo "</div>";
  echo "<link rel='stylesheet' href='/assets/-bottom-bar.css?v=991-bottomnav-css-load-fix'><script src='/assets/-bottom-bar.js?v=991-bottomnav-css-load-fix' defer></script>";
  echo "</body></html>";
  exit;
}

function ok(string $ref): never {
  header('Content-Type: text/html; charset=UTF-8');
  echo "<!doctype html><html><head><meta charset='utf-8'><meta name='viewport' content='width=device-width,initial-scale=1'>";
  echo "<title>Submit Success</title></head><body style='font-family:Arial,\"Microsoft YaHei\",sans-serif;background:linear-gradient(135deg,#fff,#fff1f2);padding:24px;color:#111827'>";
  echo "<div style='max-width:720px;margin:40px auto;background:#fff;border:1px solid #ffd1d6;border-radius:28px;padding:28px;box-shadow:0 24px 70px rgba(230,0,35,.12)'>";
  echo "<h1 style='color:#e60023;margin-top:0'>✅ 提交成功 / Submitted</h1>";
  echo "<p style='font-size:18px;font-weight:800'>Post Ref: <code>".h($ref)."</code></p>";
  echo "<p>您的发布已提交，等待后台审核同步到 Marketplace + Community。</p>";
  echo "<p>Your post is pending approval and will be synced after review.</p>";
  echo "<p><a style='display:inline-block;background:#e60023;color:#fff;text-decoration:none;padding:13px 18px;border-radius:999px;font-weight:900' href='/post/'>继续发布 / Post Again</a></p>";
  echo "</div>";
  echo "<link rel='stylesheet' href='/assets/-bottom-bar.css?v=991-bottomnav-css-load-fix'><script src='/assets/-bottom-bar.js?v=991-bottomnav-css-load-fix' defer></script>";
  echo "</body></html>";
  exit;
}

set_exception_handler(function(Throwable $e): void {
  error_log('[991 post submit] '.$e->getMessage().' @ '.$e->getFile().':'.$e->getLine());
  fail('server_error', 500);
});

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: /post/');
  exit;
}

$target = p('target');
$listingType = p('listing_type', 'product');

$titleZh = p('title_zh');
$titleEn = p('title_en');
$descZh = p('description_zh');
$descEn = p('description_en');

if ($target === 'foreign') {
  if ($titleEn === '' || $descEn === '') fail('foreign_post_requires_english_title_and_description');
  $title = $titleEn;
  $description = $descEn;
} else {
  $target = 'china';
  if ($titleZh === '' || $descZh === '') fail('china_post_requires_chinese_title_and_description');
  $title = $titleZh;
  $description = $descZh;
}

$priceRaw = p('price', '0');
$price = is_numeric($priceRaw) ? (float)$priceRaw : 0.00;
$whatsapp = preg_replace('/[^\d+]/', '', p('whatsapp', p('wa')));
if ($whatsapp === '') fail('whatsapp_required');

$postRef = 'EP' . date('YmdHis') . strtoupper(substr(bin2hex(random_bytes(6)), 0, 12));

$uploadDir = '/var/www/html/visa/public/uploads/free-posts';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);

$imagePath = '';
if (!empty($_FILES['image']['name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
  $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
  $mime = mime_content_type($_FILES['image']['tmp_name']) ?: '';
  if (!isset($allowed[$mime])) fail('invalid_image_type');
  if ((int)$_FILES['image']['size'] > 5 * 1024 * 1024) fail('image_too_large_max_5mb');

  $filename = strtolower($postRef).'.'.$allowed[$mime];
  $abs = $uploadDir.'/'.$filename;
  if (!move_uploaded_file($_FILES['image']['tmp_name'], $abs)) fail('image_upload_failed', 500);
  @chmod($abs, 0644);
  $imagePath = '/uploads/free-posts/'.$filename;
}

$db = @new mysqli('localhost', 'root', '', 'visa_db');
if ($db->connect_errno) fail('db_failed', 500, ['detail'=>$db->connect_error]);
$db->set_charset('utf8mb4');

$db->query("CREATE TABLE IF NOT EXISTS visa_free_posts (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  post_ref VARCHAR(64) NOT NULL UNIQUE,
  target VARCHAR(32) NOT NULL,
  listing_type VARCHAR(32) NOT NULL DEFAULT 'product',
  title VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  price DECIMAL(24,2) NOT NULL DEFAULT 0.00,
  whatsapp VARCHAR(64) NOT NULL,
  image_path VARCHAR(255) NULL,
  marketplace_product_id BIGINT NULL,
  community_discussion_id BIGINT NULL,
  nationality_slug VARCHAR(128) NULL,
  category_slug VARCHAR(128) NULL,
  subcategory_slug VARCHAR(128) NULL,
  location_slug VARCHAR(128) NULL,
  sub_location_slug VARCHAR(128) NULL,
  sync_status VARCHAR(32) NOT NULL DEFAULT 'pending',
  sync_message MEDIUMTEXT NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

$db->query("ALTER TABLE visa_free_posts ADD COLUMN IF NOT EXISTS nationality_slug VARCHAR(128) NULL AFTER community_discussion_id");
$db->query("ALTER TABLE visa_free_posts ADD COLUMN IF NOT EXISTS category_slug VARCHAR(128) NULL AFTER nationality_slug");
$db->query("ALTER TABLE visa_free_posts ADD COLUMN IF NOT EXISTS subcategory_slug VARCHAR(128) NULL AFTER category_slug");
$db->query("ALTER TABLE visa_free_posts ADD COLUMN IF NOT EXISTS location_slug VARCHAR(128) NULL AFTER subcategory_slug");
$db->query("ALTER TABLE visa_free_posts ADD COLUMN IF NOT EXISTS sub_location_slug VARCHAR(128) NULL AFTER location_slug");


$route = [
  'target'=>$target,
  'listing_type'=>$listingType,
  'ton_address'=>p('ton_address'),
  'nationality_slug'=>p('nationality_slug'),
  'nationality_zh'=>p('nationality_zh'),
  'nationality_en'=>p('nationality_en'),
  'category_slug'=>p('category_slug', p('category')),
  'category_zh'=>p('category_zh'),
  'category_en'=>p('category_en'),
  'subcategory_slug'=>p('subcategory_slug', p('sub_category', p('subcategory'))),
  'location_slug'=>p('location_slug', p('location')),
  'location_zh'=>p('location_zh'),
  'location_en'=>p('location_en'),
  'sub_location_slug'=>p('sub_location_slug', p('sub_location')),
  'sub_location_zh'=>p('sub_location_zh'),
  'sub_location_en'=>p('sub_location_en'),
  'title_zh'=>$titleZh,
  'title_en'=>$titleEn,
  'description_zh'=>$descZh,
  'description_en'=>$descEn,
  'facebook_link'=>p('facebook_link'),
  'tiktok_link'=>p('tiktok_link'),
  'xhs_link'=>p('xhs_link'),
  'image_path'=>$imagePath,
  'source'=>'post_index',
  'ip'=>$_SERVER['REMOTE_ADDR'] ?? '',
  'ua'=>$_SERVER['HTTP_USER_AGENT'] ?? '',
];

$syncMessage = json_encode($route, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$stmt = $db->prepare("INSERT INTO visa_free_posts
(post_ref,target,listing_type,title,description,price,whatsapp,image_path,sync_status,sync_message,created_at,updated_at)
VALUES (?,?,?,?,?,?,?,?,'pending',?,NOW(),NOW())");

if (!$stmt) fail('prepare_failed', 500, ['detail'=>$db->error]);

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

if (!$stmt->execute()) fail('insert_failed', 500, ['detail'=>$stmt->error]);

ok($postRef);
