<?php
declare(strict_types=1);

$f = basename((string)($_GET['f'] ?? ''));
$path = '/var/www/html/visa/storage/free-posts/' . $f;

if ($f === '' || !is_file($path)) {
    http_response_code(404);
    exit('Not found');
}

$mime = mime_content_type($path) ?: 'application/octet-stream';
if (!in_array($mime, ['image/jpeg','image/png','image/webp'], true)) {
    http_response_code(403);
    exit('Forbidden');
}

header('Content-Type: ' . $mime);
header('Cache-Control: public, max-age=604800');
readfile($path);


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
