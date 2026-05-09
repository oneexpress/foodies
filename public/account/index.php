<?php
declare(strict_types=1);
session_start();

if (empty($_SESSION['ev_account_id'])) {
    
    exit;
}

function h($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

$pdo = new PDO(
    'mysql:host=localhost;dbname=visa_db;charset=utf8mb4',
    'root',
    '',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
);

$accountId = (int)$_SESSION['ev_account_id'];

$a = $pdo->prepare("SELECT * FROM visa_unified_accounts WHERE id=? LIMIT 1");
$a->execute([$accountId]);
$account = $a->fetch();

$p = $pdo->prepare("SELECT * FROM visa_free_posts WHERE account_id=? ORDER BY id DESC LIMIT 100");
$p->execute([$accountId]);
$posts = $p->fetchAll();

$total = count($posts);
$synced = 0;
$pending = 0;
$boosted = 0;

foreach ($posts as $r) {
    if (($r['sync_status'] ?? '') === 'synced') $synced++;
    if (($r['sync_status'] ?? '') === 'pending') $pending++;
    if (!empty($r['boosted_until']) && strtotime((string)$r['boosted_until']) > time()) $boosted++;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>My Account · ExpressVisa</title>
<style>
body{margin:0;font-family:Arial,"Microsoft YaHei",sans-serif;background:#f6f7fb;color:#161616;padding-bottom:130px}
.wrap{max-width:1180px;margin:auto;padding:20px}
.hero{background:#e60023;color:#fff;border-radius:24px;padding:22px;display:flex;gap:14px;align-items:center}
.hero img{width:58px;height:58px;object-fit:contain;background:#fff;border-radius:16px;padding:6px}
.grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin:18px 0}
.card{background:#fff;border-radius:20px;padding:16px;box-shadow:0 12px 36px rgba(0,0,0,.08)}
.num{font-size:30px;font-weight:900;color:#e60023}
table{width:100%;border-collapse:collapse;background:#fff;border-radius:18px;overflow:hidden}
td,th{padding:12px;border-bottom:1px solid #eee;text-align:left;font-size:14px}
th{background:#111;color:#fff}
.badge{padding:6px 10px;border-radius:999px;font-weight:800;background:#eee}
.ok{background:#dcfce7;color:#166534}.warn{background:#fff7ed;color:#9a3412}
.btn{display:inline-block;background:#e60023;color:#fff;text-decoration:none;padding:8px 12px;border-radius:999px;font-weight:800}
@media(max-width:760px){.grid{grid-template-columns:repeat(2,1fr)}table{font-size:12px;display:block;overflow:auto}}
</style>
</head>
<body>
<div class="wrap">
  <div class="hero">
    <div>
      <h1 style="margin:0">My Account / 我的账户</h1>
      <p style="margin:6px 0 0">Posts, sync status and boost overview.</p>
    </div>
  </div>

  <div class="grid">
    <div class="card"><div class="num"><?=h($total)?></div><b>Total Posts</b></div>
    <div class="card"><div class="num"><?=h($pending)?></div><b>Pending</b></div>
    <div class="card"><div class="num"><?=h($synced)?></div><b>Synced</b></div>
    <div class="card"><div class="num"><?=h($boosted)?></div><b>Boosted</b></div>
  </div>

  <div class="card">
    <h2>Profile</h2>
    <p><b>Username:</b> <?=h($account['username'] ?? $_SESSION['ev_username'] ?? '-')?></p>
    <p><b>Email:</b> <?=h($account['email'] ?? '-')?></p>
    <p><b>TON:</b> <?=h($account['ton_address'] ?? $_SESSION['ev_ton_address'] ?? '-')?></p>
    <p>
      <a class="btn" href="/post/">Post Free Ad</a>
      <a class="btn" href="/api/logout.php">Logout</a>
    </p>
  </div>

  <div class="card" style="margin-top:16px">
    <h2>My Posts + Boost Status</h2>
    <?php if (!$posts): ?>
      <p>No posts linked to this account yet.</p>
    <?php else: ?>
      <table>
        <tr>
          <th>Ref</th><th>Title</th><th>Price</th><th>Sync</th><th>Boost</th><th>Action</th>
        </tr>
        <?php foreach ($posts as $r): 
          $active = !empty($r['boosted_until']) && strtotime((string)$r['boosted_until']) > time();
        ?>
        <tr>
          <td><?=h($r['post_ref'] ?? '-')?></td>
          <td><?=h($r['title'] ?? '-')?></td>
          <td>RM <?=h($r['price'] ?? '0')?></td>
          <td><span class="badge <?=($r['sync_status'] ?? '')==='synced'?'ok':'warn'?>"><?=h($r['sync_status'] ?? 'pending')?></span></td>
          <td><span class="badge <?=$active?'ok':'warn'?>"><?=$active?'active':'none'?></span></td>
          <td>
  <a class="btn" href="/boost/?post_ref=<?=urlencode((string)($r['post_ref'] ?? ''))?>">Boost</a>
  <a class="btn" href="/post/edit.php?ref=<?=urlencode((string)($r['post_ref'] ?? ''))?>">Edit</a>
  <form method="post" action="/post/delete.php" style="display:inline" onsubmit="return confirm('Delete this post?')">
    <input type="hidden" name="post_ref" value="<?=h($r['post_ref'] ?? '')?>">
    <button class="btn" style="border:0;background:#111;color:#fff;padding:8px 12px;border-radius:999px;font-weight:800" type="submit">Delete</button>
  </form>
</td>
        </tr>
        <?php endforeach; ?>
      </table>
    <?php endif; ?>
  </div>
</div>
<script src="/assets/js/991-wallet-sync.js?v=991-unified"></script>
<script src="/assets/991-balance-sync.js?v=991-balance-sync-ux-v1" defer></script>
<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
</body>
</html>
