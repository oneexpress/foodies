<?php
declare(strict_types=1);
require_once __DIR__ . '/../public/inc/env.php';

$base = '/var/www/html/visa';
$bad = [];

$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS));
foreach ($it as $f) {
    $path = $f->getPathname();
    if (!preg_match('/\.(php|ts|js|json|sh)$/', $path)) continue;
    $txt = @file_get_contents($path);
    if ($txt === false) continue;
    if (str_contains($txt, '/var/www/secure/expressvisa-ton.env') || str_contains($txt, '/var/www/html/visa/ton/.env')) {
        $bad[] = $path;
    }
}

ev_env_guard_assert();

echo json_encode([
    'ok' => count($bad) === 0,
    'canonical_env' => '/var/www/secure/.env',
    'bad_reference_files' => $bad
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), PHP_EOL;

exit(count($bad) === 0 ? 0 : 1);
