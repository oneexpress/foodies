/* 991 canonical bottom nav v20260508-sfx-no-type */
(function(){
  if(window.__EV991_CANONICAL_BOTTOM_NAV__) return;
  window.__EV991_CANONICAL_BOTTOM_NAV__=true;

  const L={
    home:"/",
    post:"/post/",
    jobs:"https://expressvisa.one/community/t/svc-jobs-posting",
    foodtruck:"https://expressvisa.one/marketplace/",
    visa:"https://expressvisa.one/community/t/svc-visa-permit",
    issueFoodies:"https://expressvisa.one/foodies-nft/",
    foodiesMarket:"https://getgems.io/foodies",
    booking:"/booking/",
    wallet:"/wallet/",
    rewards:"/rewards/",
    loan:"https://expressvisa.one/china/community/t/svc-agency-helpdesk",
    mm2h:"https://expressvisa.one/china/community/t/mm2h",
    mpv:"https://expressvisa.one/china/community/t/car-rental",
    homestay:"https://expressvisa.one/china/community/t/svc-accommodation",
    pets:"https://expressvisa.one/china/community/t/pets-services"
  };

  const sfxBase="/assets/sfx/";
  const sfx={};

  function play(name){
    try{
      const map={
        click:"click.mp3",
        gold:"click_gold.mp3",
        success:"success.mp3",
        error:"error.mp3",
        mining:"mining_start.mp3",
        mining_done:"mining_done.mp3",
        alert:"alert.mp3"
      };
      const file=map[name]||"click.mp3";
      if(!sfx[name]){
        sfx[name]=new Audio(sfxBase+file);
        sfx[name].preload="auto";
      }
      sfx[name].currentTime=0;
      sfx[name].volume=0.55;
      sfx[name].play().catch(()=>{});
    }catch(e){}
  }

  function lang(){return localStorage.getItem("ev991_lang")==="en"?"en":"zh";}
  function T(zh,en){return lang()==="zh"?zh:en;}

  function connected(){
    return localStorage.getItem("ev_wallet_connected")==="1" ||
      localStorage.getItem("ton_wallet_connected")==="1" ||
      document.cookie.indexOf("ev_ton_address=")>=0 ||
      document.cookie.indexOf("ton_wallet=")>=0;
  }

  function closePanels(except){
    document.querySelectorAll(".ev991-panel").forEach(function(p){
      if(p.id!==except) p.removeAttribute("data-open");
    });
  }

  async function refreshWallet(){
    const pill=document.getElementById("ev991WalletPill");
    if(!pill) return;

    let bal="0.00";
    let on=connected();

    try{
      const r=await fetch("/api/auth/status.php?_="+Date.now(),{
        credentials:"include",
        cache:"no-store"
      });
      const j=await r.json();
      if(j && j.ok){
        if(j.balance_vusdt) bal=j.balance_vusdt;
        if(typeof j.wallet_connected!=="undefined") on=!!j.wallet_connected;
      }
    }catch(e){}

    const led=pill.querySelector(".ev991-wallet-led");
    const balEl=pill.querySelector(".ev991-wallet-balance");
    const st=pill.querySelector(".ev991-wallet-status");

    if(led) led.className="ev991-wallet-led "+(on?"on":"off");
    if(balEl) balEl.textContent=bal+" vUSDT";
    if(st) st.textContent=on?"CONNECTED":"CONNECT WALLET";
  }

  function mount(){
    document.querySelectorAll(".ev991-bottom-nav,.ev991-panel,.ev991-wallet-pill").forEach(function(e){
      e.remove();
    });

    const nav=document.createElement("nav");
    nav.className="ev991-bottom-nav";
    nav.innerHTML=
      '<select id="ev991Lang"><option value="zh">中</option><option value="en">EN</option></select>'+
      '<a href="'+L.home+'">🏠 '+T("首页","Home")+'</a>'+
      '<a class="ev991-hi" href="'+L.post+'">➕ '+T("免费发布","Free Ads")+'</a>'+
      '<a href="'+L.jobs+'">💼 '+T("招聘","Jobs")+'</a>'+
      '<a class="ev991-foodtruck" href="'+L.foodtruck+'">🍔🚚 '+T("餐车商机","Foodtruck")+'</a>'+
      '<a href="'+L.visa+'">🛂 '+T("签证","Visa")+'</a>'+
      '<button id="ev991ServicesBtn" type="button">☰ '+T("服务菜单","Services")+'</button>'+
      '<button id="ev991FoodiesBtn" class="ev991-foodies" type="button">⭐ Foodies NFT</button>'+
      '<a class="ev991-hi" href="'+L.booking+'">🧾 '+T("预约","Booking")+'</a>'+
      '<a class="ev991-hi" href="'+L.wallet+'">💳 '+T("充值 vUSDT","Reload vUSDT")+'</a>'+
      '<a class="ev991-dig" href="'+L.rewards+'">⛏️ '+T("挖呀挖呀挖!","Start Digging")+'</a>'+
      '<a class="ev991-hi" href="'+L.loan+'">💰 '+T("网贷","Online Loan")+'</a>';
    document.body.appendChild(nav);

    const services=document.createElement("div");
    services.id="ev991ServicesPanel";
    services.className="ev991-panel";
    services.innerHTML=
      '<a href="'+L.mm2h+'">🏠 '+T("第二家园","MM2H")+'</a>'+
      '<a href="'+L.mpv+'">🚐 '+T("豪华 MPV","Luxury MPV")+'</a>'+
      '<a href="'+L.homestay+'">🏡 '+T("民宿","Homestay")+'</a>'+
      '<a href="'+L.pets+'">🐶 '+T("宠物","Pets")+'</a>';
    document.body.appendChild(services);

    const foodies=document.createElement("div");
    foodies.id="ev991FoodiesPanel";
    foodies.className="ev991-panel";
    foodies.innerHTML=
      '<a href="'+L.issueFoodies+'">🎟️ Issue Foodies NFT</a>'+
      '<a href="'+L.foodiesMarket+'" target="_blank" rel="noopener">🛒 NFT Marketplace</a>';
    document.body.appendChild(foodies);

    const pill=document.createElement("a");
    pill.id="ev991WalletPill";
    pill.className="ev991-wallet-pill";
    pill.href=L.wallet;
    pill.innerHTML=
      '<span class="ev991-wallet-led off"></span>'+
      '<img src="/metadata/991_visa_logo_only.png" alt="991">'+
      '<span class="ev991-wallet-balance">0.00 vUSDT</span>'+
      '<span class="ev991-wallet-sep"></span>'+
      '<span class="ev991-wallet-status">CONNECT WALLET</span>';
    document.body.appendChild(pill);

    const sel=document.getElementById("ev991Lang");
    if(sel){
      sel.value=lang();
      sel.onchange=function(){
        play("click");
        localStorage.setItem("ev991_lang",this.value);
        mount();
      };
    }

    document.getElementById("ev991ServicesBtn").onclick=function(e){
      e.stopPropagation();
      play("click");
      closePanels("ev991ServicesPanel");
      services.toggleAttribute("data-open");
      if(services.hasAttribute("data-open")) services.setAttribute("data-open","1");
    };

    document.getElementById("ev991FoodiesBtn").onclick=function(e){
      e.stopPropagation();
      play("success");
      closePanels("ev991FoodiesPanel");
      foodies.toggleAttribute("data-open");
      if(foodies.hasAttribute("data-open")) foodies.setAttribute("data-open","1");
    };

    document.addEventListener("click",function(){closePanels("");});

    document.querySelectorAll(".ev991-bottom-nav a,.ev991-bottom-nav button,.ev991-wallet-pill,.ev991-panel a").forEach(function(el){
      el.addEventListener("click",function(){
        if(el.id==="ev991FoodiesBtn" || el.classList.contains("ev991-foodies")) play("success");
        else if(el.classList.contains("ev991-dig")) play("mining");
        else if(el.classList.contains("ev991-hi") || el.classList.contains("ev991-foodtruck")) play("gold");
        else play("click");
      },true);
    });

    refreshWallet();
  }

  if(document.readyState==="loading") document.addEventListener("DOMContentLoaded",mount);
  else mount();

  setInterval(refreshWallet,4000);
})();
