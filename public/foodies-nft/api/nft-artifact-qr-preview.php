<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=UTF-8');

require_once dirname(__DIR__) . '/inc/nft-qr-position.php';

$uid = trim((string)($_GET['uid'] ?? $_GET['cert_uid'] ?? 'FOODIES-RWA-PENDING'));
$stars = (int)($_GET['stars'] ?? 1);
if (!in_array($stars, [1,3,5], true)) $stars = 1;

$pos = foodies_nft_qr_position($stars);
$verifyUrl = foodies_nft_verify_url($uid);

echo json_encode([
    'ok' => true,
    'cert_uid' => $uid,
    'stars' => $stars,
    'qr_target' => 'verify_url',
    'verify_url' => $verifyUrl,
    'qr_position' => $pos,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
