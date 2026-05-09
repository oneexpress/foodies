<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

function out($a){
  echo json_encode($a, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  exit;
}

$wallet = trim($_GET['wallet'] ?? '');

if (!$wallet) {
  out(['ok'=>false,'error'=>'wallet_required']);
}

$env = parse_ini_file('/var/www/secure/.env');

$vusdt = $env['VUSDT_JETTON_MASTER'] ?? '';
$vshare = $env['VSHARE_JETTON_MASTER'] ?? '';

$tonApi = 'https://toncenter.com/api/v2/';

function getJson($u){
  $c = curl_init($u);
  curl_setopt_array($c,[
    CURLOPT_RETURNTRANSFER=>1,
    CURLOPT_TIMEOUT=>20,
    CURLOPT_SSL_VERIFYPEER=>false
  ]);
  $r = curl_exec($c);
  curl_close($c);
  return json_decode((string)$r,true);
}

$ton = getJson(
  $GLOBALS['tonApi'].'getAddressBalance?address='.urlencode($wallet)
);

function jettonBalance($master,$owner){
  $u='https://toncenter.com/api/v3/jetton/wallets?owner_address='
    .urlencode($owner)
    .'&jetton_address='
    .urlencode($master);

  $j=getJson($u);

  if(!empty($j['jetton_wallets'][0]['balance'])){
    return $j['jetton_wallets'][0]['balance'];
  }

  return '0';
}

out([
  'ok'=>true,
  'wallet'=>$wallet,
  'balances'=>[
    [
      'symbol'=>'TON',
      'balance'=>isset($ton['result'])
        ? number_format(((float)$ton['result'])/1000000000,6,'.','')
        : '0'
    ],
    [
      'symbol'=>'vUSDT',
      'master'=>$vusdt,
      'balance'=>jettonBalance($vusdt,$wallet)
    ],
    [
      'symbol'=>'vSHARE',
      'master'=>$vshare,
      'balance'=>jettonBalance($vshare,$wallet)
    ],
    [
      'symbol'=>'USDT-TON',
      'master'=>'EQCxE6mUtQJKFnAnY4H5o2Zq4T4Q0k4hJY6v7h6M4s6Qv6Q',
      'balance'=>'0'
    ]
  ]
]);

<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
