<?php
declare(strict_types=1);
session_start();

if (empty($_SESSION['ev_account_id'])) {
    
    exit;
}

$pdo = new PDO(
    'mysql:host=localhost;dbname=visa_db;charset=utf8mb4',
    'root',
    '',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$accountId = (int)$_SESSION['ev_account_id'];
$ref = trim($_POST['post_ref'] ?? $_GET['ref'] ?? '');

if ($ref === '') {
    http_response_code(400);
    exit('Missing post ref');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('POST required');
}

$pdo->prepare("
    UPDATE visa_free_posts
    SET sync_status='deleted', sync_message='Deleted by owner', updated_at=NOW()
    WHERE post_ref=? AND account_id=?
")->execute([$ref, $accountId]);

header('Location: /account/?deleted=1');
exit;


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
