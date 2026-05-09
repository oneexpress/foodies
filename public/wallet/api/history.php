<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

$wallet = trim($_GET['wallet'] ?? '');
if ($wallet === '') {
  echo json_encode(['ok'=>false,'error'=>'missing_wallet']);
  exit;
}

$url = 'https://tonapi.io/v2/accounts/'.rawurlencode($wallet).'/events?limit=20';
$ch = curl_init($url);
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_TIMEOUT => 12,
  CURLOPT_HTTPHEADER => ['Accept: application/json'],
]);
$raw = curl_exec($ch);
$err = curl_error($ch);
$code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($raw === false || $code >= 400) {
  echo json_encode(['ok'=>false,'source'=>'tonapi_events','http'=>$code,'error'=>$err ?: 'fetch_failed']);
  exit;
}

$j = json_decode($raw, true);
$events = [];
foreach (($j['events'] ?? []) as $e) {
  $events[] = [
    'event_id' => $e['event_id'] ?? '',
    'timestamp' => $e['timestamp'] ?? null,
    'lt' => $e['lt'] ?? '',
    'actions' => $e['actions'] ?? [],
  ];
}
echo json_encode(['ok'=>true,'source'=>'tonapi_events','wallet'=>$wallet,'events'=>$events], JSON_UNESCAPED_SLASHES);

<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
