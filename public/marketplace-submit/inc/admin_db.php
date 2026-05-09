<?php
declare(strict_types=1);
require_once '/var/www/html/visa/public/marketplace/config.php';

function ev_admin_db(): mysqli {
    $db = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
    if ($db->connect_error) throw new RuntimeException($db->connect_error);
    $db->set_charset('utf8mb4');
    return $db;
}

function ev_submit_table(mysqli $db): string {
    $names = ['adg_marketplace_submissions','ev_marketplace_submissions','oc_adg_marketplace_submissions'];
    foreach ($names as $t) {
        $r = $db->query("SHOW TABLES LIKE '{$db->real_escape_string($t)}'");
        if ($r && $r->num_rows) return $t;
    }

    $db->query("CREATE TABLE adg_marketplace_submissions (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description MEDIUMTEXT NULL,
        whatsapp VARCHAR(64) NULL,
        price DECIMAL(15,4) NOT NULL DEFAULT 0,
        service_category_id INT NULL,
        nationality_category_id INT NULL,
        location_category_id INT NULL,
        area_category_id INT NULL,
        service_name VARCHAR(128) NULL,
        nationality_name VARCHAR(128) NULL,
        location_name VARCHAR(128) NULL,
        area_name VARCHAR(128) NULL,
        status VARCHAR(32) NOT NULL DEFAULT 'pending',
        product_id INT NULL,
        admin_note TEXT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    return 'adg_marketplace_submissions';
}


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
