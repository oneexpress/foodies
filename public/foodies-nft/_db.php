<?php
declare(strict_types=1);


function fnft_env_load(): void {
    static $loaded = false;
    if ($loaded) return;
    $loaded = true;
    $file = '/var/www/secure/.env';
    if (!is_file($file)) return;
    foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) continue;
        [$k,$v] = explode('=', $line, 2);
        $_ENV[trim($k)] = trim($v, " \t\n\r\0\x0B\"'");
    }
}

function fnft_env(string $k, string $d=''): string {
    fnft_env_load();
    return trim((string)($_ENV[$k] ?? getenv($k) ?: $d));
}

function fnft_pdo(): PDO {
    $host = fnft_env('DB_HOST', '127.0.0.1');
    $db   = fnft_env('VISA_DB_NAME', fnft_env('DB_DATABASE', 'visa_db'));
    $user = fnft_env('DB_USERNAME', fnft_env('DB_USER', 'oneexpressvisa'));
    $pass = fnft_env('DB_PASSWORD', fnft_env('DB_PASS', ''));
    return new PDO("mysql:host={$host};dbname={$db};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}

function fnft_wallet(): string {
    return trim((string)($_COOKIE['ev_ton_wallet'] ?? $_COOKIE['ev_wallet_user'] ?? 'guest'));
}

function fnft_json(array $data, int $code=200): never {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

function fnft_tiers(): array {
    return [
        1 => [
            'tier_no'=>1, 'tier_key'=>'street_bites',
            'name_en'=>'Street Bites', 'name_zh'=>'街头美食',
            'stars'=>'⭐', 'weight'=>1,
            'vshare'=>'100.000000', 'score'=>'100.000000',
            'icon'=>'🍜'
        ],
        2 => [
            'tier_no'=>2, 'tier_key'=>'signature_dish',
            'name_en'=>'Signature Dish', 'name_zh'=>'招牌菜',
            'stars'=>'⭐⭐⭐', 'weight'=>3,
            'vshare'=>'300.000000', 'score'=>'200.000000',
            'icon'=>'🍲'
        ],
        3 => [
            'tier_no'=>3, 'tier_key'=>'master_kitchen',
            'name_en'=>'Master Kitchen', 'name_zh'=>'大师厨房',
            'stars'=>'⭐⭐⭐⭐⭐', 'weight'=>5,
            'vshare'=>'1000.000000', 'score'=>'300.000000',
            'icon'=>'🍽️'
        ],
    ];
}


