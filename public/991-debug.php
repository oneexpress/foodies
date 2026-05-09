<?php
declare(strict_types=1);

$key = trim(@file_get_contents('/var/www/secure/991-debug.key'));
if (($_GET['key'] ?? '') !== $key) {
    http_response_code(403);
    exit('403 Forbidden');
}

header('Content-Type: text/plain');

echo "=== 991 QUICK DEBUG ===\n";
echo "Time: ".date('c')."\n\n";

echo "== SERVICES ==\n";
echo shell_exec("systemctl is-active nginx php8.4-fpm 2>&1");

echo "\n== NGINX TEST ==\n";
echo shell_exec("nginx -t 2>&1");

echo "\n== PHP VERSION ==\n";
echo shell_exec("php -v | head -1");

echo "\n== KEY FILES ==\n";
$files = [
  "/var/www/html/visa/public/index.php",
  "/var/www/html/visa/public/wallet/index.php",
];
foreach ($files as $f) {
    echo (file_exists($f) ? "OK " : "MISSING ") . $f . "\n";
}

echo "\n== LIVE ENDPOINTS ==\n";
$urls = ["/","/wallet/","/rewards/","/marketplace/"];
foreach ($urls as $u) {
    $code = trim(shell_exec("curl -ks -o /dev/null -w '%{http_code}' https://expressvisa.one$u"));
    echo "$code $u\n";
}

echo "\n== LAST ERRORS ==\n";
echo shell_exec("tail -20 /var/log/nginx/error.log 2>&1");
echo shell_exec("tail -20 /var/log/php8.4-fpm.log 2>&1");

echo "\n=== END ===\n";


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
