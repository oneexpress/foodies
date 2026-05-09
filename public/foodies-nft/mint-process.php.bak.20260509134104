<?php
declare(strict_types=1);

$uid=preg_replace('/[^A-Z0-9_-]/','',(string)($_GET['uid']??'FOODIES-RWA-PENDING'));
$uid=$uid?:'FOODIES-RWA-PENDING';

$stars=preg_replace('/[^0-9]/','',(string)($_GET['stars']??'1'));
$stars=in_array($stars,['1','3','5'],true)?$stars:'1';

$img="https://raw.githubusercontent.com/oneexpress/foodies/main/public/metadata/foodies/generated/{$uid}-{$stars}star.png";
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Mint @foodies NFT</title>

<script src="https://unpkg.com/@tonconnect/ui@latest/dist/tonconnect-ui.min.js"></script>

<style>
body{
 margin:0;
 background:#f7f8fa;
 font-family:Arial,sans-serif;
 color:#111827;
}
.wrap{
 max-width:1200px;
 margin:auto;
 padding:20px;
}
.grid{
 display:grid;
 grid-template-columns:1fr 420px;
 gap:18px;
}
.card{
 background:#fff;
 border-radius:24px;
 border:1px solid #e5e7eb;
 padding:18px;
 box-shadow:0 14px 32px rgba(0,0,0,.08);
}
.preview{
 width:100%;
 border-radius:22px;
 border:1px solid #e5e7eb;
}
.btn{
 border:0;
 border-radius:14px;
 padding:14px 18px;
 font-weight:900;
 cursor:pointer;
 color:#fff;
}
.green{background:#15803d}
.red{background:#E60012}
.dark{background:#111827}
.row{
 display:flex;
 gap:10px;
 flex-wrap:wrap;
}
#status{
 white-space:pre-wrap;
 background:#0b1220;
 color:#d1fae5;
 padding:14px;
 border-radius:14px;
 min-height:180px;
 overflow:auto;
 font-size:12px;
}
@media(max-width:860px){
 .grid{grid-template-columns:1fr}
}
</style>
</head>
<body>

<div class="wrap">

<h1>Mint @foodies NFT</h1>

<div class="grid">

<div class="card">

<div id="ton-connect"></div>

<p class="row">
<button class="btn green" id="connectBtn">Connect Wallet</button>
<button class="btn green" id="buildBtn">Build Payload</button>
<button class="btn red" id="mintBtn">Mint NFT</button>
<a class="btn dark" target="_blank" href="https://getgems.io/foodies">Marketplace</a>
</p>

<pre id="status">READY</pre>

</div>

<div class="card">
<img
 class="preview"
 src="<?=htmlspecialchars($img)?>"
 onerror="this.src='/metadata/foodies/foodies-<?=$stars?>star.png';">
</div>

</div>

</div>

<script>
let payload=null;

const uid=<?=json_encode($uid)?>;
const stars=<?=json_encode($stars)?>;

const statusEl=document.getElementById('status');

function log(v){
 statusEl.textContent=
   typeof v==='string'
   ? v
   : JSON.stringify(v,null,2);
}

const tonConnectUI=new TON_CONNECT_UI.TonConnectUI({
 manifestUrl:'https://expressvisa.one/tonconnect-manifest.json',
 buttonRootId:'ton-connect'
});

document.getElementById('connectBtn').onclick=async()=>{
 try{
   await tonConnectUI.openModal();
 }catch(e){
   log(e.message||e);
 }
};

async function buildPayload(){
 const r=await fetch(
   `/foodies-nft/api/build-mint-payload.php?uid=${encodeURIComponent(uid)}&stars=${encodeURIComponent(stars)}&fresh=1`,
   {cache:'no-store'}
 );

 const j=await r.json();

 if(!j.ok){
   throw new Error(j.error||'payload_error');
 }

 payload=j;

 log(j);

 return j;
}

document.getElementById('buildBtn').onclick=async()=>{
 try{
   await buildPayload();
 }catch(e){
   log(e.message||e);
 }
};

document.getElementById('mintBtn').onclick=async()=>{
 try{

   const p=payload || await buildPayload();

   await tonConnectUI.sendTransaction({
     validUntil:Math.floor(Date.now()/1000)+600,
     messages:p.messages
   });

   log('MINT TX SENT');

 }catch(e){
   log(e.message||e);
 }
};
</script>

</body>
</html>
