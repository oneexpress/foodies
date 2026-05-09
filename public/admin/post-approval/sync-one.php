<?php
declare(strict_types=1);
require_once __DIR__ . '/sync-lib.php';

$ref = trim($_POST['post_ref'] ?? $_GET['post_ref'] ?? '');
if ($ref === '') {
    http_response_code(400);
    exit('Missing post_ref');
}

try {
    sync_real_post($ref);
    header('Location: /admin/post-approval/?status=synced');
    exit;
} catch (Throwable $e) {
    $pdo = sync_pdo_visa();
    $pdo->prepare("UPDATE visa_free_posts SET sync_status='failed', sync_message=?, updated_at=NOW() WHERE post_ref=?")
        ->execute([$e->getMessage(), $ref]);

    $pdo->prepare("
        INSERT INTO visa_post_sync_log (post_ref,action,old_status,new_status,message)
        VALUES (?, 'real_sync_failed', NULL, 'failed', ?)
    ")->execute([$ref, $e->getMessage()]);

    http_response_code(500);
    echo "SYNC FAILED: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
