<?php
declare(strict_types=1);

function foodies_wallet_from_request(): string {
  $keys = ['owner_wallet','issuer_wallet','wallet','ton_wallet','wallet_address'];
  foreach ($keys as $k) {
    $v = trim((string)($_POST[$k] ?? $_GET[$k] ?? ''));
    if ($v !== '') return $v;
  }
  $raw = file_get_contents('php://input');
  if ($raw) {
    $j = json_decode($raw, true);
    if (is_array($j)) {
      foreach ($keys as $k) {
        $v = trim((string)($j[$k] ?? ''));
        if ($v !== '') return $v;
      }
    }
  }
  return '';
}

function foodies_collection_owner_wallet(): string {
  return 'UQCFC6_JIg7YcJDaybYZKNSgETbDb28q-6SArFXItaI5jsCb';
}
