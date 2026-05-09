<?php
declare(strict_types=1);


header('Content-Type: application/json; charset=utf-8');

function out(array $a, int $code=200): void {
    http_response_code($code);
    echo json_encode($a, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function envv(string $k, string $d=''): string {
    static $env=null;
    if ($env === null) {
        $env=[];
        $file='/var/www/secure/.env';
        if (is_file($file)) {
            foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                $line=trim($line);
                if ($line==='' || str_starts_with($line,'#') || !str_contains($line,'=')) continue;
                [$key,$val]=explode('=',$line,2);
                $env[trim($key)] = trim($val, " \t\n\r\0\x0B\"'");
            }
        }
    }
    return $env[$k] ?? getenv($k) ?: $d;
}

try {
    $host=envv('DB_HOST','127.0.0.1');
    if ($host==='localhost') $host='127.0.0.1';
    $db=envv('DB_DATABASE', envv('DB_NAME','visa_db'));
    $user=envv('DB_USERNAME', envv('DB_USER','root'));
    $pass=envv('DB_PASSWORD', envv('DB_PASS',''));

    $pdo=new PDO("mysql:host={$host};dbname={$db};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
    ]);

    $table=null;
    foreach (['foodies_nft_certs','foodies_certificates','foodies_certs'] as $t) {
        if ($pdo->query("SHOW TABLES LIKE ".$pdo->quote($t))->fetchColumn()) {
            $table=$t;
            break;
        }
    }
    if (!$table) out(['ok'=>true,'scope'=>'all','rows'=>[]]);

    $cols=$pdo->query("SHOW COLUMNS FROM `{$table}`")->fetchAll(PDO::FETCH_COLUMN);
    $has=function(string $c) use ($cols): bool { return in_array($c,$cols,true); };

    $select=[
        $has('id') ? 'id' : 'NULL AS id',
        $has('cert_uid') ? 'cert_uid' : ($has('uid') ? 'uid AS cert_uid' : 'NULL AS cert_uid'),
        $has('star_class') ? 'star_class' : ($has('star_rating') ? 'star_rating AS star_class' : 'NULL AS star_class'),
        $has('status') ? 'status' : "'issued' AS status",
        $has('chef_name') ? 'chef_name' : ($has('chef_real_name') ? 'chef_real_name AS chef_name' : 'NULL AS chef_name'),
        $has('brand_name') ? 'brand_name' : ($has('restaurant_name') ? 'restaurant_name AS brand_name' : 'NULL AS brand_name'),
        $has('food_title') ? 'food_title' : ($has('signature_dish') ? 'signature_dish AS food_title' : 'NULL AS food_title'),
        $has('issuer_wallet') ? 'issuer_wallet' : ($has('owner_wallet') ? 'owner_wallet AS issuer_wallet' : 'NULL AS issuer_wallet'),
        $has('created_at') ? 'created_at' : 'NULL AS created_at'
    ];

    $sql="SELECT ".implode(',', $select)." FROM `{$table}` ORDER BY ".($has('created_at') ? "created_at DESC" : ($has('id') ? "id DESC" : "1 DESC"))." LIMIT 300";
    $rows=$pdo->query($sql)->fetchAll();

    out([
        'ok'=>true,
        'scope'=>'all_platform',
        'table'=>$table,
        'rows'=>$rows
    ]);

} catch(Throwable $e) {
    out(['ok'=>false,'error'=>'list_issued_failed','detail'=>$e->getMessage()],500);
}
