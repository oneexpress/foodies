<?php
declare(strict_types=1);
require_once __DIR__ . '/../../inc/visa-vusdt.php';
$pdo = visa_pdo();
$msg='';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $action=$_POST['action']??'add';
    if ($action==='add') {
        $name=trim($_POST['name']??'');
        $wa=trim($_POST['whatsapp']??'');
        $rate=number_format((float)($_POST['commission_rate']??20),2,'.','');
        $svc=trim($_POST['service_type']??'');
        $nat=trim($_POST['nationality']??'');
        if ($name!=='') {
            $pdo->prepare("INSERT INTO visa_agents (name,whatsapp,commission_rate,service_type,nationality,active) VALUES (?,?,?,?,?,1)")
                ->execute([$name,$wa,$rate,$svc,$nat]);
            $msg='Agent added.';
        }
    }
    if ($action==='toggle') {
        $id=(int)($_POST['id']??0);
        $pdo->prepare("UPDATE visa_agents SET active=IF(active=1,0,1) WHERE id=?")->execute([$id]);
        $msg='Agent status updated.';
    }
}

foreach ($pdo->query("SELECT id FROM visa_agents")->fetchAll() as $a) {
    agent_sync_totals((int)$a['id']);
}

$list=$pdo->query("SELECT * FROM visa_agents ORDER BY active DESC, total_commission_vusdt DESC, id DESC")->fetchAll();
?>
<!doctype html><html><head><meta charset="utf-8"><title>Agents</title>
<style>
body{font-family:Arial;background:#101114;color:#eee;padding:20px}table{width:100%;border-collapse:collapse;background:#181a20}td,th{padding:8px;border:1px solid #2a2d35}input{background:#000;color:#fff;border:1px solid #444;padding:6px;border-radius:8px}button,a.btn{background:#e60023;color:#fff;border:0;padding:8px 12px;border-radius:8px;text-decoration:none}.wa{background:#22c55e!important;color:#000!important}
</style></head><body>
<h2>Agents</h2>
<?php if($msg):?><p><?=h($msg)?></p><?php endif;?>
<form method="post">
<input type="hidden" name="action" value="add">
<input name="name" placeholder="Name" required>
<input name="whatsapp" placeholder="WhatsApp">
<input name="commission_rate" placeholder="Rate %" value="20">
<input name="service_type" placeholder="Service Type optional">
<input name="nationality" placeholder="Nationality optional">
<button>Add Agent</button>
</form>
<table>
<tr><th>ID</th><th>Name</th><th>WhatsApp</th><th>Rate%</th><th>Service</th><th>Nationality</th><th>Assigned</th><th>Commission</th><th>Active</th><th>Action</th></tr>
<?php foreach($list as $r): ?>
<tr>
<td><?=h($r['id'])?></td>
<td><?=h($r['name'])?></td>
<td>
<?=h($r['whatsapp'])?><br>
<a class="btn wa" target="_blank" href="<?=h(agent_whatsapp_link((string)$r['whatsapp'], 'ExpressVisa Agent Portal: You are registered as a Verified Agent.'))?>">WhatsApp</a>
</td>
<td><?=h($r['commission_rate'])?></td>
<td><?=h($r['service_type'])?></td>
<td><?=h($r['nationality'])?></td>
<td><?=h($r['total_assigned'])?></td>
<td><?=h($r['total_commission_vusdt'])?> vUSDT</td>
<td><?=h($r['active'])?></td>
<td><form method="post"><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?=h($r['id'])?>"><button>Toggle</button></form></td>
</tr>
<?php endforeach; ?>
</table>
<script src="/assets/js/991-wallet-sync.js?v=991-unified"></script>
<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
</body></html>
