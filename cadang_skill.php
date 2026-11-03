<?php
require_once __DIR__.'/config.php';
requireLogin(); // ensure logged in (user, not admin)
?>
<!doctype html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Cadang Kemahiran</title>
    <link rel="stylesheet" href="style.css?v=<?=time()?>">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="glass-card">
<div class="container">
    <h2 class="card-title"><i class="fa-solid fa-brain"></i> Cadang Kemahiran</h2>
    <p class="card-subtitle">Pengguna biasa dapat mencadangkan kemahiran baru. Status akan disetkan “KIV” secara automatik.</p>

    <form id="skillForm" class="form-grid"
          style="grid-template-columns: repeat(auto-fit, minmax(260px,1fr));">
        <div class="form-group">
            <label class="form-label" for="skill_name">1. Nama Kemahiran</label>
            <input type="text" id="skill_name" name="skill_name"
                   class="custom-input" placeholder="Contoh: Analisis Data" required>
        </div>

        <div class="form-group">
            <label class="form-label" for="skill_type">2. Jenis Kemahiran</label>
            <select id="skill_type" name="skill_type" class="custom-select" required>
                <option value="">-- Pilih Jenis --</option>
                <option value="technical">Technical (Teknikal)</option>
                <option value="soft">Soft (Insaniah)</option>
            </select>
            <i class="fa-solid fa-chevron-down select-arrow"></i>
        </div>

        <div class="form-group" style="grid-column: 1 / -1;">
            <label class="form-label" for="skill_description">
                3. Keterangan (optional)
            </label>
            <textarea id="skill_description" name="skill_description"
                      class="custom-input" rows="3"
                      placeholder="Berikan penerangan ringkas mengenai kemahiran ini"></textarea>
        </div>

        <div class="form-group" style="grid-column: 1 / -1;">
            <button type="submit" class="btn-submit">
                <i class="fa-solid fa-plus"></i> Simpan Cadangan
            </button>
        </div>
    </form>

    <div id="alertBox" class="alert-box" style="display:none;"></div>
</div>

<script src="script.js"></script>
<script>
document.getElementById('skillForm').addEventListener('submit', e => {
    e.preventDefault();
    const form = e.target;
    const data = new FormData(form);
    data.append('action', 'suggest_skill');

    fetch('api.php', {
        method: 'POST',
        body: data
    })
    .then(r=>r.json())
    .then(res => {
        const box = document.getElementById('alertBox');
        box.className = `alert-box alert-${res.status}`;
        box.innerHTML = `<span>${res.message}</span>`;
        box.style.display = 'block';
        if (res.status === 'success') form.reset();
    })
    .catch(() => {
        const box = document.getElementById('alertBox');
        box.className = 'alert-box alert-error';
        box.innerHTML = '<span>Ralat sambungan.</span>';
        box.style.display = 'block';
    });
});
</script>
</body>
</html>

