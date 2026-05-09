<?php
require_once "/var/www/html/visa/public/marketplace/config.php";

$conn = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
$conn->set_charset("utf8mb4");

$prefix = DB_PREFIX;

// LOAD ALL CATEGORIES
$sql = "
SELECT c.category_id, c.parent_id, d.name
FROM {$prefix}a24ch6_visa_category c
JOIN {$prefix}category_description d ON c.category_id=d.category_id
WHERE d.language_id=1
ORDER BY c.parent_id, c.category_id
";

$res = $conn->query($sql);

$cats = [];
while($row = $res->fetch_assoc()){
    $cats[] = $row;
}

// BUILD TREE
$tree = [];
$map = [];

foreach ($cats as $c) {
    $c["children"] = [];
    $map[$c["category_id"]] = $c;
}

foreach ($map as $id => &$node) {
    if ($node["parent_id"] == 0) {
        $tree[] = &$node;
    } else {
        if (isset($map[$node["parent_id"]])) {
            $map[$node["parent_id"]]["children"][] = &$node;
        }
    }
}

header("Content-Type: application/json");
echo json_encode($tree);

