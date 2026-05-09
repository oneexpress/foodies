<?php
function is_duplicate($pdo, $post){

$stmt = $pdo->prepare("
SELECT id FROM oc_product_description
WHERE name = ?
LIMIT 1
");

$stmt->execute([$post['title']]);

return $stmt->fetch() ? true : false;
}


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
