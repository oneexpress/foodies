<?php
declare(strict_types=1);
require_once __DIR__ . '/../../foodies-nft/_db.php';

$pdo = fnft_pdo();
$id = (int)($_POST['id'] ?? 0);
$action = trim((string)($_POST['action'] ?? ''));

if ($id <= 0 || !in_array($action, ['approve','minted'], true)) {
    header('Location: /admin/foodies-nft/?err=bad_request');
    exit;
}

if ($action === 'approve') {
    $st = $pdo->prepare("UPDATE ev_foodies_nft_redeems SET status='approved', approved_at=NOW() WHERE id=? AND status='pending'");
    $st->execute([$id]);
} elseif ($action === 'minted') {
    $st = $pdo->prepare("UPDATE ev_foodies_nft_redeems SET status='minted', minted_at=NOW() WHERE id=? AND status IN ('pending','approved')");
    $st->execute([$id]);
}

header('Location: /admin/foodies-nft/?ok=' . urlencode($action));
exit;


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
