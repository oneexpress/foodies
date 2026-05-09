(function(){
  function qs(sel){ return document.querySelector(sel); }

  function field(name, label, afterSelector){
    let el = qs('[name="'+name+'"]');
    if (el) return el;

    const wrap = document.createElement('div');
    wrap.className = 'ev-tax-field';
    wrap.innerHTML =
      '<label>'+label+'</label>' +
      '<select name="'+name+'" required>' +
      '<option value="">Select '+label+'</option>' +
      '</select>';

    const after = qs(afterSelector);
    const parent = after ? (after.closest('.field,.form-group,.ev-field,div') || after.parentNode) : null;
    if (parent && parent.parentNode) parent.parentNode.insertBefore(wrap, parent.nextSibling);
    else {
      const form = qs('form');
      if (form) form.insertBefore(wrap, form.firstChild);
    }

    return wrap.querySelector('select');
  }

  function option(id, name){
    return '<option value="'+String(id).replace(/"/g,'&quot;')+'" data-name="'+String(name).replace(/"/g,'&quot;')+'">'+name+'</option>';
  }

  async function boot(){
    const form = qs('form');
    if (!form) return;

    const service = field('service_category_id','Service Category','[name="title"],[name="product_name"],input[type="text"]');
    const nationality = field('nationality_category_id','Nationality','[name="service_category_id"]');
    const location = field('location_category_id','Location','[name="nationality_category_id"]');
    const area = field('area_category_id','Sub Location','[name="location_category_id"]');

    const serviceName = field('service_name','Service Name','[name="area_category_id"]');
    const nationalityName = field('nationality_name','Nationality Name','[name="service_name"]');
    const locationName = field('location_name','Location Name','[name="nationality_name"]');
    const areaName = field('area_name','Area Name','[name="location_name"]');

    [serviceName,nationalityName,locationName,areaName].forEach(x=>{
      x.closest('.ev-tax-field').style.display='none';
      x.required=false;
    });

    const res = await fetch('/marketplace-submit/api/taxonomy.php?ts=' + Date.now(), {cache:'no-store'});
    const data = await res.json();
    if (!data.ok) throw new Error(data.error || 'taxonomy_failed');

    service.innerHTML = '<option value="">Select Service Category</option>' + data.services.map(x=>option(x.id,x.name)).join('');
    nationality.innerHTML = '<option value="">Select Nationality</option>' + data.nationalities.map(x=>option(x.id,x.name)).join('');
    location.innerHTML = '<option value="">Select Location</option>' + data.locations.map(x=>option(x.id,x.name)).join('');
    area.innerHTML = '<option value="">Select Sub Location</option>';

    location.addEventListener('change', function(){
      const loc = data.locations.find(x => String(x.id) === String(location.value));
      area.innerHTML = '<option value="">Select Sub Location</option>' + (loc ? loc.areas.map(x=>option(x.id,x.name)).join('') : '');
      locationName.value = location.options[location.selectedIndex]?.dataset.name || '';
      areaName.value = '';
    });

    service.addEventListener('change', function(){
      serviceName.value = service.options[service.selectedIndex]?.dataset.name || '';
    });

    nationality.addEventListener('change', function(){
      nationalityName.value = nationality.options[nationality.selectedIndex]?.dataset.name || '';
    });

    area.addEventListener('change', function(){
      areaName.value = area.options[area.selectedIndex]?.dataset.name || '';
    });

    form.addEventListener('submit', function(){
      serviceName.value = service.options[service.selectedIndex]?.dataset.name || '';
      nationalityName.value = nationality.options[nationality.selectedIndex]?.dataset.name || '';
      locationName.value = location.options[location.selectedIndex]?.dataset.name || '';
      areaName.value = area.options[area.selectedIndex]?.dataset.name || '';
    });
  }

  document.addEventListener('DOMContentLoaded', boot);
})();
