<?php
declare(strict_types=1);

$pdo=new PDO('mysql:host=localhost;dbname=visa_db;charset=utf8mb4','oneexpressvisa','$Express4653',[
  PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
]);

function h($s){return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
function refno(){return 'BOOST-'.date('YmdHis').'-'.strtoupper(substr(bin2hex(random_bytes(3)),0,6));}

if($_SERVER['REQUEST_METHOD']==='POST'){
  $action=$_POST['action'] ?? '';
  $postRef=trim($_POST['post_ref'] ?? '');
  $amount=(float)($_POST['amount_vusdt'] ?? 10);

  if($action==='create' && $postRef){
    $boostRef=refno();
    $pdo->prepare("INSERT INTO visa_post_boost_orders (boost_ref,post_ref,amount_vusdt,status,message) VALUES (?,?,?,'pending_payment','Waiting for vUSDT payment')")
        ->execute([$boostRef,$postRef,$amount]);

    $pdo->prepare("UPDATE visa_free_posts SET boost_status='pending_payment', boost_ref=?, boost_amount_vusdt=? WHERE post_ref=?")
        ->execute([$boostRef,$amount,$postRef]);
  }

  if($action==='manual_confirm'){
    $boostRef=trim($_POST['boost_ref'] ?? '');
    $tx=trim($_POST['tx_hash'] ?? '');
    if($boostRef){
      $pdo->prepare("UPDATE visa_post_boost_orders SET status='confirmed', tx_hash=?, paid_at=NOW(), message='Manually confirmed by admin' WHERE boost_ref=?")
          ->execute([$tx,$boostRef]);

      $s=$pdo->prepare("SELECT * FROM visa_post_boost_orders WHERE boost_ref=? LIMIT 1");
      $s->execute([$boostRef]);
      $o=$s->fetch();

      if($o){
        $pdo->prepare("UPDATE visa_free_posts SET boost_status='boosted', boost_paid_at=NOW(), boosted_until=DATE_ADD(NOW(), INTERVAL 7 DAY), sync_message=CONCAT(COALESCE(sync_message,''),' | Boost active') WHERE post_ref=?")
            ->execute([$o['post_ref']]);
      }
    }
  }

  header('Location: /admin/boost/');
  exit;
}

$posts=$pdo->query("SELECT post_ref,title,price,sync_status,boost_status,boost_ref,boosted_until FROM visa_free_posts ORDER BY id DESC LIMIT 200")->fetchAll();
$orders=$pdo->query("SELECT * FROM visa_post_boost_orders ORDER BY id DESC LIMIT 100")->fetchAll();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title> Boost Admin</title>
<style>
body{font-family:Arial;background:#f8fafc;padding:20px}
.card{background:#fff;border-radius:18px;padding:18px;margin-bottom:18px;box-shadow:0 12px 35px rgba(0,0,0,.08)}
input,select{height:42px;border:1px solid #ddd;border-radius:10px;padding:0 10px}
button{height:42px;border:0;border-radius:10px;background:#e60023;color:#fff;font-weight:900;padding:0 14px}
table{width:100%;border-collapse:collapse}
td,th{padding:10px;border-bottom:1px solid #eee;text-align:left}
.badge{padding:4px 8px;border-radius:999px;background:#eee;font-size:12px;font-weight:900}
.boosted{background:#dcfce7;color:#166534}.pending_payment{background:#fef3c7;color:#92400e}.none{background:#e5e7eb}
</style>
</head>
<body>
<div class="card">
<h1> vUSDT Boost Admin</h1>
<p>Create boost order → verify payment → auto mark post as boosted for 7 days.</p>
</div>

<div class="card">
<h2>Create Boost Order</h2>
<form method="post">
<input type="hidden" name="action" value="create">
<select name="post_ref" required>
<?php foreach($posts as $p): ?>
<option value="<?=h($p['post_ref'])?>"><?=h($p['post_ref'].' — '.$p['title'])?></option>
<?php endforeach; ?>
</select>
<input name="amount_vusdt" value="10.000000">
<button>Create Boost Payment</button>
</form>
</div>

<div class="card">
<h2>Boost Orders</h2>
<table>
<tr><th>Boost Ref</th><th>Post Ref</th><th>Amount</th><th>Status</th><th>TX</th><th>Action</th></tr>
<?php foreach($orders as $o): ?>
<tr>
<td><?=h($o['boost_ref'])?></td>
<td><?=h($o['post_ref'])?></td>
<td><?=number_format((float)$o['amount_vusdt'],6)?> vUSDT</td>
<td><span class="badge <?=h($o['status'])?>"><?=h($o['status'])?></span></td>
<td><?=h($o['tx_hash'] ?? '-')?></td>
<td>
<?php if($o['status']!=='confirmed'): ?>
<form method="post" style="display:flex;gap:6px">
<input type="hidden" name="action" value="manual_confirm">
<input type="hidden" name="boost_ref" value="<?=h($o['boost_ref'])?>">
<input name="tx_hash" placeholder="tx hash optional">
<button>Confirm + Boost</button>
</form>
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</table>
</div>

<div class="card">
<h2>Post Boost Status</h2>
<table>
<tr><th>Post</th><th>Sync</th><th>Boost</th><th>Until</th></tr>
<?php foreach($posts as $p): ?>
<tr>
<td><b><?=h($p['post_ref'])?></b><br><?=h($p['title'])?></td>
<td><?=h($p['sync_status'])?></td>
<td><span class="badge <?=h($p['boost_status'])?>"><?=h($p['boost_status'])?></span></td>
<td><?=h($p['boosted_until'] ?? '-')?></td>
</tr>
<?php endforeach; ?>
</table>
</div>
<script src="/assets/-bottom-bar.js?v=991-logo-final" defer></script>
<script src="/assets/js/991-wallet-sync.js?v=991-unified"></script>
</body>
</html>
