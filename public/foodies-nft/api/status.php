<?php
declare(strict_types=1);


header('Content-Type: application/json; charset=utf-8');

function out($a, int $code=200): void {
    http_response_code($code);
    echo json_encode(
        $a,
        JSON_UNESCAPED_UNICODE |
        JSON_UNESCAPED_SLASHES |
        JSON_PRETTY_PRINT
    );
    exit;
}

$owner = 'UQCFC6_JIg7YcJDaybYZKNSgETbDb28q-6SArFXItaI5jsCb';

$dbHost = '127.0.0.1';
$dbName = 'visa_db';
$dbUser = 'root';
$dbPass = '';

try {

    if (file_exists('/var/www/secure/.env')) {

        $env = parse_ini_file('/var/www/secure/.env');

        $dbHost = $env['DB_HOST'] ?? $dbHost;
        $dbName = $env['DB_DATABASE'] ?? ($env['DB_NAME'] ?? $dbName);
        $dbUser = $env['DB_USERNAME'] ?? ($env['DB_USER'] ?? $dbUser);
        $dbPass = $env['DB_PASSWORD'] ?? ($env['DB_PASS'] ?? $dbPass);

        if ($dbHost === 'localhost') {
            $dbHost = '127.0.0.1';
        }
    }

    $pdo = new PDO(
        "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    $tables = [];

    foreach ([
        'foodies_nft_certs',
        'foodies_certificates',
        'foodies_certs'
    ] as $t) {

        $exists = (bool)$pdo
            ->query("SHOW TABLES LIKE " . $pdo->quote($t))
            ->fetchColumn();

        $count = null;

        if ($exists) {
            try {
                $count = (int)$pdo
                    ->query("SELECT COUNT(*) FROM ")
                    ->fetchColumn();
            } catch(Throwable $e){}
        }

        $tables[$t] = [
            'exists' => $exists,
            'count' => $count
        ];
    }

    out([
        'ok' => true,
        'module' => 'foodies-nft',
        'owner_wallet' => $owner,
        'db' => [
            'host' => $dbHost,
            'database' => $dbName,
            'connected' => true
        ],
        'tables' => $tables,
        'files' => [
            'submit-cert.php' => file_exists(__DIR__.'/submit-cert.php'),
            'status.php' => file_exists(__FILE__),
            'footer-js' => file_exists(dirname(__DIR__).'/assets/foodies-footer-nav.js'),
            'footer-css' => file_exists(dirname(__DIR__).'/assets/foodies-footer-nav.css')
        ],
        'time' => date('c')
    ]);

} catch(Throwable $e) {

    out([
        'ok' => false,
        'error' => 'db_connect_failed',
        'detail' => $e->getMessage(),
        'host' => $dbHost,
        'database' => $dbName
    ],500);
}
