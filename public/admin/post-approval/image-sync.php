<?php
require_once __DIR__.'/config.php';

function ev_sync_image($post){
    $src = trim($post['image_path'] ?? '');
    if ($src === '') return '';

    $src_abs = '';
    if (str_starts_with($src, '/')) {
        $src_abs = EV_ROOT . $src;
    } else {
        $src_abs = EV_ROOT . '/' . ltrim($src, '/');
    }

    if (!is_file($src_abs)) return '';

    $ext = strtolower(pathinfo($src_abs, PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg','jpeg','png','webp','gif'], true)) $ext = 'jpg';

    $name = 'ev-post-'.$post['id'].'-'.substr(sha1_file($src_abs),0,12).'.'.$ext;
    $dest = EV_IMAGE_DIR.'/'.$name;

    if (!is_file($dest)) copy($src_abs, $dest);

    return EV_IMAGE_REL.$name;
}


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
