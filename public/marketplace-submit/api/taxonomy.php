<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

$cfg = '/var/www/html/visa/public/marketplace/config.php';
if (!is_file($cfg)) {
    http_response_code(500);
    echo json_encode(['ok'=>false,'error'=>'marketplace_config_missing']);
    exit;
}

require_once $cfg;

$mysqli = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(['ok'=>false,'error'=>'db_connect_failed']);
    exit;
}
$mysqli->set_charset('utf8mb4');

$prefix = DB_PREFIX;

$sql = "
SELECT c.category_id, c.parent_id, d.name
FROM {$prefix}category c
JOIN {$prefix}category_description d ON d.category_id = c.category_id
WHERE c.status = 1
AND d.language_id = (
  SELECT MIN(language_id) FROM {$prefix}language
)
ORDER BY c.parent_id ASC, c.sort_order ASC, d.name ASC
";

$res = $mysqli->query($sql);
if (!$res) {
    http_response_code(500);
    echo json_encode(['ok'=>false,'error'=>'category_query_failed','detail'=>$mysqli->error]);
    exit;
}

$rows = [];
while ($r = $res->fetch_assoc()) {
    $rows[] = [
        'id' => (int)$r['category_id'],
        'parent_id' => (int)$r['parent_id'],
        'name' => $r['name'],
    ];
}

function children_of(array $rows, string $parentName): array {
    $parentId = 0;
    foreach ($rows as $r) {
        if (strcasecmp($r['name'], $parentName) === 0) {
            $parentId = (int)$r['id'];
            break;
        }
    }
    if (!$parentId) return [];

    $out = [];
    foreach ($rows as $r) {
        if ((int)$r['parent_id'] === $parentId) {
            $out[] = $r;
        }
    }
    return $out;
}

$services = children_of($rows, 'ExpressVisa Services');
$nationalities = children_of($rows, 'Nationalities');
$locationParents = children_of($rows, 'Service Locations');

$locations = [];
foreach ($locationParents as $loc) {
    $areas = [];
    foreach ($rows as $r) {
        if ((int)$r['parent_id'] === (int)$loc['id']) {
            $areas[] = $r;
        }
    }
    $locations[] = [
        'id' => $loc['id'],
        'name' => $loc['name'],
        'areas' => $areas,
    ];
}

echo json_encode([
    'ok' => true,
    'services' => $services,
    'nationalities' => $nationalities,
    'locations' => $locations,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
