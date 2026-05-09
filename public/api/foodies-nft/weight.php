<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../inc/ev991-db.php';

function ev991_try_pdo(): PDO {
    try {
        return ev991_pdo();
    } catch (Throwable $e) {
        $conf = '/var/www/html/visa/public/marketplace/config.php';
        if (!is_file($conf)) throw $e;
        $s = file_get_contents($conf);
        $get = function(string $k, string $d='') use ($s): string {
            if (preg_match('/define\s*\(\s*[\"\']'.preg_quote($k,'/').'[\"\']\s*,\s*[\"\']([^\"\']*)[\"\']\s*\)/', $s, $m)) return $m[1];
            return $d;
        };
        $host = $get('DB_HOSTNAME', 'localhost');
        $db   = $get('DB_DATABASE', 'visa_db');
        $user = $get('DB_USERNAME', 'oneexpressvisa');
        $pass = $get('DB_PASSWORD', '$Express4653');
        $port = $get('DB_PORT', '3306');

        return new PDO(
            "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4",
            $user,
            $pass,
            [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]
        );
    }
}

try {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $wallet = trim((string)($_SESSION['ton_wallet'] ?? $_SESSION['wallet'] ?? $_GET['wallet'] ?? ''));

    $out = [
        'ok' => true,
        'wallet' => $wallet,
        'total_weight' => 0,
        'total_minted' => 0,
        'tiers' => [
            '1' => ['name'=>'Street Bites','zh'=>'街头美食','stars'=>'⭐','weight'=>1,'count'=>0],
            '2' => ['name'=>'Signature Dish','zh'=>'招牌菜','stars'=>'⭐⭐⭐','weight'=>3,'count'=>0],
            '3' => ['name'=>'Master Kitchen','zh'=>'大师厨房','stars'=>'⭐⭐⭐⭐⭐','weight'=>5,'count'=>0],
        ],
    ];

    $pdo = ev991_try_pdo();
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

    $table = null;
    foreach (['foodies_nfts','foodies_nft','visa_foodies_nfts','foodies_redeems'] as $t) {
        if (in_array($t, $tables, true)) { $table = $t; break; }
    }
    $out['source_table'] = $table;

    if (!$table || $wallet === '') {
        echo json_encode($out, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    $cols = $pdo->query("SHOW COLUMNS FROM `$table`")->fetchAll(PDO::FETCH_COLUMN);
    $walletCol = in_array('wallet', $cols, true) ? 'wallet' : (in_array('ton_wallet', $cols, true) ? 'ton_wallet' : null);
    $tierCol = in_array('tier', $cols, true) ? 'tier' : (in_array('tier_id', $cols, true) ? 'tier_id' : null);
    $statusCol = in_array('status', $cols, true) ? 'status' : null;

    if (!$walletCol || !$tierCol) {
        echo json_encode($out, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    $where = "`$walletCol` = ?";
    $params = [$wallet];
    if ($statusCol) $where .= " AND `$statusCol` IN ('approved','minted')";

    $st = $pdo->prepare("SELECT `$tierCol` tier, COUNT(*) c FROM `$table` WHERE $where GROUP BY `$tierCol`");
    $st->execute($params);

    foreach ($st->fetchAll() as $r) {
        $tier = (string)(int)$r['tier'];
        $count = (int)$r['c'];
        if (!isset($out['tiers'][$tier])) continue;
        $out['tiers'][$tier]['count'] = $count;
        $out['total_minted'] += $count;
        $out['total_weight'] += $count * (int)$out['tiers'][$tier]['weight'];
    }

    echo json_encode($out, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok'=>false,'error'=>$e->getMessage()], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
