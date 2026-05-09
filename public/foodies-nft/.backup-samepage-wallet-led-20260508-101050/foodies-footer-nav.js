(function(){
'use strict';
if(window.__FOODIES991NAV__) return;
window.__FOODIES991NAV__=1;

function wallet(){
  return (
    window.__991WalletAddress ||
    localStorage.getItem('ton_wallet') ||
    localStorage.getItem('wallet_address') ||
    localStorage.getItem('connected_wallet') ||
    ''
  ).trim();
}

function shortAddr(w){
  if(!w || w.length < 16) return w;
  return w.slice(0,6)+'...'+w.slice(-6);
}

async function connectWallet(e){
  if(e)e.preventDefault();

  try{

    if(window.tonConnectUI && typeof window.tonConnectUI.openModal==='function'){
      await window.tonConnectUI.openModal();
      return false;
    }

    if(window.__991TonConnectUI && typeof window.__991TonConnectUI.openModal==='function'){
      await window.__991TonConnectUI.openModal();
      return false;
    }

    if(window.TON_CONNECT_UI && typeof window.TON_CONNECT_UI.openModal==='function'){
      await window.TON_CONNECT_UI.openModal();
      return false;
    }

    const btn=document.getElementById('walletConnectBtn');
    if(btn){
      btn.classList.add('active');
      btn.querySelector('.fnav-text').textContent='Wallet Loader...';
    }

    window.dispatchEvent(new CustomEvent('991-connect-wallet'));

    setTimeout(()=>{
      const w=
        window.__991WalletAddress ||
        localStorage.getItem('ton_wallet') ||
        localStorage.getItem('wallet_address') ||
        localStorage.getItem('connected_wallet') ||
        '';

      if(w){
        render();
      }
    },1800);

  }catch(err){
    console.error(err);
  }

  return false;
}

function mode(){
  const q=new URLSearchParams(location.search);
  return q.get('mode') || '';
}

function item(label,icon,href,active,extra){
  return `
  <a class="fnav-btn ${active?'active':''} ${extra||''}" href="${href}">
    <span class="fnav-icon">${icon}</span>
    <span class="fnav-text">${label}</span>
  </a>`;
}

function render(){
  document.querySelectorAll('.foodies991nav').forEach(x=>x.remove());

  const w=wallet();
  const m=mode();

  const nav=document.createElement('div');
  nav.className='foodies991nav';

  nav.innerHTML=`
  <style>
  .foodies991nav{
    position:fixed;
    left:50%;
    transform:translateX(-50%);
    bottom:10px;
    width:min(1180px,calc(100vw - 14px));
    background:#fff;
    border-radius:26px;
    box-shadow:0 14px 40px rgba(0,0,0,.18);
    z-index:999999;
    border:1px solid #e5e7eb;
    overflow:hidden;
  }

  .foodies991nav-row{
    display:flex;
    gap:10px;
    padding:10px;
    align-items:center;
    justify-content:space-between;
  }

  .fnav-btn{
    flex:1;
    min-height:68px;
    border-radius:18px;
    text-decoration:none;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    gap:5px;
    color:#111;
    font-weight:900;
    transition:.18s ease;
  }

  .fnav-btn:hover{
    background:#E60012;
    color:#fff;
  }

  .fnav-btn.active{
    background:#E60012;
    color:#fff;
  }

  .fnav-icon{
    width:34px;
    height:34px;
    border-radius:12px;
    background:#E60012;
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:17px;
    font-weight:900;
  }

  .fnav-text{
    font-size:12px;
    line-height:1;
  }

  .wallet-status{
    padding:6px 12px;
    text-align:center;
    border-top:1px solid #eee;
    background:#fff5f5;
    color:#B8000E;
    font-size:11px;
    font-weight:900;
  }

  @media(max-width:700px){
    .foodies991nav-row{
      gap:4px;
      padding:6px;
    }

    .fnav-btn{
      min-height:58px;
    }

    .fnav-icon{
      width:28px;
      height:28px;
      font-size:14px;
    }

    .fnav-text{
      font-size:10px;
    }
  }
  </style>

  <div class="foodies991nav-row">

    ${item('Home','⌂','https://expressvisa.one/',false)}

    ${item('Launcher','◎','/foodies-nft/',!m)}

    ${item('Issue','★','/foodies-nft/?mode=issue',m==='issue')}

    ${item('Re-Issue','♻','/foodies-nft/?mode=reissue',m==='reissue'||m==='list')}

    <a class="fnav-btn ${w?'active':''}" href="/wallet/" id="walletConnectBtn">
      <span class="fnav-icon">${w?'✓':'⌁'}</span>
      <span class="fnav-text">${w?shortAddr(w):'Connect Wallet'}</span>
    </a>

  </div>

  <div class="wallet-status">
    ${w ? 'Wallet Connected : '+shortAddr(w) : 'Wallet Not Connected'}
  </div>
  `;

  document.body.appendChild(nav);

  const btn=document.getElementById('walletConnectBtn');
  if(btn){
    
btn.addEventListener('click',function(e){
  e.preventDefault();
  connectWallet(e);
  return false;
});

  }
}

if(document.readyState==='loading'){
  document.addEventListener('DOMContentLoaded',render);
}else{
  render();
}

setTimeout(render,600);

})();
