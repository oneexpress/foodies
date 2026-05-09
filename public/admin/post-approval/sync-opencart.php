<?php
require_once __DIR__.'/config.php';
require_once __DIR__.'/category-map.php';
require_once __DIR__.'/image-sync.php';

function ev_sync_opencart($post){
    $pdo = ev_pdo('visa_marketplace_db', 'oneexpressvisa', '$Express4653');

    $check = $pdo->prepare("SELECT product_id FROM oc_product_description WHERE name=? LIMIT 1");
    $check->execute([$post['title']]);
    $dup = $check->fetch();
    if ($dup) return (int)$dup['product_id'];

    $image = ev_sync_image($post);
    $price = (float)($post['price'] ?? 0);
    $cat_id = ev_oc_category_id($post);

    $stmt = $pdo->prepare("INSERT INTO oc_product SET model=?, sku='', upc='', ean='', jan='', isbn='', mpn='', location='', quantity=1, stock_status_id=7, image=?, manufacturer_id=0, shipping=0, price=?, points=0, tax_class_id=0, date_available=CURDATE(), weight=0, weight_class_id=1, length=0, width=0, height=0, length_class_id=1, subtract=0, minimum=1, sort_order=0, status=1, viewed=0, date_added=NOW(), date_modified=NOW()");
    $stmt->execute(['EV-'.$post['post_ref'], $image, $price]);
    $product_id = (int)$pdo->lastInsertId();

    $desc = $post['description']."\n\nWhatsApp: https://wa.me/".preg_replace('/\D+/', '', $post['whatsapp'] ?? '');
    $stmt = $pdo->prepare("INSERT INTO oc_product_description SET product_id=?, language_id=1, name=?, description=?, tag='', meta_title=?, meta_description=?, meta_keyword=''");
    $stmt->execute([$product_id, $post['title'], $desc, $post['title'], mb_substr(strip_tags($desc),0,250)]);

    $stmt = $pdo->prepare("INSERT IGNORE INTO oc_product_to_category SET product_id=?, category_id=?");
    $stmt->execute([$product_id, $cat_id]);

    $stmt = $pdo->prepare("INSERT IGNORE INTO oc_product_to_store SET product_id=?, store_id=0");
    $stmt->execute([$product_id]);

    if ($image) {
        $stmt = $pdo->prepare("INSERT INTO oc_product_image SET product_id=?, image=?, sort_order=0");
        $stmt->execute([$product_id, $image]);
    }

    $slug = ev_slug(($post['category_slug'] ?? 'listing').'-'.($post['title'] ?? '').'-'.$product_id);
    $stmt = $pdo->prepare("INSERT IGNORE INTO oc_seo_url SET store_id=0, language_id=1, query=?, keyword=?");
    $stmt->execute(['product_id='.$product_id, $slug]);

    return $product_id;
}


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
