<?php
const EV_ROOT = '/var/www/html/visa/public';
const EV_DOMAIN = 'https://expressvisa.one';
const EV_IMAGE_DIR = '/var/www/html/visa/public/marketplace/image/catalog/visa';
const EV_IMAGE_REL = 'catalog/visa/';

function ev_pdo($db, $user='root', $pass=''){
    return new PDO("mysql:host=localhost;dbname={$db};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}

function ev_log($post_id, $msg){
    try {
        $pdo = ev_pdo('visa_ops_db');
        $stmt = $pdo->prepare("INSERT INTO ev_sync_errors(module,post_id,error_text) VALUES('sync-v3',?,?)");
        $stmt->execute([$post_id, $msg]);
    } catch(Throwable $e) {}
}

function ev_slug($s){
    $s = strtolower(trim((string)$s));
    $s = preg_replace('/[^a-z0-9]+/', '-', $s);
    $s = trim($s, '-');
    return $s ?: 'expressvisa-listing';
}


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
