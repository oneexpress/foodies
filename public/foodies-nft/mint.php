<?php
declare(strict_types=1);
header('Content-Type: text/html; charset=UTF-8');

const FOODIES_COLLECTION_OWNER = 'UQCFC6_JIg7YcJDaybYZKNSgETbDb28q-6SArFXItaI5jsCb';
const FOODIES_VERIFY_BASE = 'https://expressvisa.one/foodies-nft/verify.php?uid=';
const FOODIES_GETGEMS_COLLECTION = 'https://getgems.io/foodies';

const FOODIES_NFT_QR_TARGET = 'verify_url';
const FOODIES_NFT_VERIFY_BASE = 'https://expressvisa.one/foodies-nft/verify.php?uid=';

$FOODIES_NFT_QR_POSITIONS = [
  1 => ['qr_x'=>82, 'qr_y'=>796, 'qr_size'=>154, 'qr_margin'=>0, 'qr_anchor'=>'top-left', 'qr_target'=>'verify_url'],
  3 => ['qr_x'=>82, 'qr_y'=>781, 'qr_size'=>167, 'qr_margin'=>0, 'qr_anchor'=>'top-left', 'qr_target'=>'verify_url'],
  5 => ['qr_x'=>78, 'qr_y'=>777, 'qr_size'=>169, 'qr_margin'=>0, 'qr_anchor'=>'top-left', 'qr_target'=>'verify_url'],
];

