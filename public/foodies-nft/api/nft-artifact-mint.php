<?php
declare(strict_types=1);

$uid = trim((string)($_GET['uid'] ?? $_GET['cert_uid'] ?? 'FOODIES-MINT-0001'));
$stars = (int)($_GET['stars'] ?? $_GET['star'] ?? 1);
if (!in_array($stars, [1,3,5], true)) $stars = 1;

$doc = rtrim((string)($_SERVER['DOCUMENT_ROOT'] ?? '/var/www/html/visa/public'), '/');

$template = match($stars) {
  5 => '/metadata/foodies/NFT/5_stars_foodies.png',
  3 => '/metadata/foodies/NFT/3_stars_foodies.png',
  default => '/metadata/foodies/NFT/1_star_foodies.png',
};

$fallbackOld = match($stars) {
  5 => '/metadata/foodies/foodies-5star.png',
  3 => '/metadata/foodies/foodies-3star.png',
  default => '/metadata/foodies/foodies-1star.png',
};

$outDir = $doc . '/metadata/foodies/generated';
@mkdir($outDir, 0775, true);

$safe = preg_replace('/[^A-Za-z0-9._-]/', '-', $uid);
$out = $outDir . '/' . $safe . '-' . $stars . 'star.png';

$src = is_file($doc.$template) ? $doc.$template : $doc.$fallbackOld;

if (!is_file($out) && is_file($src)) {
  @copy($src, $out);
  @chmod($out, 0644);
}

$serve = is_file($out) ? $out : (is_file($src) ? $src : '');

if ($serve === '') {
  http_response_code(404);
  header('Content-Type: text/plain; charset=UTF-8');
  echo "NFT artifact image not found\n";
  echo "uid={$uid}\nstars={$stars}\n";
  echo "checked={$out}\n{$doc}{$template}\n{$doc}{$fallbackOld}\n";
  exit;
}

header('Content-Type: image/png');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Content-Length: ' . filesize($serve));
readfile($serve);
