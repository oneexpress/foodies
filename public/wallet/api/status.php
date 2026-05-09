<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

$ton = $_GET['ton_wallet'] ?? ($_COOKIE['ev_ton_wallet'] ?? '');
$ton = trim($ton);

if (
  !preg_match('/^(UQ|EQ)[A-Za-z0-9_-]{40,}$/', $ton) &&
  !preg_match('/^-?[0-9]+:[a-fA-F0-9]{64}$/', $ton)
) {
  echo json_encode(["ok"=>false,"error"=>"TON_WALLET_REQUIRED","received"=>$ton], JSON_UNESCAPED_SLASHES);
  exit;
}

$pdo = new PDO("mysql:host=localhost;dbname=visa_db;charset=utf8mb4","oneexpressvisa",'$Express4653',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);

$stmt=$pdo->prepare("
SELECT COALESCE(SUM(CASE
  WHEN status='confirmed' AND direction IN ('reload','adjust') THEN amount
  WHEN status='confirmed' AND direction='debit' THEN -amount
  ELSE 0 END),0) AS balance
FROM ev_wallet_ledger
WHERE ton_wallet=?
");
$stmt->execute([$ton]);
$bal=$stmt->fetch()['balance'] ?? 0;

echo json_encode(["ok"=>true,"ton_wallet"=>$ton,"token"=>"vUSDT","balance"=>(float)$bal], JSON_UNESCAPED_SLASHES);


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
