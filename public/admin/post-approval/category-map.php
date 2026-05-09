<?php
require_once __DIR__.'/config.php';

function ev_category_map(){
    return [
        'svc-marketplace'    => ['oc'=>100, 'label'=>'FoodTruck Marketplace'],
        'svc-jobs-posting'   => ['oc'=>200, 'label'=>'Jobs Posting'],
        'svc-visa-permit'    => ['oc'=>300, 'label'=>'Visa & Permit'],
        'svc-transport'      => ['oc'=>400, 'label'=>'Luxury MPV Rental'],
        'svc-accommodation'  => ['oc'=>500, 'label'=>'Premium Homestay'],
        'svc-agency-helpdesk'=> ['oc'=>600, 'label'=>'Agency Helpdesk'],
    ];
}

function ev_subcategory_label($main_slug, $sub_slug){
    if (!$sub_slug) return '';
    $pdo = ev_pdo('visa_ops_db');
    $stmt = $pdo->prepare("SELECT name_zh,name_en FROM ev_post_subcategories WHERE main_slug=? AND sub_slug=? LIMIT 1");
    $stmt->execute([$main_slug, $sub_slug]);
    $r = $stmt->fetch();
    return $r ? ($r['name_zh'].' / '.$r['name_en']) : $sub_slug;
}

function ev_oc_category_id($post){
    $map = ev_category_map();
    $slug = $post['category_slug'] ?? '';
    return $map[$slug]['oc'] ?? 100;
}


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
