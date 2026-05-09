<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=UTF-8');

try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=visa_ops_db;charset=utf8mb4',
        'oneexpressvisa',
        '$Express4653',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $wallet = trim($_GET['wallet_key'] ?? $_GET['whatsapp'] ?? '');
    if ($wallet === '') {
        echo json_encode(['ok'=>true,'balance'=>'0.00']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT balance_vusdt FROM visa_vusdt_balances WHERE wallet_key=? OR whatsapp=? LIMIT 1");
    $stmt->execute([$wallet,$wallet]);
    $bal = $stmt->fetchColumn();

    echo json_encode([
        'ok'=>true,
        'balance'=>number_format((float)($bal ?: 0), 2, '.', '')
    ]);
} catch (Throwable $e) {
    echo json_encode(['ok'=>false,'balance'=>'0.00']);
}


<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
