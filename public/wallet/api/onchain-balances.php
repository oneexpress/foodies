<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$envFile = '/var/www/secure/.env';
if (is_file($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) continue;
        [$k, $v] = explode('=', $line, 2);
        $_ENV[trim($k)] = trim($v, " \t\n\r\0\x0B\"'");
    }
}

function ev_env(string $k, string $d=''): string {
    return trim((string)($_ENV[$k] ?? getenv($k) ?: $d));
}

function ev_http_json(string $url, array $headers): ?array {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_HTTPHEADER => $headers,
    ]);
    $raw = curl_exec($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if (!is_string($raw) || $code >= 400) return null;
    $j = json_decode($raw, true);
    return is_array($j) ? $j : null;
}

function ev_dec(string $raw, int $decimals): string {
    $raw = preg_replace('/\D/', '', $raw) ?: '0';
    $raw = ltrim($raw, '0');
    if ($raw === '') $raw = '0';

    if (strlen($raw) <= $decimals) {
        $whole = '0';
        $frac = str_pad($raw, $decimals, '0', STR_PAD_LEFT);
    } else {
        $whole = substr($raw, 0, -$decimals);
        $frac = substr($raw, -$decimals);
    }

    $frac = rtrim(substr($frac, 0, 6), '0');
    return $frac === '' ? $whole . '.000000' : $whole . '.' . str_pad($frac, 6, '0');
}

$address = trim((string)($_GET['address'] ?? ''));
if ($address === '') {
    echo json_encode(['ok'=>false,'error'=>'ADDRESS_REQUIRED']);
    exit;
}

$base = rtrim(ev_env('TONCENTER_BASE', 'https://toncenter.com/api/v3'), '/');
$key  = ev_env('TONCENTER_API_KEY', '');

$masters = [
    'USDT-TON' => [
        'master' => ev_env('USDT_TON_MASTER', 'EQCxE6mUtQJKFnGfaROTKOt1lZbDiiX1kCixRv7Nw2Id_sDs'),
        'decimals' => 6,
    ],
    'vUSDT' => [
        'master' => ev_env('VUSDT_TON_MASTER', ''),
        'decimals' => 9,
    ],
    'vSHARE' => [
        'master' => ev_env('VSHARE_TON_MASTER', ''),
        'decimals' => 9,
    ],
];

$headers = ['Accept: application/json'];
if ($key !== '') $headers[] = 'X-API-Key: '.$key;

$balances = [
    'vUSDT' => null,
    'vSHARE' => null,
    'USDT-TON' => '0.000000',
    'Native TON' => '0.000000',
];

$acc = ev_http_json($base.'/account?address='.rawurlencode($address), $headers);
if (isset($acc['balance'])) {
    $balances['Native TON'] = ev_dec((string)$acc['balance'], 9);
} elseif (isset($acc['accounts'][0]['balance'])) {
    $balances['Native TON'] = ev_dec((string)$acc['accounts'][0]['balance'], 9);
}

$jettons = ev_http_json($base.'/jetton/wallets?owner_address='.rawurlencode($address), $headers);
$rows = $jettons['jetton_wallets'] ?? $jettons['wallets'] ?? $jettons['accounts'] ?? [];

if (is_array($rows)) {
    foreach ($rows as $r) {
        $master = (string)($r['jetton'] ?? $r['jetton_master'] ?? $r['jetton_master_address'] ?? $r['master'] ?? '');
        $balance = (string)($r['balance'] ?? $r['amount'] ?? '0');

        foreach ($masters as $symbol => $cfg) {
            if ($cfg['master'] === '') continue;
            if ($master === $cfg['master'] || str_contains($master, $cfg['master'])) {
                $balances[$symbol] = ev_dec($balance, (int)$cfg['decimals']);
            }
        }
    }
}

echo json_encode([
    'ok' => true,
    'address' => $address,
    'balances' => $balances,
    'configured' => [
        'vUSDT' => $masters['vUSDT']['master'] !== '',
        'vSHARE' => $masters['vSHARE']['master'] !== '',
        'USDT-TON' => $masters['USDT-TON']['master'] !== '',
    ],
    'source' => 'toncenter_v3',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
