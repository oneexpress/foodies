async function loadOptions(type, elId) {
    const res = await fetch('/post/api/options.php?type=' + type);
    const j = await res.json();

    const el = document.getElementById(elId);
    el.innerHTML = '<option value="">Select</option>';

    j.rows.forEach(r => {
        let opt = document.createElement('option');
        opt.value = r.code;
        opt.innerText = r.name;
        el.appendChild(opt);
    });
}

function previewImage(input) {
    const file = input.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('imgPreview').src = e.target.result;
    };
    reader.readAsDataURL(file);
}

async function submitPost() {
    const fd = new FormData();

    fd.append('target', document.getElementById('target').value);
    fd.append('listing_type', 'general');
    fd.append('title', document.getElementById('title_en').value);
    fd.append('description', document.getElementById('description_en').value);
    fd.append('price', document.getElementById('price').value);
    fd.append('wa', document.getElementById('wa').value);

    const res = await fetch('/post/submit.php', {
        method: 'POST',
        body: fd
    });

    const j = await res.json();

    if (j.ok) {
        alert("Posted: " + j.ref);
        location.reload();
    } else {
        alert("Error");
    }
}

window.onload = () => {
    loadOptions('category','category_slug');
    loadOptions('nationality','nationality');
    loadOptions('location','location');
};
