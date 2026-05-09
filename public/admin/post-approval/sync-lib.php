<?php
declare(strict_types=1);
function sync_pdo_visa(): PDO {
    return new PDO('mysql:host=localhost;dbname=visa_db;charset=utf8mb4','root','',[
        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
    ]);
}

function sync_pdo_marketplace(): PDO {
    return new PDO('mysql:host=localhost;dbname=visa_marketplace_db;charset=utf8mb4','root','',[
        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
    ]);
}

function sync_pdo_flarum(string $target): PDO {
    $db = $target === 'china' ? 'china_flarum_db' : 'foreign_flarum_db';
    return new PDO("mysql:host=localhost;dbname={$db};charset=utf8mb4",'root','',[
        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
    ]);
}

function sync_slug(string $s): string {
    $s = strtolower(trim($s));
    $s = preg_replace('/[^a-z0-9]+/','-',$s);
    return trim($s ?: 'expressvisa-post','-');
}

function sync_create_opencart_product(array $post): int {
    $db = sync_pdo_marketplace();

    if (!empty($post['marketplace_product_id'])) {
        $chk = $db->prepare("SELECT product_id FROM oc_product WHERE product_id=? LIMIT 1");
        $chk->execute([(int)$post['marketplace_product_id']]);
        if ($chk->fetchColumn()) return (int)$post['marketplace_product_id'];
    }

    $price = (float)($post['price'] ?? 0);
    $model = $post['post_ref'];

    $db->beginTransaction();

    $db->prepare("
        INSERT INTO oc_product
        SET model=?, sku='', upc='', ean='', jan='', isbn='', mpn='', location='',
            quantity=999, stock_status_id=7, image=?, manufacturer_id=0, shipping=0,
            price=?, points=0, tax_class_id=0, date_available=CURDATE(), weight=0,
            weight_class_id=1, length=0, width=0, height=0, length_class_id=1,
            subtract=0, minimum=1, sort_order=0, status=1, viewed=0,
            date_added=NOW(), date_modified=NOW()
    ")->execute([
        $model,
        !empty($post['image_path']) ? ltrim((string)$post['image_path'],'/') : '',
        $price
    ]);

    $productId = (int)$db->lastInsertId();

    $name = mb_substr((string)$post['title'], 0, 255, 'UTF-8');
    $desc = nl2br(htmlspecialchars((string)$post['description'], ENT_QUOTES, 'UTF-8'));

    $db->prepare("
        INSERT INTO oc_product_description
        (product_id, language_id, name, description, tag, meta_title, meta_description, meta_keyword)
        VALUES (?, 1, ?, ?, ?, ?, ?, ?)
    ")->execute([
        $productId,
        $name,
        $desc,
        (string)$post['listing_type'],
        $name,
        mb_substr(strip_tags((string)$post['description']),0,255,'UTF-8'),
        $model
    ]);

    $db->prepare("INSERT INTO oc_product_to_store (product_id, store_id) VALUES (?,0)")
       ->execute([$productId]);

    $db->commit();

    return $productId;
}

function sync_find_flarum_user(PDO $db): int {
    $uid = (int)$db->query("SELECT id FROM flarum_users ORDER BY id ASC LIMIT 1")->fetchColumn();
    return $uid > 0 ? $uid : 1;
}

function sync_find_flarum_tag(PDO $db, string $target): ?int {
    $slug = $target === 'china' ? 'foodtruck' : 'svc-foodtruck';
    $s = $db->prepare("SELECT id FROM flarum_tags WHERE slug=? LIMIT 1");
    $s->execute([$slug]);
    $id = $s->fetchColumn();
    return $id ? (int)$id : null;
}

function sync_create_flarum_discussion(array $post): int {
    $db = sync_pdo_flarum((string)$post['target']);

    if (!empty($post['community_discussion_id'])) {
        $chk = $db->prepare("SELECT id FROM flarum_discussions WHERE id=? LIMIT 1");
        $chk->execute([(int)$post['community_discussion_id']]);
        if ($chk->fetchColumn()) return (int)$post['community_discussion_id'];
    }

    $userId = sync_find_flarum_user($db);
    $title = mb_substr((string)$post['title'], 0, 180, 'UTF-8');
    $content =
        $post['title'] . "\n\n" .
        $post['description'] . "\n\n" .
        "Price: RM " . $post['price'] . "\n" .
        "WhatsApp: https://wa.me/" . preg_replace('/[^0-9]/','',(string)$post['whatsapp']) . "\n" .
        "Post Ref: " . $post['post_ref'];

    $db->beginTransaction();

    $db->prepare("
        INSERT INTO flarum_discussions
        (title, comment_count, participant_count, post_number_index, created_at, user_id, first_post_id, last_posted_at, last_posted_user_id, last_post_id, slug, is_private)
        VALUES (?, 1, 1, 1, NOW(), ?, NULL, NOW(), ?, NULL, ?, 0)
    ")->execute([$title, $userId, $userId, sync_slug($title)]);

    $discussionId = (int)$db->lastInsertId();

    $db->prepare("
        INSERT INTO flarum_posts
        (discussion_id, number, created_at, user_id, type, content, edited_at, edited_user_id, hidden_at, hidden_user_id, ip_address, is_private)
        VALUES (?, 1, NOW(), ?, 'comment', ?, NULL, NULL, NULL, NULL, '127.0.0.1', 0)
    ")->execute([$discussionId, $userId, $content]);

    $postId = (int)$db->lastInsertId();

    $db->prepare("UPDATE flarum_discussions SET first_post_id=?, last_post_id=? WHERE id=?")
       ->execute([$postId, $postId, $discussionId]);

    $tagId = sync_find_flarum_tag($db, (string)$post['target']);
    if ($tagId) {
        $db->prepare("INSERT IGNORE INTO flarum_discussion_tag (discussion_id, tag_id) VALUES (?,?)")
           ->execute([$discussionId, $tagId]);
    }

    $db->commit();

    return $discussionId;
}

function sync_real_post(string $ref): array {
    $pdo = sync_pdo_visa();

    $s = $pdo->prepare("SELECT * FROM visa_free_posts WHERE post_ref=? LIMIT 1");
    $s->execute([$ref]);
    $post = $s->fetch();

    if (!$post) throw new RuntimeException("Post not found");
    if (($post['sync_status'] ?? '') === 'deleted') throw new RuntimeException("Deleted post skipped");

    $old = (string)($post['sync_status'] ?? '');
    $productId = sync_create_opencart_product($post);
    $discussionId = sync_create_flarum_discussion($post);

    $msg = "Real sync OK: OpenCart product {$productId}, Flarum discussion {$discussionId}";

    $pdo->beginTransaction();

    $pdo->prepare("
        UPDATE visa_free_posts
        SET sync_status='synced',
            sync_message=?,
            marketplace_product_id=?,
            community_discussion_id=?,
            updated_at=NOW()
        WHERE post_ref=?
    ")->execute([$msg,$productId,$discussionId,$ref]);

    $pdo->prepare("
        INSERT INTO visa_post_sync_log
        (post_ref,action,old_status,new_status,marketplace_product_id,community_discussion_id,message)
        VALUES (?, 'real_sync', ?, 'synced', ?, ?, ?)
    ")->execute([$ref,$old,$productId,$discussionId,$msg]);

    $pdo->commit();

    return [$productId,$discussionId,$msg];
}


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
