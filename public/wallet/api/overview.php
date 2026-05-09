<?php
declare(strict_types=1);

session_start();
header('Content-Type: application/json; charset=utf-8');

function ev_env(string $key, string $default = ''): string {
    $v = getenv($key);
    if (is_string($v) && trim($v) !== '') return trim($v);
    if (isset($_ENV[$key]) && is_string($_ENV[$key]) && trim($_ENV[$key]) !== '') return trim($_ENV[$key]);
    if (isset($_SERVER[$key]) && is_string($_SERVER[$key]) && trim($_SERVER[$key]) !== '') return trim($_SERVER[$key]);

    $env = '/var/www/secure/.env';
    if (is_file($env)) {
        foreach (file($env, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) continue;
            [$k, $val] = array_map('trim', explode('=', $line, 2));
            if ($k === $key) return trim($val, "\"'");
        }
    }

    return $default;
}

function ev_http_json(string $url, array $headers = []): ?array {
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
    $json = json_decode($raw, true);
    return is_array($json) ? $json : null;
}

function ev_fmt_units(string $raw, int $decimals = 9): string {
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

    $frac = substr(rtrim($frac, '0'), 0, 6);
    return $whole . '.' . str_pad($frac, 6, '0');
}

function ev_pdo(): PDO {
    return new PDO(
        'mysql:host=localhost;dbname=visa_db;charset=utf8mb4',
        'oneexpressvisa',
        '$Express4653',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
}

$accountId = (int)($_SESSION['ev_account_id'] ?? 0);
$tonWallet = trim((string)(
    $_SESSION['ev_ton_address']
    ?? $_SESSION['ton_wallet']
    ?? $_SESSION['wallet_address']
    ?? ''
));

$vshare = '0.000000';
$vusdt  = '0.000000';

try {
    if ($accountId > 0) {
        $pdo = ev_pdo();

        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        $hasLedger = in_array('visa_wallet_balances', $tables, true);

        if ($hasLedger) {
            $stmt = $pdo->prepare("
                SELECT token_symbol, balance
                FROM visa_wallet_balances
                WHERE account_id = ?
            ");
            $stmt->execute([$accountId]);

            foreach ($stmt->fetchAll() as $row) {
                $sym = strtoupper((string)$row['token_symbol']);
                $bal = number_format((float)$row['balance'], 6, '.', '');
                if ($sym === 'VSHARE') $vshare = $bal;
                if ($sym === 'VUSDT')  $vusdt  = $bal;
            }
        }
    }
} catch (Throwable $e) {
    // Keep wallet UI alive even if internal ledger table differs.
}

$base = rtrim(ev_env('TONCENTER_BASE', 'https://toncenter.com/api/v3'), '/');
$key  = ev_env('TONCENTER_API_KEY', '');
$usdtMaster = ev_env('USDT_TON_MASTER', 'EQCxE6mUtQJKFnGfaROTKOt1lZbDiiX1kCixRv7Nw2Id_sDs');

$headers = ['Accept: application/json'];
if ($key !== '') $headers[] = 'X-API-Key: ' . $key;

$nativeTon = '0.000000';
$usdtTon   = '0.000000';

if ($tonWallet !== '') {
    $account = ev_http_json($base . '/account?address=' . rawurlencode($tonWallet), $headers);
    if (isset($account['balance'])) {
        $nativeTon = ev_fmt_units((string)$account['balance'], 9);
    } elseif (isset($account['accounts'][0]['balance'])) {
        $nativeTon = ev_fmt_units((string)$account['accounts'][0]['balance'], 9);
    }

    $jettons = ev_http_json(
        $base . '/jetton/wallets?owner_address=' . rawurlencode($tonWallet) .
        '&jetton_address=' . rawurlencode($usdtMaster),
        $headers
    );

    $rows = [];
    if (isset($jettons['jetton_wallets']) && is_array($jettons['jetton_wallets'])) $rows = $jettons['jetton_wallets'];
    if (isset($jettons['wallets']) && is_array($jettons['wallets'])) $rows = $jettons['wallets'];

    foreach ($rows as $jw) {
        $raw = $jw['balance'] ?? $jw['amount'] ?? null;
        if ($raw !== null) {
            $usdtTon = ev_fmt_units((string)$raw, 6);
            break;
        }
    }
}

echo json_encode([
    'ok' => true,
    'message' => 'WALLET_OVERVIEW_OK',
    'connected' => $tonWallet !== '',
    'wallet_address' => $tonWallet,
    'source' => [
        'offchain' => 'visa_db',
        'onchain' => 'toncenter_v3',
    ],
    'balances' => [
        'vshare' => $vshare,
        'vusdt' => $vusdt,
        'usdt_ton' => $usdtTon,
        'native_ton' => $nativeTon,
    ],
    'tokens' => [
        [
            'symbol' => 'vSHARE',
            'name' => 'ExpressVisa vSHARE',
            'type' => 'offchain',
            'balance' => $vshare,
            'logo' => '/metadata/991_vshare_logo.png',
        ],
        [
            'symbol' => 'vUSDT',
            'name' => 'ExpressVisa vUSDT',
            'type' => 'offchain',
            'balance' => $vusdt,
        ],
        [
            'symbol' => 'USDT-TON',
            'name' => 'Tether USD on TON',
            'type' => 'onchain',
            'balance' => $usdtTon,
        ],
        [
            'symbol' => 'TON',
            'name' => 'Native TON',
            'type' => 'onchain',
            'balance' => $nativeTon,
            'logo' => '/metadata/ton.png',
        ],
    ],
], JSON_UNESCAPED_SLASHES);


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
