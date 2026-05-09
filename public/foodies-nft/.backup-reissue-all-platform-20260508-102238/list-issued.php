<?php
declare(strict_types=1);


if (!defined('FOODIES_COLLECTION_OWNER')) define('FOODIES_COLLECTION_OWNER', 'UQCFC6_JIg7YcJDaybYZKNSgETbDb28q-6SArFXItaI5jsCb');
header('Content-Type: application/json; charset=utf-8');

function out(array $a): void {
  echo json_encode($a, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  exit;
}
function envv(string $k, string $d=''): string {
  static $c=null;
  if($c===null){
    $c=[];
    $f='/var/www/secure/.env';
    if(is_file($f)){
      foreach(file($f, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $l){
        $l=trim($l);
        if($l==='' || str_starts_with($l,'#') || !str_contains($l,'=')) continue;
        [$a,$b]=explode('=',$l,2);
        $c[trim($a)] = trim(trim($b), "\"'");
      }
    }
  }
  return $c[$k] ?? getenv($k) ?: $d;
}
function db(): PDO {
  $h=envv('VISA_DB_HOST', envv('DB_HOST','127.0.0.1'));
  if($h==='localhost') $h='127.0.0.1';
  return new PDO(
    "mysql:host={$h};dbname=".envv('VISA_DB_NAME','visa_db').";charset=utf8mb4",
    envv('VISA_DB_USER', envv('DB_USER','root')),
    envv('VISA_DB_PASS', envv('DB_PASS','')),
    [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]
  );
}

try {
  $wallet = trim((string)($_GET['wallet'] ?? ''));
  if($wallet === '') out(['ok'=>true, 'rows'=>[]]);

  $pdo = db();
  $st = $pdo->prepare("
    SELECT cert_uid, nft_uid, chef_name, brand_name, food_title, star_class, status, verify_url, created_at
    FROM foodies_rwa_certs
    WHERE owner_wallet = ?
      AND status IN ('paid','nft_pending','minted','payment_pending','pending')
    ORDER BY id DESC
    LIMIT 50
  ");
  $st->execute([$wallet]);
  out(['ok'=>true, 'rows'=>$st->fetchAll()]);
} catch(Throwable $e) {
  error_log('[foodies list-issued] '.$e->getMessage());
  out(['ok'=>false, 'error'=>'list_failed']);
}
