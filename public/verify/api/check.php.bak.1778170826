<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=UTF-8');

function out(array $a): void {
  echo json_encode($a, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  exit;
}

$code = strtoupper(trim($_GET['c'] ?? $_POST['c'] ?? ''));
$code = preg_replace('/[^A-Z0-9\-]/', '', $code);

if ($code === '') {
  out(['ok'=>false, 'error'=>'missing_code']);
}

$db = new mysqli('localhost', 'root', '', 'visa_db');
if ($db->connect_errno) {
  out(['ok'=>false, 'error'=>'db_failed']);
}
$db->set_charset('utf8mb4');

$stmt = $db->prepare("
  SELECT public_code,vendor_name,vendor_slug,mob_type,location_name,
         avg_rating,verified_visits,approved_reviews,cert_status,issued_at,meta_json
  FROM ev_vendor_quality_certs
  WHERE public_code = ?
  LIMIT 1
");
$stmt->bind_param('s', $code);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row) {
  out(['ok'=>false, 'error'=>'not_found']);
}

out([
  'ok'=>true,
  'cert'=>[
    'public_code'=>$row['public_code'],
    'vendor_name'=>$row['vendor_name'],
    'vendor_slug'=>$row['vendor_slug'],
    'mob_type'=>$row['mob_type'],
    'location_name'=>$row['location_name'],
    'avg_rating'=>(float)$row['avg_rating'],
    'verified_visits'=>(int)$row['verified_visits'],
    'approved_reviews'=>(int)$row['approved_reviews'],
    'status'=>$row['cert_status'],
    'issued_at'=>$row['issued_at'],
    'meta'=>$row['meta_json'] ? json_decode($row['meta_json'], true) : null
  ]
]);

