<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

$uid = trim((string)($_GET['uid'] ?? 'FOODIES-TEST-0001'));
$stars = (int)($_GET['stars'] ?? $_GET['star'] ?? 1);
if (!in_array($stars, [1,3,5], true)) $stars = 1;

$safe = preg_replace('/[^A-Za-z0-9._-]/', '-', $uid);
$image = "https://expressvisa.one/metadata/foodies/generated/{$safe}-{$stars}star.png";
$verify = "https://expressvisa.one/foodies-nft/verify.php?uid=" . rawurlencode($uid);
$nameMap = [1=>'@foodies 1-Star Food Reputation Card',3=>'@foodies 3-Star Food Reputation Card',5=>'@foodies 5-Star Food Reputation Card'];

$j = [
  'name' => $nameMap[$stars] . ' #' . $uid,
  'description' => 'Foodies RWA Food Reputation Card NFT. Verify URL QR is merged into the final NFT PNG artifact.',
  'image' => $image,
  'external_url' => $verify,
  'attributes' => [
    ['trait_type'=>'Star Rating','value'=>(string)$stars],
    ['trait_type'=>'Verify QR','value'=>'Merged into final PNG artifact'],
    ['trait_type'=>'Green & Clean Food','value'=>'Yes'],
    ['trait_type'=>'RWA Unit','value'=>'Food Reputation Responsibility'],
    ['trait_type'=>'Cert UID','value'=>$uid],
  ],
];

$outDir = '/var/www/html/visa/public/metadata/foodies/items';
@mkdir($outDir, 0775, true);
file_put_contents($outDir.'/'.$safe.'.json', json_encode($j, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
echo json_encode($j, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
