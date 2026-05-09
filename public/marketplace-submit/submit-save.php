<?php
declare(strict_types=1);
require_once __DIR__ . '/inc/admin_db.php';

$db = ev_admin_db();
$table = ev_submit_table($db);

$stmt = $db->prepare("INSERT INTO `$table`
(title, description, whatsapp, price, service_category_id, nationality_category_id, location_category_id, area_category_id, service_name, nationality_name, location_name, area_name, status)
VALUES (?,?,?,?,?,?,?,?,?,?,?,?, 'pending')");

$title = $_POST['title'] ?? $_POST['product_name'] ?? '';
$description = $_POST['description'] ?? '';
$whatsapp = $_POST['whatsapp'] ?? $_POST['contact'] ?? '';
$price = (float)($_POST['price'] ?? 0);
$service_category_id = (int)($_POST['service_category_id'] ?? 0);
$nationality_category_id = (int)($_POST['nationality_category_id'] ?? 0);
$location_category_id = (int)($_POST['location_category_id'] ?? 0);
$area_category_id = (int)($_POST['area_category_id'] ?? 0);
$service_name = $_POST['service_name'] ?? '';
$nationality_name = $_POST['nationality_name'] ?? '';
$location_name = $_POST['location_name'] ?? '';
$area_name = $_POST['area_name'] ?? '';

$stmt->bind_param(
    'sssdiiiissss',
    $title,
    $description,
    $whatsapp,
    $price,
    $service_category_id,
    $nationality_category_id,
    $location_category_id,
    $area_category_id,
    $service_name,
    $nationality_name,
    $location_name,
    $area_name
);
$stmt->execute();

header('Location: /marketplace-submit/?submitted=1');


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
