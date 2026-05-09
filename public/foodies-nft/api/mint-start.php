<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=UTF-8');

$uid = trim((string)($_POST['uid'] ?? $_GET['uid'] ?? ''));
$stars = (int)($_POST['stars'] ?? $_GET['stars'] ?? 1);
$wallet = trim((string)($_POST['wallet'] ?? $_GET['wallet'] ?? ''));

if ($uid === '') {
  echo json_encode(['ok'=>false,'error'=>'missing_cert_uid']);
  exit;
}
if (!in_array($stars, [1,3,5], true)) $stars = 1;
if ($wallet === '') {
  echo json_encode(['ok'=>false,'error'=>'wallet_required']);
  exit;
}

$meta = 'https://expressvisa.one/foodies-nft/api/nft-metadata.php?uid=' . rawurlencode($uid) . '&stars=' . $stars;
$artifact = 'https://expressvisa.one/foodies-nft/api/nft-artifact-mint.php?uid=' . rawurlencode($uid) . '&stars=' . $stars;
$verify = 'https://expressvisa.one/foodies-nft/verify.php?uid=' . rawurlencode($uid);

echo json_encode([
  'ok'=>true,
  'stage'=>'ready_to_submit_ton_mint',
  'cert_uid'=>$uid,
  'stars'=>$stars,
  'wallet'=>$wallet,
  'collection_owner'=>'UQCFC6_JIg7YcJDaybYZKNSgETbDb28q-6SArFXItaI5jsCb',
  'metadata_url'=>$meta,
  'artifact_url'=>$artifact,
  'verify_url'=>$verify,
  'ton_payload'=>[
    'type'=>'foodies_nft_collection_mint',
    'chain'=>'TON',
    'metadata_url'=>$meta,
    'owner_wallet'=>$wallet,
    'forward_amount_ton'=>'0.05',
    'treasury_contribution_ton'=>'0.50',
    'treasury_wallet'=>'UQBdYfGArtoCUmBs5TjYQtfPFfQuGC2Ydbj2pQr3zIlNrDta',
    'bit_overload_guard'=>'metadata_url_only_no_large_json_in_cell',
    'status'=>'payload_ready_next_wire_to_collection_contract',
    'withdraw_helper'=>'https://expressvisa.one/foodies-nft/api/ton-withdraw.php?action=mint_treasury_contribution&uid=' . rawurlencode($uid) . '&stars=' . $stars
  ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