function h($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

function db(): ?PDO {
    $env = '/var/www/secure/.env';
    $kv = [];
    if (is_file($env)) {
        foreach (file($env, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
            if ($line === '' || $line[0] === '#') continue;
            if (!str_contains($line, '=')) continue;
            [$k,$v] = explode('=', $line, 2);
            $kv[trim($k)] = trim($v, " \t\n\r\0\x0B\"'");
        }
    }
    $dbs = array_unique(array_filter([$kv['DB_DATABASE'] ?? '', $kv['DB_NAME'] ?? '', 'visa_db', 'wems_db']));
    $user = $kv['DB_USERNAME'] ?? $kv['DB_USER'] ?? 'root';
    $pass = $kv['DB_PASSWORD'] ?? $kv['DB_PASS'] ?? '';
    $host = $kv['DB_HOST'] ?? '127.0.0.1';
    foreach ($dbs as $name) {
        try {
            return new PDO("mysql:host={$host};dbname={$name};charset=utf8mb4", $user, $pass, [
                PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
            ]);
        } catch (Throwable $e) {}
    }
    return null;
}
function table_exists(PDO $pdo, string $t): bool {
    try { $s=$pdo->prepare("SHOW TABLES LIKE ?"); $s->execute([$t]); return (bool)$s->fetchColumn(); }
    catch(Throwable $e){ return false; }
}
function columns(PDO $pdo, string $t): array {
    try { return array_column($pdo->query("DESCRIBE `$t`")->fetchAll(), 'Field'); }
    catch(Throwable $e){ return []; }
}
function col(array $cols, array $cands): ?string {
    foreach($cands as $c) if(in_array($c,$cols,true)) return $c;
    return null;
}
function rows(): array {
    $pdo = db();
    if (!$pdo) return [];
    $tables = ['foodies_nft_certs','foodies_rwa_certs','visa_foodies_certs','foodies_certs','rwa_foodies_certs','visa_free_posts'];
    foreach ($tables as $t) {
        if (!table_exists($pdo,$t)) continue;
        $c = columns($pdo,$t);
        $uid = col($c,['cert_uid','uid','post_ref','nft_uid','rwa_uid']);
        if (!$uid) continue;
        $title = col($c,['title','brand_name','food_name','name','listing_title']);
        $wallet = col($c,['wallet','wallet_address','issuer_wallet','owner_wallet']);
        $stars = col($c,['star_rating','stars','rating']);
        $status = col($c,['status','sync_status','mint_status']);
        $minted = col($c,['nft_minted','minted','is_minted']);
        $addr = col($c,['nft_item_address','nft_address','item_address']);
        $created = col($c,['issued_at','created_at','updated_at']);
        $select = [
            "`$uid` cert_uid",
            $title ? "`$title` title" : "'' title",
            $wallet ? "`$wallet` wallet" : "'' wallet",
            $stars ? "`$stars` stars" : "1 stars",
            $status ? "`$status` status" : "'READY' status",
            $minted ? "`$minted` nft_minted" : "0 nft_minted",
            $addr ? "`$addr` nft_item_address" : "'' nft_item_address",
            $created ? "`$created` created_at" : "NULL created_at",
        ];
        $where = '1=1';
        if ($t === 'visa_free_posts') $where = "(`title` LIKE '%Foodies%' OR `description` LIKE '%Foodies%' OR `$uid` LIKE '%FOODIES%')";
        try {
            $order = $created ? "`$created` DESC" : "`$uid` DESC";
            $out = $pdo->query("SELECT ".implode(',',$select)." FROM `$t` WHERE $where ORDER BY $order LIMIT 120")->fetchAll();
            if ($out) return $out;
        } catch(Throwable $e){}
    }
    return [];
}

$rows = rows();
if (!$rows) $rows = [[
  'cert_uid'=>'FOODIES-RWA-PENDING','title'=>'Foodies RWA Cert','wallet'=>FOODIES_COLLECTION_OWNER,
  'stars'=>1,'status'=>'READY','nft_minted'=>0,'nft_item_address'=>'','created_at'=>date('Y-m-d H:i:s')
]];
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<title>Foodies RWA Minting Factory</title>
<style>
:root{--red:#E60012;--dark:#B8000E;--soft:#FFF1F2;--bg:#F7F8FA;--ink:#101828;--muted:#667085;--line:#E5E7EB;--green:#22c55e;--gold:#f59e0b}
*{box-sizing:border-box}
body{margin:0;background:radial-gradient(circle at top,#fff 0,#fff5f6 28%,var(--bg) 60%);color:var(--ink);font-family:Arial,Helvetica,sans-serif;padding-bottom:126px}
.wrap{max-width:1240px;margin:0 auto;padding:18px}
.langbar{display:flex;justify-content:flex-end;gap:8px;margin:0 0 12px}
.langbtn{border:1px solid var(--line);background:#fff;color:#111;border-radius:999px;padding:9px 13px;font-weight:1000;cursor:pointer}
.langbtn.active{background:var(--red);color:#fff;border-color:var(--red)}
.hero{background:linear-gradient(135deg,var(--red),var(--dark));color:#fff;border-radius:28px;padding:24px;box-shadow:0 18px 46px rgba(230,0,18,.25);position:relative;overflow:hidden}
.hero:after{content:"";position:absolute;right:-60px;top:-80px;width:240px;height:240px;border-radius:50%;background:rgba(255,255,255,.14)}
.hero h1{margin:0 0 8px;font-size:28px}.hero p{margin:0;opacity:.92;line-height:1.55;max-width:760px}
.walletbar{margin-top:13px;background:rgba(255,255,255,.16);border:1px solid rgba(255,255,255,.25);border-radius:16px;padding:11px 13px;font-weight:900}
.toolbar{display:grid;grid-template-columns:1.2fr .6fr .6fr auto;gap:10px;margin:16px 0}
.inp,.sel{border:1px solid var(--line);border-radius:14px;background:#fff;min-height:48px;padding:0 14px;font-weight:800}
.connect{border:0;border-radius:14px;background:#111;color:#fff;font-weight:900;padding:0 16px;cursor:pointer;min-height:48px}
.led{display:inline-block;width:10px;height:10px;border-radius:50%;background:#ef4444;margin-right:8px;box-shadow:0 0 0 5px rgba(239,68,68,.12)}
.led.on{background:var(--green);box-shadow:0 0 0 5px rgba(34,197,94,.14),0 0 18px rgba(34,197,94,.8)}
.kpis{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin:16px 0}
.kpi{background:#fff;border:1px solid var(--line);border-radius:18px;padding:15px;box-shadow:0 8px 24px rgba(0,0,0,.05)}
.kpi b{font-size:24px;color:var(--red)}.kpi span{display:block;color:var(--muted);font-size:12px;font-weight:900;text-transform:uppercase;margin-top:4px}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(310px,1fr));gap:14px}
.card{background:#fff;border:1px solid var(--line);border-radius:22px;padding:17px;box-shadow:0 10px 26px rgba(0,0,0,.07)}
.badge{display:inline-flex;background:var(--soft);color:var(--red);border-radius:999px;padding:8px 12px;font-size:13px;font-weight:900;margin-bottom:11px}
.title{font-size:19px;font-weight:900;margin:0 0 9px}.meta{font-size:13px;color:#475467;line-height:1.7;word-break:break-word}
.actions{display:grid;grid-template-columns:1fr 1fr;margin-top:14px;gap:9px}
.btn{border:0;border-radius:14px;background:var(--red);color:#fff;font-weight:900;padding:13px 12px;text-decoration:none;text-align:center;cursor:pointer;font-size:15px}
.btn.alt{background:#111}.btn.done{background:var(--green)}
.preview{height:150px;border-radius:16px;background:#111;margin:10px 0;display:flex;align-items:center;justify-content:center;overflow:hidden}
.preview img{max-height:150px;max-width:100%;display:block}
@media(max-width:780px){.toolbar{grid-template-columns:1fr}.kpis{grid-template-columns:repeat(2,1fr)}.hero h1{font-size:23px}}
</style>
<link rel="stylesheet" href="/foodies-nft/assets/foodies-footer-nav.css?v=foodies-footer-fullwidth-pro">
<script src="https://unpkg.com/@tonconnect/ui@latest/dist/tonconnect-ui.min.js"></script>


</head>
<body>
<div class="wrap">
  <div class="langbar">
    <button type="button" class="langbtn active" id="langEN">EN</button>
    <button type="button" class="langbtn" id="langZH">中文</button>
  </div>

  <section class="hero">
    <h1 data-i18n="heroTitle">🍽️ Foodies RWA Minting Factory</h1>
    <p data-i18n="heroText">Mint issued Foodies RWA certificates into the Foodies RWA Collection. NFT artifact QR uses the locked Verify URL overlay positions.</p>
    <div class="walletbar"><span id="heroLed" class="led"></span><span id="heroWallet">Wallet: Not connected</span></div>
  </section>

  <div class="toolbar">
    <input id="q" class="inp" placeholder="Search cert UID / title / wallet" data-i18n-placeholder="search">
    <select id="starFilter" class="sel">
      <option value="" data-i18n="allStars">All Stars</option><option value="1">1 Star</option><option value="3">3 Stars</option><option value="5">5 Stars</option>
    </select>
    <select id="mintFilter" class="sel">
      <option value="" data-i18n="allStatus">All Status</option><option value="pending" data-i18n="pendingMint">Pending Mint</option><option value="minted" data-i18n="minted">Minted</option>
    </select>
    <button id="connectTop" class="connect" type="button"><span id="topLed" class="led"></span><span id="connectTopText">Connect Wallet</span></button>
  </div>

  <div class="kpis">
    <div class="kpi"><b id="totalKpi"><?= count($rows) ?></b><span data-i18n="totalCerts">Total Certs</span></div>
    <div class="kpi"><b id="pendingKpi">0</b><span data-i18n="pendingMint">Pending Mint</span></div>
    <div class="kpi"><b id="mintedKpi">0</b><span data-i18n="minted">Minted</span></div>
    <div class="kpi"><b><?= h(substr(FOODIES_COLLECTION_OWNER,0,6).'...'.substr(FOODIES_COLLECTION_OWNER,-6)) ?></b><span data-i18n="collectionOwner">Collection Owner</span></div>
  </div>

  <div class="grid" id="grid">
    <?php foreach ($rows as $r):
      $uid=(string)($r['cert_uid'] ?? 'FOODIES-RWA-PENDING');
      $stars=(int)($r['stars'] ?? 1); if(!in_array($stars,[1,3,5],true)) $stars=1;
      $minted=(int)($r['nft_minted'] ?? 0) === 1 || (string)($r['nft_item_address'] ?? '') !== '';
      $verify=FOODIES_VERIFY_BASE.rawurlencode($uid);
      $qr=$FOODIES_NFT_QR_POSITIONS[$stars] ?? $FOODIES_NFT_QR_POSITIONS[1];
      $img="/metadata/foodies/NFT/".($stars===1?'1_star_foodies.png':($stars===3?'3_stars_foodies.png':'5_stars_foodies.png'));
    ?>
    <article class="card" data-search="<?= h(strtolower($uid.' '.$r['title'].' '.$r['wallet'])) ?>" data-stars="<?= $stars ?>" data-minted="<?= $minted?'minted':'pending' ?>">
      <div class="badge"><?= h($stars) ?> Star NFT Artifact</div>
      <div class="preview"><img src="<?= h($img) ?>" alt=""></div>
      <h2 class="title"><?= h($r['title'] ?: 'Foodies RWA Cert') ?></h2>
      <div class="meta">
        <b>Cert UID:</b> <?= h($uid) ?><br>
        <b>Wallet:</b> <?= h($r['wallet'] ?? '') ?><br>
        <b>Status:</b> <?= $minted ? 'MINTED' : h($r['status'] ?? 'READY') ?><br>
        <b>QR Position:</b> x<?= h($qr['qr_x']) ?> / y<?= h($qr['qr_y']) ?> / size<?= h($qr['qr_size']) ?><br>
        <b>Verify URL:</b> <?= h($verify) ?><br>
        <?php if ($minted): ?><b>NFT Item:</b> <?= h($r['nft_item_address']) ?><?php endif; ?>
      </div>
      <div class="actions">
        <a class="btn <?= $minted?'done':'' ?>" href="/foodies-nft/mint-process.php?uid=<?= rawurlencode($uid) ?>&stars=<?= $stars ?>"><?= $minted?'Minted':'Mint Now' ?></a>
        <a class="btn alt" href="<?= h($verify) ?>">Verify</a>
      </div>
    </article>
    <?php endforeach; ?>
  </div>
</div>

<script>
(function(){
 const KEYS=['foodies_wallet','ton_wallet','wallet_address','connected_wallet','wallet'];
 const MANIFEST='https://expressvisa.one/tonconnect-manifest.json';
 const dict={
   en:{heroTitle:'🍽️ Foodies RWA Minting Factory',heroText:'Mint issued Foodies RWA certificates into the Foodies RWA Collection. NFT artifact QR uses the locked Verify URL overlay positions.',search:'Search cert UID / title / wallet',allStars:'All Stars',allStatus:'All Status',pendingMint:'Pending Mint',minted:'Minted',totalCerts:'Total Certs',collectionOwner:'Collection Owner',connect:'Connect Wallet',connected:'Connected',walletOff:'Wallet: Not connected',walletOn:'Wallet Connected: '},
   zh:{heroTitle:'🍽️ Foodies RWA 铸造工厂',heroText:'把已发行的 Foodies RWA 证书铸造成 Foodies RWA Collection NFT。NFT 图片 QR 使用已锁定的验证链接位置。',search:'搜索证书 UID / 标题 / 钱包',allStars:'全部星级',allStatus:'全部状态',pendingMint:'待铸造',minted:'已铸造',totalCerts:'证书总数',collectionOwner:'合集 Owner',connect:'连接钱包',connected:'已连接',walletOff:'钱包：未连接',walletOn:'钱包已连接：'}
 };
 const short=a=>a&&a.length>14?a.slice(0,7)+'...'+a.slice(-6):a;
 function getWallet(){for(const k of KEYS){const v=localStorage.getItem(k)||sessionStorage.getItem(k); if(v&&v.trim()) return v.trim();} return '';}
 function setWallet(w){
   if(w){localStorage.setItem('foodies_wallet',w);localStorage.setItem('ton_wallet',w);localStorage.setItem('wallet_address',w);window.__991WalletAddress=w;window.FOODIES_WALLET=w;}
   else{KEYS.forEach(k=>{localStorage.removeItem(k);sessionStorage.removeItem(k);});window.__991WalletAddress='';window.FOODIES_WALLET='';}
   syncWalletUI();
 }
 async function tonUI(){
   if(window.FOODIES_TON_UI) return window.FOODIES_TON_UI;
   if(window.TON_CONNECT_UI && window.TON_CONNECT_UI.TonConnectUI){
     window.FOODIES_TON_UI = new window.TON_CONNECT_UI.TonConnectUI({manifestUrl:MANIFEST});
     window.FOODIES_TON_UI.onStatusChange(function(wallet){
       if(wallet && wallet.account && wallet.account.address) setWallet(wallet.account.address);
       else syncWalletUI();
     });
     return window.FOODIES_TON_UI;
   }
   return null;
 }
 async function connectWallet(){
   const old=getWallet();
   if(old){
     if(confirm('Wallet connected: '+short(old)+'\n\nDisconnect this wallet?')){
       const ui=await tonUI(); try{if(ui&&ui.disconnect) await ui.disconnect();}catch(e){}
       setWallet('');
     }
     return;
   }
   const ui=await tonUI();
   if(ui && ui.openModal){try{await ui.openModal();return;}catch(e){}}
   const w=prompt('Paste TON wallet address');
   if(w&&w.trim()) setWallet(w.trim());
 }
 function lang(){return localStorage.getItem('foodies_lang')||'en';}
 function applyLang(l){
   const t=dict[l]||dict.en;
   localStorage.setItem('foodies_lang',l);
   document.documentElement.lang=l==='zh'?'zh-CN':'en';
   document.getElementById('langEN')?.classList.toggle('active',l==='en');
   document.getElementById('langZH')?.classList.toggle('active',l==='zh');
   document.querySelectorAll('[data-i18n]').forEach(el=>{const k=el.dataset.i18n;if(t[k])el.textContent=t[k];});
   document.querySelectorAll('[data-i18n-placeholder]').forEach(el=>{const k=el.dataset.i18nPlaceholder;if(t[k])el.placeholder=t[k];});
   syncWalletUI();
   window.dispatchEvent(new CustomEvent('foodies-lang-change',{detail:{lang:l}}));
 }
 function syncWalletUI(){
   const w=getWallet(), on=!!w, t=dict[lang()]||dict.en;
   ['topLed','heroLed'].forEach(id=>document.getElementById(id)?.classList.toggle('on',on));
   const txt=document.getElementById('connectTopText'); if(txt) txt.textContent=on?t.connected:t.connect;
   const hw=document.getElementById('heroWallet'); if(hw) hw.textContent=on?t.walletOn+short(w):t.walletOff;
   window.FOODIES_WALLET=w;
   window.__991WalletAddress=w;
   window.dispatchEvent(new CustomEvent('foodies-wallet-sync',{detail:{wallet:w,connected:on}}));
 }
 document.getElementById('connectTop')?.addEventListener('click',connectWallet);
 document.getElementById('langEN')?.addEventListener('click',()=>applyLang('en'));
 document.getElementById('langZH')?.addEventListener('click',()=>applyLang('zh'));

 const q=document.getElementById('q'), sf=document.getElementById('starFilter'), mf=document.getElementById('mintFilter');
 const cards=[...document.querySelectorAll('.card')];
 function filter(){
   const term=(q.value||'').toLowerCase(), st=sf.value, mt=mf.value;
   let pending=0,minted=0,shown=0;
   cards.forEach(c=>{
     const ok=(!term||c.dataset.search.includes(term))&&(!st||c.dataset.stars===st)&&(!mt||c.dataset.minted===mt);
     c.style.display=ok?'block':'none';
     if(ok) shown++;
     if(c.dataset.minted==='minted') minted++; else pending++;
   });
   document.getElementById('pendingKpi').textContent=pending;
   document.getElementById('mintedKpi').textContent=minted;
   document.getElementById('totalKpi').textContent=shown;
 }
 [q,sf,mf].forEach(x=>x&&x.addEventListener('input',filter));
 window.addEventListener('storage',syncWalletUI);
 window.addEventListener('foodies-footer-wallet-changed',syncWalletUI);
 window.foodiesConnectWallet=connectWallet;
 window.foodiesSetWallet=setWallet;
 applyLang(lang());
 filter();
 setTimeout(tonUI,500);
})();
</script>


<script src="/foodies-nft/assets/foodies-footer-nav.js?v=foodies-footer-fullwidth-pro" defer></script>


</body>
</html>
