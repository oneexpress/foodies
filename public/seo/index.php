<?php
require_once __DIR__.'/../inc/visa-vusdt.php';
$pdo = visa_pdo();

$ref = $_GET['ref'] ?? '';
$s = $pdo->prepare("SELECT * FROM visa_bookings WHERE booking_ref=?");
$s->execute([$ref]);
$b = $s->fetch();

if (!$b) { http_response_code(404); exit; }

$title = $b['service_type']." Malaysia - ".$b['booking_ref'];
?>
<!doctype html>
<html>
<head>
<title><?=htmlspecialchars($title)?></title>
<meta name="description" content="Visa service Malaysia <?=htmlspecialchars($b['service_type'])?>">
</head>
<body>
<h1><?=htmlspecialchars($title)?></h1>
<p>Location: <?=htmlspecialchars($b['location'])?></p>
<p>Price: <?=htmlspecialchars($b['service_fee_vusdt'])?> vUSDT</p>
<a href="/pay/?ref=<?=htmlspecialchars($b['booking_ref'])?>">Pay Now</a>
<script src="/assets/js/991-wallet-sync.js?v=991-unified"></script>
<script src="/assets/991-balance-sync.js?v=991-balance-sync-ux-v1" defer></script>
<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
</body>
</html>
