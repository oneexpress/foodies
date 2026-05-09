<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=300');

$file = __DIR__ . '/../assets/data/visa-directory-taxonomy.json';
if (!is_file($file)) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'taxonomy_missing']);
    exit;
}

$data = json_decode((string)file_get_contents($file), true);
if (!is_array($data)) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'taxonomy_invalid']);
    exit;
}

echo json_encode(['ok' => true, 'data' => $data], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
