<?php
declare(strict_types=1);
$pdo = new PDO("mysql:host=localhost;dbname=visa_db;charset=utf8mb4","oneexpressvisa",'$Express4653',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['confirm_id'])) {
  $id=(int)$_POST['confirm_id'];
  $tx=trim($_POST['tx_hash'] ?? '');
  $pdo->prepare("UPDATE ev_wallet_ledger SET status='confirmed', tx_hash=?, confirmed_at=NOW() WHERE id=?")->execute([$tx,$id]);
  header("Location: /admin/wallet/");
  exit;
}

$rows=$pdo->query("SELECT * FROM ev_wallet_ledger ORDER BY id DESC LIMIT 100")->fetchAll();
?>
<!doctype html><html><head><meta charset="utf-8"><title>TON Wallet Admin</title>
<style>
body{background:#111;color:#fff;font-family:Arial;padding:24px}
table{width:100%;border-collapse:collapse}
td,th{padding:10px;border-bottom:1px solid #333;vertical-align:top}
input{background:#050505;color:#fff;border:1px solid #444;border-radius:8px;padding:8px}
.btn{background:#e60012;color:white;padding:8px 12px;border-radius:8px;border:0;font-weight:800}
.badge{padding:4px 8px;border-radius:999px;background:#333}.pending{background:#7a4b00}.confirmed{background:#0b6b45}
.addr{word-break:break-all;font-family:monospace;color:#ffd7dc}
</style></head><body>
<h1>TON vUSDT Wallet Admin</h1>
<table>
<tr><th>ID</th><th>User</th><th>TON Wallet</th><th>Ref</th><th>Amount</th><th>Status</th><th>Action</th></tr>
<?php foreach($rows as $r): ?>
<tr>
<td><?=$r['id']?></td>
<td><?=htmlspecialchars($r['user_key'])?></td>
<td class="addr"><?=htmlspecialchars($r['ton_wallet'] ?? '')?></td>
<td><?=htmlspecialchars($r['wallet_ref'])?><br><small><?=htmlspecialchars($r['payment_network'] ?? '')?></small></td>
<td><?=number_format((float)$r['amount'],2)?> <?=htmlspecialchars($r['token'])?></td>
<td><span class="badge <?=htmlspecialchars($r['status'])?>"><?=htmlspecialchars($r['status'])?></span><br><?=htmlspecialchars($r['tx_hash'] ?? '')?></td>
<td>
<?php if($r['status']==='pending'): ?>
<form method="post">
  <input type="hidden" name="confirm_id" value="<?=$r['id']?>">
  <input name="tx_hash" placeholder="tx hash optional">
  <button class="btn">Confirm</button>
</form>
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</table>
</body></html>
