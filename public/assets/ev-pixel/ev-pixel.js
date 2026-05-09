(function(){
  var cfg = window.EV_PIXEL_CONFIG || {};
  if(!cfg.enabled) return;

  var path = location.pathname || "/";
  var paths = cfg.trackPaths || ["/"];
  var allowed = paths.some(function(p){ return path.indexOf(p) === 0; });
  if(!allowed) return;

  function send(name, data){
    data = data || {};
    data.brand = "ExpressVisa";
    data.path = location.pathname;
    data.url = location.href;
    data.ts = Date.now();

    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push(Object.assign({event:name}, data));

    try { if(window.fbq) fbq("trackCustom", name, data); } catch(e){}
    try { if(window.ttq) ttq.track(name, data); } catch(e){}
    try { if(window.gtag) gtag("event", name, data); } catch(e){}

    try {
      if(navigator.sendBeacon){
        navigator.sendBeacon(
          "/api/pixel/collect.php",
          new Blob([JSON.stringify({event:name,data:data})], {type:"application/json"})
        );
      }
    } catch(e){}
  }

  window.evPixelEvent = send;
  send("EV_PageView");

  document.addEventListener("click", function(e){
    var a = e.target.closest && e.target.closest("a,button");
    if(!a) return;
    var txt = (a.textContent || "").trim();
    var href = a.getAttribute("href") || "";

    if(href.indexOf("/post") >= 0) send("EV_PostStart",{label:txt,href:href});
    if(href.indexOf("/boost") >= 0) send("EV_BoostClick",{label:txt,href:href});
    if(href.indexOf("/wallet") >= 0) send("EV_WalletOpen",{label:txt,href:href});
    if(href.indexOf("/cert/verify") >= 0) send("EV_CertVerifyClick",{label:txt,href:href});
    if(href.indexOf("sync-engine") >= 0 || txt.toLowerCase().indexOf("sync") >= 0) send("EV_AdminSyncClick",{label:txt,href:href});
  });
})();
