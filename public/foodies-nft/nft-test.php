<?php
declare(strict_types=1);
header('Content-Type: text/html; charset=UTF-8');
function h($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

$uid = trim((string)($_GET['uid'] ?? 'FOODIES-MINT-0001'));
$stars = (int)($_GET['stars'] ?? $_GET['star'] ?? 5);
if (!in_array($stars,[1,3,5],true)) $stars = 5;

$safe = preg_replace('/[^A-Za-z0-9._-]/', '-', $uid);
$recipient = trim((string)($_GET['to'] ?? 'UQCFC6_JIg7YcJDaybYZKNSgETbDb28q-6SArFXItaI5jsCb'));
$artifactApi = "/foodies-nft/api/nft-artifact-mint.php?fresh=1&uid=".rawurlencode($uid)."&stars=".$stars;
$artifactFinal = "/metadata/foodies/generated/{$safe}-{$stars}star.png";
$metaApi = "/foodies-nft/api/nft-metadata.php?uid=".rawurlencode($uid)."&stars=".$stars;
$metaFinal = "/metadata/foodies/items/{$safe}.json";
$payloadApi = "/foodies-nft/api/build-mint-payload.php?uid=".rawurlencode($uid)."&to=".rawurlencode($recipient)."&star=".$stars;
$verify = "https://expressvisa.one/foodies-nft/verify.php?uid=".rawurlencode($uid);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<title>Foodies NFT Mint Test</title>
<script src="https://unpkg.com/@tonconnect/ui@latest/dist/tonconnect-ui.min.js"></script>
<style>
body{margin:0;background:#f7f8fa;color:#111;font-family:Arial,Helvetica,sans-serif;padding:18px}
.wrap{max-width:1180px;margin:0 auto}.grid{display:grid;grid-template-columns:420px 1fr;gap:16px}
.card{background:white;border:1px solid #e5e7eb;border-radius:20px;padding:16px;box-shadow:0 10px 28px rgba(0,0,0,.06)}
img{max-width:100%;border-radius:16px;background:#111}.ok{color:#15803d;font-weight:900}.wait{color:#b45309;font-weight:900}
button{border:0;border-radius:14px;background:#e60012;color:#fff;padding:13px 16px;font-weight:900;cursor:pointer}
input,select{padding:11px;border-radius:12px;border:1px solid #ddd}pre{white-space:pre-wrap;background:#101828;color:#e5e7eb;padding:14px;border-radius:14px;max-height:360px;overflow:auto}
a{color:#e60012;font-weight:800;word-break:break-all}@media(max-width:860px){.grid{grid-template-columns:1fr}}
</style>
</head>
<body>
<div class="wrap">
<h1>Foodies NFT Mint Test</h1>
<form>
<input name="uid" value="<?=h($uid)?>">
<select name="stars">
<option value="1" <?=$stars===1?'selected':''?>>1 Star</option>
<option value="3" <?=$stars===3?'selected':''?>>3 Star</option>
<option value="5" <?=$stars===5?'selected':''?>>5 Star</option>
</select>
<input name="to" value="<?=h($recipient)?>" style="width:360px;max-width:100%">
<button>Reload Test</button>
</form>
<br>
<div class="grid">
<div class="card">
<h2>Final QR-Merged NFT PNG</h2>
<img src="<?=h($artifactApi)?>" onerror="document.getElementById('artifactStatus').textContent='artifact_failed'">
<p id="artifactStatus" class="ok">single_file_artifact_ready</p>
<p><a target="_blank" href="<?=h($artifactFinal)?>">Open final PNG</a></p>
</div>
<div class="card">
<h2>Minting Process</h2>
<pre id="status">1 Validate issued cert: ready
2 Merge NFT artifact with Verify URL QR: ready
3 Build metadata traits JSON: ready
4 Build Getgems collection/item payload: ready
5 Confirm TON wallet: waiting_wallet
6 Submit mint transaction: next_integration</pre>
<div id="ton-connect"></div><br>
<button onclick="mintFoodies()">Mint NFT with TON Wallet</button>
<h3>Links</h3>
<p>Verify: <a target="_blank" href="<?=h($verify)?>"><?=h($verify)?></a></p>
<p>Metadata API: <a target="_blank" href="<?=h($metaApi)?>"><?=h($metaApi)?></a></p>
<p>Metadata File: <a target="_blank" href="<?=h($metaFinal)?>"><?=h($metaFinal)?></a></p>
<p>Payload API: <a target="_blank" href="<?=h($payloadApi)?>"><?=h($payloadApi)?></a></p>
<pre id="debug">loading...</pre>
</div>
</div>
</div>
<script>
const tc = new TON_CONNECT_UI.TonConnectUI({
  manifestUrl: 'https://expressvisa.one/tonconnect-manifest.json',
  buttonRootId: 'ton-connect'
});

async function preflight(){
  const meta = await fetch(<?=json_encode($metaApi)?>).then(r=>r.json());
  const payload = await fetch(<?=json_encode($payloadApi)?>).then(r=>r.json());
  document.getElementById('debug').textContent = JSON.stringify({metadata:meta,payload:payload}, null, 2);
}



async function ensureTonConnected(timeoutMs = 90000){
  await tc.connectionRestored.catch(()=>{});

  if(tc.connected || tc.account || tc.wallet){
    return true;
  }

  tc.openModal();

  return await new Promise((resolve, reject)=>{
    let done = false;

    const timer = setTimeout(()=>{
      if(done) return;
      done = true;
      reject(new Error('wallet_connect_timeout'));
    }, timeoutMs);

    const unsub = tc.onStatusChange((wallet)=>{
      if(done) return;

      if(wallet || tc.connected || tc.account){
        done = true;
        clearTimeout(timer);
        if(typeof unsub === 'function') unsub();
        resolve(true);
      }
    });
  });
}

async function mintFoodies(){
  const status = document.getElementById('status');
  const debug = document.getElementById('debug');

  try{
    status.textContent =
`1 Validate issued cert: ready
2 Merge NFT artifact with Verify URL QR: ready
3 Build metadata traits JSON: ready
4 Build Getgems collection/item payload: loading
5 Confirm TON wallet: pending
6 Submit mint transaction: pending`;

    const payload = await fetch(<?=json_encode($payloadApi)?>, {cache:'no-store'}).then(r=>r.json());

    if(!payload.ok){
      throw new Error(payload.error || 'payload_failed');
    }

    if(!payload.tonconnect || !Array.isArray(payload.tonconnect.messages) || payload.tonconnect.messages.length < 1){
      throw new Error('invalid_tonconnect_payload');
    }

    debug.textContent = JSON.stringify({step:'payload_ready', payload}, null, 2);

    status.textContent =
`1 Validate issued cert: ready
2 Merge NFT artifact with Verify URL QR: ready
3 Build metadata traits JSON: ready
4 Build Getgems collection/item payload: ready
5 Confirm TON wallet: connecting
6 Submit mint transaction: waiting`;

    await ensureTonConnected();

    status.textContent =
`1 Validate issued cert: ready
2 Merge NFT artifact with Verify URL QR: ready
3 Build metadata traits JSON: ready
4 Build Getgems collection/item payload: ready
5 Confirm TON wallet: connected
6 Submit mint transaction: requesting_wallet_approval`;

    console.log('FOODIES_SEND_TRANSACTION', payload.tonconnect);

    const result = await tc.sendTransaction(payload.tonconnect);

    status.textContent =
`1 Validate issued cert: ready
2 Merge NFT artifact with Verify URL QR: ready
3 Build metadata traits JSON: ready
4 Build Getgems collection/item payload: ready
5 Confirm TON wallet: approved
6 Submit mint transaction: submitted`;

    debug.textContent = JSON.stringify({step:'submitted', payload, result}, null, 2);

  }catch(e){
    console.error('FOODIES_MINT_ERROR', e);
    status.textContent += "\\n\\nERROR: " + (e && e.message ? e.message : String(e));
    debug.textContent = JSON.stringify({error: e && e.message ? e.message : String(e)}, null, 2);
    alert(e && e.message ? e.message : String(e));
  }
}


preflight().catch(e=>document.getElementById('debug').textContent=String(e));
</script>
</body>
</html>
