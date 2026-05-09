<?php
header('Content-Type: application/json');

$main = $_GET['main'] ?? '';

$pdo = new PDO("mysql:host=localhost;dbname=visa_ops_db;charset=utf8mb4","root","");

$stmt = $pdo->prepare("
SELECT sub_slug, name_zh, name_en
FROM ev_post_subcategories
WHERE main_slug = :main AND is_active=1
ORDER BY sort_order ASC
");

$stmt->execute(['main'=>$main]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
