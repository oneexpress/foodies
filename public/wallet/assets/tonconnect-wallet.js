(function(){
  const MANIFEST = "https://expressvisa.one/tonconnect-manifest.json";
  let tonConnectUI = null;
  let bindingNow = false;
  let lastBoundAddress = "";

  function setText(id, value){
    const el = document.getElementById(id);
    if(el) el.textContent = value || "";
  }

  function normalizeAddress(addr){
    if(!addr) return "";
    addr = String(addr).trim();
    if(addr.startsWith("UQ") || addr.startsWith("EQ")) return addr;
    if(/^[-]?[0-9]+:[a-fA-F0-9]{64}$/.test(addr)) return addr;
    return "";
  }

  function cookieValue(name){
    const m = document.cookie.match(new RegExp('(?:^|; )' + name + '=([^;]*)'));
    return m ? decodeURIComponent(m[1]) : "";
  }

  async function bindWallet(account, walletRaw, reloadAfterBind){
    const ton = normalizeAddress(account && account.address);
    if(!ton){
      setText("tonStatus","Wallet connected but address missing.");
      return;
    }

    const currentCookie = cookieValue("ev_ton_wallet");

    if(currentCookie === ton || lastBoundAddress === ton){
      setText("tonStatus","TON wallet connected.");
      setText("tonWalletText",ton);
      return;
    }

    if(bindingNow) return;
    bindingNow = true;

    try {
      setText("tonStatus","Binding wallet...");

      const res = await fetch("/wallet/api/bind-ton.php", {
        method: "POST",
        headers: {"Content-Type":"application/json"},
        body: JSON.stringify({ton_wallet: ton, wallet_raw: walletRaw || {}})
      });

      const text = await res.text();
      let json;
      try {
        json = JSON.parse(text);
      } catch(e) {
        setText("tonStatus","Bind failed: invalid server JSON. HTTP " + res.status);
        console.error(text);
        return;
      }

      if(!res.ok || !json.ok){
        setText("tonStatus","Bind failed: " + (json.message || json.error || ("HTTP " + res.status)));
        return;
      }

      lastBoundAddress = json.ton_wallet;
      document.cookie = "ev_ton_wallet=" + encodeURIComponent(json.ton_wallet) + ";path=/;max-age=31536000";
      setText("tonStatus","TON wallet connected.");
      setText("tonWalletText",json.ton_wallet);

      if(reloadAfterBind) setTimeout(()=>location.reload(), 500);

    } finally {
      bindingNow = false;
    }
  }

  async function init(){
    const btn = document.getElementById("tonConnectBtn");
    const disconnectBtn = document.getElementById("tonDisconnectBtn");
    if(!btn) return;

    if(!window.TON_CONNECT_UI || !window.TON_CONNECT_UI.TonConnectUI){
      setText("tonStatus","TON Connect library not loaded.");
      return;
    }

    tonConnectUI = new window.TON_CONNECT_UI.TonConnectUI({
      manifestUrl: MANIFEST,
      buttonRootId: "tonConnectButtonRoot"
    });

    tonConnectUI.onStatusChange(async function(wallet){
      if(wallet && wallet.account){
        await bindWallet(wallet.account, wallet, false);
      }
    });

    btn.addEventListener("click", async function(){
      try {
        setText("tonStatus","Opening TON wallet...");
        await tonConnectUI.openModal();
      } catch(e){
        setText("tonStatus","Connect failed: " + (e.message || e));
      }
    });

    if(disconnectBtn){
      disconnectBtn.addEventListener("click", async function(){
        try { await tonConnectUI.disconnect(); } catch(e){}
        document.cookie = "ev_ton_wallet=;path=/;max-age=0";
        lastBoundAddress = "";
        setText("tonStatus","Disconnected.");
        setTimeout(()=>location.reload(), 500);
      });
    }
  }

  document.addEventListener("DOMContentLoaded", init);
})();
