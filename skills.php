<?php
/**
 * Admin Skills Management Page - Add, View, and Delete Skills (Technical & Soft) for Diploma & Degree
 * System: Survey IPT Cascading Dynamic System
 */
require_once __DIR__ . '/config.php';
requireLogin();

$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="ms">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengurusan Kemahiran - Panel Admin IPT</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .container {
            width: 95% !important;
            max-width: 1200px !important;
        }

        .tab-nav {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 0.5rem;
        }

        .tab-btn {
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 0.95rem;
            font-weight: 600;
            padding: 0.6rem 1.2rem;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .tab-btn:hover {
            color: var(--text-main);
            background: rgba(255, 255, 255, 0.05);
        }

        .tab-btn.active {
            background: rgba(56, 189, 248, 0.15);
            color: var(--primary-glow);
            border: 1px solid rgba(56, 189, 248, 0.3);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        .table-responsive {
            max-height: 600px;
            overflow-y: auto;
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .table-responsive thead th {
            position: sticky;
            top: 0;
            z-index: 2;
        }

        .checkbox-custom {
            cursor: pointer;
            width: 16px;
            height: 16px;
            accent-color: #38bdf8;
        }

        .usage-badge {
            background: rgba(148, 163, 184, 0.15);
            color: #cbd5e1;
            padding: 0.2rem 0.55rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .usage-badge.active {
            background: rgba(16, 185, 129, 0.2);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .count-indicator {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: 0.5rem;
        }

        .badge-level.spm {
            background: rgba(249, 115, 22, 0.18);
            color: #fb923c;
            border: 1px solid rgba(249, 115, 22, 0.35);
        }

        .badge-level.skm {
            background: rgba(6, 182, 212, 0.18);
            color: #22d3ee;
            border: 1px solid rgba(6, 182, 212, 0.35);
        }

        .badge-level.diploma {
            background: rgba(245, 158, 11, 0.18);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.35);
        }

        .badge-level.degree {
            background: rgba(16, 185, 129, 0.18);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.35);
        }

        .badge-level.master {
            background: rgba(168, 85, 247, 0.18);
            color: #c084fc;
            border: 1px solid rgba(168, 85, 247, 0.35);
        }
    </style>
</head>

<body>

    <div class="container">
        <!-- Navigation Header -->
        <?php include __DIR__ . '/menu.php'; ?>

        <div id="adminAlert" class="alert-box"></div>

        <!-- KPI Summary Cards -->
        <div class="stats-grid">
            <div class="stat-card" style="--stat-accent: #38bdf8; --stat-icon-bg: rgba(56, 189, 248, 0.15);">
                <div class="stat-icon"><i class="fa-solid fa-layer-group"></i></div>
                <div class="stat-content">
                    <span class="stat-number" id="statTotalSkills">-</span>
                    <span class="stat-label">Jumlah Keseluruhan</span>
                </div>
            </div>

            <div class="stat-card" style="--stat-accent: #fb923c; --stat-icon-bg: rgba(249, 115, 22, 0.15);">
                <div class="stat-icon"><i class="fa-solid fa-book"></i></div>
                <div class="stat-content">
                    <span class="stat-number" id="statSpm">-</span>
                    <span class="stat-label">SPM</span>
                </div>
            </div>

            <div class="stat-card" style="--stat-accent: #22d3ee; --stat-icon-bg: rgba(6, 182, 212, 0.15);">
                <div class="stat-icon"><i class="fa-solid fa-wrench"></i></div>
                <div class="stat-content">
                    <span class="stat-number" id="statSkm">-</span>
                    <span class="stat-label">SKM</span>
                </div>
            </div>

            <div class="stat-card" style="--stat-accent: #fbbf24; --stat-icon-bg: rgba(245, 158, 11, 0.15);">
                <div class="stat-icon"><i class="fa-solid fa-certificate"></i></div>
                <div class="stat-content">
                    <span class="stat-number" id="statDiploma">-</span>
                    <span class="stat-label">Diploma</span>
                </div>
            </div>

            <div class="stat-card" style="--stat-accent: #34d399; --stat-icon-bg: rgba(16, 185, 129, 0.15);">
                <div class="stat-icon"><i class="fa-solid fa-graduation-cap"></i></div>
                <div class="stat-content">
                    <span class="stat-number" id="statDegree">-</span>
                    <span class="stat-label">Degree</span>
                </div>
            </div>

            <div class="stat-card" style="--stat-accent: #c084fc; --stat-icon-bg: rgba(168, 85, 247, 0.15);">
                <div class="stat-icon"><i class="fa-solid fa-user-graduate"></i></div>
                <div class="stat-content">
                    <span class="stat-number" id="statMaster">-</span>
                    <span class="stat-label">Master</span>
                </div>
            </div>
        </div>

        <!-- SECTION 1: ADD NEW SKILL CARD -->
        <section class="glass-card">
            <h2 class="card-title">
                <i class="fa-solid fa-circle-plus" style="color: var(--primary-glow);"></i>
                Tambah Kemahiran Baharu
            </h2>
            <p class="card-subtitle">
                Daftar kemahiran teknikal atau insaniah baharu untuk dipaparkan secara dinamik dalam borang tinjauan mengikut tahap pengajian.
            </p>

            <div class="tab-nav">
                <button type="button" class="tab-btn active" data-tab="single"><i class="fa-solid fa-plus"></i> Tambah Satu Kemahiran</button>
                <button type="button" class="tab-btn" data-tab="bulk"><i class="fa-solid fa-list-check"></i> Tambah Pukal (Senarai)</button>
            </div>

            <!-- Single Skill Form -->
            <div id="tab-single" class="tab-content active">
                <form id="addSkillForm" class="form-grid">
                    <div class="form-group">
                        <label class="form-label" for="skill_name">
                            <span>Nama Kemahiran</span>
                            <span class="badge-step">Wajib</span>
                        </label>
                        <input type="text" id="skill_name" name="skill_name" class="custom-input" placeholder="Contoh: Artificial Intelligence / Project Management / PLC Automation" required>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                        <!-- Skill Type Selection -->
                        <div class="form-group">
                            <label class="form-label">Jenis Kemahiran (Skill Type)</label>
                            <div class="radio-pill-group">
                                <label class="radio-pill active" id="pill_tech">
                                    <input type="radio" name="skill_type" value="technical" checked>
                                    <i class="fa-solid fa-microchip" style="color: #38bdf8;"></i> Teknikal (Technical)
                                </label>
                                <label class="radio-pill" id="pill_soft">
                                    <input type="radio" name="skill_type" value="soft">
                                    <i class="fa-solid fa-users-gear" style="color: #c084fc;"></i> Insaniah (Soft Skill)
                                </label>
                            </div>
                        </div>

                        <!-- Qualification Level Selection -->
                        <div class="form-group">
                            <label class="form-label">Tahap Pengajian (Qualification Level)</label>
                            <div class="radio-pill-group">
                                <label class="radio-pill" id="pill_spm">
                                    <input type="radio" name="qualification_level" value="SPM">
                                    <i class="fa-solid fa-book" style="color: #fb923c;"></i> SPM
                                </label>
                                <label class="radio-pill" id="pill_skm">
                                    <input type="radio" name="qualification_level" value="SKM">
                                    <i class="fa-solid fa-wrench" style="color: #22d3ee;"></i> SKM
                                </label>
                                <label class="radio-pill active" id="pill_diploma">
                                    <input type="radio" name="qualification_level" value="Diploma" checked>
                                    <i class="fa-solid fa-certificate" style="color: #fbbf24;"></i> Diploma
                                </label>
                                <label class="radio-pill" id="pill_degree">
                                    <input type="radio" name="qualification_level" value="Degree">
                                    <i class="fa-solid fa-graduation-cap" style="color: #34d399;"></i> Degree
                                </label>
                                <label class="radio-pill" id="pill_master">
                                    <input type="radio" name="qualification_level" value="Master">
                                    <i class="fa-solid fa-user-graduate" style="color: #c084fc;"></i> Master
                                </label>
                                <label class="radio-pill" id="pill_both">
                                    <input type="radio" name="qualification_level" value="Both">
                                    <i class="fa-solid fa-arrows-split-up-and-left" style="color: #818cf8;"></i> Kedua-duanya (Diploma & Degree)
                                </label>
                                <label class="radio-pill" id="pill_all">
                                    <input type="radio" name="qualification_level" value="All">
                                    <i class="fa-solid fa-layer-group" style="color: #f472b6;"></i> Semua Tahap (All Levels)
                                </label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit" style="margin-top: 0.5rem;">
                        <i class="fa-solid fa-plus"></i> Simpan Kemahiran
                    </button>
                </form>
            </div>

            <!-- Bulk Skill Form -->
            <div id="tab-bulk" class="tab-content">
                <form id="bulkSkillForm" class="form-grid">
                    <div class="form-group">
                        <label class="form-label" for="bulk_skills_text">
                            <span>Senarai Kemahiran (Pisahkan setiap kemahiran dengan baris baharu atau tanda koma)</span>
                            <span class="badge-step">Pukal</span>
                        </label>
                        <textarea id="bulk_skills_text" name="skills_text" class="custom-input" rows="4" style="height: auto; resize: vertical;" placeholder="Contoh:&#10;Python Programming&#10;Machine Learning&#10;Cloud Computing&#10;Scrum & Agile" required></textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                        <div class="form-group">
                            <label class="form-label">Jenis Kemahiran</label>
                            <div class="radio-pill-group">
                                <label class="radio-pill active" id="bulk_pill_tech">
                                    <input type="radio" name="skill_type" value="technical" checked>
                                    <i class="fa-solid fa-microchip" style="color: #38bdf8;"></i> Teknikal
                                </label>
                                <label class="radio-pill" id="bulk_pill_soft">
                                    <input type="radio" name="skill_type" value="soft">
                                    <i class="fa-solid fa-users-gear" style="color: #c084fc;"></i> Insaniah
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Tahap Pengajian</label>
                            <div class="radio-pill-group">
                                <label class="radio-pill" id="bulk_pill_spm">
                                    <input type="radio" name="qualification_level" value="SPM">
                                    <i class="fa-solid fa-book" style="color: #fb923c;"></i> SPM
                                </label>
                                <label class="radio-pill" id="bulk_pill_skm">
                                    <input type="radio" name="qualification_level" value="SKM">
                                    <i class="fa-solid fa-wrench" style="color: #22d3ee;"></i> SKM
                                </label>
                                <label class="radio-pill active" id="bulk_pill_diploma">
                                    <input type="radio" name="qualification_level" value="Diploma" checked>
                                    <i class="fa-solid fa-certificate" style="color: #fbbf24;"></i> Diploma
                                </label>
                                <label class="radio-pill" id="bulk_pill_degree">
                                    <input type="radio" name="qualification_level" value="Degree">
                                    <i class="fa-solid fa-graduation-cap" style="color: #34d399;"></i> Degree
                                </label>
                                <label class="radio-pill" id="bulk_pill_master">
                                    <input type="radio" name="qualification_level" value="Master">
                                    <i class="fa-solid fa-user-graduate" style="color: #c084fc;"></i> Master
                                </label>
                                <label class="radio-pill" id="bulk_pill_both">
                                    <input type="radio" name="qualification_level" value="Both">
                                    <i class="fa-solid fa-arrows-split-up-and-left" style="color: #818cf8;"></i> Kedua-duanya
                                </label>
                                <label class="radio-pill" id="bulk_pill_all">
                                    <input type="radio" name="qualification_level" value="All">
                                    <i class="fa-solid fa-layer-group" style="color: #f472b6;"></i> Semua Tahap
                                </label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit" style="margin-top: 0.5rem; background: linear-gradient(90deg, #10b981, #0284c7);">
                        <i class="fa-solid fa-layer-group"></i> Simpan Senarai Pukal
                    </button>
                </form>
            </div>
        </section>

        <!-- SECTION 2: SKILLS LIST & MANAGEMENT -->
        <section class="glass-card">
            <h2 class="card-title">
                <i class="fa-solid fa-table-list" style="color: var(--primary-glow);"></i>
                Senarai Kemahiran Terkini
            </h2>
            <p class="card-subtitle">
                Cari, tapis, sunting, dan padam kemahiran mengikut tahap pengajian.
            </p>

            <!-- Filter Toolbar -->
            <div class="filter-toolbar">
                <div class="filter-left">
                    <div class="filter-search-wrap">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="skillSearchInput" class="filter-search-input" placeholder="Cari nama kemahiran...">
                    </div>

                    <select id="filterLevel" class="filter-select">
                        <option value="all">Semua Tahap Pengajian</option>
                        <option value="SPM">SPM</option>
                        <option value="SKM">SKM</option>
                        <option value="Diploma">Diploma</option>
                        <option value="Degree">Degree</option>
                        <option value="Master">Master</option>
                    </select>

                    <select id="filterType" class="filter-select">
                        <option value="all">Semua Jenis Kemahiran</option>
                        <option value="technical">Teknikal (Technical)</option>
                        <option value="soft">Insaniah (Soft Skill)</option>
                    </select>

                    <button type="button" id="btnResetFilter" class="btn-secondary" style="padding: 0.6rem 0.9rem; font-size: 0.82rem;">
                        <i class="fa-solid fa-rotate-left"></i> Reset
                    </button>
                </div>

                <div class="filter-right">
                    <span id="skillFilterCount" class="count-indicator">Memuatkan...</span>
                </div>
            </div>

            <!-- Bulk Selection Action Bar -->
            <div id="bulkBar" class="bulk-bar" style="display: none;">
                <div class="bulk-info">
                    <i class="fa-solid fa-circle-check"></i> <span id="selectedCount">0</span> kemahiran dipilih
                </div>
                <button type="button" id="btnDeleteSelected" class="btn-bulk-del">
                    <i class="fa-solid fa-trash-can"></i> Padam Pilihan Terpilih
                </button>
            </div>

            <!-- Skills Table -->
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th style="width: 40px; text-align: center;">
                                <input type="checkbox" id="selectAllCheckbox" class="checkbox-custom" title="Pilih Semua">
                            </th>
                            <th style="width: 50px;">#</th>
                            <th>Nama Kemahiran</th>
                            <th style="width: 170px;">Jenis Kemahiran</th>
                            <th style="width: 140px;">Tahap Pengajian</th>
                            <th style="width: 140px; text-align: center;">Pilihan Responden</th>
                            <th style="width: 160px; text-align: center;">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody id="skillsTableBody">
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                                <i class="fa-solid fa-circle-notch fa-spin"></i> Memuatkan senarai kemahiran...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <footer>
            <p>&copy; <?php echo date('Y'); ?> Sistem Tinjauan IPT Malaysia | Panel Pengurusan Kemahiran</p>
        </footer>
    </div>

    <!-- Edit Skill Modal -->
    <div id="editModal" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-header">
                <div class="modal-title">
                    <i class="fa-solid fa-pen-to-square" style="color: var(--primary-glow);"></i> Sunting Kemahiran
                </div>
                <button type="button" class="modal-close" id="btnModalClose">&times;</button>
            </div>
            <form id="editSkillForm">
                <div class="modal-body">
                    <input type="hidden" id="edit_skill_id" name="skill_id">

                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label class="form-label" for="edit_skill_name">Nama Kemahiran</label>
                        <input type="text" id="edit_skill_name" name="skill_name" class="custom-input" required>
                    </div>

                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label class="form-label" for="edit_skill_type">Jenis Kemahiran</label>
                        <div class="select-wrapper">
                            <select id="edit_skill_type" name="skill_type" class="custom-select" required>
                                <option value="technical">Teknikal (Technical)</option>
                                <option value="soft">Insaniah (Soft Skill)</option>
                            </select>
                            <i class="fa-solid fa-chevron-down select-arrow"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="edit_qualification_level">Tahap Pengajian</label>
                        <div class="select-wrapper">
                            <select id="edit_qualification_level" name="qualification_level" class="custom-select" required>
                                <option value="SPM">SPM</option>
                                <option value="SKM">SKM</option>
                                <option value="Diploma">Diploma</option>
                                <option value="Degree">Degree</option>
                                <option value="Master">Master</option>
                            </select>
                            <i class="fa-solid fa-chevron-down select-arrow"></i>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" id="btnModalCancel">Batal</button>
                    <button type="submit" class="btn-submit" style="width: auto; padding: 0.6rem 1.4rem;">
                        <i class="fa-solid fa-check"></i> Kemaskini
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Page Logic Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const alertBox = document.getElementById('adminAlert');
            const skillsTableBody = document.getElementById('skillsTableBody');
            const searchInput = document.getElementById('skillSearchInput');
            const filterLevel = document.getElementById('filterLevel');
            const filterType = document.getElementById('filterType');
            const filterCount = document.getElementById('skillFilterCount');
            const selectAllCheckbox = document.getElementById('selectAllCheckbox');
            const bulkBar = document.getElementById('bulkBar');
            const selectedCountElem = document.getElementById('selectedCount');
            const btnDeleteSelected = document.getElementById('btnDeleteSelected');
            const editModal = document.getElementById('editModal');

            let allSkillsData = [];
            let selectedSkillIds = new Set();

            // Load initial data
            loadStats();
            loadSkills();

            // Tabs switching
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                    btn.classList.add('active');
                    const tabId = btn.getAttribute('data-tab');
                    document.getElementById(`tab-${tabId}`).classList.add('active');
                });
            });

            // Radio pill group active state management
            setupRadioPills('addSkillForm');
            setupRadioPills('bulkSkillForm');

            function setupRadioPills(formId) {
                const form = document.getElementById(formId);
                if (!form) return;
                form.querySelectorAll('.radio-pill').forEach(pill => {
                    pill.addEventListener('click', () => {
                        const radio = pill.querySelector('input[type="radio"]');
                        if (!radio) return;
                        const name = radio.name;
                        form.querySelectorAll(`input[name="${name}"]`).forEach(r => {
                            r.closest('.radio-pill').classList.remove('active');
                        });
                        pill.classList.add('active');
                    });
                });
            }

            // Single Add Form
            document.getElementById('addSkillForm').addEventListener('submit', (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                formData.append('action', 'add_skill');

                const btn = e.target.querySelector('button[type="submit"]');
                const origHtml = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';

                fetch('api.php', { method: 'POST', body: formData })
                    .then(r => r.json())
                    .then(data => {
                        btn.disabled = false;
                        btn.innerHTML = origHtml;
                        if (data.status === 'success') {
                            showAlert(data.message, 'success');
                            e.target.reset();
                            // Reset pill visual active states
                            e.target.querySelectorAll('.radio-pill').forEach(pill => {
                                const radio = pill.querySelector('input[type="radio"]');
                                if (radio && radio.checked) {
                                    pill.classList.add('active');
                                } else {
                                    pill.classList.remove('active');
                                }
                            });

                            loadStats();
                            loadSkills();
                        } else {
                            showAlert(data.message, 'error');
                        }
                    })
                    .catch(() => {
                        btn.disabled = false;
                        btn.innerHTML = origHtml;
                        showAlert('Ralat sambungan ketika menyimpan kemahiran.', 'error');
                    });
            });

            // Bulk Add Form
            document.getElementById('bulkSkillForm').addEventListener('submit', (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                formData.append('action', 'bulk_add_skills');

                const btn = e.target.querySelector('button[type="submit"]');
                const origHtml = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan Pukal...';

                fetch('api.php', { method: 'POST', body: formData })
                    .then(r => r.json())
                    .then(data => {
                        btn.disabled = false;
                        btn.innerHTML = origHtml;
                        if (data.status === 'success') {
                            showAlert(data.message, 'success');
                            e.target.reset();
                            // Reset pill visual active states
                            e.target.querySelectorAll('.radio-pill').forEach(pill => {
                                const radio = pill.querySelector('input[type="radio"]');
                                if (radio && radio.checked) {
                                    pill.classList.add('active');
                                } else {
                                    pill.classList.remove('active');
                                }
                            });
                            loadStats();
                            loadSkills();
                        } else {
                            showAlert(data.message, 'error');
                        }
                    })
                    .catch(() => {
                        btn.disabled = false;
                        btn.innerHTML = origHtml;
                        showAlert('Ralat sambungan ketika memproses senarai pukal.', 'error');
                    });
            });

            // Search and Filter Listeners
            searchInput.addEventListener('input', applyFilters);
            filterLevel.addEventListener('change', applyFilters);
            filterType.addEventListener('change', applyFilters);

            document.getElementById('btnResetFilter').addEventListener('click', () => {
                searchInput.value = '';
                filterLevel.value = 'all';
                filterType.value = 'all';
                applyFilters();
            });

            // Fetch Stats
            function loadStats() {
                fetch('api.php?action=get_skills_stats')
                    .then(r => r.json())
                    .then(res => {
                        if (res.status === 'success') {
                            const d = res.data;
                            if (document.getElementById('statTotalSkills')) document.getElementById('statTotalSkills').textContent = d.total_skills || 0;
                            if (document.getElementById('statSpm')) document.getElementById('statSpm').textContent = d.spm_count || 0;
                            if (document.getElementById('statSkm')) document.getElementById('statSkm').textContent = d.skm_count || 0;
                            if (document.getElementById('statDiploma')) document.getElementById('statDiploma').textContent = d.diploma_count || 0;
                            if (document.getElementById('statDegree')) document.getElementById('statDegree').textContent = d.degree_count || 0;
                            if (document.getElementById('statMaster')) document.getElementById('statMaster').textContent = d.master_count || 0;
                        }
                    });
            }

            // Fetch Skills List
            function loadSkills() {
                fetch('api.php?action=get_skills_admin')
                    .then(r => r.json())
                    .then(res => {
                        if (res.status === 'success') {
                            allSkillsData = res.data;
                            selectedSkillIds.clear();
                            updateBulkBar();
                            applyFilters();
                        } else {
                            skillsTableBody.innerHTML = `<tr><td colspan="7" style="text-align:center; color: #fca5a5;">${res.message}</td></tr>`;
                        }
                    })
                    .catch(() => {
                        skillsTableBody.innerHTML = `<tr><td colspan="7" style="text-align:center; color: #fca5a5;">Gagal memuatkan rekod kemahiran.</td></tr>`;
                    });
            }

            // Apply Filters & Search
            function applyFilters() {
                const q = searchInput.value.toLowerCase().trim();
                const lvl = filterLevel.value;
                const type = filterType.value;

                const filtered = allSkillsData.filter(s => {
                    const matchQ = !q || s.skill_name.toLowerCase().includes(q);
                    const matchLvl = (lvl === 'all') || (s.qualification_level === lvl);
                    const matchType = (type === 'all') || (s.skill_type === type);
                    return matchQ && matchLvl && matchType;
                });

                filterCount.textContent = `Menunjukkan ${filtered.length} daripada ${allSkillsData.length} kemahiran`;
                renderSkillsTable(filtered);
            }

            // Render Skills Table
            function renderSkillsTable(list) {
                if (list.length === 0) {
                    skillsTableBody.innerHTML = `
                        <tr>
                            <td colspan="7" style="text-align:center; color: var(--text-muted); padding: 2.5rem 1rem;">
                                <i class="fa-solid fa-folder-open" style="font-size: 2rem; margin-bottom: 0.5rem; display: block; opacity: 0.5;"></i>
                                Tiada kemahiran ditemui mengikut padanan carian/penapis.
                            </td>
                        </tr>
                    `;
                    selectAllCheckbox.checked = false;
                    return;
                }

                // Check if all filtered are selected
                const allSelected = list.every(s => selectedSkillIds.has(s.id));
                selectAllCheckbox.checked = allSelected && list.length > 0;

                skillsTableBody.innerHTML = list.map((s, idx) => {
                    const isChecked = selectedSkillIds.has(s.id) ? 'checked' : '';
                    let levelClass = 'all';
                    let levelIcon = 'fa-award';

                    if (s.qualification_level === 'SPM') {
                        levelClass = 'spm';
                        levelIcon = 'fa-book';
                    } else if (s.qualification_level === 'SKM') {
                        levelClass = 'skm';
                        levelIcon = 'fa-wrench';
                    } else if (s.qualification_level === 'Diploma') {
                        levelClass = 'diploma';
                        levelIcon = 'fa-certificate';
                    } else if (s.qualification_level === 'Degree') {
                        levelClass = 'degree';
                        levelIcon = 'fa-graduation-cap';
                    } else if (s.qualification_level === 'Master') {
                        levelClass = 'master';
                        levelIcon = 'fa-user-graduate';
                    }

                    const typeClass = s.skill_type === 'technical' ? 'tech' : 'soft';
                    const typeLabel = s.skill_type === 'technical' ? 'Teknikal' : 'Insaniah';
                    const typeIcon = s.skill_type === 'technical' ? 'fa-microchip' : 'fa-users-gear';

                    const usageCount = parseInt(s.usage_count || 0);
                    const usageHtml = usageCount > 0 
                        ? `<span class="usage-badge active" title="${usageCount} responden memilih kemahiran ini"><i class="fa-solid fa-user-check"></i> ${usageCount} kali</span>`
                        : `<span class="usage-badge"><i class="fa-regular fa-circle"></i> 0</span>`;

                    return `
                        <tr>
                            <td style="text-align:center;">
                                <input type="checkbox" class="checkbox-custom row-checkbox" data-id="${s.id}" ${isChecked}>
                            </td>
                            <td>${idx + 1}</td>
                            <td>
                                <strong>${escapeHtml(s.skill_name)}</strong>
                            </td>
                            <td>
                                <span class="badge-skill-type ${typeClass}">
                                    <i class="fa-solid ${typeIcon}"></i> ${typeLabel}
                                </span>
                            </td>
                            <td>
                                <span class="badge-level ${levelClass}">
                                    <i class="fa-solid ${levelIcon}"></i> ${escapeHtml(s.qualification_level)}
                                </span>
                            </td>
                            <td style="text-align:center;">
                                ${usageHtml}
                            </td>
                            <td style="text-align:center;">
                                <div style="display: inline-flex; gap: 0.4rem;">
                                    <button onclick="openEditModal(${s.id})" class="btn-action-edit" title="Sunting">
                                        <i class="fa-solid fa-pen"></i> Sunting
                                    </button>
                                    <button onclick="deleteSingleSkill(${s.id}, '${escapeHtml(s.skill_name)}', '${escapeHtml(s.qualification_level)}')" class="btn-action-del" title="Padam">
                                        <i class="fa-solid fa-trash"></i> Padam
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                }).join('');

                // Row checkbox listeners
                skillsTableBody.querySelectorAll('.row-checkbox').forEach(cb => {
                    cb.addEventListener('change', (e) => {
                        const id = parseInt(e.target.getAttribute('data-id'));
                        if (e.target.checked) {
                            selectedSkillIds.add(id);
                        } else {
                            selectedSkillIds.delete(id);
                        }
                        updateBulkBar();
                        const allFilteredChecked = list.every(item => selectedSkillIds.has(item.id));
                        selectAllCheckbox.checked = allFilteredChecked;
                    });
                });
            }

            // Select All Checkbox
            selectAllCheckbox.addEventListener('change', (e) => {
                const checked = e.target.checked;
                const q = searchInput.value.toLowerCase().trim();
                const lvl = filterLevel.value;
                const type = filterType.value;

                const filtered = allSkillsData.filter(s => {
                    const matchQ = !q || s.skill_name.toLowerCase().includes(q);
                    const matchLvl = (lvl === 'all') || (s.qualification_level === lvl);
                    const matchType = (type === 'all') || (s.skill_type === type);
                    return matchQ && matchLvl && matchType;
                });

                filtered.forEach(s => {
                    if (checked) {
                        selectedSkillIds.add(s.id);
                    } else {
                        selectedSkillIds.delete(s.id);
                    }
                });

                skillsTableBody.querySelectorAll('.row-checkbox').forEach(cb => {
                    cb.checked = checked;
                });

                updateBulkBar();
            });

            function updateBulkBar() {
                const count = selectedSkillIds.size;
                if (count > 0) {
                    bulkBar.style.display = 'flex';
                    selectedCountElem.textContent = count;
                } else {
                    bulkBar.style.display = 'none';
                    selectedCountElem.textContent = 0;
                }
            }

            // Delete Single Skill
            window.deleteSingleSkill = function(id, name, level) {
                if (!confirm(`Adakah anda pasti untuk memadam kemahiran "${name}" (${level})?`)) return;

                const formData = new FormData();
                formData.append('action', 'delete_skill');
                formData.append('skill_id', id);

                fetch('api.php', { method: 'POST', body: formData })
                    .then(r => r.json())
                    .then(data => {
                        if (data.status === 'success') {
                            showAlert(data.message, 'success');
                            selectedSkillIds.delete(id);
                            loadStats();
                            loadSkills();
                        } else {
                            showAlert(data.message, 'error');
                        }
                    })
                    .catch(() => showAlert('Ralat ketika memadam kemahiran.', 'error'));
            };

            // Bulk Delete Action
            btnDeleteSelected.addEventListener('click', () => {
                const count = selectedSkillIds.size;
                if (count === 0) return;

                if (!confirm(`AMARAN: Anda akan memadam ${count} kemahiran terpilih. Teruskan?`)) return;

                const formData = new FormData();
                formData.append('action', 'bulk_delete_skills');
                formData.append('skill_ids', Array.from(selectedSkillIds).join(','));

                btnDeleteSelected.disabled = true;
                btnDeleteSelected.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memadam...';

                fetch('api.php', { method: 'POST', body: formData })
                    .then(r => r.json())
                    .then(data => {
                        btnDeleteSelected.disabled = false;
                        btnDeleteSelected.innerHTML = '<i class="fa-solid fa-trash-can"></i> Padam Pilihan Terpilih';
                        if (data.status === 'success') {
                            showAlert(data.message, 'success');
                            selectedSkillIds.clear();
                            loadStats();
                            loadSkills();
                        } else {
                            showAlert(data.message, 'error');
                        }
                    })
                    .catch(() => {
                        btnDeleteSelected.disabled = false;
                        btnDeleteSelected.innerHTML = '<i class="fa-solid fa-trash-can"></i> Padam Pilihan Terpilih';
                        showAlert('Ralat ketika memproses pemadaman pukal.', 'error');
                    });
            });

            // Edit Modal Functions
            window.openEditModal = function(id) {
                const skill = allSkillsData.find(s => s.id === id);
                if (!skill) return;

                document.getElementById('edit_skill_id').value = skill.id;
                document.getElementById('edit_skill_name').value = skill.skill_name;
                document.getElementById('edit_skill_type').value = skill.skill_type;
                document.getElementById('edit_qualification_level').value = skill.qualification_level;

                editModal.classList.add('active');
            };

            function closeEditModal() {
                editModal.classList.remove('active');
            }

            document.getElementById('btnModalClose').addEventListener('click', closeEditModal);
            document.getElementById('btnModalCancel').addEventListener('click', closeEditModal);
            editModal.addEventListener('click', (e) => {
                if (e.target === editModal) closeEditModal();
            });

            document.getElementById('editSkillForm').addEventListener('submit', (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                formData.append('action', 'update_skill');

                fetch('api.php', { method: 'POST', body: formData })
                    .then(r => r.json())
                    .then(data => {
                        if (data.status === 'success') {
                            showAlert(data.message, 'success');
                            closeEditModal();
                            loadStats();
                            loadSkills();
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(() => alert('Ralat kemaskini kemahiran.'));
            });

            // Helper: Alert Display
            function showAlert(msg, type) {
                alertBox.className = `alert-box ${type}`;
                alertBox.innerHTML = `<i class="fa-solid ${type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'}"></i> ${escapeHtml(msg)}`;
                alertBox.style.display = 'block';
                alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });

                setTimeout(() => {
                    alertBox.style.display = 'none';
                }, 5000);
            }

            function escapeHtml(text) {
                if (!text) return '';
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
        });
    </script>
</body>

</html>
