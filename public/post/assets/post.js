(function(){
  const $ = id => document.getElementById(id);

  function targetMode(){
    return ($('target')?.value === 'foreign') ? 'foreign' : 'china';
  }

  function isChina(){
    return targetMode() === 'china';
  }

  function label(row){
    if (isChina()) return row.name_zh || row.label_zh || row.name || row.code || row.slug || '';
    return row.name_en || row.label_en || row.name || row.code || row.slug || '';
  }

  async function loadOptions(type, elId, extra = '') {
    const el = $(elId);
    if (!el) return;

    const res = await fetch('/post/api/options.php?type=' + encodeURIComponent(type) + extra, {
      cache: 'no-store',
      credentials: 'same-origin'
    });

    const j = await res.json();
    const rows = j.rows || j.items || [];

    el.innerHTML = '<option value="">' + (isChina() ? '请选择' : 'Select') + '</option>';

    rows.forEach(r => {
      const opt = document.createElement('option');
      opt.value = r.slug || r.code || r.id || '';
      opt.innerText = label(r);
      opt.dataset.zh = r.name_zh || r.label_zh || r.zh || '';
      opt.dataset.en = r.name_en || r.label_en || r.en || '';
      el.appendChild(opt);
    });
  }

  const lockedSubcats = {
    'svc-marketplace': [
      ['foodtruck-rental','餐车出租','Foodtruck Rental'],
      ['foodtruck-sale','餐车出售','Foodtruck Sale'],
      ['foodtruck-franchise','餐车加盟','Foodtruck Franchise'],
      ['foodtruck-supplier','餐车供应商','Foodtruck Supplier'],
      ['foodtruck-equipment','餐车设备','Foodtruck Equipment'],
      ['foodtruck-location-rent','餐车地点出租','Foodtruck Location Rent'],
      ['foodtruck-event-booking','活动餐车预订','Foodtruck Event Booking'],
      ['foodtruck-renovation','餐车装修','Foodtruck Renovation'],
      ['foodtruck-permit','餐车执照','Foodtruck Permit'],
      ['foodtruck-loan','餐车贷款','Foodtruck Loan']
    ],
    'svc-jobs-posting': [
      ['fw-construction','建筑工','Construction'],
      ['fw-factory','工厂工','Factory'],
      ['fw-plantation','种植园','Plantation'],
      ['fw-cleaning','清洁工','Cleaning'],
      ['fw-restaurant','餐饮工','Restaurant'],
      ['fw-domestic-helper','家庭帮佣','Domestic Helper'],
      ['fw-driver','司机','Driver'],
      ['fw-security','保安','Security'],
      ['fw-warehouse','仓库工','Warehouse'],
      ['fw-general-labour','普通劳工','General Labour']
    ]
  };

  function loadSubcategories(){
    const el = $('subcategory') || $('subcategory_slug');
    const cat = $('category') || $('category_slug');
    if (!el || !cat) return;

    const rows = lockedSubcats[cat.value] || [['general','一般','General']];
    el.innerHTML = '';

    rows.forEach(r => {
      const opt = document.createElement('option');
      opt.value = r[0];
      opt.innerText = isChina() ? r[1] : r[2];
      opt.dataset.zh = r[1];
      opt.dataset.en = r[2];
      el.appendChild(opt);
    });
  }

  async function loadSubLocations(){
    const loc = $('location');
    const sub = $('subloc') || $('sub_location') || $('sub_location_slug');
    if (!loc || !sub) return;

    await loadOptions('sublocations', sub.id, '&loc=' + encodeURIComponent(loc.value || ''));
  }

  function syncHidden(){
    const map = [
      ['nationality','nationality_zh','nationality_en'],
      ['category','category_zh','category_en'],
      ['subcategory','subcategory_zh','subcategory_en'],
      ['location','location_zh','location_en'],
      ['subloc','sub_location_zh','sub_location_en']
    ];

    map.forEach(([src, zh, en]) => {
      const el = $(src);
      const opt = el?.selectedOptions?.[0];
      if ($(zh)) $(zh).value = opt?.dataset.zh || '';
      if ($(en)) $(en).value = opt?.dataset.en || '';
    });
  }

  window.previewImage = function(input) {
    const file = input.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = e => {
      const box = $('imgPreview');
      if (!box) return;
      if (box.tagName === 'IMG') box.src = e.target.result;
      else box.innerHTML = '<img src="' + e.target.result + '" style="width:100%;height:100%;object-fit:cover">';
    };
    reader.readAsDataURL(file);
  };

  window.validatePost = function(){
    syncHidden();

    const zhTitle = $('title_zh')?.value.trim() || '';
    const enTitle = $('title_en')?.value.trim() || '';
    const zhDesc = $('description_zh')?.value.trim() || '';
    const enDesc = $('description_en')?.value.trim() || '';
    const wa = $('wa')?.value.trim() || '';

    if (isChina()) {
      if (!zhTitle) { alert('请填写中文标题'); return false; }
      if (!zhDesc) { alert('请填写中文描述'); return false; }
    } else {
      if (!enTitle) { alert('Please enter English title'); return false; }
      if (!enDesc) { alert('Please enter English description'); return false; }
    }

    if (!wa) { alert('WhatsApp required'); return false; }
    return true;
  };

  async function init(){
    await loadOptions('nationalities', 'nationality', '&target=' + targetMode());
    await loadOptions('categories', 'category');
    await loadOptions('locations', 'location');

    loadSubcategories();
    await loadSubLocations();
    syncHidden();

    $('target')?.addEventListener('change', init);
    $('category')?.addEventListener('change', () => { loadSubcategories(); syncHidden(); });
    $('location')?.addEventListener('change', loadSubLocations);

    ['nationality','category','subcategory','location','subloc'].forEach(id => {
      $(id)?.addEventListener('change', syncHidden);
    });
  }

  document.addEventListener('DOMContentLoaded', init);
})();
