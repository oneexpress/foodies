<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/admin_db.php';
require_once __DIR__ . '/../inc/publish_engine.php';

$db = ev_admin_db();
$table = ev_submit_table($db);

$id = (int)($_POST['id'] ?? 0);
$action = (string)($_POST['action'] ?? '');

if ($id <= 0) die('Invalid ID');

$q = $db->query("SELECT * FROM `$table` WHERE id={$id} LIMIT 1");
$row = $q ? $q->fetch_assoc() : null;
if (!$row) die('Submission not found');

if ($action === 'approve') {
    $productId = (int)($row['product_id'] ?? 0);
    if ($productId <= 0) {
        $productId = ev_create_opencart_product($row);
        ev_log_publish($row, $productId);
    }
    $db->query("UPDATE `$table` SET status='approved', product_id={$productId}, updated_at=NOW() WHERE id={$id}");
} elseif ($action === 'reject') {
    $db->query("UPDATE `$table` SET status='rejected', updated_at=NOW() WHERE id={$id}");
}

header('Location: index.php');


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
