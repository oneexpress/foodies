<?php
declare(strict_types=1);
require_once __DIR__ . '/sync-lib.php';

$pdo = sync_pdo_visa();
$type = $_GET['type'] ?? 'pending';

if ($type === 'failed') {
    $rows = $pdo->query("SELECT post_ref FROM visa_free_posts WHERE sync_status='failed' LIMIT 50")->fetchAll();
} elseif ($type === 'all') {
    $rows = $pdo->query("SELECT post_ref FROM visa_free_posts WHERE sync_status!='deleted' LIMIT 50")->fetchAll();
} else {
    $rows = $pdo->query("SELECT post_ref FROM visa_free_posts WHERE sync_status='pending' LIMIT 50")->fetchAll();
}

$ok = 0; $fail = 0;

foreach ($rows as $r) {
    $ref = $r['post_ref'];
    try {
        sync_real_post($ref);
        $ok++;
    } catch (Throwable $e) {
        $fail++;
        $pdo->prepare("UPDATE visa_free_posts SET sync_status='failed', sync_message=?, updated_at=NOW() WHERE post_ref=?")
            ->execute([$e->getMessage(), $ref]);
        $pdo->prepare("
            INSERT INTO visa_post_sync_log (post_ref,action,new_status,message)
            VALUES (?, 'bulk_real_sync_failed', 'failed', ?)
        ")->execute([$ref, $e->getMessage()]);
    }
}

echo "DONE real sync: ok={$ok}, failed={$fail}";


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
