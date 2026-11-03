<?php
/**
 * Public Form - Cadang / Maklumkan Institusi IPT Baharu
 * Normal user form to submit new institute into `temp_institutes`
 * Note: Normal user CANNOT view or edit the "status" field.
 */
require_once __DIR__ . '/config.php';

$db = getDBConnection();
$states = $db->query("SELECT id, state_name FROM states ORDER BY state_name ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ms">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadang Institusi (IPT) Baharu - Sistem Tinjauan IPT</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .form-card-wrapper {
            max-width: 650px;
            margin: 2rem auto;
        }

        .info-notice {
            background: rgba(56, 189, 248, 0.1);
            border-left: 4px solid var(--primary-glow, #38bdf8);
            border-radius: 8px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            line-height: 1.5;
            color: #e2e8f0;
        }

        .info-notice i {
            color: var(--primary-glow, #38bdf8);
            margin-right: 0.4rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Reusable Navigation Bar -->
        <nav class="navbar">
            <div class="brand">
                <i class="fa-solid fa-building-columns brand-icon"></i>
                <span>Sistem Tinjauan IPT</span>
            </div>
            <div class="nav-links">
                <a href="index.php" class="nav-btn">
                    <i class="fa-solid fa-list-check"></i> Borang Tinjauan
                </a>
                <a href="cadang_institusi.php" class="nav-btn active">
                    <i class="fa-solid fa-school-flag"></i> Cadang IPT
                </a>
                <a href="cadang_kursus.php" class="nav-btn">
                    <i class="fa-solid fa-graduation-cap"></i> Cadang Kursus
                </a>
                <?php if (isLoggedIn()): ?>
                    <a href="admin.php" class="nav-btn">
                        <i class="fa-solid fa-sliders"></i> Panel Admin
                    </a>
                <?php else: ?>
                    <a href="login.php" class="nav-btn">
                        <i class="fa-solid fa-right-to-bracket"></i> Log Masuk Admin
                    </a>
                <?php endif; ?>
            </div>
        </nav>

        <div class="form-card-wrapper">
            <main class="glass-card">
                <h1 class="card-title">
                    <i class="fa-solid fa-school-flag" style="color: var(--primary-glow);"></i>
                    Maklum / Cadang Institusi (IPT) Baharu
                </h1>
                <p class="card-subtitle">
                    Jika institusi pengajian tinggi (IPT) anda tidak tersenarai di dalam borang tinjauan, sila lengkapkan maklumat di bawah untuk memaklumkan kepada Pentadbir Sistem.
                </p>

                <div class="info-notice">
                    <i class="fa-solid fa-circle-info"></i>
                    <strong>Pemberitahuan:</strong> Maklumat institusi yang dihantar akan disalurkan terus kepada Pentadbir Sistem untuk disemak dan diluluskan sebelum dikemaskini ke senarai pilihan rasmi.
                </div>

                <div id="alertBox" class="alert-box" style="display: none;"></div>

                <form id="suggestInstituteForm" autocomplete="off">
                    <div class="form-grid" style="grid-template-columns: 1fr;">

                        <!-- Select State -->
                        <div class="form-group">
                            <label class="form-label" for="state_id">
                                <span>1. Pilih Negeri Institusi</span>
                                <span class="badge-step">Wajib</span>
                            </label>
                            <div class="select-wrapper">
                                <select id="state_id" name="state_id" class="custom-select" required>
                                    <option value="">-- Pilih Negeri --</option>
                                    <?php foreach ($states as $st): ?>
                                        <option value="<?php echo $st['id']; ?>">
                                            <?php echo htmlspecialchars($st['state_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <i class="fa-solid fa-chevron-down select-arrow"></i>
                            </div>
                        </div>

                        <!-- Institute Name (Normal user keys in) -->
                        <div class="form-group">
                            <label class="form-label" for="institute_name">
                                <span>2. Nama Penuh Institusi / IPT</span>
                                <span class="badge-step">Wajib</span>
                            </label>
                            <input type="text" id="institute_name" name="institute_name" class="custom-input"
                                placeholder="Contoh: Universiti Sultan Azlan Shah (USAS) / ADTEC Kulim" required>
                            <small style="color: var(--text-muted); font-size: 0.8rem; margin-top: 0.35rem; display: block;">
                                Sila masukkan nama penuh beserta singkatan jika ada.
                            </small>
                        </div>

                        <!-- NOTE: Status is completely HIDDEN from normal users as per system requirement -->

                        <!-- Action Buttons -->
                        <div style="display: flex; gap: 0.75rem; align-items: center; margin-top: 1rem; flex-wrap: wrap;">
                            <button type="submit" id="btnSubmitSuggest" class="btn-submit" style="flex: 1; min-width: 200px;">
                                <i class="fa-solid fa-paper-plane"></i> Hantar Cadangan Institusi
                            </button>
                            <a href="index.php" class="btn-secondary" style="text-decoration: none; padding: 0.65rem 1.25rem;">
                                <i class="fa-solid fa-arrow-left"></i> Kembali ke Borang
                            </a>
                        </div>

                    </div>
                </form>
            </main>
        </div>

        <footer>
            <p>&copy; <?php echo date('Y'); ?> Sistem Tinjauan Program Kejuruteraan IPT Malaysia | Develop by : TD9 KISMEC</p>
        </footer>
    </div>

    <script>
        const form = document.getElementById('suggestInstituteForm');
        const alertBox = document.getElementById('alertBox');
        const submitBtn = document.getElementById('btnSubmitSuggest');

        function showAlert(msg, type = 'info') {
            alertBox.style.display = 'block';
            alertBox.className = `alert-box alert-${type}`;
            const icons = {
                success: 'fa-circle-check',
                error: 'fa-circle-exclamation',
                warning: 'fa-triangle-exclamation',
                info: 'fa-circle-info'
            };
            const icon = icons[type] || 'fa-circle-info';
            alertBox.innerHTML = `<i class="fa-solid ${icon}"></i> <span>${msg}</span>`;
            alertBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const stateId = document.getElementById('state_id').value;
            const instituteName = document.getElementById('institute_name').value.trim();

            if (!stateId || !instituteName) {
                showAlert('Sila pilih negeri dan isi nama institusi.', 'error');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Sedang Menghantar...';

            const fd = new FormData();
            fd.append('action', 'suggest_institute');
            fd.append('state_id', stateId);
            fd.append('institute_name', instituteName);

            fetch('api.php', {
                method: 'POST',
                body: fd
            })
            .then(res => res.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Hantar Cadangan Institusi';

                if (data.status === 'success') {
                    showAlert(data.message, 'success');
                    form.reset();
                } else if (data.status === 'warning') {
                    showAlert(data.message, 'warning');
                } else if (data.status === 'info') {
                    showAlert(data.message, 'info');
                } else {
                    showAlert(data.message || 'Ralat semasa menghantar cadangan institusi.', 'error');
                }
            })
            .catch(err => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Hantar Cadangan Institusi';
                showAlert('Ralat sambungan pelayan: ' + err.message, 'error');
            });
        });
    </script>
</body>

</html>

