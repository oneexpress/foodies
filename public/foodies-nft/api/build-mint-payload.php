<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function out(array $a,int $c=200):void{
 http_response_code($c);
 echo json_encode($a,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
 exit;
}

$root='/var/www/html/visa';

$uid=preg_replace('/[^A-Z0-9_-]/','',(string)($_GET['uid']??'FOODIES-RWA-PENDING'));
$uid=$uid?:'FOODIES-RWA-PENDING';
$uid=substr($uid,0,80);

$stars=preg_replace('/[^0-9]/','',(string)($_GET['stars']??'1'));
$stars=in_array($stars,['1','3','5'],true)?$stars:'1';

$collection='UQBP2ofSKqVWs_LlSactlylcpeuuxz-sKXYyWNttLz50j6Dh';

$rawBase='https://raw.githubusercontent.com/oneexpress/foodies/main/public/metadata/foodies';

$verify='https://expressvisa.one/foodies-nft/verify.php?uid='.rawurlencode($uid);

$image="$rawBase/generated/$uid-{$stars}star.png";

$meta="$rawBase/items/$uid.json";

@mkdir("$root/public/metadata/foodies/items",0775,true);
@mkdir("$root/public/metadata/foodies/generated",0775,true);

file_put_contents(
 "$root/public/metadata/foodies/items/$uid.json",
 json_encode([
  'name'=>"@foodies RWA Food Reputation Card #$uid",
  'description'=>'Foodies RWA NFT',
  'image'=>$image,
  'external_url'=>$verify,
  'attributes'=>[
   ['trait_type'=>'Stars','value'=>(int)$stars],
   ['trait_type'=>'Responsibility','value'=>'Green & Clean Food']
  ]
 ],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)
);

$cmd='node '
 .escapeshellarg($root.'/foodies-blueprint/scripts/buildFoodiesMintPayload.cjs')
 .' uid='.escapeshellarg($uid)
 .' stars='.escapeshellarg($stars)
 .' rawBase='.escapeshellarg($rawBase)
 .' 2>&1';

$raw=shell_exec($cmd);

$j=json_decode((string)$raw,true);

if(!$j || empty($j['ok'])){
 out([
  'ok'=>false,
  'error'=>'payload_build_failed',
  'raw'=>$raw
 ],500);
}

if(str_starts_with((string)$j['messages'][0]['address'],'0:')){
 out([
  'ok'=>false,
  'error'=>'raw_address_detected'
 ],500);
}

out($j);
