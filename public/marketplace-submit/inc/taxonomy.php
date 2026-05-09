<?php
declare(strict_types=1);

function ev_taxonomy(): array {
    $file = '/var/www/html/visa/public/assets/data/-unified-taxonomy.json';
    $data = json_decode((string)file_get_contents($file), true);
    return is_array($data) ? $data : ['services'=>[], 'nationalities'=>[], 'locations'=>[]];
}

function ev_slugify(string $s): string {
    $s = strtolower(trim($s));
    $s = preg_replace('/[^a-z0-9]+/i', '-', $s);
    return trim((string)$s, '-');
}

function ev_valid_taxonomy_value(string $type, string $value): bool {
    $tax = ev_taxonomy();
    if ($value === '') return true;

    if ($type === 'service') {
        foreach ($tax['services'] as $x) if (($x['key'] ?? '') === $value) return true;
    }

    if ($type === 'nationality') {
        foreach ($tax['nationalities'] as $x) if (($x['key'] ?? '') === $value) return true;
    }

    if ($type === 'location') {
        return array_key_exists($value, $tax['locations']);
    }

    if ($type === 'area') {
        foreach ($tax['locations'] as $areas) {
            if (in_array($value, $areas, true)) return true;
        }
    }

    return false;
}

function ev_find_oc_category_id(PDO $pdo, string $prefix, string $name): int {
    $q = $pdo->prepare("SELECT c.category_id FROM {$prefix}category c JOIN {$prefix}category_description d ON d.category_id=c.category_id WHERE d.name=? LIMIT 1");
    $q->execute([$name]);
    return (int)($q->fetchColumn() ?: 0);
}

function ev_marketplace_taxonomy_category_ids(PDO $pdo, string $prefix, array $row): array {
    $ids = [];
    $tax = ev_taxonomy();

    $serviceKey = (string)($row['service_key'] ?? '');
    foreach ($tax['services'] as $svc) {
        if (($svc['key'] ?? '') === $serviceKey) {
            $id = ev_find_oc_category_id($pdo, $prefix, (string)$svc['name']);
            if ($id) $ids[] = $id;
        }
    }

    $natKey = (string)($row['nationality_key'] ?? '');
    foreach ($tax['nationalities'] as $nat) {
        if (($nat['key'] ?? '') === $natKey) {
            $id = ev_find_oc_category_id($pdo, $prefix, (string)$nat['name']);
            if ($id) $ids[] = $id;
        }
    }

    $loc = (string)($row['location_name'] ?? '');
    $area = (string)($row['area_name'] ?? '');

    if ($loc !== '') {
        $id = ev_find_oc_category_id($pdo, $prefix, $loc);
        if ($id) $ids[] = $id;
    }

    if ($area !== '') {
        $id = ev_find_oc_category_id($pdo, $prefix, $area);
        if ($id) $ids[] = $id;
    }

    return array_values(array_unique(array_filter($ids)));
}


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
