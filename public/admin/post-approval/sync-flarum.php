<?php
require_once __DIR__.'/config.php';
require_once __DIR__.'/category-map.php';

function ev_sync_flarum($post, $product_id){
    $target = $post['target'] ?? '';
    $db = ($target === 'china') ? 'flarum_china_db' : 'flarum_db';
    $pdo = ev_pdo($db);

    $check = $pdo->prepare("SELECT id FROM fl_discussions WHERE title=? LIMIT 1");
    $check->execute([$post['title']]);
    $dup = $check->fetch();
    if ($dup) return (int)$dup['id'];

    $wa = preg_replace('/\D+/', '', $post['whatsapp'] ?? '');
    $sub = ev_subcategory_label($post['category_slug'] ?? '', $post['subcategory_slug'] ?? '');
    $url = EV_DOMAIN.'/marketplace/index.php?route=product/product&product_id='.$product_id;

    $content =
        "🔥 ".$post['title']."\n\n".
        ($sub ? "🏷 ".$sub."\n" : "").
        "💰 RM ".number_format((float)$post['price'],2)."\n\n".
        "🛒 View Listing:\n".$url."\n\n".
        "📲 WhatsApp:\nhttps://wa.me/".$wa."\n\n".
        $post['description'];

    $stmt = $pdo->prepare("INSERT INTO fl_discussions (title, comment_count, participant_count, post_number_index, created_at, last_posted_at, user_id, first_post_id, last_post_id, is_private) VALUES (?,0,0,0,NOW(),NOW(),1,NULL,NULL,0)");
    $stmt->execute([$post['title']]);
    $discussion_id = (int)$pdo->lastInsertId();

    $stmt = $pdo->prepare("INSERT INTO fl_posts (discussion_id, number, created_at, user_id, type, content, is_private) VALUES (?,1,NOW(),1,'comment',?,0)");
    $stmt->execute([$discussion_id, $content]);
    $post_id = (int)$pdo->lastInsertId();

    $stmt = $pdo->prepare("UPDATE fl_discussions SET first_post_id=?, last_post_id=?, comment_count=1, post_number_index=1 WHERE id=?");
    $stmt->execute([$post_id, $post_id, $discussion_id]);

    return $discussion_id;
}


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
