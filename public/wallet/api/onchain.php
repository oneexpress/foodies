<?php
declare(strict_types=1);

session_start();
header('Content-Type: application/json; charset=utf-8');

function envv(string $k, string $d=''): string {
    $v = getenv($k);
    if (is_string($v) && trim($v) !== '') return trim($v);
    if (isset($_ENV[$k]) && is_string($_ENV[$k]) && trim($_ENV[$k]) !== '') return trim($_ENV[$k]);
    if (isset($_SERVER[$k]) && is_string($_SERVER[$k]) && trim($_SERVER[$k]) !== '') return trim($_SERVER[$k]);
    $env = '/var/www/secure/.env';
    if (is_file($env)) {
        foreach (file($env, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
            [$key,$val] = array_map('trim', explode('=', $line, 2));
            if ($key === $k) return trim($val, "\"'");
        }
    }
    return $d;
}

function http_json(string $url, array $headers=[]): ?array {
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

function fmt_units(string $raw, int $decimals): string {
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

    $frac = substr($frac, 0, 6);
    return $whole . '.' . str_pad(rtrim($frac, '0'), 6, '0');
}

$wallet = $_SESSION['ev_ton_address']
    ?? $_SESSION['ton_wallet']
    ?? $_SESSION['wallet_address']
    ?? '';

$wallet = trim((string)$wallet);

if ($wallet === '') {
    echo json_encode([
        'ok' => false,
        'connected' => false,
        'message' => 'TON wallet not connected',
        'balances' => [
            'native_ton' => '0.000000',
            'usdt_ton' => '0.000000',
        ],
    ]);
    exit;
}

$base = rtrim(envv('TONCENTER_BASE', 'https://toncenter.com/api/v3'), '/');
$key  = envv('TONCENTER_API_KEY', '');
$usdtMaster = envv('USDT_TON_MASTER', 'EQCxE6mUtQJKFnGfaROTKOt1lZbDiiX1kCixRv7Nw2Id_sDs');

$headers = ['Accept: application/json'];
if ($key !== '') $headers[] = 'X-API-Key: ' . $key;

$native = '0.000000';
$account = http_json($base . '/account?address=' . rawurlencode($wallet), $headers);
if (isset($account['balance'])) {
    $native = fmt_units((string)$account['balance'], 9);
} elseif (isset($account['accounts'][0]['balance'])) {
    $native = fmt_units((string)$account['accounts'][0]['balance'], 9);
}

$usdt = '0.000000';
$jettons = http_json($base . '/jetton/wallets?owner_address=' . rawurlencode($wallet) . '&jetton_address=' . rawurlencode($usdtMaster), $headers);

$candidates = [];
if (isset($jettons['jetton_wallets']) && is_array($jettons['jetton_wallets'])) $candidates = $jettons['jetton_wallets'];
if (isset($jettons['wallets']) && is_array($jettons['wallets'])) $candidates = $jettons['wallets'];

foreach ($candidates as $jw) {
    $balance = $jw['balance'] ?? $jw['amount'] ?? null;
    if ($balance !== null) {
        $usdt = fmt_units((string)$balance, 6);
        break;
    }
}

echo json_encode([
    'ok' => true,
    'connected' => true,
    'wallet' => $wallet,
    'source' => 'toncenter_v3',
    'balances' => [
        'native_ton' => $native,
        'usdt_ton' => $usdt,
    ],
]);


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
