
/* ExpressVisa live vUSDT wallet pill */
(function(){
  async function updateVusdtPill(){
    try{
      var ton = (document.cookie.match(/(?:^|; )ev_ton_wallet=([^;]*)/)||[])[1];
      var url = '/wallet/api/status.php';
      if(ton) url += '?ton_wallet=' + encodeURIComponent(decodeURIComponent(ton));

      var res = await fetch(url, {credentials:'same-origin'});
      var json = await res.json();
      if(!json.ok) return;

      var text = Number(json.balance || 0).toFixed(2) + ' vUSDT';

      document.querySelectorAll('a,button,span,div').forEach(function(el){
        var t = (el.textContent || '').trim();
        if(t.includes('0.00 vUSDT') || t.includes('vUSDT')){
          if(t.length < 30) el.textContent = '💳 · ' + text;
        }
      });
    }catch(e){}
  }

  if(document.readyState === 'loading'){
    document.addEventListener('DOMContentLoaded', updateVusdtPill);
  }else{
    updateVusdtPill();
  }
})();
