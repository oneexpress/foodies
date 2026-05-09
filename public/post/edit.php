<?php
declare(strict_types=1);
session_start();

if (empty($_SESSION['ev_account_id'])) {
    
    exit;
}

function h($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

$pdo = new PDO(
    'mysql:host=localhost;dbname=visa_db;charset=utf8mb4',
    'root',
    '',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
);

$accountId = (int)$_SESSION['ev_account_id'];
$ref = trim($_GET['ref'] ?? $_POST['post_ref'] ?? '');

if ($ref === '') {
    http_response_code(400);
    exit('Missing post ref');
}

$stmt = $pdo->prepare("SELECT * FROM visa_free_posts WHERE post_ref=? AND account_id=? LIMIT 1");
$stmt->execute([$ref, $accountId]);
$post = $stmt->fetch();

if (!$post) {
    http_response_code(403);
    exit('Unauthorized or post not found');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = preg_replace('/[^0-9.]/', '', (string)($_POST['price'] ?? '0'));
    $whatsapp = preg_replace('/[^0-9]/', '', (string)($_POST['whatsapp'] ?? ''));

    if ($title === '') exit('Title required');
    if ($description === '') exit('Description required');
    if ($whatsapp === '' || strlen($whatsapp) < 8) exit('Valid WhatsApp required');

    $pdo->prepare("
        UPDATE visa_free_posts
        SET title=?, description=?, price=?, whatsapp=?, sync_status='pending', sync_message='Edited by owner; pending admin re-approval', updated_at=NOW()
        WHERE post_ref=? AND account_id=?
    ")->execute([$title, $description, number_format((float)$price, 2, '.', ''), $whatsapp, $ref, $accountId]);

    header('Location: /account/?updated=1');
    exit;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Edit Post · ExpressVisa</title>
<style>
body{font-family:Arial,"Microsoft YaHei",sans-serif;background:#f6f7fb;margin:0;padding:24px 18px 150px}
.wrap{max-width:850px;margin:auto;background:#fff;border-radius:24px;padding:24px;box-shadow:0 18px 50px #0001}
input,textarea{width:100%;border:1px solid #ddd;border-radius:16px;padding:14px;font-size:15px;margin:7px 0 16px}
textarea{height:220px}
button,a.btn{display:inline-block;border:0;background:#e60023;color:#fff;border-radius:999px;padding:12px 18px;text-decoration:none;font-weight:900;cursor:pointer}
</style>
</head>
<body>
<div class="wrap">
<h1>Edit Post</h1>
<form method="post">
<input type="hidden" name="post_ref" value="<?=h($post['post_ref'])?>">
<label>Title</label>
<input name="title" value="<?=h($post['title'])?>" required>
<label>Description</label>
<textarea name="description" required><?=h($post['description'])?></textarea>
<label>Price RM</label>
<input name="price" value="<?=h($post['price'])?>">
<label>WhatsApp</label>
<input name="whatsapp" value="<?=h($post['whatsapp'])?>" required>
<button type="submit">Save Changes</button>
<a class="btn" href="/account/">Back</a>
</form>
</div>
<script src="/assets/js/991-wallet-sync.js?v=991-unified"></script>
<script src="/assets/991-balance-sync.js?v=991-balance-sync-ux-v1" defer></script>
<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
</body>
</html>
