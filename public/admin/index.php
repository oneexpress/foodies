<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/visa-vusdt.php';

$pdo = visa_pdo();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id = (int)($_POST['id'] ?? 0);
        $action = $_POST['action'] ?? 'update';

        $s = $pdo->prepare("SELECT * FROM visa_bookings WHERE id=? LIMIT 1");
        $s->execute([$id]);
        $b = $s->fetch();
        if (!$b) throw new RuntimeException('Booking not found.');

        if ($action === 'pay') {
            $walletKey = trim($_POST['wallet_key'] ?? '');
            $amount = trim($_POST['service_fee_vusdt'] ?? '');
            if ($walletKey === '') throw new RuntimeException('Wallet key required.');
            $payRef = vusdt_debit_booking_with_hook($walletKey, $b['booking_ref'], $amount);
            $msg = 'Booking paid. Payment Ref: '.$payRef;
        } else {
            $status = $_POST['status'] ?? $b['status'];
            $agent = trim($_POST['agent'] ?? $b['agent_name']);
            $message = trim($_POST['message'] ?? '');
            $fee = trim($_POST['service_fee_vusdt'] ?? '0');

            $pdo->prepare("UPDATE visa_bookings SET status=?, agent_name=?, message=?, service_fee_vusdt=? WHERE id=?")
                ->execute([$status,$agent,$message,number_format((float)$fee,6,'.',''),$id]);

            $pdo->prepare("INSERT INTO visa_status_history (booking_ref,status,message,changed_by)
                VALUES (?,?,?,'admin')")
                ->execute([$b['booking_ref'],$status,$message]);

            $msg = 'Updated.';
        }
    } catch (Throwable $e) {
        $msg = 'ERROR: '.$e->getMessage();
    }
}

$list = $pdo->query("SELECT * FROM visa_bookings ORDER BY id DESC LIMIT 100")->fetchAll();
$statuses=['submitted','under_review','document_required','agent_assigned','appointment_confirmed','processing','completed','rejected','cancelled'];
?>
<!doctype html><html><head><meta charset="utf-8"><title> Admin</title>
<style>
body{font-family:Arial;background:#111;color:#eee;padding:20px}table{width:100%;border-collapse:collapse;background:#181818}td,th{padding:8px;border:1px solid #333;vertical-align:top}input,select{background:#000;color:#fff;border:1px solid #555;padding:7px;border-radius:8px;max-width:160px}button,a.btn{background:#e60023;color:#fff;border:0;padding:8px 12px;border-radius:8px;text-decoration:none;display:inline-block;margin:2px}.pay{background:#22c55e!important}.small{font-size:12px;color:#bbb}
</style></head><body>
<h2> Booking Admin Panel</h2>
<?php if($msg): ?><p><?=h($msg)?></p><?php endif; ?>
<table>
<tr><th>ID</th><th>Ref</th><th>Name</th><th>Payment</th><th>Status</th><th>Agent</th><th>Message</th><th>Actions</th></tr>
<?php foreach($list as $r): ?>
<tr>
<form method="post">
<td><?=h($r['id'])?><input type="hidden" name="id" value="<?=h($r['id'])?>"></td>
<td><?=h($r['booking_ref'])?><br><span class="small"><?=h($r['service_type'])?></span></td>
<td><?=h($r['applicant_name'])?><br><span class="small"><?=h($r['whatsapp'])?></span></td>
<td>
<b><?=h($r['payment_status'] ?? 'unpaid')?></b><br>
Fee: <input name="service_fee_vusdt" value="<?=h($r['service_fee_vusdt'] ?? '0.000000')?>"><br>
Wallet: <input name="wallet_key" placeholder="wallet key"><br>
<?=h($r['payment_ref'] ?? '')?>
</td>
<td><select name="status"><?php foreach($statuses as $s): ?><option value="<?=h($s)?>" <?=$r['status']===$s?'selected':''?>><?=h(booking_status_label($s))?></option><?php endforeach; ?></select></td>
<td><input name="agent" value="<?=h($r['agent_name'])?>"></td>
<td><input name="message" value="<?=h($r['message'])?>"></td>
<td>
<button name="action" value="update">Update</button>
<button class="pay" name="action" value="pay">Pay Booking</button>
<?php if(!empty($r['community_discussion_id'])): ?><a class="btn" target="_blank" href="/community/d/<?=h($r['community_discussion_id'])?>">Community</a><?php endif; ?>
</td>
</form>
</tr>
<?php endforeach; ?>
</table>
<script src="/assets/js/991-wallet-sync.js?v=991-unified"></script>
<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
</body></html>
