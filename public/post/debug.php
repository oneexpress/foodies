<?php
declare(strict_types=1);
header("Content-Type: text/html; charset=UTF-8");

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function check_file($p){ return is_file($p) ? 'OK' : 'MISSING'; }
function check_dir($p){ return is_dir($p) && is_writable($p) ? 'OK' : (is_dir($p) ? 'NOT WRITABLE' : 'MISSING'); }

$root = "/var/www/html/visa";
$public = "$root/public";

$checks = [];
$checks['/post/index.php'] = check_file("$public/post/index.php");
$checks['/post/submit.php'] = check_file("$public/post/submit.php");
$checks['/post/image.php'] = check_file("$public/post/image.php");
$checks['/post/api/options.php'] = check_file("$public/post/api/options.php");
$checks['storage/free-posts'] = check_dir("$root/storage/free-posts");

$db = [];
$api = [];
$latest = [];

try {
  $pdo = new PDO(
    'mysql:host=localhost;dbname=visa_ops_db;charset=utf8mb4',
    'oneexpressvisa',
    '$Express4653',
    [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]
  );

  foreach ([
    'ev_post_nationalities',
    'ev_post_categories',
    'ev_post_locations',
    'ev_post_sublocations',
    'visa_free_posts'
  ] as $t) {
    try {
      $db[$t] = $pdo->query("SELECT COUNT(*) FROM `$t`")->fetchColumn();
    } catch (Throwable $e) {
      $db[$t] = 'ERROR: '.$e->getMessage();
    }
  }

  try {
    $latest = $pdo->query("SELECT * FROM visa_free_posts ORDER BY id DESC LIMIT 10")->fetchAll();
  } catch (Throwable $e) {
    $latest = [['error'=>$e->getMessage()]];
  }

} catch (Throwable $e) {
  $db['DB_CONNECT'] = 'ERROR: '.$e->getMessage();
}

foreach ([
  'nationalities&target=china',
  'nationalities&target=foreign',
  'categories',
  'locations',
  'sublocations&loc=loc-kuala-lumpur',
  'sublocations&loc=loc-selangor',
  'sublocations&loc=loc-johor-bahru'
] as $q) {
  $url = "https://expressvisa.one/post/api/options.php?type=".$q;
  $raw = @file_get_contents($url);
  $api[$q] = $raw ? json_decode($raw, true) : ['ok'=>false,'error'=>'no response'];
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title> Post Debug</title>
<style>
body{font-family:Arial;background:#f5f6f8;margin:0;padding:24px}
.card{background:#fff;border-radius:18px;padding:20px;margin:0 0 18px;box-shadow:0 12px 30px rgba(0,0,0,.08)}
.ok{color:#0a7a31;font-weight:900}.bad{color:#d00000;font-weight:900}
table{width:100%;border-collapse:collapse}td,th{border:1px solid #ddd;padding:8px;text-align:left;font-size:13px}
pre{background:#111;color:#fff;padding:14px;border-radius:12px;overflow:auto}
</style>
</head>
<body>
<h1> Post Debug</h1>

<div class="card">
<h2>File Split Check</h2>
<table><tr><th>Item</th><th>Status</th></tr>
<?php foreach($checks as $k=>$v): ?>
<tr><td><?=h($k)?></td><td class="<?=$v==='OK'?'ok':'bad'?>"><?=h($v)?></td></tr>
<?php endforeach; ?>
</table>
</div>

<div class="card">
<h2>DB Selector Counts</h2>
<table><tr><th>Table</th><th>Count / Status</th></tr>
<?php foreach($db as $k=>$v): ?>
<tr><td><?=h($k)?></td><td><?=h($v)?></td></tr>
<?php endforeach; ?>
</table>
</div>

<div class="card">
<h2>API Output Check</h2>
<?php foreach($api as $k=>$v): ?>
<h3><?=h($k)?></h3>
<pre><?=h(json_encode($v, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES))?></pre>
<?php endforeach; ?>
</div>

<div class="card">
<h2>Latest Free Posts</h2>
<table>
<tr><th>ID</th><th>Ref</th><th>Target</th><th>Title</th><th>Sync</th><th>🚐 FoodTruck</th><th>Community</th><th>Created</th></tr>
<?php foreach($latest as $r): ?>
<tr>
<td><?=h($r['id'] ?? '-')?></td>
<td><?=h($r['post_ref'] ?? '-')?></td>
<td><?=h($r['target'] ?? '-')?></td>
<td><?=h($r['title'] ?? ($r['title_en'] ?? ($r['title_zh'] ?? '-')))?></td>
<td><?=h($r['sync_status'] ?? '-')?></td>
<td><?=h($r['marketplace_product_id'] ?? '-')?></td>
<td><?=h($r['community_discussion_id'] ?? ($r['foreign_discussion_id'] ?? ($r['china_discussion_id'] ?? '-')))?></td>
<td><?=h($r['created_at'] ?? '-')?></td>
</tr>
<?php endforeach; ?>
</table>
</div>
<script src="/assets/js/991-wallet-sync.js?v=991-unified"></script>
<script src="/assets/991-balance-sync.js?v=991-balance-sync-ux-v1" defer></script>
<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
</body>
</html>
