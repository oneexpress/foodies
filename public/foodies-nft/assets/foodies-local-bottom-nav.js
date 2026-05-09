(function(){
  function wallet(){
    return (window.__991WalletAddress||localStorage.getItem('ton_wallet')||localStorage.getItem('wallet_address')||localStorage.getItem('connected_wallet')||'').trim();
  }
  function short(w){return w?w.slice(0,6)+'...'+w.slice(-6):'CONNECT WALLET'}
  function build(){
    document.querySelectorAll('#ev-bottom-nav,#ev991-bottom-nav,.ev-bottom-nav,.ev991-bottom-nav,.visa-bottom-nav,.bottom-nav,.adg-bottom-nav,[data-bottom-nav]').forEach(e=>e.remove());
    if(document.getElementById('foodiesLocalBottomNav')) return;

    const nav=document.createElement('nav');
    nav.id='foodiesLocalBottomNav';
    nav.className='foodies-local-bottom-nav';
    nav.innerHTML=[
      '<a class="active" href="/foodies-nft/">Foodies NFT</a>',
      '<button id="foodiesIssueNew" type="button">Issue New</button>',
      '<button id="foodiesReissue" type="button">Re-Issue</button>',
      '<a href="/foodies-rwa/verify.php">Verify</a>',
      '<a href="/wallet/">Wallet</a>',
      '<span id="foodiesLoginStatus" class="login-bad">LOGIN: OFF</span>',
      '<button id="foodiesLocalConnect" type="button">'+short(wallet())+'</button>'
    ].join('');
    document.body.appendChild(nav);

    document.getElementById('foodiesIssueNew').onclick=function(){
      const newBox=document.getElementById('newIssuePanel');
      const reBox=document.getElementById('reissuePanel');
      if(newBox) newBox.scrollIntoView({behavior:'smooth',block:'start'});
      document.querySelector('[name="issue_type"][value="new"]')?.click();
      if(reBox) reBox.style.display='none';
    };

    document.getElementById('foodiesReissue').onclick=function(){
      const w=wallet();
      if(!w){ alert('Connect wallet first to select already issued Foodies NFT.'); return; }
      const reBox=document.getElementById('reissuePanel');
      if(reBox){ reBox.style.display='block'; reBox.scrollIntoView({behavior:'smooth',block:'start'}); }
      document.querySelector('[name="issue_type"][value="reissue"]')?.click();
      if(window.loadIssuedFoodies) window.loadIssuedFoodies();
    };

    document.getElementById('foodiesLocalConnect').onclick=async function(){
      if(window.tonConnectUI&&window.tonConnectUI.connectWallet){
        const r=await window.tonConnectUI.connectWallet();
        const w=r&&r.account&&r.account.address?r.account.address:'';
        if(w){localStorage.setItem('ton_wallet',w);localStorage.setItem('wallet_address',w);window.__991WalletAddress=w;}
      }else{
        const w=prompt('Paste TON wallet address');
        if(w){localStorage.setItem('ton_wallet',w.trim());localStorage.setItem('wallet_address',w.trim());window.__991WalletAddress=w.trim();}
      }
      refresh();
      if(window.loadIssuedFoodies) window.loadIssuedFoodies();
    };
    refresh();
  }
  function refresh(){
    const w=wallet();
    const btn=document.getElementById('foodiesLocalConnect');
    const st=document.getElementById('foodiesLoginStatus');
    const ow=document.getElementById('ownerWallet');
    if(btn) btn.textContent=short(w);
    if(ow) ow.value=w;
    if(st){
      st.textContent=w?'LOGIN: '+short(w):'LOGIN: OFF';
      st.className=w?'login-ok':'login-bad';
    }
  }
  document.addEventListener('DOMContentLoaded',build);
  setInterval(refresh,1000);
})();
