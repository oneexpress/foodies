<?php
declare(strict_types=1);


header('Content-Type: application/json; charset=utf-8');

function out_json(array $a, int $code = 200): void {
    http_response_code($code);
    echo json_encode($a, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    exit;
}

function envv(string $key, string $default = ''): string {
    static $env = null;
    if ($env === null) {
        $env = [];
        $file = '/var/www/secure/.env';
        if (is_file($file)) {
            foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) continue;
                [$k, $v] = explode('=', $line, 2);
                $env[trim($k)] = trim($v, " \t\n\r\0\x0B\"'");
            }
        }
    }
    return $env[$key] ?? getenv($key) ?: $default;
}

$owner = 'UQCFC6_JIg7YcJDaybYZKNSgETbDb28q-6SArFXItaI5jsCb';
$host = envv('DB_HOST', '127.0.0.1');
if ($host === 'localhost') $host = '127.0.0.1';
$db = envv('DB_DATABASE', envv('DB_NAME', 'visa_db'));
$user = envv('DB_USERNAME', envv('DB_USER', 'root'));
$pass = envv('DB_PASSWORD', envv('DB_PASS', ''));

$tables = [];
$dbOk = false;
$dbError = null;

try {
    $pdo = new PDO("mysql:host={$host};dbname={$db};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $dbOk = true;
    foreach (['foodies_nft_certs','foodies_certs','foodies_certificates'] as $t) {
        $exists = (bool)$pdo->query("SHOW TABLES LIKE ".$pdo->quote($t))->fetchColumn();
        $count = null;
        if ($exists) {
            try { $count = (int)$pdo->query("SELECT COUNT(*) FROM `$t`")->fetchColumn(); } catch (Throwable $e) {}
        }
        $tables[$t] = ['exists'=>$exists, 'count'=>$count];
    }
} catch (Throwable $e) {
    $dbError = $e->getMessage();
}

out_json([
    'ok' => $dbOk,
    'module' => 'foodies-nft',
    'owner_wallet' => $owner,
    'db' => [
        'host' => $host,
        'database' => $db,
        'connected' => $dbOk,
        'error' => $dbError,
        'tables' => $tables,
    ],
    'files' => [
        'submit_cert' => is_file(__DIR__.'/submit-cert.php'),
        'list_issued' => is_file(__DIR__.'/list-issued.php'),
        'footer_css' => is_file(dirname(__DIR__).'/assets/foodies-footer-nav.css'),
        'footer_js' => is_file(dirname(__DIR__).'/assets/foodies-footer-nav.js'),
    ],
    'time' => date('c'),
]);
