<?php
declare(strict_types=1);
session_start();

function ev_is_logged_in(): bool {
  return !empty($_SESSION['ev_account_id']) || !empty($_SESSION['ev_username']);
}

function ev_require_login(string $redirect='/post/'): void {
  if (!ev_is_logged_in()) {
    
    exit;
  }
}


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
