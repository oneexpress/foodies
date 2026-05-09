<?php
declare(strict_types=1);
header('Content-Type: application/json');

$mysqli = new mysqli('localhost','oneexpressvisa','$Express4653','visa_marketplace_db');
if ($mysqli->connect_error) {
    echo json_encode(['ok'=>false,'error'=>'db']); exit;
}

$parent = isset($_GET['parent_id']) ? (int)$_GET['parent_id'] : 0;

/**
 * IMPORTANT:
 * - join oc_category_description
 * - enforce language_id = 1 (default EN)
 * - filter status=1
 */
$sql = "
SELECT 
    c.category_id,
    c.parent_id,
    cd.name
FROM oc_category c
LEFT JOIN oc_category_description cd 
    ON c.category_id = cd.category_id
WHERE 
    c.parent_id = ?
    AND c.status = 1
    AND cd.language_id = 1
ORDER BY c.sort_order, cd.name
";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $parent);
$stmt->execute();
$res = $stmt->get_result();

$data = [];
while ($row = $res->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode([
    'ok' => true,
    'count' => count($data),
    'data' => $data
]);


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
