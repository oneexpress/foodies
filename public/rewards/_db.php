<?php
declare(strict_types=1);

function ev_env_load(): void {
    $file = '/var/www/secure/.env';
    if (!is_file($file)) return;
    foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) continue;
        [$k,$v] = explode('=', $line, 2);
        $_ENV[trim($k)] = trim($v, " \t\n\r\0\x0B\"'");
    }
}

function ev_env(string $k, string $d=''): string {
    ev_env_load();
    return trim((string)($_ENV[$k] ?? getenv($k) ?: $d));
}

function ev_pdo(): PDO {
    $host = ev_env('DB_HOST', '127.0.0.1');
    $db   = ev_env('VISA_DB_NAME', ev_env('DB_DATABASE', 'visa_db'));
    $user = ev_env('DB_USERNAME', ev_env('DB_USER', 'oneexpressvisa'));
    $pass = ev_env('DB_PASSWORD', ev_env('DB_PASS', ''));
    return new PDO("mysql:host={$host};dbname={$db};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}

function ev_wallet(): string {
    return trim((string)($_COOKIE['ev_ton_wallet'] ?? $_COOKIE['ev_wallet_user'] ?? 'guest'));
}

function ev_json(array $data, int $code=200): never {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
