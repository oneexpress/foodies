<?php
declare(strict_types=1);
session_start();

$err='';
$next=$_GET['next'] ?? '/admin/control/';

if ($_SERVER['REQUEST_METHOD']==='POST') {
  $pin=trim($_POST['pin'] ?? '');
  $next=$_POST['next'] ?? '/admin/control/';
  if ($pin === '4653') {
    $_SESSION['ev_admin_ok']=1;
    header('Location: '.$next);
    exit;
  }
  $err='Invalid PIN';
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>ExpressVisa Admin Login</title>
<style>
body{margin:0;background:#0b0b10;color:#fff;font-family:Arial;display:grid;place-items:center;min-height:100vh}
.card{width:min(420px,92vw);background:#16161d;border:1px solid #2d2d38;border-radius:22px;padding:24px;box-shadow:0 20px 50px #0008}
input{width:100%;box-sizing:border-box;background:#08080d;color:#fff;border:1px solid #333;border-radius:12px;padding:14px;font-size:18px}
button{width:100%;background:#e60012;color:#fff;border:0;border-radius:12px;padding:14px;font-weight:900;margin-top:14px}
.err{color:#ffb4b4;margin:10px 0}
</style>
</head>
<body>
<div class="card">
<h1>ExpressVisa Admin</h1>
<form method="post">
<input type="hidden" name="next" value="<?=htmlspecialchars($next)?>">
<input name="pin" type="password" placeholder="Admin PIN" autofocus>
<?php if($err): ?><div class="err"><?=htmlspecialchars($err)?></div><?php endif; ?>
<button>Login</button>
</form>
</div>
<script src="/assets/js/991-wallet-sync.js?v=991-unified"></script>
<link rel="stylesheet" href="/assets/css/991-bottom-nav.css?v=991-latest-full-20260507162825">
<script src="/assets/js/991-bottom-nav.js?v=991-latest-full-20260507162825" defer></script>
</body>
</html>
