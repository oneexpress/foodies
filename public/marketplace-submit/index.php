<?php declare(strict_types=1); ?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>FoodTruck Marketplace Submit</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<form action="/marketplace-submit/submit-save.php" method="post">
  <input name="title" placeholder="Title" required>
  <textarea name="description" placeholder="Description" required></textarea>
  <input name="whatsapp" placeholder="WhatsApp" required>
  <button type="submit">Submit</button>
</form>
<script src="/marketplace-submit/taxonomy-bind.js?v=-final"></script>
<script src="/assets/js/991-wallet-sync.js?v=991-unified"></script>
<script src="/assets/991-balance-sync.js?v=991-balance-sync-ux-v1" defer></script>
<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
</body>
</html>
