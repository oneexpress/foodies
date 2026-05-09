<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

function out(array $a, int $c=200): void {
  http_response_code($c);
  echo json_encode($a, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
  exit;
}

$uid = trim((string)($_GET['uid'] ?? $_POST['uid'] ?? ''));
$to = trim((string)($_GET['to'] ?? $_POST['to'] ?? ''));
$star = (int)($_GET['star'] ?? $_POST['star'] ?? 1);

if ($uid === '') out(['ok'=>false,'error'=>'uid_required'],400);
if ($to === '') out(['ok'=>false,'error'=>'recipient_required'],400);
if (!in_array($star,[1,3,5],true)) out(['ok'=>false,'error'=>'invalid_star'],400);

putenv('FOODIES_COLLECTION_ADDRESS=EQBP2ofSKqVWs_LlSpcpXKUOuqbHP6wpp2MljbbS8-dI-sbk');
putenv('FOODIES_PUBLIC_MINT_ATTACH_TON=0.50');
putenv('FOODIES_ITEM_METADATA_BASE=https://expressvisa.one/metadata/foodies/items');
putenv('FOODIES_VERIFY_BASE=https://expressvisa.one/foodies-nft/verify.php?uid=');

$cmd = 'cd /var/www/html/visa/foodies-blueprint && HUSKY=0 CI=1 npx ts-node scripts/buildFoodiesMintPayload.ts'
  . ' --uid=' . escapeshellarg($uid)
  . ' --to=' . escapeshellarg($to)
  . ' --star=' . escapeshellarg((string)$star)
  . ' 2>&1';

$raw = shell_exec($cmd);
$j = json_decode((string)$raw, true);
if (!is_array($j)) out(['ok'=>false,'error'=>'invalid_builder_json','raw'=>$raw],500);
out($j, !empty($j['ok']) ? 200 : 500);
