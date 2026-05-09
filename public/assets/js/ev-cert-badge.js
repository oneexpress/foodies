(function(){
  function getProductId(){
    const url = new URL(window.location.href);
    return url.searchParams.get('product_id');
  }

  async function loadCert(){
    const pid = getProductId();
    if(!pid) return;

    try{
      const res = await fetch('/cert/api/by-product.php?product_id='+pid);
      const j = await res.json();
      if(!j.ok) return;

      const box = document.createElement('div');
      box.style.cssText = 'margin:10px 0;padding:10px 12px;border-radius:12px;background:#16161d;border:1px solid #2d2d38;color:#fff;font-weight:700';

      box.innerHTML =
        '🏅 ONE Cert<br>' +
        '<span style="color:#e60012;font-weight:900">'+j.code+'</span><br>' +
        '<a href="/cert/verify.php?code='+encodeURIComponent(j.code)+'" style="color:#22c55e;font-size:13px">Verify Certificate</a>';

      const target =
        document.querySelector('.product-info') ||
        document.querySelector('#product') ||
        document.querySelector('.product-title') ||
        document.body;

      target.prepend(box);

    }catch(e){}
  }

  if(document.readyState==='loading'){
    document.addEventListener('DOMContentLoaded',loadCert);
  }else{
    loadCert();
  }
})();
