<?php
declare(strict_types=1);
session_start();

const EV_ADMIN_PIN = '4653';

if (isset($_GET['logout'])) {
  unset($_SESSION['ev_admin_ok']);
  header('Location: /admin/login.php');
  exit;
}

if (!empty($_SESSION['ev_admin_ok'])) {
  return;
}

header('Location: /admin/login.php?next=' . urlencode($_SERVER['REQUEST_URI'] ?? '/admin/control/'));
exit;


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
