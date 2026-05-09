<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

function out(array $a): void {
  echo json_encode($a, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  exit;
}

function envv(string $k, string $d=''): string {
  static $env = null;
  if ($env === null) {
    $env = is_file('/var/www/secure/.env') ? (parse_ini_file('/var/www/secure/.env', false, INI_SCANNER_RAW) ?: []) : [];
  }
  return trim((string)($env[$k] ?? $d), "\"'");
}

function http_json(string $url): array {
  $ch = curl_init($url);
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 25,
    CURLOPT_CONNECTTIMEOUT => 8,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_HTTPHEADER => [
      'Accept: application/json',
      'User-Agent: ExpressVisa991Wallet/1.0'
    ],
  ]);
  $body = curl_exec($ch);
  $code = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
  $err = curl_error($ch);
  curl_close($ch);
  $j = json_decode((string)$body, true);
  return is_array($j) ? $j + ['_http'=>$code] : ['_http'=>$code, '_error'=>$err, '_raw'=>(string)$body];
}

function dec_units(string $units, int $dec): string {
  $units = preg_replace('/[^0-9]/', '', $units) ?: '0';
  if ($dec <= 0) return $units;
  if (strlen($units) <= $dec) $units = str_pad($units, $dec + 1, '0', STR_PAD_LEFT);
  $a = ltrim(substr($units, 0, -$dec), '0');
  if ($a === '') $a = '0';
  $b = rtrim(substr($units, -$dec), '0');
  return $b === '' ? $a : "$a.$b";
}

function norm(string $s): string {
  return strtolower(str_replace(['-', '_'], ['+', '/'], trim($s)));
}

function ton_balance(string $owner): string {
  if ($owner === '') return '0';
  $j = http_json('https://toncenter.com/api/v2/getAddressBalance?address=' . urlencode($owner));
  return isset($j['result']) ? dec_units((string)$j['result'], 9) : '0';
}

function jetton_balance(string $owner, string $master, int $dec, string $symbol): array {
  if ($owner === '' || $master === '') return ['units'=>'0','display'=>'0','source'=>'empty'];

  $debug = [];

  $urls = [
    'https://tonapi.io/v2/accounts/' . urlencode($owner) . '/jettons/' . urlencode($master),
    'https://tonapi.io/v2/accounts/' . urlencode($owner) . '/jettons',
    'https://toncenter.com/api/v3/jetton/wallets?owner_address=' . urlencode($owner) . '&jetton_address=' . urlencode($master),
    'https://toncenter.com/api/v3/jetton/wallets?owner_address=' . urlencode($owner),
  ];

  foreach ($urls as $url) {
    $j = http_json($url);
    $debug[] = ['url'=>$url, 'http'=>$j['_http'] ?? null];

    if (isset($j['balance']) && is_scalar($j['balance'])) {
      $units = (string)$j['balance'];
      if ($units !== '0') return ['units'=>$units,'display'=>dec_units($units,$dec),'source'=>'tonapi_direct'];
    }

    foreach (($j['balances'] ?? []) as $b) {
      $jetton = $b['jetton'] ?? [];
      $addr = (string)($jetton['address'] ?? '');
      $sym = (string)($jetton['symbol'] ?? '');
      if (($addr && norm($addr) === norm($master)) || strcasecmp($sym, $symbol) === 0) {
        $units = (string)($b['balance'] ?? '0');
        return ['units'=>$units,'display'=>dec_units($units,$dec),'source'=>'tonapi_list'];
      }
    }

    foreach (($j['jetton_wallets'] ?? $j['wallets'] ?? []) as $w) {
      $candidate = (string)($w['jetton'] ?? $w['jetton_address'] ?? $w['jetton_master'] ?? $w['master'] ?? '');
      if ($candidate === '' || norm($candidate) === norm($master)) {
        $units = (string)($w['balance'] ?? '0');
        if ($units !== '0') return ['units'=>$units,'display'=>dec_units($units,$dec),'source'=>'toncenter_v3'];
      }
    }
  }

  return ['units'=>'0','display'=>'0','source'=>'not_found','debug'=>$debug];
}

$wallet = trim($_GET['address'] ?? $_GET['wallet'] ?? '');

$vusdtMaster  = envv('VUSDT_JETTON_MASTER', 'EQD5pA15iVimLBmD-MEnfn8tGJ6RnCClUB-qdDZtRmowse0W');
$vshareMaster = envv('VSHARE_JETTON_MASTER', 'EQBuZ2-qxsK7cMr6wmhRHhPtdt1qYiZ09JrL1RR5AQ5Gscee');
$usdtMaster   = envv('USDT_TON_JETTON_MASTER', envv('USDT_TON_MASTER', ''));

$vusdt  = jetton_balance($wallet, $vusdtMaster, 9, 'vUSDT');
$vshare = jetton_balance($wallet, $vshareMaster, 9, 'vSHARE');
$usdt   = jetton_balance($wallet, $usdtMaster, 6, 'USD₮');
$ton    = ton_balance($wallet);

out([
  'ok'=>true,
  'source'=>'tonapi_direct+tonapi_list+toncenter_v3',
  'wallet'=>$wallet,
  'masters'=>[
    'vUSDT'=>$vusdtMaster,
    'vSHARE'=>$vshareMaster,
    'USDT_TON'=>$usdtMaster,
  ],
  'tokens'=>[
    'vSHARE'=>['offchain'=>0,'onchain'=>$vshare['display'],'units'=>$vshare['units'],'master'=>$vshareMaster,'source'=>$vshare['source']],
    'vUSDT'=>['offchain'=>0,'onchain'=>$vusdt['display'],'units'=>$vusdt['units'],'master'=>$vusdtMaster,'source'=>$vusdt['source']],
    'USDT_TON'=>['balance'=>$usdt['display'],'units'=>$usdt['units'],'master'=>$usdtMaster,'source'=>$usdt['source']],
    'TON'=>['balance'=>$ton],
  ],
  'balances'=>[
    'vshare_onchain'=>$vshare['display'],
    'vusdt_onchain'=>$vusdt['display'],
    'usdt_ton'=>$usdt['display'],
    'native_ton'=>$ton,
  ],
]);

<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
