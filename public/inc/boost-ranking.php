<?php
function ev_get_boost_score(PDO $pdo, $post_ref) {
    $stmt = $pdo->prepare("
        SELECT SUM(boost_score) FROM ev_listing_boost
        WHERE post_ref=? AND status='confirmed'
    ");
    $stmt->execute([$post_ref]);
    return intval($stmt->fetchColumn());
}


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
