<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/visa-vusdt.php';

$pdo = visa_pdo();
$msg='';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';

    $s = $pdo->prepare("SELECT * FROM visa_vusdt_reloads WHERE id=? LIMIT 1");
    $s->execute([$id]);
    $r = $s->fetch();

    if ($r && $action === 'confirm' && $r['status'] !== 'confirmed') {
        vusdt_credit($r['wallet_key'], $r['reload_ref'], (string)$r['amount_vusdt']);
        $pdo->prepare("UPDATE visa_vusdt_reloads SET status='confirmed', verified_by='admin', confirmed_at=NOW(), message='vUSDT OffChain balance credited.' WHERE id=?")
            ->execute([$id]);
        $msg='Reload confirmed and balance credited.';
    } elseif ($r && $action === 'reject') {
        $pdo->prepare("UPDATE visa_vusdt_reloads SET status='rejected', verified_by='admin', message='Reload rejected by admin.' WHERE id=?")
            ->execute([$id]);
        $msg='Reload rejected.';
    }
}

$list = $pdo->query("SELECT * FROM visa_vusdt_reloads ORDER BY id DESC LIMIT 100")->fetchAll();
?>
<!doctype html><html><head><meta charset="utf-8"><title>vUSDT Admin</title>
<style>
body{font-family:Arial;background:#111;color:#eee;padding:20px}table{width:100%;border-collapse:collapse;background:#181818}td,th{padding:8px;border:1px solid #333}button{background:#e60023;color:#fff;border:0;padding:8px 12px;border-radius:8px}.ok{background:#22c55e}
</style></head><body>
<h2>vUSDT Reload Admin</h2>
<?php if($msg): ?><p><?=h($msg)?></p><?php endif; ?>
<table><tr><th>ID</th><th>Ref</th><th>Wallet</th><th>Amount</th><th>Status</th><th>TX</th><th>Action</th></tr>
<?php foreach($list as $r): ?>
<tr>
<td><?=h($r['id'])?></td><td><?=h($r['reload_ref'])?></td><td><?=h($r['wallet_key'])?></td>
<td><?=h($r['amount_vusdt'])?></td><td><?=h($r['status'])?></td><td><?=h($r['tx_hash'])?></td>
<td><form method="post"><input type="hidden" name="id" value="<?=h($r['id'])?>">
<button class="ok" name="action" value="confirm">Confirm</button>
<button name="action" value="reject">Reject</button></form></td>
</tr>
<?php endforeach; ?>
</table>
<script src="/assets/js/991-wallet-sync.js?v=991-unified"></script>
<script src="/assets/991-balance-sync.js?v=991-balance-sync-ux-v1" defer></script>
<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
</body></html>
