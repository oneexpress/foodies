<?php
declare(strict_types=1);
require_once __DIR__ . '/inc/publish_engine.php';

$row = [
  'title' => 'Test ExpressVisa Listing',
  'description' => 'Test listing created by publish engine.',
  'whatsapp' => '0123456789',
  'price' => '0',
  'service_category_id' => $_GET['service_category_id'] ?? 0,
  'nationality_category_id' => $_GET['nationality_category_id'] ?? 0,
  'location_category_id' => $_GET['location_category_id'] ?? 0,
  'area_category_id' => $_GET['area_category_id'] ?? 0,
  'service_name' => 'Visa & Permit',
  'nationality_name' => 'Indonesia',
  'location_name' => 'Kuala Lumpur',
  'area_name' => 'Cheras',
];

$product_id = ev_create_opencart_product($row);
ev_log_publish($row, $product_id);

header('Content-Type: text/plain; charset=utf-8');
echo "OK product_id={$product_id}\n\n";
echo ev_community_template($row, $product_id);


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
