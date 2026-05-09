<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/visa-vusdt.php';

$pdo = visa_pdo();
$ref = strtoupper(trim($_GET['ref'] ?? $_POST['ref'] ?? ''));
$msg = ''; $err = '';
$b = null;

if ($ref !== '') {
    $s = $pdo->prepare("SELECT * FROM visa_bookings WHERE booking_ref=? LIMIT 1");
    $s->execute([$ref]);
    $b = $s->fetch();
    if (!$b) $err = 'Invalid booking reference.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $b) {
    try {
        if (($b['payment_status'] ?? 'unpaid') === 'paid') {
            throw new RuntimeException('Already paid.');
        }

        $method = $_POST['method'] ?? '';

        if ($method === 'wallet') {
            $wallet = trim($_POST['wallet_key'] ?? '');
            if ($wallet === '') throw new RuntimeException('Wallet required.');
            $payRef = vusdt_debit_booking_with_hook($wallet, $ref, $b['service_fee_vusdt']);
            $msg = "Payment success. Ref: $payRef";
        }

        if ($method === 'ton') {
            $deeplink = vusdt_ton_deeplink($ref, $b['service_fee_vusdt']);
            $msg = "Send vUSDT via TON wallet using link below.";
        }

    } catch (Throwable $e) {
        $err = $e->getMessage();
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Pay Booking</title>
<style>
body{font-family:Arial;background:#f7f7f8;margin:0}
.wrap{max-width:800px;margin:auto;padding:24px}
.card{background:#fff;border-radius:20px;padding:20px;box-shadow:0 10px 30px #0001}
.btn{display:inline-block;padding:12px 18px;background:#e60023;color:#fff;border-radius:999px;border:0;text-decoration:none}
.ok{color:#0a7a2b}.err{color:#b00020}
</style>
</head>
<body>
<div class="wrap">
<div class="card">
<h2>Pay Booking</h2>

<?php if($err): ?><p class="err"><?=h($err)?></p><?php endif; ?>
<?php if($msg): ?><p class="ok"><?=h($msg)?></p><?php endif; ?>

<form method="get">
<input name="ref" value="<?=h($ref)?>" placeholder="Booking Ref">
<button class="btn">Load</button>
</form>

<?php if($b): ?>
<hr>
<p><b>Reference:</b> <?=h($b['booking_ref'])?></p>
<p><b>Service:</b> <?=h($b['service_type'])?></p>
<p><b>Fee:</b> <?=h($b['service_fee_vusdt'])?> vUSDT</p>
<p><b>Status:</b> <?=h($b['payment_status'])?></p>

<?php if(($b['payment_status'] ?? '') !== 'paid'): ?>

<h3>Pay via Wallet</h3>
<form method="post">
<input type="hidden" name="ref" value="<?=h($ref)?>">
<input type="hidden" name="method" value="wallet">
<input name="wallet_key" placeholder="Wallet Key">
<button class="btn">Pay with Balance</button>
</form>

<h3>Pay via TON</h3>
<?php $link = vusdt_ton_deeplink($ref, $b['service_fee_vusdt']); ?>
<p><a class="btn" href="<?=h($link)?>">Open TON Wallet</a></p>

<?php else: ?>
<p class="ok">Payment completed.</p>
<?php endif; ?>

<?php endif; ?>

</div>
</div>
<script src="/assets/js/991-wallet-sync.js?v=991-unified"></script>
<script src="/assets/991-balance-sync.js?v=991-balance-sync-ux-v1" defer></script>
<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
</body>
</html>
