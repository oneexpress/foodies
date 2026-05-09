<?php
declare(strict_types=1);
require dirname(__DIR__) . '/_guard.php';

error_reporting(E_ALL);
ini_set('display_errors','1');

const DB_USER='oneexpressvisa';
const DB_PASS='$Express4653';

function pdo(): PDO {
  return new PDO(
    "mysql:host=localhost;dbname=visa_db;charset=utf8mb4",
    DB_USER,
    DB_PASS,
    [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]
  );
}

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
if ($id <= 0) {
  http_response_code(400);
  exit('Missing post id');
}

$pdo = pdo();

$stmt = $pdo->prepare("SELECT id FROM visa_free_posts WHERE id=? LIMIT 1");
$stmt->execute([$id]);
if (!$stmt->fetch()) {
  http_response_code(404);
  exit('Post not found');
}

$pdo->prepare("
  UPDATE visa_free_posts
  SET status='approved',
      sync_status='pending',
      updated_at=NOW()
  WHERE id=?
")->execute([$id]);

$url = "https://expressvisa.one/admin/post-approval/sync-engine.php?id=".$id;
$result = @file_get_contents($url);

echo "<h2>Approved + Sync Triggered</h2>";
echo "<pre>";
echo htmlspecialchars((string)$result, ENT_QUOTES, 'UTF-8');
echo "</pre>";
echo "<p><a href='/admin/post-approval/'>Back</a></p>";


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
