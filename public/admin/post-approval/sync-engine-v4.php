<?php

require_once __DIR__ . '/rewards-hook.php';
// SYNC ENGINE V4 — SAFE PRODUCTION

error_reporting(E_ALL);
ini_set('display_errors',1);

$pdo = new PDO("mysql:host=localhost;dbname=visa_db;charset=utf8mb4","oneexpressvisa",'$Express4653');
$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

$post_id = intval($_GET['id'] ?? 0);
if(!$post_id) die("Missing ID");

// ========================
// LOAD POST
// ========================
$post = $pdo->query("SELECT * FROM visa_free_posts WHERE id=$post_id")->fetch(PDO::FETCH_ASSOC);
if(!$post) die("Post not found");

// ========================
// CONNECT OPENCART
// ========================
$oc = new PDO("mysql:host=localhost;dbname=visa_marketplace_db;charset=utf8mb4","oneexpressvisa",'$Express4653');
$oc->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

// ========================
// CATEGORY AUTO DETECT
// ========================
$category_name = 'FoodTruck'; // default

$cat = $oc->prepare("
SELECT c.category_id 
FROM oc_category_description cd
JOIN oc_category c ON c.category_id=cd.category_id
WHERE cd.name LIKE ?
LIMIT 1
");
$cat->execute(["%$category_name%"]);
$category_id = $cat->fetchColumn();

if(!$category_id) {
    die("❌ category_id not found");
}

// ========================
// IMAGE SAFE FIX
// ========================
$image = $post['image_path'];
if(!$image || !file_exists("/var/www/html/visa/public/marketplace/image/".$image)){
    $image = 'catalog/default.png';
}

// ========================
// INSERT PRODUCT
// ========================
$model = $post['post_ref'];

$oc->prepare("
INSERT INTO oc_product 
(model,price,status,date_added) 
VALUES (?,?,1,NOW())
")->execute([
    $model,
    $post['price']
]);

$product_id = $oc->lastInsertId();

// ========================
// DESCRIPTION
// ========================
$oc->prepare("
INSERT INTO oc_product_description 
(product_id,language_id,name,description)
VALUES (?,?,?,?)
")->execute([
    $product_id,
    1,
    $post['title'],
    $post['description']
]);

// ========================
// CATEGORY LINK
// ========================
$oc->prepare("
INSERT INTO oc_product_to_category 
(product_id,category_id)
VALUES (?,?)
")->execute([
    $product_id,
    $category_id
]);

// ========================
// IMAGE LINK
// ========================
$oc->prepare("
UPDATE oc_product SET image=? WHERE product_id=?
")->execute([$image,$product_id]);

// ========================
// SEO SAFE INSERT
// ========================
$keyword = strtolower(preg_replace('/[^a-z0-9]+/','-',$post['title'])).'-'.$product_id;

$exist = $oc->prepare("SELECT COUNT(*) FROM oc_seo_url WHERE keyword=?");
$exist->execute([$keyword]);

if(!$exist->fetchColumn()){
    $oc->prepare("
    INSERT INTO oc_seo_url (store_id,language_id,query,keyword)
    VALUES (0,1,?,?)
    ")->execute([
        "product_id=".$product_id,
        $keyword
    ]);
}

// ========================
// FLARUM SYNC
// ========================
$fl = new PDO("mysql:host=localhost;dbname=flarum_db;charset=utf8mb4","oneexpressvisa",'$Express4653');
$fl->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

// discussion
$fl->prepare("
INSERT INTO fl_discussions (title,created_at)
VALUES (?,NOW())
")->execute([$post['title']]);

$discussion_id = $fl->lastInsertId();

// post
$content = $post['description']."\n\nWhatsApp: ".$post['whatsapp'];

$fl->prepare("
INSERT INTO fl_posts (discussion_id,content,created_at,type)
VALUES (?,?,NOW(),'comment')
")->execute([
    $discussion_id,
    $content
]);

// ========================
// UPDATE STATUS
// ========================
$pdo->prepare("
UPDATE visa_free_posts 
SET sync_status='synced',
marketplace_product_id=?,
community_discussion_id=?
WHERE id=?
")->execute([
    $product_id,
    $discussion_id,
    $post_id
]);

echo "✅ SYNC SUCCESS: PRODUCT $product_id / DISCUSSION $discussion_id";

<?php
/* Foodies Rewards Engine safe post-approval trigger */
if (function_exists('ev_post_approval_reward_if_possible')) {
    try {
        if (isset($row) && is_array($row)) {
            ev_post_approval_reward_if_possible($row);
        } elseif (isset($post) && is_array($post)) {
            ev_post_approval_reward_if_possible($post);
        }
    } catch (Throwable $e) {
        error_log('[Foodies Rewards Engine] post approval reward failed: '.$e->getMessage());
    }
}


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
