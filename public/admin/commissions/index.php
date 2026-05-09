<?php
declare(strict_types=1);
require_once __DIR__ . '/../../inc/visa-vusdt.php';
$pdo = visa_pdo();
$msg='';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $id=(int)($_POST['id']??0);
    $note=trim($_POST['payout_note']??'');
    $pdo->prepare("UPDATE visa_agent_commissions SET status='paid', payout_note=?, paid_at=NOW() WHERE id=?")->execute([$note,$id]);
    $msg='Commission marked as paid.';
}

$list=$pdo->query("
SELECT c.*, a.name agent_name, a.whatsapp agent_whatsapp
FROM visa_agent_commissions c
LEFT JOIN visa_agents a ON a.id=c.agent_id
ORDER BY c.id DESC
LIMIT 300
")->fetchAll();
?>
<!doctype html><html><head><meta charset="utf-8"><title>Commissions</title>
<style>
body{font-family:Arial;background:#101114;color:#eee;padding:20px}table{width:100%;border-collapse:collapse;background:#181a20}td,th{padding:8px;border:1px solid #2a2d35}button,a.btn{background:#22c55e;color:#000;border:0;padding:6px 10px;border-radius:8px;text-decoration:none}input{background:#000;color:#fff;border:1px solid #444;padding:6px;border-radius:8px}
</style></head><body>
<h2>Agent Commissions</h2>
<?php if($msg):?><p><?=h($msg)?></p><?php endif;?>
<table>
<tr><th>ID</th><th>Booking</th><th>Agent</th><th>Amount</th><th>Rate%</th><th>Commission</th><th>Status</th><th>WhatsApp</th><th>Payout</th></tr>
<?php foreach($list as $r):
$text='ExpressVisa commission update. Booking '.$r['booking_ref'].' earned '.$r['commission_vusdt'].' vUSDT. Status: '.$r['status'];
?>
<tr>
<td><?=h($r['id'])?></td>
<td><?=h($r['booking_ref'])?></td>
<td><?=h($r['agent_name'])?></td>
<td><?=h($r['amount_vusdt'])?></td>
<td><?=h($r['commission_rate'])?></td>
<td><?=h($r['commission_vusdt'])?></td>
<td><?=h($r['status'])?></td>
<td><a class="btn" target="_blank" href="<?=h(agent_whatsapp_link((string)$r['agent_whatsapp'], $text))?>">Notify</a></td>
<td>
<?php if($r['status']!=='paid'): ?>
<form method="post">
<input type="hidden" name="id" value="<?=h($r['id'])?>">
<input name="payout_note" placeholder="Payout note">
<button>Mark Paid</button>
</form>
<?php else: ?>
Paid <?=h($r['paid_at'])?>
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</table>
<script src="/assets/-bottom-bar.js?v=991-logo-final" defer></script>
<script src="/assets/js/991-wallet-sync.js?v=991-unified"></script>
</body></html>
