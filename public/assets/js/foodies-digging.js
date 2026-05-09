(function(){
  const API = '/rewards/api/digging.php';
  async function status(){
    try{
      const r = await fetch(API + '?action=status');
      const j = await r.json();
      document.querySelectorAll('[data-foodies-vshare]').forEach(el => {
        el.textContent = j.vShare ? ('vShare: ' + j.vShare) : 'vShare: 0.0000';
      });
      document.querySelectorAll('[data-foodies-rate]').forEach(el => {
        el.textContent = j.rate_text || '0.3300 vShare / 10s';
      });
    }catch(e){}
  }
  document.addEventListener('DOMContentLoaded', status);
  setInterval(status, 10000);
})();
