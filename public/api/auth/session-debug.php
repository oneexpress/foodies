<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

session_start();

echo json_encode([
  'session_id' => session_id(),
  'cookie' => $_COOKIE,
  'session' => $_SESSION,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
