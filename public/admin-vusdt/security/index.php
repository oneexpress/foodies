<?php
declare(strict_types=1);
require_once __DIR__ . '/../../inc/visa-vusdt.php';

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $wallet = trim($_POST['wallet_key'] ?? '');
        $movement = trim($_POST['movement'] ?? '');
        $amount = trim($_POST['amount_vusdt'] ?? '');
        $reason = trim($_POST['reason'] ?? '');
        $ref = vusdt_manual_adjust($wallet, $movement, $amount, $reason, 'admin');
        $msg = 'Adjustment completed: '.$ref;
    } catch (Throwable $e) {
        $msg = 'ERROR: '.$e->getMessage();
    }
}

$logs = visa_pdo()->query("SELECT * FROM visa_vusdt_security_log ORDER BY id DESC LIMIT 100")->fetchAll();
$adjustments = visa_pdo()->query("SELECT * FROM visa_vusdt_adjustments ORDER BY id DESC LIMIT 50")->fetchAll();
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>vUSDT Security</title>
<style>
body{font-family:Arial;background:#101114;color:#eee;margin:0}.wrap{max-width:1200px;margin:auto;padding:24px}.hero{background:linear-gradient(135deg,#e60023,#ff6a00);border-radius:28px;padding:28px}.card{background:#181a20;border:1px solid #2a2d35;border-radius:22px;padding:20px;margin-top:18px}input,select{background:#000;color:#fff;border:1px solid #444;border-radius:10px;padding:10px;margin:5px}button{background:#e60023;color:#fff;border:0;border-radius:10px;padding:10px 15px}table{width:100%;border-collapse:collapse}td,th{border-bottom:1px solid #2a2d35;padding:8px;font-size:13px}.warn{color:#f59e0b}.critical{color:#ef4444}.info{color:#22c55e}
</style></head><body><div class="wrap">
<div class="hero"><h1>vUSDT Payment Security</h1><p>No tx_hash double credit · No silent balance change · Full audit log.</p></div>

<div class="card">
<h2>Manual Adjustment</h2>
<?php if($msg): ?><p><?=h($msg)?></p><?php endif; ?>
<form method="post">
<input name="wallet_key" placeholder="Wallet Key" required>
<select name="movement"><option value="credit">Credit</option><option value="debit">Debit</option></select>
<input name="amount_vusdt" type="number" step="0.000001" placeholder="Amount" required>
<input name="reason" placeholder="Reason" required style="min-width:320px">
<button>Apply Adjustment</button>
</form>
</div>

<div class="card">
<h2>Recent Security Logs</h2>
<table><tr><th>Time</th><th>Severity</th><th>Event</th><th>Ref</th><th>Wallet</th><th>TX</th><th>Message</th></tr>
<?php foreach($logs as $r): ?>
<tr>
<td><?=h($r['created_at'])?></td>
<td class="<?=h($r['severity'])?>"><?=h($r['severity'])?></td>
<td><?=h($r['event_type'])?></td>
<td><?=h($r['ref_no'])?></td>
<td><?=h($r['wallet_key'])?></td>
<td><?=h($r['tx_hash'])?></td>
<td><?=h($r['message'])?></td>
</tr>
<?php endforeach; ?>
</table>
</div>

<div class="card">
<h2>Manual Adjustments</h2>
<table><tr><th>Time</th><th>Ref</th><th>Wallet</th><th>Type</th><th>Amount</th><th>Before</th><th>After</th><th>Reason</th></tr>
<?php foreach($adjustments as $r): ?>
<tr>
<td><?=h($r['created_at'])?></td>
<td><?=h($r['adjustment_ref'])?></td>
<td><?=h($r['wallet_key'])?></td>
<td><?=h($r['movement'])?></td>
<td><?=h($r['amount_vusdt'])?></td>
<td><?=h($r['balance_before'])?></td>
<td><?=h($r['balance_after'])?></td>
<td><?=h($r['reason'])?></td>
</tr>
<?php endforeach; ?>
</table>
</div>
<script src="/assets/js/991-wallet-sync.js?v=991-unified"></script>
<script src="/assets/991-balance-sync.js?v=991-balance-sync-ux-v1" defer></script>
<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
</body></html>
