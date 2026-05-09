<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=UTF-8');

function out(array $a, int $code=200): void {
  http_response_code($code);
  echo json_encode($a, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
  exit;
}

$uid = strtoupper(trim((string)($_GET['uid'] ?? 'FOODIES-MINT-0001')));
$uid = preg_replace('/[^A-Za-z0-9._-]/', '-', $uid);
$star = (int)($_GET['star'] ?? $_GET['stars'] ?? 5);
if (!in_array($star, [1,3,5], true)) $star = 5;

$collection = '0:4fda87d22aa556b3f2e54a97295ca50ebaa6c73fac29a763258db6d2f3e748fa';
$owner = 'UQCFC6_JIg7YcJDaybYZKNSgETbDb28q-6SArFXItaI5jsCb';
$cdn = 'https://cdn.jsdelivr.net/gh/oneexpress/foodies@main/public';

$itemDir = '/var/www/html/visa/public/metadata/foodies/items';
$genDir = '/var/www/html/visa/public/metadata/foodies/generated';
@mkdir($itemDir, 0775, true);
@mkdir($genDir, 0775, true);

$artifact = $cdn . '/metadata/foodies/generated/' . $uid . '-' . $star . 'star.png';
$fallback = $cdn . '/metadata/foodies/foodies-' . $star . 'star.png';
$verify = 'https://expressvisa.one/foodies-nft/verify.php?uid=' . rawurlencode($uid);
$metaUrl = $cdn . '/metadata/foodies/items/' . rawurlencode($uid) . '.json';

$meta = [
  'name' => '@foodies RWA Food Reputation Card #' . $uid,
  'description' => 'Foodies RWA Food Reputation NFT with Verify QR, Green & Clean Food responsibility, and star-based reputation.',
  'image' => $artifact,
  'external_url' => $verify,
  'attributes' => [
    ['trait_type'=>'Star Rating','value'=>(string)$star],
    ['trait_type'=>'Green & Clean Food','value'=>'YES'],
    ['trait_type'=>'Verify URL','value'=>$verify],
    ['trait_type'=>'UID','value'=>$uid],
    ['trait_type'=>'Chain','value'=>'TON']
  ]
];

file_put_contents($itemDir . '/' . $uid . '.json', json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . "\n");

$indexFile = '/var/www/html/visa/foodies-blueprint/.foodies-next-index';
if (!is_file($indexFile)) file_put_contents($indexFile, '1');
$index = max(1, (int)trim((string)file_get_contents($indexFile)));
file_put_contents($indexFile, (string)($index + 1));

$cmd = 'node ' . escapeshellarg('/var/www/html/visa/foodies-blueprint/scripts/buildFoodiesMintPayload.cjs')
  . ' --uid=' . escapeshellarg($uid)
  . ' --star=' . escapeshellarg((string)$star)
  . ' --index=' . escapeshellarg((string)$index)
  . ' --collection=' . escapeshellarg($collection)
  . ' --owner=' . escapeshellarg($owner)
  . ' --cdn=' . escapeshellarg($cdn);

$raw = shell_exec($cmd . ' 2>&1');
$built = json_decode((string)$raw, true);

if (!is_array($built) || empty($built['payload'])) {
  out(['ok'=>false, 'error'=>'payload_builder_failed', 'raw'=>$raw], 500);
}

out([
  'ok' => true,
  'uid' => $uid,
  'star' => $star,
  'collection' => $collection,
  'owner' => $owner,
  'index' => $index,
  'metadata_url' => $metaUrl,
  'artifact_image' => $artifact,
  'verify_url' => $verify,
  'tonconnect' => [
    'validUntil' => time() + 600,
    'messages' => [[
      'address' => $collection,
      'amount' => '550000000',
      'payload' => $built['payload']
    ]]
  ],
  'debug' => [
    'payload_magic' => $built['payload_magic'] ?? '',
    'payload_len' => strlen((string)$built['payload']),
    'local_metadata' => $itemDir . '/' . $uid . '.json'
  ]
]);
