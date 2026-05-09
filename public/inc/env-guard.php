<?php
declare(strict_types=1);

function ev_env_guard_assert(): void {
    $forbiddenFiles = [
        '/var/www/secure/expressvisa-ton.env',
        '/var/www/html/visa/ton/.env',
        '/var/www/html/visa/.env',
        '/var/www/html/visa/public/.env',
    ];

    foreach ($forbiddenFiles as $file) {
        if (is_file($file)) {
            throw new RuntimeException('Forbidden env file exists: ' . $file);
        }
    }

    if (!is_file('/var/www/secure/.env')) {
        throw new RuntimeException('Missing canonical env: /var/www/secure/.env');
    }
}


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
