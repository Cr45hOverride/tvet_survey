<?php
/**
 * Admin Management Page - Comprehensive CRUD for States, Institutes, Courses & Users
 */
require_once __DIR__ . '/config.php';
requireLogin();

$db = getDBConnection();
$currentUser = getCurrentUser();

// Initial fetch for state and institute dropdowns
$states = $db->query("SELECT id, state_name FROM states ORDER BY state_name ASC")->fetchAll();
$institutes = $db->query("
    SELECT i.id, i.state_id, i.institute_name, s.state_name 
    FROM institutes i 
    JOIN states s ON i.state_id = s.id 
    ORDER BY s.state_name ASC, i.institute_name ASC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ms">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - Pengurusan Data IPT (CRUD)</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .container {
            width: 95% !important;
            max-width: 1320px !important;
        }

        /* Admin Tab Navigation */
        .admin-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.75rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 0.5rem;
            flex-wrap: wrap;
        }

        .admin-tab-btn {
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 0.95rem;
            font-weight: 600;
            padding: 0.65rem 1.25rem;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .admin-tab-btn:hover {
            color: var(--text-main);
            background: rgba(255, 255, 255, 0.05);
        }

        .admin-tab-btn.active {
            background: rgba(56, 189, 248, 0.18);
            color: var(--primary-glow);
            border: 1px solid rgba(56, 189, 248, 0.35);
        }

        .admin-tab-pane {
            display: none;
        }

        .admin-tab-pane.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(4px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Action Buttons */
        .btn-action-edit {
            background: rgba(56, 189, 248, 0.15);
            border: 1px solid rgba(56, 189, 248, 0.35);
            color: #38bdf8;
            padding: 0.35rem 0.75rem;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }

        .btn-action-edit:hover {
            background: rgba(56, 189, 248, 0.85);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(56, 189, 248, 0.4);
        }

        .btn-action-delete {
            background: rgba(239, 68, 68, 0.18);
            border: 1px solid rgba(239, 68, 68, 0.35);
            color: #fca5a5;
            padding: 0.35rem 0.75rem;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }

        .btn-action-delete:hover {
            background: rgba(239, 68, 68, 0.85);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
        }

        /* Status Badges & Action Buttons for Temp Institutes */
        .badge-status-kiv {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.65rem;
            border-radius: 9999px;
            font-size: 0.76rem;
            font-weight: 700;
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.4);
        }

        .badge-status-approve {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.65rem;
            border-radius: 9999px;
            font-size: 0.76rem;
            font-weight: 700;
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.4);
        }

        .btn-action-approve {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.35);
            color: #34d399;
            padding: 0.32rem 0.65rem;
            border-radius: 8px;
            font-size: 0.78rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .btn-action-approve:hover {
            background: rgba(16, 185, 129, 0.85);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
        }

        .btn-action-kiv {
            background: rgba(245, 158, 11, 0.15);
            border: 1px solid rgba(245, 158, 11, 0.35);
            color: #fbbf24;
            padding: 0.32rem 0.65rem;
            border-radius: 8px;
            font-size: 0.78rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .btn-action-kiv:hover {
            background: rgba(245, 158, 11, 0.85);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
        }

        .btn-action-transfer {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.25), rgba(5, 150, 105, 0.4));
            border: 1px solid rgba(16, 185, 129, 0.5);
            color: #6ee7b7;
            padding: 0.32rem 0.65rem;
            border-radius: 8px;
            font-size: 0.78rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .btn-action-transfer:hover {
            background: #10b981;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.5);
        }

        .status-quick-select {
            background: rgba(15, 23, 42, 0.8) !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
            color: #f8fafc !important;
            font-size: 0.78rem !important;
            border-radius: 6px !important;
            padding: 0.25rem 1.6rem 0.25rem 0.5rem !important;
            cursor: pointer;
        }

        .status-quick-select:hover {
            border-color: var(--primary-glow) !important;
        }

        /* Filter Toolbar */
        .filter-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1.25rem;
            background: rgba(15, 23, 42, 0.6);
            padding: 1rem 1.25rem;
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .filter-search-box {
            position: relative;
            min-width: 240px;
        }

        .filter-search-box input {
            width: 100%;
            padding: 0.55rem 0.9rem 0.55rem 2.2rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--input-border);
            border-radius: 10px;
            color: var(--text-main);
            font-size: 0.85rem;
            outline: none;
        }

        .filter-search-box i {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-glow);
            font-size: 0.85rem;
        }

        /* Pagination Toolbar */
        .pagination-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 1.25rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .pagination-controls {
            display: flex;
            gap: 0.4rem;
            align-items: center;
        }

        .btn-page {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-main);
            padding: 0.35rem 0.75rem;
            border-radius: 8px;
            font-size: 0.82rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-page:hover:not(:disabled) {
            background: rgba(56, 189, 248, 0.2);
            color: #38bdf8;
            border-color: rgba(56, 189, 248, 0.4);
        }

        .btn-page:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        /* Collapsible Form Card */
        .collapse-toggle {
            cursor: pointer;
            user-select: none;
        }

        .level-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.2rem 0.55rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .level-badge.degree {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .level-badge.diploma {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .level-badge.certificate {
            background: rgba(56, 189, 248, 0.15);
            color: #38bdf8;
            border: 1px solid rgba(56, 189, 248, 0.3);
        }
    </style>
</head>

<body>

    <div class="container">
        <!-- Reusable Navigation Header -->
        <?php include __DIR__ . '/menu.php'; ?>

        <!-- Feedback Alert Box -->
        <div id="adminAlert" class="alert-box"></div>

        <!-- EXECUTIVE KPI SUMMARY CARDS -->
        <div class="stats-grid" style="margin-bottom: 1.75rem;">
            <div class="stat-card" style="--stat-accent: #38bdf8; --stat-icon-bg: rgba(56, 189, 248, 0.15);">
                <div class="stat-icon"><i class="fa-solid fa-map-location-dot"></i></div>
                <div class="stat-content">
                    <span class="stat-number" id="kpiTotalStates"><?php echo count($states); ?></span>
                    <span class="stat-label">Jumlah Negeri</span>
                </div>
            </div>

            <div class="stat-card" style="--stat-accent: #fbbf24; --stat-icon-bg: rgba(245, 158, 11, 0.15);">
                <div class="stat-icon"><i class="fa-solid fa-building-columns"></i></div>
                <div class="stat-content">
                    <span class="stat-number" id="kpiTotalInstitutes"><?php echo count($institutes); ?></span>
                    <span class="stat-label">Jumlah Institusi (IPT)</span>
                </div>
            </div>

            <div class="stat-card" style="--stat-accent: #10b981; --stat-icon-bg: rgba(16, 185, 129, 0.15);">
                <div class="stat-icon"><i class="fa-solid fa-graduation-cap"></i></div>
                <div class="stat-content">
                    <span class="stat-number" id="kpiTotalCourses">-</span>
                    <span class="stat-label">Kursus Ditawarkan</span>
                </div>
            </div>

            <div class="stat-card" style="--stat-accent: #c084fc; --stat-icon-bg: rgba(168, 85, 247, 0.15);">
                <div class="stat-icon"><i class="fa-solid fa-users-gear"></i></div>
                <div class="stat-content">
                    <span class="stat-number" id="kpiTotalUsers">-</span>
                    <span class="stat-label">Pentadbir Sistem</span>
                </div>
            </div>

            <div class="stat-card" style="--stat-accent: #f59e0b; --stat-icon-bg: rgba(245, 158, 11, 0.15); cursor: pointer;" onclick="document.querySelector('.admin-tab-btn[data-tab=\'temp_ipt\']').click();">
                <div class="stat-icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
                <div class="stat-content">
                    <span class="stat-number" id="kpiTotalTempInstitutes">-</span>
                    <span class="stat-label">Permohonan IPT (Temp)</span>
                </div>
            </div>

            <div class="stat-card" style="--stat-accent: #10b981; --stat-icon-bg: rgba(16, 185, 129, 0.15); cursor: pointer;" onclick="document.querySelector('.admin-tab-btn[data-tab=\'temp_kursus\']').click();">
                <div class="stat-icon"><i class="fa-solid fa-graduation-cap"></i></div>
                <div class="stat-content">
                    <span class="stat-number" id="kpiTotalTempCourses">-</span>
                    <span class="stat-label">Permohonan Kursus (Temp)</span>
                </div>
            </div>

            <div class="stat-card" style="--stat-accent: #a855f7; --stat-icon-bg: rgba(168, 85, 247, 0.15); cursor: pointer;" onclick="document.querySelector('.admin-tab-btn[data-tab=\'temp_skills\']').click();">
                <div class="stat-icon"><i class="fa-solid fa-brain"></i></div>
                <div class="stat-content">
                    <span class="stat-number" id="kpiTotalTempSkills">-</span>
                    <span class="stat-label">Permohonan Kemahiran (Temp)</span>
                </div>
            </div>
        </div>

        <!-- ADMIN TABS NAVIGATION (NEGERI, IPT, KURSUS, USERS, TEMP IPT, TEMP KURSUS, TEMP KEMAHIRAN) -->
        <div class="admin-tabs">
            <button type="button" class="admin-tab-btn active" data-tab="negeri">
                <i class="fa-solid fa-map-location-dot"></i> 1. Pengurusan Negeri
            </button>
            <button type="button" class="admin-tab-btn" data-tab="ipt">
                <i class="fa-solid fa-building-columns"></i> 2. Pengurusan Institusi (IPT)
            </button>
            <button type="button" class="admin-tab-btn" data-tab="kursus">
                <i class="fa-solid fa-graduation-cap"></i> 3. Pengurusan Kursus Ditawarkan
            </button>
            <button type="button" class="admin-tab-btn" data-tab="users">
                <i class="fa-solid fa-users-gear"></i> 4. Pengguna Pentadbir
            </button>
            <button type="button" class="admin-tab-btn" data-tab="temp_ipt" id="tabBtnTempIpt">
                <i class="fa-solid fa-clock-rotate-left"></i> 5. Permohonan IPT (Temp)
                <span id="badgeKivCount" style="display:none; background:#f59e0b; color:#0f172a; font-weight:700; border-radius:9999px; padding:0.12rem 0.45rem; font-size:0.72rem; margin-left:0.35rem;">0 KIV</span>
            </button>
            <button type="button" class="admin-tab-btn" data-tab="temp_kursus" id="tabBtnTempKursus">
                <i class="fa-solid fa-graduation-cap"></i> 6. Permohonan Kursus (Temp)
                <span id="badgeCourseKivCount" style="display:none; background:#10b981; color:#ffffff; font-weight:700; border-radius:9999px; padding:0.12rem 0.45rem; font-size:0.72rem; margin-left:0.35rem;">0 KIV</span>
            </button>
            <button type="button" class="admin-tab-btn" data-tab="temp_skills" id="tabBtnTempSkills">
                <i class="fa-solid fa-brain"></i> 7. Permohonan Kemahiran (Temp)
                <span id="badgeSkillKivCount" style="display:none; background:#a855f7; color:#ffffff; font-weight:700; border-radius:9999px; padding:0.12rem 0.45rem; font-size:0.72rem; margin-left:0.35rem;">0 KIV</span>
            </button>
        </div>

        <!-- ========================================== -->
        <!-- TAB 1: PENGURUSAN NEGERI (STATES CRUD)     -->
        <!-- ========================================== -->
        <div id="tab-negeri" class="admin-tab-pane active">
            <!-- Add State Form -->
            <section class="glass-card" style="margin-bottom: 1.5rem; padding: 1.5rem 2rem;">
                <h3 class="card-title" style="font-size: 1.25rem;">
                    <i class="fa-solid fa-plus-circle" style="color: var(--primary-glow);"></i>
                    Daftar Negeri Baharu
                </h3>
                <p class="card-subtitle">Negeri yang didaftarkan akan terus dipaparkan dalam penapis borang tinjauan dan
                    institusi.</p>

                <form id="addStateForm" class="form-grid">
                    <div class="form-group">
                        <label class="form-label" for="state_name">Nama Negeri</label>
                        <input type="text" id="state_name" name="state_name" class="custom-input"
                            placeholder="Contoh: Sabah / Pahang / Johor" required>
                    </div>
                    <button type="submit" class="btn-submit" style="align-self: flex-end;">
                        <i class="fa-solid fa-plus"></i> Simpan Negeri
                    </button>
                </form>
            </section>

            <!-- States Table Card -->
            <section class="glass-card">
                <div
                    style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem;">
                    <div>
                        <h3 class="card-title" style="margin-bottom: 0.2rem; font-size: 1.25rem;">
                            <i class="fa-solid fa-table-list" style="color: var(--primary-glow);"></i>
                            Senarai Negeri Berdaftar
                        </h3>
                        <p class="card-subtitle" style="margin-bottom: 0;">Urus, kemas kini nama, atau padam negeri.</p>
                    </div>
                    <div class="filter-search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="searchStatesInput" placeholder="Cari nama negeri...">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th>Nama Negeri</th>
                                <th style="width: 140px; text-align: center;">Jumlah IPT</th>
                                <th style="width: 140px; text-align: center;">Jumlah Kursus</th>
                                <th style="width: 160px; text-align: center;">Responden Terlibat</th>
                                <th style="width: 180px; text-align: center;">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody id="statesTableBody">
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                                    <i class="fa-solid fa-spinner fa-spin"></i> Memuatkan senarai negeri...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <!-- ========================================== -->
        <!-- TAB 2: PENGURUSAN INSTITUSI (IPT CRUD)     -->
        <!-- ========================================== -->
        <div id="tab-ipt" class="admin-tab-pane">
            <!-- Notice if there are pending Temp Institutes -->
            <div id="tempInstituteBanner" style="display:none; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem; padding:0.9rem 1.25rem; background:rgba(245, 158, 11, 0.12); border:1px solid rgba(245, 158, 11, 0.35); border-radius:12px; margin-bottom:1.5rem;">
                <div style="display:flex; align-items:center; gap:0.6rem; color:#fde68a; font-size:0.9rem;">
                    <i class="fa-solid fa-bell" style="color:#fbbf24; font-size:1.1rem;"></i>
                    <span>Terdapat <strong><span id="bannerKivCount">0</span> permohonan KIV</strong> dan <strong><span id="bannerApproveCount">0</span> sedia Approve</strong> daripada pengguna untuk institusi baharu.</span>
                </div>
                <div style="display:flex; gap:0.5rem; align-items:center;">
                    <button type="button" class="btn-submit" onclick="document.querySelector('.admin-tab-btn[data-tab=\'temp_ipt\']').click();" style="padding:0.4rem 0.9rem; font-size:0.82rem; background:linear-gradient(135deg, #f59e0b, #d97706); border:none;">
                        <i class="fa-solid fa-list-check"></i> Semak di Tab 5
                    </button>
                    <button type="button" class="btn-submit" onclick="transferApprovedInstitutes();" style="padding:0.4rem 0.9rem; font-size:0.82rem; background:linear-gradient(135deg, #10b981, #059669); border:none;">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Update Institure
                    </button>
                </div>
            </div>

            <!-- Add Institute Form -->
            <section class="glass-card" style="margin-bottom: 1.5rem; padding: 1.5rem 2rem;">
                <h3 class="card-title" style="font-size: 1.25rem;">
                    <i class="fa-solid fa-plus-circle" style="color: #fbbf24;"></i>
                    Daftar Institusi (IPT) Baharu
                </h3>
                <p class="card-subtitle">Daftarkan institusi pengajian tinggi (Universiti, Politeknik, Kolej Komuniti,
                    ILP, ADTEC) di bawah negeri berkaitan.</p>

                <form id="addInstituteForm" class="form-grid">
                    <div class="form-group">
                        <label class="form-label" for="add_ipt_state_id">Pilih Negeri</label>
                        <div class="select-wrapper">
                            <select id="add_ipt_state_id" name="state_id" class="custom-select" required>
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

                    <div class="form-group">
                        <label class="form-label" for="add_institute_name">Nama Institusi / IPT</label>
                        <input type="text" id="add_institute_name" name="institute_name" class="custom-input"
                            placeholder="Contoh: Universiti Malaysia Sabah (UMS)" required>
                    </div>

                    <button type="submit" class="btn-submit" style="align-self: flex-end;">
                        <i class="fa-solid fa-plus"></i> Simpan Institusi
                    </button>
                </form>
            </section>

            <!-- Institutes Table Card -->
            <section class="glass-card">
                <div
                    style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem;">
                    <div>
                        <h3 class="card-title" style="margin-bottom: 0.2rem; font-size: 1.25rem;">
                            <i class="fa-solid fa-building-columns" style="color: #fbbf24;"></i>
                            Senarai Institusi (IPT) Berdaftar
                        </h3>
                        <p class="card-subtitle" style="margin-bottom: 0;">Tapis mengikut negeri atau cari nama IPT.</p>
                    </div>

                    <div class="filter-group">
                        <div class="select-wrapper" style="min-width: 180px;">
                            <select id="filterIptState" class="custom-select"
                                style="padding: 0.55rem 2rem 0.55rem 0.8rem; font-size: 0.85rem;">
                                <option value="0">-- Semua Negeri --</option>
                                <?php foreach ($states as $st): ?>
                                    <option value="<?php echo $st['id']; ?>">
                                        <?php echo htmlspecialchars($st['state_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <i class="fa-solid fa-chevron-down select-arrow"></i>
                        </div>

                        <div class="filter-search-box">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="text" id="searchIptInput" placeholder="Cari nama IPT...">
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th>Nama Institusi / IPT</th>
                                <th style="width: 160px;">Negeri</th>
                                <th style="width: 140px; text-align: center;">Kursus Ditawarkan</th>
                                <th style="width: 150px; text-align: center;">Responden</th>
                                <th style="width: 180px; text-align: center;">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody id="institutesTableBody">
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                                    <i class="fa-solid fa-spinner fa-spin"></i> Memuatkan senarai institusi...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <!-- ========================================== -->
        <!-- TAB 3: PENGURUSAN KURSUS (COURSES CRUD)    -->
        <!-- ========================================== -->
        <div id="tab-kursus" class="admin-tab-pane">
            <!-- Notice if there are pending Temp Courses -->
            <div id="tempCourseBanner" style="display:none; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem; padding:0.9rem 1.25rem; background:rgba(16, 185, 129, 0.12); border:1px solid rgba(16, 185, 129, 0.35); border-radius:12px; margin-bottom:1.5rem;">
                <div style="display:flex; align-items:center; gap:0.6rem; color:#a7f3d0; font-size:0.9rem;">
                    <i class="fa-solid fa-bell" style="color:#10b981; font-size:1.1rem;"></i>
                    <span>Terdapat <strong><span id="bannerCourseKivCount">0</span> permohonan KIV</strong> dan <strong><span id="bannerCourseApproveCount">0</span> sedia Approve</strong> daripada pengguna untuk kursus baharu.</span>
                </div>
                <div style="display:flex; gap:0.5rem; align-items:center;">
                    <button type="button" class="btn-submit" onclick="document.querySelector('.admin-tab-btn[data-tab=\'temp_kursus\']').click();" style="padding:0.4rem 0.9rem; font-size:0.82rem; background:linear-gradient(135deg, #059669, #10b981); border:none;">
                        <i class="fa-solid fa-list-check"></i> Semak di Tab 6
                    </button>
                    <button type="button" class="btn-submit" onclick="transferApprovedCourses();" style="padding:0.4rem 0.9rem; font-size:0.82rem; background:linear-gradient(135deg, #10b981, #059669); border:none;">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Update Course
                    </button>
                </div>
            </div>

            <!-- Add Course Form Card -->
            <section class="glass-card" style="margin-bottom: 1.5rem; padding: 1.5rem 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <div>
                        <h3 class="card-title" style="font-size: 1.25rem; margin-bottom: 0.2rem;">
                            <i class="fa-solid fa-plus-circle" style="color: #10b981;"></i>
                            Daftar Kursus Ditawarkan Baharu
                        </h3>
                        <p class="card-subtitle" style="margin-bottom: 0;">Tambah program akademik mengikut institusi
                            dan tahap kelayakan.</p>
                    </div>
                    <button type="button" id="btnToggleCourseForm" class="btn-secondary"
                        style="font-size: 0.8rem; padding: 0.4rem 0.8rem;">
                        <i class="fa-solid fa-chevron-up" id="iconCourseToggle"></i> Lipat Borang
                    </button>
                </div>

                <div id="courseFormWrapper">
                    <form id="addCourseForm" class="form-grid"
                        style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));">
                        <div class="form-group">
                            <label class="form-label" for="add_course_state_filter">1. Tapis Negeri IPT</label>
                            <div class="select-wrapper">
                                <select id="add_course_state_filter" class="custom-select">
                                    <option value="all">-- Semua Negeri --</option>
                                    <?php foreach ($states as $st): ?>
                                        <option value="<?php echo $st['id']; ?>">
                                            <?php echo htmlspecialchars($st['state_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <i class="fa-solid fa-chevron-down select-arrow"></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="add_course_institute_id">2. Pilih Institusi (IPT)</label>
                            <div class="select-wrapper">
                                <select id="add_course_institute_id" name="institute_id" class="custom-select" required>
                                    <option value="">-- Pilih Institusi --</option>
                                    <?php foreach ($institutes as $inst): ?>
                                        <option value="<?php echo $inst['id']; ?>"
                                            data-state="<?php echo $inst['state_id']; ?>">
                                            <?php echo htmlspecialchars($inst['institute_name']) . ' (' . htmlspecialchars($inst['state_name']) . ')'; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <i class="fa-solid fa-chevron-down select-arrow"></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="add_course_name">3. Nama Kursus / Program</label>
                            <input type="text" id="add_course_name" name="course_name" class="custom-input"
                                placeholder="Contoh: Diploma Kejuruteraan Mekatronik" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="add_qualification_level">4. Tahap Pengajian</label>
                            <div class="select-wrapper">
                                <select id="add_qualification_level" name="qualification_level" class="custom-select"
                                    required>
                                    <option value="Certificate">SPM / STPM / Sijil</option>
                                    <option value="SKM">SKM</option>
                                    <option value="Diploma">Diploma</option>
                                    <option value="Degree">Degree (Ijazah Sarjana Muda)</option>
                                    <option value="Master">Master (Ijazah Sarjana)</option>

                                </select>
                                <i class="fa-solid fa-chevron-down select-arrow"></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="add_campus_branch">5. Kampus / Cawangan</label>
                            <input type="text" id="add_campus_branch" name="campus_branch" class="custom-input"
                                placeholder="Contoh: Kampus Utama" value="Kampus Utama" required>
                        </div>

                        <div class="form-group" style="display: flex; align-items: flex-end;">
                            <button type="submit" class="btn-submit" style="width: 100%;">
                                <i class="fa-solid fa-plus"></i> Simpan Kursus
                            </button>
                        </div>
                    </form>
                </div>
            </section>

            <!-- Courses Table & Integrated Filters Card -->
            <section class="glass-card">
                <div
                    style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem;">
                    <div>
                        <h3 class="card-title" style="margin-bottom: 0.2rem; font-size: 1.25rem;">
                            <i class="fa-solid fa-book-open-reader" style="color: #10b981;"></i>
                            Katalog Kursus Ditawarkan
                        </h3>
                        <p class="card-subtitle" style="margin-bottom: 0;">
                            Urus senarai kursus IPT. Gunakan penapis untuk navigasi pantas.
                        </p>
                    </div>
                    <div>
                        <span id="badgeCourseMatchCount" class="badge-step"
                            style="font-size: 0.8rem; padding: 0.35rem 0.8rem; background: rgba(16, 185, 129, 0.15); color: #34d399; border-color: rgba(16, 185, 129, 0.35);">
                            0 Kursus Dijumpai
                        </span>
                    </div>
                </div>

                <!-- Integrated Filter Bar for Courses -->
                <div class="filter-toolbar">
                    <div class="filter-group">
                        <!-- Filter State -->
                        <div class="select-wrapper" style="min-width: 160px;">
                            <select id="courseFilterState" class="custom-select"
                                style="font-size: 0.82rem; padding: 0.45rem 2rem 0.45rem 0.75rem;">
                                <option value="0">-- Semua Negeri --</option>
                                <?php foreach ($states as $st): ?>
                                    <option value="<?php echo $st['id']; ?>">
                                        <?php echo htmlspecialchars($st['state_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <i class="fa-solid fa-chevron-down select-arrow"></i>
                        </div>

                        <!-- Filter IPT -->
                        <div class="select-wrapper" style="min-width: 220px;">
                            <select id="courseFilterInstitute" class="custom-select"
                                style="font-size: 0.82rem; padding: 0.45rem 2rem 0.45rem 0.75rem;">
                                <option value="0">-- Semua Institusi (IPT) --</option>
                                <?php foreach ($institutes as $inst): ?>
                                    <option value="<?php echo $inst['id']; ?>"
                                        data-state="<?php echo $inst['state_id']; ?>">
                                        <?php echo htmlspecialchars($inst['institute_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <i class="fa-solid fa-chevron-down select-arrow"></i>
                        </div>

                        <!-- Filter Level -->
                        <div class="select-wrapper" style="min-width: 150px;">
                            <select id="courseFilterLevel" class="custom-select"
                                style="font-size: 0.82rem; padding: 0.45rem 2rem 0.45rem 0.75rem;">
                                <option value="all">-- Semua Tahap --</option>
                                <option value="Diploma">Diploma</option>
                                <option value="Degree">Degree</option>
                                <option value="Certificate">Certificate</option>
                            </select>
                            <i class="fa-solid fa-chevron-down select-arrow"></i>
                        </div>
                    </div>

                    <!-- Search Input -->
                    <div class="filter-search-box" style="min-width: 260px;">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="searchCourseInput" placeholder="Cari nama kursus / IPT...">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Nama Kursus</th>
                                <th style="width: 120px; text-align: center;">Tahap</th>
                                <th>Institusi (IPT)</th>
                                <th style="width: 130px;">Negeri</th>
                                <th style="width: 140px;">Cawangan</th>
                                <th style="width: 110px; text-align: center;">Responden</th>
                                <th style="width: 160px; text-align: center;">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody id="coursesTableBody">
                            <tr>
                                <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                                    <i class="fa-solid fa-spinner fa-spin"></i> Memuatkan senarai kursus...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Bar for Courses -->
                <div class="pagination-bar" id="coursePaginationBar">
                    <div id="coursePaginationInfo">Memaparkan 0 daripada 0 kursus</div>
                    <div class="pagination-controls">
                        <button type="button" id="btnCoursePrev" class="btn-page"><i
                                class="fa-solid fa-chevron-left"></i> Sebelum</button>
                        <span id="coursePageIndicator" style="padding: 0 0.5rem; font-weight: 600; color: #f8fafc;">1 /
                            1</span>
                        <button type="button" id="btnCourseNext" class="btn-page">Seterusnya <i
                                class="fa-solid fa-chevron-right"></i></button>
                    </div>
                </div>
            </section>
        </div>

        <!-- ========================================== -->
        <!-- TAB 4: PENGURUSAN PENGGUNA (USERS CRUD)    -->
        <!-- ========================================== -->
        <div id="tab-users" class="admin-tab-pane">
            <section class="glass-card" style="margin-bottom: 1.5rem; padding: 1.5rem 2rem;">
                <h3 class="card-title" style="font-size: 1.25rem;">
                    <i class="fa-solid fa-user-plus" style="color: #c084fc;"></i>
                    Tambah Pengguna Pentadbir Baharu
                </h3>
                <p class="card-subtitle">Daftarkan akaun pentadbir dengan akses portal pengurusan sistem.</p>

                <form id="addUserForm" class="form-grid">
                    <div class="form-group">
                        <label class="form-label" for="new_username">Nama Pengguna (Username)</label>
                        <input type="text" id="new_username" name="username" class="custom-input"
                            placeholder="Contoh: admin_utara" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="new_fullname">Nama Penuh</label>
                        <input type="text" id="new_fullname" name="full_name" class="custom-input"
                            placeholder="Contoh: Norazlan Shah Bin Yusof" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="new_password">Kata Laluan (Password)</label>
                        <input type="password" id="new_password" name="password" class="custom-input"
                            placeholder="Masukkan kata laluan" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="new_role">Peranan / Role</label>
                        <div class="select-wrapper">
                            <select id="new_role" name="role" class="custom-select" required>
                                <option value="admin">Admin</option>
                                <option value="superadmin">Superadmin</option>
                            </select>
                            <i class="fa-solid fa-chevron-down select-arrow"></i>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit" style="align-self: flex-end;">
                        <i class="fa-solid fa-user-plus"></i> Tambah Pentadbir
                    </button>
                </form>
            </section>

            <section class="glass-card">
                <h3 class="card-title" style="font-size: 1.25rem; margin-bottom: 1rem;">
                    <i class="fa-solid fa-users"></i> Senarai Pentadbir Berdaftar
                </h3>
                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th>Nama Pengguna</th>
                                <th>Nama Penuh</th>
                                <th style="width: 140px;">Peranan</th>
                                <th style="width: 200px;">Tarikh Dicipta</th>
                                <th style="width: 120px; text-align: center;">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody id="usersBody">
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                                    <i class="fa-solid fa-spinner fa-spin"></i> Memuatkan pentadbir...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <!-- ======================================================== -->
        <!-- TAB 5: PERMOHONAN INSTITUSI SEMENTARA (TEMP INSTITUTES)  -->
        <!-- ======================================================== -->
        <div id="tab-temp_ipt" class="admin-tab-pane">
            <section class="glass-card" style="margin-bottom: 1.5rem; padding: 1.5rem 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.25rem;">
                    <div style="max-width: 750px;">
                        <h3 class="card-title" style="font-size: 1.3rem; margin-bottom: 0.35rem;">
                            <i class="fa-solid fa-clock-rotate-left" style="color: #f59e0b;"></i>
                            Pengurusan Permohonan Institusi (temp_institutes)
                        </h3>
                        <p class="card-subtitle" style="margin-bottom: 0; line-height: 1.5;">
                            Pengguna biasa memasukkan maklumat institusi baharu ke dalam jadual <code>temp_institutes</code> dengan status lalai <strong>KIV</strong>.
                            Sebagai pentadbir, anda boleh menyemak permohonan, mengubah status kepada <strong>Approve</strong>, dan menekan butang <strong>Update Institure</strong> untuk memindahkan data ke jadual rasmi <code>institutes</code>.
                        </p>
                    </div>

                    <!-- THE REQUESTED BUTTON: "Update Institure" -->
                    <div>
                        <button type="button" id="btnUpdateInstitute" class="btn-submit"
                            style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4); font-size: 0.95rem; padding: 0.75rem 1.6rem; display: inline-flex; align-items: center; gap: 0.55rem; border: none; cursor: pointer; color: #ffffff; font-weight: 700; border-radius: 10px;">
                            <i class="fa-solid fa-cloud-arrow-up" style="font-size: 1.1rem;"></i> Update Institure
                        </button>
                    </div>
                </div>
            </section>

            <!-- Temp Institutes Table Card -->
            <section class="glass-card">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem;">
                    <div>
                        <h3 class="card-title" style="margin-bottom: 0.2rem; font-size: 1.25rem;">
                            <i class="fa-solid fa-table-list" style="color: #f59e0b;"></i>
                            Senarai Permohonan Institusi (temp_institutes)
                        </h3>
                        <p class="card-subtitle" style="margin-bottom: 0;">
                            Tukar status kepada "Approve" bagi permohonan yang sah, kemudian klik "Update Institure".
                        </p>
                    </div>

                    <div class="filter-group">
                        <!-- Filter Status -->
                        <div class="select-wrapper" style="min-width: 150px;">
                            <select id="filterTempStatus" class="custom-select" style="padding: 0.55rem 2rem 0.55rem 0.8rem; font-size: 0.85rem;">
                                <option value="all">-- Semua Status --</option>
                                <option value="KIV">Status: KIV</option>
                                <option value="Approve">Status: Approve</option>
                            </select>
                            <i class="fa-solid fa-chevron-down select-arrow"></i>
                        </div>

                        <!-- Filter State -->
                        <div class="select-wrapper" style="min-width: 180px;">
                            <select id="filterTempState" class="custom-select" style="padding: 0.55rem 2rem 0.55rem 0.8rem; font-size: 0.85rem;">
                                <option value="0">-- Semua Negeri --</option>
                                <?php foreach ($states as $st): ?>
                                    <option value="<?php echo $st['id']; ?>">
                                        <?php echo htmlspecialchars($st['state_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <i class="fa-solid fa-chevron-down select-arrow"></i>
                        </div>

                        <!-- Search Box -->
                        <div class="filter-search-box">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="text" id="searchTempInput" placeholder="Cari nama IPT dicadangkan...">
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Nama Institusi Dicadangkan</th>
                                <th style="width: 160px;">Negeri</th>
                                <th style="width: 160px; text-align: center;">Tarikh Cadang</th>
                                <th style="width: 180px; text-align: center;">Status</th>
                                <th style="width: 240px; text-align: center;">Tindakan Pentadbir</th>
                            </tr>
                        </thead>
                        <tbody id="tempInstitutesTableBody">
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                                    <i class="fa-solid fa-spinner fa-spin"></i> Memuatkan senarai permohonan institusi...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <!-- ======================================================== -->
        <!-- TAB 6: PERMOHONAN KURSUS SEMENTARA (TEMP COURSES)        -->
        <!-- ======================================================== -->
        <div id="tab-temp_kursus" class="admin-tab-pane">
            <section class="glass-card" style="margin-bottom: 1.5rem; padding: 1.5rem 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.25rem;">
                    <div style="max-width: 750px;">
                        <h3 class="card-title" style="font-size: 1.3rem; margin-bottom: 0.35rem;">
                            <i class="fa-solid fa-graduation-cap" style="color: #10b981;"></i>
                            Pengurusan Permohonan Kursus Ditawarkan (temp_courses)
                        </h3>
                        <p class="card-subtitle" style="margin-bottom: 0; line-height: 1.5;">
                            Pengguna biasa memasukkan maklumat kursus baharu ke dalam jadual <code>temp_courses</code> dengan status lalai <strong>KIV</strong>.
                            Sebagai pentadbir, anda boleh menyemak permohonan kursus, mengubah status kepada <strong>Approve</strong>, dan menekan butang <strong>Update Course</strong> untuk memindahkan data ke jadual rasmi <code>courses</code>.
                        </p>
                    </div>

                    <!-- THE BUTTON: "Update Course" -->
                    <div>
                        <button type="button" id="btnUpdateCourse" class="btn-submit"
                            style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4); font-size: 0.95rem; padding: 0.75rem 1.6rem; display: inline-flex; align-items: center; gap: 0.55rem; border: none; cursor: pointer; color: #ffffff; font-weight: 700; border-radius: 10px;">
                            <i class="fa-solid fa-cloud-arrow-up" style="font-size: 1.1rem;"></i> Update Course
                        </button>
                    </div>
                </div>
            </section>

            <!-- Temp Courses Table Card -->
            <section class="glass-card">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem;">
                    <div>
                        <h3 class="card-title" style="margin-bottom: 0.2rem; font-size: 1.25rem;">
                            <i class="fa-solid fa-table-list" style="color: #10b981;"></i>
                            Senarai Permohonan Kursus (temp_courses)
                        </h3>
                        <p class="card-subtitle" style="margin-bottom: 0;">
                            Tukar status kepada "Approve" bagi permohonan yang sah, kemudian klik "Update Course".
                        </p>
                    </div>

                    <div class="filter-group">
                        <!-- Filter Status -->
                        <div class="select-wrapper" style="min-width: 140px;">
                            <select id="filterTempCourseStatus" class="custom-select" style="padding: 0.55rem 2rem 0.55rem 0.8rem; font-size: 0.85rem;">
                                <option value="all">-- Semua Status --</option>
                                <option value="KIV">Status: KIV</option>
                                <option value="Approve">Status: Approve</option>
                            </select>
                            <i class="fa-solid fa-chevron-down select-arrow"></i>
                        </div>

                        <!-- Filter State -->
                        <div class="select-wrapper" style="min-width: 160px;">
                            <select id="filterTempCourseState" class="custom-select" style="padding: 0.55rem 2rem 0.55rem 0.8rem; font-size: 0.85rem;">
                                <option value="0">-- Semua Negeri --</option>
                                <?php foreach ($states as $st): ?>
                                    <option value="<?php echo $st['id']; ?>">
                                        <?php echo htmlspecialchars($st['state_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <i class="fa-solid fa-chevron-down select-arrow"></i>
                        </div>

                        <!-- Filter Institute -->
                        <div class="select-wrapper" style="min-width: 200px;">
                            <select id="filterTempCourseInstitute" class="custom-select" style="padding: 0.55rem 2rem 0.55rem 0.8rem; font-size: 0.85rem;">
                                <option value="0">-- Semua Institusi (IPT) --</option>
                                <?php foreach ($institutes as $inst): ?>
                                    <option value="<?php echo $inst['id']; ?>" data-state="<?php echo $inst['state_id']; ?>">
                                        <?php echo htmlspecialchars($inst['institute_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <i class="fa-solid fa-chevron-down select-arrow"></i>
                        </div>

                        <!-- Filter Level -->
                        <div class="select-wrapper" style="min-width: 140px;">
                            <select id="filterTempCourseLevel" class="custom-select" style="padding: 0.55rem 2rem 0.55rem 0.8rem; font-size: 0.85rem;">
                                <option value="all">-- Semua Tahap --</option>
                                <option value="Diploma">Diploma</option>
                                <option value="Degree">Degree</option>
                                <option value="Certificate">Certificate / Sijil</option>
                                <option value="SKM">SKM</option>
                                <option value="Master">Master</option>
                            </select>
                            <i class="fa-solid fa-chevron-down select-arrow"></i>
                        </div>

                        <!-- Search Box -->
                        <div class="filter-search-box">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="text" id="searchTempCourseInput" placeholder="Cari nama kursus / IPT...">
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Nama Kursus Dicadangkan</th>
                                <th>Institusi (IPT) & Negeri</th>
                                <th style="width: 130px; text-align: center;">Tahap</th>
                                <th style="width: 150px;">Kampus / Cawangan</th>
                                <th style="width: 150px; text-align: center;">Tarikh Cadang</th>
                                <th style="width: 180px; text-align: center;">Status</th>
                                <th style="width: 240px; text-align: center;">Tindakan Pentadbir</th>
                            </tr>
                        </thead>
                        <tbody id="tempCoursesTableBody">
                            <tr>
                                <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                                    <i class="fa-solid fa-spinner fa-spin"></i> Memuatkan senarai permohonan kursus...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <!-- ======================================================== -->
        <!-- TAB 7: PERMOHONAN KEMAHIRAN SEMENTARA (TEMP SKILLS)      -->
        <!-- ======================================================== -->
        <div id="tab-temp_skills" class="admin-tab-pane">
            <section class="glass-card" style="margin-bottom: 1.5rem; padding: 1.5rem 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.25rem;">
                    <div style="max-width: 750px;">
                        <h3 class="card-title" style="font-size: 1.3rem; margin-bottom: 0.35rem;">
                            <i class="fa-solid fa-brain" style="color: #a855f7;"></i>
                            Pengurusan Permohonan Kemahiran (temp_skills)
                        </h3>
                        <p class="card-subtitle" style="margin-bottom: 0; line-height: 1.5;">
                            Pengguna biasa memasukkan maklumat kemahiran baharu ke dalam jadual <code>temp_skills</code> dengan status lalai <strong>KIV</strong>.
                            Pentadbir boleh menyemak, menukar status kepada <strong>Approve</strong>, atau memindahkan semua rekod yang diluluskan terus ke senarai kemahiran rasmi.
                        </p>
                    </div>

                    <!-- Action: Update Skills button -->
                    <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                        <button type="button" class="btn-submit" id="btnUpdateSkill" style="background: linear-gradient(135deg, #a855f7 0%, #7e22ce 100%); padding: 0.7rem 1.4rem; font-size: 0.9rem;">
                            <i class="fa-solid fa-cloud-arrow-up"></i> Update Kemahiran
                        </button>
                    </div>
                </div>
            </section>

            <!-- Table Card for Temp Skills -->
            <section class="glass-card">
                <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem;">
                    <div>
                        <h3 class="card-title" style="margin-bottom: 0.2rem; font-size: 1.25rem;">
                            <i class="fa-solid fa-table-list" style="color: #a855f7;"></i>
                            Senarai Permohonan Kemahiran (temp_skills)
                        </h3>
                        <p class="card-subtitle" style="margin-bottom: 0;">
                            Tukar status kepada "Approve" bagi permohonan yang sah, kemudian klik "Update Kemahiran".
                        </p>
                    </div>
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <span class="badge-step" id="countTempSkillsApproved" style="background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.4);">0 Sedia Dipindahkan</span>
                    </div>
                </div>

                <!-- Filter Toolbar -->
                <div class="filter-toolbar">
                    <div class="filter-group">
                        <!-- Status Filter -->
                        <div class="select-wrapper" style="min-width: 150px;">
                            <select id="filterTempSkillStatus" class="custom-select status-quick-select" style="padding: 0.55rem 2rem 0.55rem 0.85rem !important;">
                                <option value="all">Semua Status</option>
                                <option value="KIV">Status: KIV (Semakan)</option>
                                <option value="Approve">Status: Approve (Lulus)</option>
                            </select>
                            <i class="fa-solid fa-chevron-down select-arrow"></i>
                        </div>

                        <!-- Skill Type Filter -->
                        <div class="select-wrapper" style="min-width: 170px;">
                            <select id="filterTempSkillType" class="custom-select status-quick-select" style="padding: 0.55rem 2rem 0.55rem 0.85rem !important;">
                                <option value="all">Semua Jenis Kemahiran</option>
                                <option value="technical">Technical (Teknikal)</option>
                                <option value="soft">Soft (Insaniah)</option>
                            </select>
                            <i class="fa-solid fa-chevron-down select-arrow"></i>
                        </div>

                        <!-- Qualification Level Filter -->
                        <div class="select-wrapper" style="min-width: 170px;">
                            <select id="filterTempSkillLevel" class="custom-select status-quick-select" style="padding: 0.55rem 2rem 0.55rem 0.85rem !important;">
                                <option value="all">Semua Tahap Kelayakan</option>
                                <option value="SKM">SKM</option>
                                <option value="SPM">SPM</option>
                                <option value="Diploma">Diploma</option>
                                <option value="Degree">Degree</option>
                                <option value="Master">Master</option>
                            </select>
                            <i class="fa-solid fa-chevron-down select-arrow"></i>
                        </div>

                        <!-- Search Box -->
                        <div class="filter-search-box">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="text" id="searchTempSkillInput" placeholder="Cari nama kemahiran...">
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Nama Kemahiran Dicadangkan</th>
                                <th style="width: 150px; text-align: center;">Jenis Kemahiran</th>
                                <th style="width: 130px; text-align: center;">Tahap</th>
                                <th>Keterangan</th>
                                <th style="width: 150px; text-align: center;">Tarikh Cadang</th>
                                <th style="width: 180px; text-align: center;">Status</th>
                                <th style="width: 240px; text-align: center;">Tindakan Pentadbir</th>
                            </tr>
                        </thead>
                        <tbody id="tempSkillsTableBody">
                            <tr>
                                <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                                    <i class="fa-solid fa-spinner fa-spin"></i> Memuatkan senarai permohonan kemahiran...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <footer>
            <p>&copy; <?php echo date('Y'); ?> Sistem Tinjauan IPT Malaysia | Admin Control Panel CRUD</p>
        </footer>
    </div>

    <!-- ========================================== -->
    <!-- MODAL 1: KEMAS KINI NEGERI (EDIT STATE)   -->
    <!-- ========================================== -->
    <div id="modalEditState" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-header">
                <div class="modal-title">
                    <i class="fa-solid fa-pen-to-square" style="color: var(--primary-glow);"></i> Kemas Kini Nama Negeri
                </div>
                <button type="button" class="modal-close" onclick="closeModal('modalEditState')">&times;</button>
            </div>
            <form id="editStateForm">
                <input type="hidden" id="edit_state_id" name="state_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="edit_state_name">Nama Negeri</label>
                        <input type="text" id="edit_state_name" name="state_name" class="custom-input" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('modalEditState')">Batal</button>
                    <button type="submit" class="btn-submit" style="padding: 0.6rem 1.4rem;">
                        <i class="fa-solid fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL 2: KEMAS KINI IPT (EDIT INSTITUTE)  -->
    <!-- ========================================== -->
    <div id="modalEditInstitute" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-header">
                <div class="modal-title">
                    <i class="fa-solid fa-pen-to-square" style="color: #fbbf24;"></i> Kemas Kini Institusi (IPT)
                </div>
                <button type="button" class="modal-close" onclick="closeModal('modalEditInstitute')">&times;</button>
            </div>
            <form id="editInstituteForm">
                <input type="hidden" id="edit_institute_id" name="institute_id">
                <div class="modal-body">
                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label class="form-label" for="edit_ipt_state_id">Negeri Tempat IPT</label>
                        <div class="select-wrapper">
                            <select id="edit_ipt_state_id" name="state_id" class="custom-select" required>
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

                    <div class="form-group">
                        <label class="form-label" for="edit_institute_name">Nama Institusi / IPT</label>
                        <input type="text" id="edit_institute_name" name="institute_name" class="custom-input" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary"
                        onclick="closeModal('modalEditInstitute')">Batal</button>
                    <button type="submit" class="btn-submit" style="padding: 0.6rem 1.4rem;">
                        <i class="fa-solid fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL 3: KEMAS KINI KURSUS (EDIT COURSE)  -->
    <!-- ========================================== -->
    <div id="modalEditCourse" class="modal-overlay">
        <div class="modal-card" style="max-width: 600px;">
            <div class="modal-header">
                <div class="modal-title">
                    <i class="fa-solid fa-pen-to-square" style="color: #10b981;"></i> Kemas Kini Kursus Ditawarkan
                </div>
                <button type="button" class="modal-close" onclick="closeModal('modalEditCourse')">&times;</button>
            </div>
            <form id="editCourseForm">
                <input type="hidden" id="edit_course_id" name="course_id">
                <div class="modal-body">
                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label class="form-label" for="edit_course_institute_id">Institusi (IPT)</label>
                        <div class="select-wrapper">
                            <select id="edit_course_institute_id" name="institute_id" class="custom-select" required>
                                <option value="">-- Pilih Institusi --</option>
                                <?php foreach ($institutes as $inst): ?>
                                    <option value="<?php echo $inst['id']; ?>">
                                        <?php echo htmlspecialchars($inst['institute_name']) . ' (' . htmlspecialchars($inst['state_name']) . ')'; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <i class="fa-solid fa-chevron-down select-arrow"></i>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label class="form-label" for="edit_course_name">Nama Kursus / Program</label>
                        <input type="text" id="edit_course_name" name="course_name" class="custom-input" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label class="form-label" for="edit_qualification_level">Tahap Pengajian</label>
                            <div class="select-wrapper">
                                <select id="edit_qualification_level" name="qualification_level" class="custom-select"
                                    required>
                                    <option value="Diploma">Diploma</option>
                                    <option value="Degree">Degree</option>
                                    <option value="Certificate">Certificate / Sijil</option>
                                </select>
                                <i class="fa-solid fa-chevron-down select-arrow"></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="edit_campus_branch">Kampus / Cawangan</label>
                            <input type="text" id="edit_campus_branch" name="campus_branch" class="custom-input"
                                required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('modalEditCourse')">Batal</button>
                    <button type="submit" class="btn-submit" style="padding: 0.6rem 1.4rem;">
                        <i class="fa-solid fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL 4: KEMAS KINI PERMOHONAN IPT (EDIT TEMP INSTITUTE) -->
    <!-- ======================================================== -->
    <div id="modalEditTempInstitute" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-header">
                <div class="modal-title">
                    <i class="fa-solid fa-pen-to-square" style="color: #f59e0b;"></i> Semak / Kemas Kini Permohonan IPT
                </div>
                <button type="button" class="modal-close" onclick="closeModal('modalEditTempInstitute')">&times;</button>
            </div>
            <form id="editTempInstituteForm">
                <input type="hidden" id="edit_temp_id" name="id">
                <div class="modal-body">
                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label class="form-label" for="edit_temp_state_id">Negeri</label>
                        <div class="select-wrapper">
                            <select id="edit_temp_state_id" name="state_id" class="custom-select" required>
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

                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label class="form-label" for="edit_temp_institute_name">Nama Institusi / IPT</label>
                        <input type="text" id="edit_temp_institute_name" name="institute_name" class="custom-input" required>
                    </div>

                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label class="form-label" for="edit_temp_status">Status Permohonan</label>
                        <div class="select-wrapper">
                            <select id="edit_temp_status" name="status" class="custom-select" required>
                                <option value="KIV">KIV (Dalam Semakan)</option>
                                <option value="Approve">Approve (Diluluskan)</option>
                            </select>
                            <i class="fa-solid fa-chevron-down select-arrow"></i>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('modalEditTempInstitute')">Batal</button>
                    <button type="submit" class="btn-submit" style="padding: 0.6rem 1.4rem;">
                        <i class="fa-solid fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL 5: KEMAS KINI PERMOHONAN KURSUS (EDIT TEMP COURSE) -->
    <!-- ======================================================== -->
    <div id="modalEditTempCourse" class="modal-overlay">
        <div class="modal-card" style="max-width: 600px;">
            <div class="modal-header">
                <div class="modal-title">
                    <i class="fa-solid fa-pen-to-square" style="color: #10b981;"></i> Semak / Kemas Kini Permohonan Kursus
                </div>
                <button type="button" class="modal-close" onclick="closeModal('modalEditTempCourse')">&times;</button>
            </div>
            <form id="editTempCourseForm">
                <input type="hidden" id="edit_temp_course_id" name="id">
                <div class="modal-body">
                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label class="form-label" for="edit_temp_course_institute_id">Institusi (IPT)</label>
                        <div class="select-wrapper">
                            <select id="edit_temp_course_institute_id" name="institute_id" class="custom-select" required>
                                <option value="">-- Pilih Institusi --</option>
                                <?php foreach ($institutes as $inst): ?>
                                    <option value="<?php echo $inst['id']; ?>">
                                        <?php echo htmlspecialchars($inst['institute_name']) . ' (' . htmlspecialchars($inst['state_name']) . ')'; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <i class="fa-solid fa-chevron-down select-arrow"></i>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label class="form-label" for="edit_temp_course_name">Nama Kursus / Program</label>
                        <input type="text" id="edit_temp_course_name" name="course_name" class="custom-input" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
                        <div class="form-group">
                            <label class="form-label" for="edit_temp_course_qualification_level">Tahap Pengajian</label>
                            <div class="select-wrapper">
                                <select id="edit_temp_course_qualification_level" name="qualification_level" class="custom-select" required>
                                    <option value="Diploma">Diploma</option>
                                    <option value="Degree">Degree / Sarjana Muda</option>
                                    <option value="Certificate">Certificate / Sijil</option>
                                    <option value="SKM">SKM</option>
                                    <option value="Master">Master</option>
                                </select>
                                <i class="fa-solid fa-chevron-down select-arrow"></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="edit_temp_course_campus_branch">Kampus / Cawangan</label>
                            <input type="text" id="edit_temp_course_campus_branch" name="campus_branch" class="custom-input" required>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 0.5rem;">
                        <label class="form-label" for="edit_temp_course_status">Status Permohonan</label>
                        <div class="select-wrapper">
                            <select id="edit_temp_course_status" name="status" class="custom-select" required>
                                <option value="KIV">KIV (Dalam Semakan)</option>
                                <option value="Approve">Approve (Diluluskan)</option>
                            </select>
                            <i class="fa-solid fa-chevron-down select-arrow"></i>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('modalEditTempCourse')">Batal</button>
                    <button type="submit" class="btn-submit" style="padding: 0.6rem 1.4rem;">
                        <i class="fa-solid fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL 6: KEMAS KINI PERMOHONAN KEMAHIRAN (EDIT TEMP SKILL) -->
    <!-- ======================================================== -->
    <div id="modalEditTempSkill" class="modal-overlay">
        <div class="modal-card" style="max-width: 600px;">
            <div class="modal-header">
                <div class="modal-title">
                    <i class="fa-solid fa-pen-to-square" style="color: #a855f7;"></i> Semak / Kemas Kini Permohonan Kemahiran
                </div>
                <button type="button" class="modal-close" onclick="closeModal('modalEditTempSkill')">&times;</button>
            </div>
            <form id="editTempSkillForm">
                <input type="hidden" id="edit_temp_skill_id" name="id">
                <div class="modal-body">
                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label class="form-label" for="edit_temp_skill_name">Nama Kemahiran</label>
                        <input type="text" id="edit_temp_skill_name" name="skill_name" class="custom-input" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
                        <div class="form-group">
                            <label class="form-label" for="edit_temp_skill_type">Jenis Kemahiran</label>
                            <div class="select-wrapper">
                                <select id="edit_temp_skill_type" name="skill_type" class="custom-select" required>
                                    <option value="technical">Technical (Teknikal)</option>
                                    <option value="soft">Soft (Insaniah)</option>
                                </select>
                                <i class="fa-solid fa-chevron-down select-arrow"></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="edit_temp_skill_qualification">Tahap Pengajian</label>
                            <div class="select-wrapper">
                                <select id="edit_temp_skill_qualification" name="qualification_level" class="custom-select" required>
                                    <option value="SKM">SKM</option>
                                    <option value="SPM">SPM</option>
                                    <option value="Diploma">Diploma</option>
                                    <option value="Degree">Degree / Sarjana Muda</option>
                                    <option value="Master">Master</option>
                                </select>
                                <i class="fa-solid fa-chevron-down select-arrow"></i>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label class="form-label" for="edit_temp_skill_description">Keterangan (Pilihan)</label>
                        <textarea id="edit_temp_skill_description" name="skill_description" class="custom-input" rows="3"></textarea>
                    </div>

                    <div class="form-group" style="margin-bottom: 0.5rem;">
                        <label class="form-label" for="edit_temp_skill_status">Status Permohonan</label>
                        <div class="select-wrapper">
                            <select id="edit_temp_skill_status" name="status" class="custom-select" required>
                                <option value="KIV">KIV (Dalam Semakan)</option>
                                <option value="Approve">Approve (Diluluskan)</option>
                            </select>
                            <i class="fa-solid fa-chevron-down select-arrow"></i>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('modalEditTempSkill')">Batal</button>
                    <button type="submit" class="btn-submit" style="background: linear-gradient(135deg, #a855f7 0%, #7e22ce 100%); padding: 0.6rem 1.4rem;">
                        <i class="fa-solid fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Client-side Logic Script -->
    <script>
        const currentUserId = <?php echo (int) ($currentUser['id'] ?? 0); ?>;
        const initialInstitutesData = <?php echo json_encode($institutes, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
        const initialStatesData = <?php echo json_encode($states, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

        // In-memory collections for active filtering & pagination
        let statesList = [];
        let institutesList = [];
        let tempInstitutesList = [];
        let tempCoursesList = [];
        let tempSkillsList = [];
        let coursesList = [];
        let currentCoursePage = 1;
        const coursesPerPage = 15;

        document.addEventListener('DOMContentLoaded', () => {
            const alertBox = document.getElementById('adminAlert');

            // 1. Tab Navigation Handling
            const tabButtons = document.querySelectorAll('.admin-tab-btn');
            const tabPanes = document.querySelectorAll('.admin-tab-pane');

            tabButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    tabButtons.forEach(b => b.classList.remove('active'));
                    tabPanes.forEach(p => p.classList.remove('active'));

                    btn.classList.add('active');
                    const targetTab = btn.getAttribute('data-tab');
                    const targetPane = document.getElementById(`tab-${targetTab}`);
                    if (targetPane) targetPane.classList.add('active');

                    // Update URL query parameter without full reload
                    const url = new URL(window.location);
                    url.searchParams.set('tab', targetTab);
                    window.history.replaceState({}, '', url);
                });
            });

            // Activate initial tab from URL param if present
            const urlParams = new URLSearchParams(window.location.search);
            const initialTab = urlParams.get('tab');
            if (initialTab && document.getElementById(`tab-${initialTab}`)) {
                const btn = document.querySelector(`.admin-tab-btn[data-tab="${initialTab}"]`);
                if (btn) btn.click();
            }

            // 2. Collapsible Course Form
            const btnToggleCourseForm = document.getElementById('btnToggleCourseForm');
            const courseFormWrapper = document.getElementById('courseFormWrapper');
            const iconCourseToggle = document.getElementById('iconCourseToggle');

            btnToggleCourseForm.addEventListener('click', () => {
                if (courseFormWrapper.style.display === 'none') {
                    courseFormWrapper.style.display = 'block';
                    btnToggleCourseForm.innerHTML = '<i class="fa-solid fa-chevron-up"></i> Lipat Borang';
                } else {
                    courseFormWrapper.style.display = 'none';
                    btnToggleCourseForm.innerHTML = '<i class="fa-solid fa-chevron-down"></i> Buka Borang';
                }
            });

            // 3. Cascading Dropdowns in Course Add Form
            const addCourseStateFilter = document.getElementById('add_course_state_filter');
            const addCourseInstituteId = document.getElementById('add_course_institute_id');

            addCourseStateFilter.addEventListener('change', () => {
                const selectedState = addCourseStateFilter.value;
                const options = addCourseInstituteId.querySelectorAll('option');

                options.forEach(opt => {
                    if (!opt.value) return;
                    const optState = opt.getAttribute('data-state');
                    if (selectedState === 'all' || optState === selectedState) {
                        opt.style.display = '';
                    } else {
                        opt.style.display = 'none';
                    }
                });

                // Reset selection if currently hidden
                const selectedOpt = addCourseInstituteId.selectedOptions[0];
                if (selectedOpt && selectedOpt.style.display === 'none') {
                    addCourseInstituteId.value = '';
                }
            });

            // 4. Initial Data Load
            loadAdminStats();
            loadStates();
            loadInstitutes();
            loadTempInstitutes();
            loadTempCourses();
            loadTempSkills();
            loadCourses();
            loadAdminUsers();

            // 5. Search & Filter Listeners
            document.getElementById('searchStatesInput').addEventListener('input', filterStatesTable);
            document.getElementById('filterIptState').addEventListener('change', loadInstitutes);
            document.getElementById('searchIptInput').addEventListener('input', filterInstitutesTable);

            // Temp Institutes Filter Listeners & "Update Institure" Button
            document.getElementById('filterTempStatus').addEventListener('change', loadTempInstitutes);
            document.getElementById('filterTempState').addEventListener('change', loadTempInstitutes);
            document.getElementById('searchTempInput').addEventListener('input', filterTempInstitutesTable);
            document.getElementById('btnUpdateInstitute').addEventListener('click', () => transferApprovedInstitutes());

            // Temp Courses Filter Listeners & "Update Course" Button
            document.getElementById('filterTempCourseStatus').addEventListener('change', loadTempCourses);
            document.getElementById('filterTempCourseState').addEventListener('change', () => {
                syncTempCourseInstituteFilter();
                loadTempCourses();
            });
            document.getElementById('filterTempCourseInstitute').addEventListener('change', loadTempCourses);
            document.getElementById('filterTempCourseLevel').addEventListener('change', loadTempCourses);
            document.getElementById('searchTempCourseInput').addEventListener('input', filterTempCoursesTable);
            document.getElementById('btnUpdateCourse').addEventListener('click', () => transferApprovedCourses());

            // Temp Skills Filter Listeners & "Update Kemahiran" Button
            document.getElementById('filterTempSkillStatus').addEventListener('change', loadTempSkills);
            document.getElementById('filterTempSkillType').addEventListener('change', loadTempSkills);
            document.getElementById('filterTempSkillLevel').addEventListener('change', loadTempSkills);
            document.getElementById('searchTempSkillInput').addEventListener('input', filterTempSkillsTable);
            document.getElementById('btnUpdateSkill').addEventListener('click', () => transferApprovedSkills());

            // Integrated Course Filters
            document.getElementById('courseFilterState').addEventListener('change', () => {
                syncCourseInstituteFilter();
                currentCoursePage = 1;
                filterAndRenderCourses();
            });
            document.getElementById('courseFilterInstitute').addEventListener('change', () => {
                currentCoursePage = 1;
                filterAndRenderCourses();
            });
            document.getElementById('courseFilterLevel').addEventListener('change', () => {
                currentCoursePage = 1;
                filterAndRenderCourses();
            });
            document.getElementById('searchCourseInput').addEventListener('input', () => {
                currentCoursePage = 1;
                filterAndRenderCourses();
            });

            // Course Pagination buttons
            document.getElementById('btnCoursePrev').addEventListener('click', () => {
                if (currentCoursePage > 1) {
                    currentCoursePage--;
                    renderCoursesPage();
                }
            });
            document.getElementById('btnCourseNext').addEventListener('click', () => {
                const totalPages = Math.ceil(getFilteredCourses().length / coursesPerPage) || 1;
                if (currentCoursePage < totalPages) {
                    currentCoursePage++;
                    renderCoursesPage();
                }
            });

            // 6. Form Submit Handlers
            document.getElementById('addStateForm').addEventListener('submit', (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                formData.append('action', 'add_state');
                sendAjax('api.php', formData, () => {
                    e.target.reset();
                    loadStates();
                    loadAdminStats();
                });
            });

            document.getElementById('addInstituteForm').addEventListener('submit', (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                formData.append('action', 'add_institute');
                sendAjax('api.php', formData, () => {
                    e.target.reset();
                    loadInstitutes();
                    loadAdminStats();
                });
            });

            document.getElementById('addCourseForm').addEventListener('submit', (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                formData.append('action', 'add_course');
                sendAjax('api.php', formData, () => {
                    document.getElementById('add_course_name').value = '';
                    loadCourses();
                    loadAdminStats();
                });
            });

            document.getElementById('addUserForm').addEventListener('submit', (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                formData.append('action', 'add_user');
                sendAjax('api.php', formData, () => {
                    e.target.reset();
                    loadAdminUsers();
                    loadAdminStats();
                });
            });

            // 7. Modal Form Submit Handlers (Updates)
            document.getElementById('editStateForm').addEventListener('submit', (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                formData.append('action', 'update_state');
                sendAjax('api.php', formData, () => {
                    closeModal('modalEditState');
                    loadStates();
                });
            });

            document.getElementById('editInstituteForm').addEventListener('submit', (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                formData.append('action', 'update_institute');
                sendAjax('api.php', formData, () => {
                    closeModal('modalEditInstitute');
                    loadInstitutes();
                });
            });

            document.getElementById('editCourseForm').addEventListener('submit', (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                formData.append('action', 'update_course');
                sendAjax('api.php', formData, () => {
                    closeModal('modalEditCourse');
                    loadCourses();
                });
            });

            document.getElementById('editTempInstituteForm').addEventListener('submit', (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                formData.append('action', 'update_temp_institute');
                sendAjax('api.php', formData, () => {
                    closeModal('modalEditTempInstitute');
                    loadTempInstitutes();
                    loadAdminStats();
                });
            });

            document.getElementById('editTempCourseForm').addEventListener('submit', (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                formData.append('action', 'update_temp_course');
                sendAjax('api.php', formData, () => {
                    closeModal('modalEditTempCourse');
                    loadTempCourses();
                    loadAdminStats();
                });
            });

            document.getElementById('editTempSkillForm').addEventListener('submit', (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                formData.append('action', 'update_temp_skill');
                sendAjax('api.php', formData, () => {
                    closeModal('modalEditTempSkill');
                    loadTempSkills();
                    loadAdminStats();
                });
            });
        });

        // ==========================================
        // DATA LOADERS & RENDERERS
        // ==========================================
        function loadAdminStats() {
            fetch('api.php?action=get_admin_stats')
                .then(r => r.json())
                .then(res => {
                    if (res.status === 'success' && res.data) {
                        document.getElementById('kpiTotalStates').textContent = res.data.total_states || 0;
                        document.getElementById('kpiTotalInstitutes').textContent = res.data.total_institutes || 0;
                        document.getElementById('kpiTotalCourses').textContent = res.data.total_courses || 0;
                        document.getElementById('kpiTotalUsers').textContent = res.data.total_users || 0;

                        // Temp Institutes KPIs & Badges
                        const totalTemp = parseInt(res.data.total_temp_institutes || 0);
                        const kivCount = parseInt(res.data.total_temp_kiv || 0);
                        const approveCount = parseInt(res.data.total_temp_approve || 0);

                        const kpiTemp = document.getElementById('kpiTotalTempInstitutes');
                        if (kpiTemp) kpiTemp.textContent = totalTemp;

                        const badgeKiv = document.getElementById('badgeKivCount');
                        if (badgeKiv) {
                            if (kivCount > 0) {
                                badgeKiv.textContent = `${kivCount} KIV`;
                                badgeKiv.style.display = 'inline-block';
                            } else {
                                badgeKiv.style.display = 'none';
                            }
                        }

                        const banner = document.getElementById('tempInstituteBanner');
                        if (banner) {
                            if (kivCount > 0 || approveCount > 0) {
                                banner.style.display = 'flex';
                                const bKiv = document.getElementById('bannerKivCount');
                                const bApprove = document.getElementById('bannerApproveCount');
                                if (bKiv) bKiv.textContent = kivCount;
                                if (bApprove) bApprove.textContent = approveCount;
                            } else {
                                banner.style.display = 'none';
                            }
                        }

                        // Temp Courses KPIs & Badges
                        const totalTempCourses = parseInt(res.data.total_temp_courses || 0);
                        const courseKivCount = parseInt(res.data.total_temp_courses_kiv || 0);
                        const courseApproveCount = parseInt(res.data.total_temp_courses_approve || 0);

                        const kpiTempCourse = document.getElementById('kpiTotalTempCourses');
                        if (kpiTempCourse) kpiTempCourse.textContent = totalTempCourses;

                        const badgeCourseKiv = document.getElementById('badgeCourseKivCount');
                        if (badgeCourseKiv) {
                            if (courseKivCount > 0) {
                                badgeCourseKiv.textContent = `${courseKivCount} KIV`;
                                badgeCourseKiv.style.display = 'inline-block';
                            } else {
                                badgeCourseKiv.style.display = 'none';
                            }
                        }

                        const bannerCourse = document.getElementById('tempCourseBanner');
                        if (bannerCourse) {
                            if (courseKivCount > 0 || courseApproveCount > 0) {
                                bannerCourse.style.display = 'flex';
                                const bCKiv = document.getElementById('bannerCourseKivCount');
                                const bCApprove = document.getElementById('bannerCourseApproveCount');
                                if (bCKiv) bCKiv.textContent = courseKivCount;
                                if (bCApprove) bCApprove.textContent = courseApproveCount;
                            } else {
                                bannerCourse.style.display = 'none';
                            }
                        }

                        // Temp Skills KPIs & Badges
                        const totalTempSkills = parseInt(res.data.total_temp_skills || 0);
                        const skillKivCount = parseInt(res.data.total_temp_skills_kiv || 0);
                        const skillApproveCount = parseInt(res.data.total_temp_skills_approve || 0);

                        const kpiTempSkill = document.getElementById('kpiTotalTempSkills');
                        if (kpiTempSkill) kpiTempSkill.textContent = totalTempSkills;

                        const badgeSkillKiv = document.getElementById('badgeSkillKivCount');
                        if (badgeSkillKiv) {
                            if (skillKivCount > 0) {
                                badgeSkillKiv.textContent = `${skillKivCount} KIV`;
                                badgeSkillKiv.style.display = 'inline-block';
                            } else {
                                badgeSkillKiv.style.display = 'none';
                            }
                        }

                        const countApprovedSkill = document.getElementById('countTempSkillsApproved');
                        if (countApprovedSkill) {
                            countApprovedSkill.textContent = `${skillApproveCount} Sedia Dipindahkan`;
                        }
                    }
                })
                .catch(() => { });
        }

        // --- States CRUD ---
        function loadStates() {
            const tbody = document.getElementById('statesTableBody');
            fetch('api.php?action=get_states_admin')
                .then(r => r.json())
                .then(res => {
                    if (res.status === 'success' && res.data) {
                        statesList = res.data;
                        renderStatesTable(statesList);
                    }
                })
                .catch(err => {
                    tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; color:#ef4444;">Gagal memuatkan data negeri: ${escapeHtml(err.message)}</td></tr>`;
                });
        }

        function renderStatesTable(list) {
            const tbody = document.getElementById('statesTableBody');
            if (list.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; color:var(--text-muted); padding:2rem;">Tiada negeri dijumpai.</td></tr>';
                return;
            }

            tbody.innerHTML = list.map((st, idx) => `
                <tr>
                    <td>${idx + 1}</td>
                    <td><strong style="color:#f8fafc; font-size:1rem;">${escapeHtml(st.state_name)}</strong></td>
                    <td style="text-align:center;">
                        <span class="badge-step" style="font-size:0.75rem;">${st.total_institutes} IPT</span>
                    </td>
                    <td style="text-align:center;">
                        <span class="badge-step" style="font-size:0.75rem; background:rgba(16,185,129,0.15); color:#34d399; border-color:rgba(16,185,129,0.35);">
                            ${st.total_courses} Kursus
                        </span>
                    </td>
                    <td style="text-align:center;">
                        ${st.total_submissions > 0
                    ? `<span style="color:#38bdf8; font-weight:700;"><i class="fa-solid fa-users"></i> ${st.total_submissions} Responden</span>`
                    : '<span style="color:var(--text-muted);">0</span>'
                }
                    </td>
                    <td style="text-align:center;">
                        <div style="display:inline-flex; gap:0.4rem;">
                            <button type="button" class="btn-action-edit" onclick="openEditState(${st.id}, '${escapeJs(st.state_name)}')">
                                <i class="fa-solid fa-pen"></i> Edit
                            </button>
                            <button type="button" class="btn-action-delete" onclick="deleteState(${st.id}, '${escapeJs(st.state_name)}', ${st.total_submissions})">
                                <i class="fa-solid fa-trash"></i> Padam
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        function filterStatesTable() {
            const query = document.getElementById('searchStatesInput').value.toLowerCase().trim();
            const filtered = statesList.filter(s => s.state_name.toLowerCase().includes(query));
            renderStatesTable(filtered);
        }

        function openEditState(id, name) {
            document.getElementById('edit_state_id').value = id;
            document.getElementById('edit_state_name').value = name;
            openModal('modalEditState');
        }

        function deleteState(id, name, submissionCount) {
            if (submissionCount > 0) {
                showAlert(`Negeri "${name}" tidak boleh dipadam kerana terdapat ${submissionCount} rekod tinjauan responden dipautkan kepadanya.`, 'error');
                return;
            }
            if (!confirm(`Adakah anda pasti ingin memadam negeri "${name}"?\nPerhatian: Tindakan ini turut memadam semua IPT dan kursus di bawah negeri ini.`)) {
                return;
            }
            const fd = new FormData();
            fd.append('action', 'delete_state');
            fd.append('state_id', id);
            sendAjax('api.php', fd, () => {
                loadStates();
                loadAdminStats();
            });
        }

        // --- Institutes CRUD ---
        function loadInstitutes() {
            const tbody = document.getElementById('institutesTableBody');
            const stateId = document.getElementById('filterIptState').value;
            fetch(`api.php?action=get_institutes_admin&state_id=${stateId}`)
                .then(r => r.json())
                .then(res => {
                    if (res.status === 'success' && res.data) {
                        institutesList = res.data;
                        renderInstitutesTable(institutesList);
                    }
                })
                .catch(err => {
                    tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; color:#ef4444;">Gagal memuatkan data IPT: ${escapeHtml(err.message)}</td></tr>`;
                });
        }

        function renderInstitutesTable(list) {
            const tbody = document.getElementById('institutesTableBody');
            if (list.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; color:var(--text-muted); padding:2rem;">Tiada institusi dijumpai bagi tapisan semasa.</td></tr>';
                return;
            }

            tbody.innerHTML = list.map((ipt, idx) => `
                <tr>
                    <td>${idx + 1}</td>
                    <td>
                        <strong style="color:#f8fafc; font-size:0.95rem;">${escapeHtml(ipt.institute_name)}</strong>
                    </td>
                    <td>
                        <span class="badge-step" style="font-size:0.75rem; color:#38bdf8; border-color:rgba(56,189,248,0.35);">
                            <i class="fa-solid fa-location-dot"></i> ${escapeHtml(ipt.state_name)}
                        </span>
                    </td>
                    <td style="text-align:center;">
                        <span class="badge-step" style="font-size:0.75rem; background:rgba(16,185,129,0.15); color:#34d399; border-color:rgba(16,185,129,0.35);">
                            ${ipt.total_courses} Kursus
                        </span>
                    </td>
                    <td style="text-align:center;">
                        ${ipt.total_submissions > 0
                    ? `<span style="color:#38bdf8; font-weight:700;"><i class="fa-solid fa-users"></i> ${ipt.total_submissions}</span>`
                    : '<span style="color:var(--text-muted);">0</span>'
                }
                    </td>
                    <td style="text-align:center;">
                        <div style="display:inline-flex; gap:0.4rem;">
                            <button type="button" class="btn-action-edit" onclick="openEditInstitute(${ipt.id}, ${ipt.state_id}, '${escapeJs(ipt.institute_name)}')">
                                <i class="fa-solid fa-pen"></i> Edit
                            </button>
                            <button type="button" class="btn-action-delete" onclick="deleteInstitute(${ipt.id}, '${escapeJs(ipt.institute_name)}', ${ipt.total_submissions})">
                                <i class="fa-solid fa-trash"></i> Padam
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        function filterInstitutesTable() {
            const query = document.getElementById('searchIptInput').value.toLowerCase().trim();
            const filtered = institutesList.filter(i =>
                i.institute_name.toLowerCase().includes(query) ||
                i.state_name.toLowerCase().includes(query)
            );
            renderInstitutesTable(filtered);
        }

        function openEditInstitute(id, stateId, name) {
            document.getElementById('edit_institute_id').value = id;
            document.getElementById('edit_ipt_state_id').value = stateId;
            document.getElementById('edit_institute_name').value = name;
            openModal('modalEditInstitute');
        }

        function deleteInstitute(id, name, submissionCount) {
            if (submissionCount > 0) {
                showAlert(`Institusi "${name}" tidak boleh dipadam kerana terdapat ${submissionCount} rekod tinjauan responden dipautkan kepadanya.`, 'error');
                return;
            }
            if (!confirm(`Adakah anda pasti ingin memadam institusi "${name}"?\nPerhatian: Tindakan ini turut memadam semua kursus di bawah institusi ini.`)) {
                return;
            }
            const fd = new FormData();
            fd.append('action', 'delete_institute');
            fd.append('institute_id', id);
            sendAjax('api.php', fd, () => {
                loadInstitutes();
                loadAdminStats();
            });
        }

        // --- Courses CRUD & Pagination ---
        function loadCourses() {
            const tbody = document.getElementById('coursesTableBody');
            fetch('api.php?action=get_courses_admin')
                .then(r => r.json())
                .then(res => {
                    if (res.status === 'success' && res.data) {
                        coursesList = res.data;
                        currentCoursePage = 1;
                        filterAndRenderCourses();
                    }
                })
                .catch(err => {
                    tbody.innerHTML = `<tr><td colspan="8" style="text-align:center; color:#ef4444;">Gagal memuatkan data kursus: ${escapeHtml(err.message)}</td></tr>`;
                });
        }

        function syncCourseInstituteFilter() {
            const selectedState = parseInt(document.getElementById('courseFilterState').value) || 0;
            const iptSelect = document.getElementById('courseFilterInstitute');
            const options = iptSelect.querySelectorAll('option');

            options.forEach(opt => {
                if (!opt.value || opt.value === '0') return;
                const optState = parseInt(opt.getAttribute('data-state')) || 0;
                if (selectedState === 0 || optState === selectedState) {
                    opt.style.display = '';
                } else {
                    opt.style.display = 'none';
                }
            });

            // If selected institute does not belong to newly selected state, reset to 0
            const currentSelected = iptSelect.selectedOptions[0];
            if (currentSelected && currentSelected.style.display === 'none') {
                iptSelect.value = '0';
            }
        }

        function getFilteredCourses() {
            const stateFilter = parseInt(document.getElementById('courseFilterState').value) || 0;
            const instFilter = parseInt(document.getElementById('courseFilterInstitute').value) || 0;
            const levelFilter = document.getElementById('courseFilterLevel').value;
            const search = document.getElementById('searchCourseInput').value.toLowerCase().trim();

            return coursesList.filter(c => {
                const matchState = (stateFilter === 0) || (parseInt(c.state_id) === stateFilter);
                const matchInst = (instFilter === 0) || (parseInt(c.institute_id) === instFilter);
                const matchLevel = (levelFilter === 'all') || (c.qualification_level === levelFilter);
                const matchSearch = (!search) || (
                    c.course_name.toLowerCase().includes(search) ||
                    c.institute_name.toLowerCase().includes(search) ||
                    (c.campus_branch && c.campus_branch.toLowerCase().includes(search))
                );
                return matchState && matchInst && matchLevel && matchSearch;
            });
        }

        function filterAndRenderCourses() {
            const filtered = getFilteredCourses();
            document.getElementById('badgeCourseMatchCount').textContent = `${filtered.length} Kursus Dijumpai`;
            renderCoursesPage();
        }

        function renderCoursesPage() {
            const tbody = document.getElementById('coursesTableBody');
            const filtered = getFilteredCourses();
            const total = filtered.length;

            if (total === 0) {
                tbody.innerHTML = '<tr><td colspan="8" style="text-align:center; color:var(--text-muted); padding:2rem;">Tiada kursus dijumpai bagi kriteria tapisan semasa.</td></tr>';
                document.getElementById('coursePaginationInfo').textContent = 'Memaparkan 0 daripada 0 kursus';
                document.getElementById('coursePageIndicator').textContent = '0 / 0';
                document.getElementById('btnCoursePrev').disabled = true;
                document.getElementById('btnCourseNext').disabled = true;
                return;
            }

            const totalPages = Math.ceil(total / coursesPerPage);
            if (currentCoursePage > totalPages) currentCoursePage = totalPages;
            if (currentCoursePage < 1) currentCoursePage = 1;

            const startIndex = (currentCoursePage - 1) * coursesPerPage;
            const endIndex = Math.min(startIndex + coursesPerPage, total);
            const pageData = filtered.slice(startIndex, endIndex);

            tbody.innerHTML = pageData.map((c, idx) => {
                const rowNum = startIndex + idx + 1;
                const levelClass = (c.qualification_level || '').toLowerCase().includes('degree') ? 'degree'
                    : ((c.qualification_level || '').toLowerCase().includes('diploma') ? 'diploma' : 'certificate');

                return `
                    <tr>
                        <td>${rowNum}</td>
                        <td>
                            <strong style="color:#f8fafc; font-size:0.92rem;">${escapeHtml(c.course_name)}</strong>
                        </td>
                        <td style="text-align:center;">
                            <span class="level-badge ${levelClass}">
                                ${escapeHtml(c.qualification_level)}
                            </span>
                        </td>
                        <td>
                            <small style="color:#e2e8f0; font-weight:600;">${escapeHtml(c.institute_name)}</small>
                        </td>
                        <td>
                            <small style="color:var(--text-muted);">${escapeHtml(c.state_name)}</small>
                        </td>
                        <td>
                            <small style="color:var(--text-muted);">${escapeHtml(c.campus_branch || 'Kampus Utama')}</small>
                        </td>
                        <td style="text-align:center;">
                            ${c.total_submissions > 0
                        ? `<span style="color:#38bdf8; font-weight:700;"><i class="fa-solid fa-users"></i> ${c.total_submissions}</span>`
                        : '<span style="color:var(--text-muted);">0</span>'
                    }
                        </td>
                        <td style="text-align:center;">
                            <div style="display:inline-flex; gap:0.35rem;">
                                <button type="button" class="btn-action-edit" onclick="openEditCourse(${c.id}, ${c.institute_id}, '${escapeJs(c.course_name)}', '${escapeJs(c.qualification_level)}', '${escapeJs(c.campus_branch || '')}')">
                                    <i class="fa-solid fa-pen"></i> Edit
                                </button>
                                <button type="button" class="btn-action-delete" onclick="deleteCourse(${c.id}, '${escapeJs(c.course_name)}', ${c.total_submissions})">
                                    <i class="fa-solid fa-trash"></i> Padam
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');

            // Update Pagination Status
            document.getElementById('coursePaginationInfo').textContent = `Memaparkan ${startIndex + 1} - ${endIndex} daripada ${total} kursus`;
            document.getElementById('coursePageIndicator').textContent = `${currentCoursePage} / ${totalPages}`;
            document.getElementById('btnCoursePrev').disabled = (currentCoursePage <= 1);
            document.getElementById('btnCourseNext').disabled = (currentCoursePage >= totalPages);
        }

        function openEditCourse(id, instituteId, name, level, branch) {
            document.getElementById('edit_course_id').value = id;
            document.getElementById('edit_course_institute_id').value = instituteId;
            document.getElementById('edit_course_name').value = name;
            document.getElementById('edit_qualification_level').value = level;
            document.getElementById('edit_campus_branch').value = branch || 'Kampus Utama';
            openModal('modalEditCourse');
        }

        function deleteCourse(id, name, submissionCount) {
            if (submissionCount > 0) {
                showAlert(`Kursus "${name}" tidak boleh dipadam kerana terdapat ${submissionCount} rekod tinjauan responden yang berdaftar di bawahnya.`, 'error');
                return;
            }
            if (!confirm(`Adakah anda pasti ingin memadam kursus:\n"${name}"?`)) {
                return;
            }
            const fd = new FormData();
            fd.append('action', 'delete_course');
            fd.append('course_id', id);
            sendAjax('api.php', fd, () => {
                loadCourses();
                loadAdminStats();
            });
        }

        // ==========================================
        // TEMP INSTITUTES CRUD & UPDATE FUNCTION
        // ==========================================
        function loadTempInstitutes() {
            const tbody = document.getElementById('tempInstitutesTableBody');
            if (!tbody) return;
            const stateId = document.getElementById('filterTempState').value;
            const status = document.getElementById('filterTempStatus').value;
            const search = document.getElementById('searchTempInput').value.trim();

            tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; color: var(--text-muted); padding: 2rem;"><i class="fa-solid fa-spinner fa-spin"></i> Memuatkan senarai permohonan institusi...</td></tr>';

            const params = new URLSearchParams({
                action: 'get_temp_institutes_admin',
                state_id: stateId,
                status: status,
                search: search
            });

            fetch(`api.php?${params.toString()}`)
                .then(r => r.json())
                .then(res => {
                    if (res.status === 'success' && res.data) {
                        tempInstitutesList = res.data;
                        renderTempInstitutesTable(tempInstitutesList);
                    } else {
                        tbody.innerHTML = `<tr><td colspan="6" style="text-align: center; color: #ef4444; padding: 2rem;">${escapeHtml(res.message || 'Gagal memuatkan data.')}</td></tr>`;
                    }
                })
                .catch(err => {
                    tbody.innerHTML = `<tr><td colspan="6" style="text-align: center; color: #ef4444; padding: 2rem;">Ralat: ${escapeHtml(err.message)}</td></tr>`;
                });
        }

        function filterTempInstitutesTable() {
            const search = document.getElementById('searchTempInput').value.toLowerCase().trim();
            if (!search) {
                renderTempInstitutesTable(tempInstitutesList);
                return;
            }
            const filtered = tempInstitutesList.filter(item => {
                return item.institute_name.toLowerCase().includes(search) ||
                    (item.state_name && item.state_name.toLowerCase().includes(search));
            });
            renderTempInstitutesTable(filtered);
        }

        function renderTempInstitutesTable(list) {
            const tbody = document.getElementById('tempInstitutesTableBody');
            if (!tbody) return;

            if (list.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; color: var(--text-muted); padding: 2.5rem;">Tiada permohonan institusi dijumpai bagi tapisan semasa.</td></tr>';
                return;
            }

            tbody.innerHTML = list.map((t, idx) => {
                const isKiv = t.status === 'KIV';
                const statusBadge = isKiv
                    ? `<span class="badge-status-kiv"><i class="fa-solid fa-hourglass-half"></i> KIV</span>`
                    : `<span class="badge-status-approve"><i class="fa-solid fa-circle-check"></i> Approve</span>`;

                return `
                    <tr>
                        <td style="color:var(--text-muted); font-size:0.85rem;">${idx + 1}</td>
                        <td>
                            <strong style="color:#ffffff; font-size:0.95rem;">${escapeHtml(t.institute_name)}</strong>
                        </td>
                        <td>
                            <span class="badge-step" style="font-size:0.78rem;">
                                <i class="fa-solid fa-map-pin"></i> ${escapeHtml(t.state_name || 'Negeri #' + t.state_id)}
                            </span>
                        </td>
                        <td style="text-align:center; font-size:0.82rem; color:var(--text-muted);">
                            ${escapeHtml(t.created_at)}
                        </td>
                        <td style="text-align:center;">
                            <div style="display:flex; flex-direction:column; align-items:center; gap:0.35rem;">
                                ${statusBadge}
                                <select class="status-quick-select" onchange="changeTempStatus(${t.id}, this.value)" title="Tukar status permohonan">
                                    <option value="KIV" ${isKiv ? 'selected' : ''}>KIV</option>
                                    <option value="Approve" ${!isKiv ? 'selected' : ''}>Approve</option>
                                </select>
                            </div>
                        </td>
                        <td style="text-align:center;">
                            <div style="display:inline-flex; gap:0.4rem; align-items:center; flex-wrap:wrap; justify-content:center;">
                                ${isKiv ? `
                                    <button type="button" class="btn-action-approve" onclick="changeTempStatus(${t.id}, 'Approve')" title="Luluskan permohonan ini">
                                        <i class="fa-solid fa-check"></i> Luluskan
                                    </button>
                                ` : `
                                    <button type="button" class="btn-action-transfer" onclick="transferApprovedInstitutes(${t.id})" title="Pindahkan sekarang ke jadual institutes">
                                        <i class="fa-solid fa-cloud-arrow-up"></i> Pindahkan
                                    </button>
                                    <button type="button" class="btn-action-kiv" onclick="changeTempStatus(${t.id}, 'KIV')" title="Kembalikan status ke KIV">
                                        <i class="fa-solid fa-rotate-left"></i> KIV
                                    </button>
                                `}
                                <button type="button" class="btn-action-edit" onclick="openEditTempInstitute(${t.id}, ${t.state_id}, '${escapeJs(t.institute_name)}', '${t.status}')" title="Kemas kini maklumat">
                                    <i class="fa-solid fa-pen"></i> Edit
                                </button>
                                <button type="button" class="btn-action-delete" onclick="deleteTempInstitute(${t.id}, '${escapeJs(t.institute_name)}')" title="Padam permohonan">
                                    <i class="fa-solid fa-trash"></i> Padam
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function changeTempStatus(id, newStatus) {
            const fd = new FormData();
            fd.append('action', 'update_temp_institute_status');
            fd.append('id', id);
            fd.append('status', newStatus);
            sendAjax('api.php', fd, () => {
                loadTempInstitutes();
                loadAdminStats();
            });
        }

        // ========================================================
        // THE BUTTON FUNCTION: "Update Institure"
        // Transfers data from table "temp_institutes" into "institutes"
        // ========================================================
        function transferApprovedInstitutes(targetId = 0) {
            const isSingle = targetId > 0;
            const promptMsg = isSingle
                ? 'Adakah anda pasti ingin memindahkan institusi yang diluluskan ini ke jadual rasmi institutes?'
                : 'Fungsi [Update Institure]: Adakah anda pasti ingin memindahkan SEMUA institusi berstatus "Approve" dari temp_institutes ke jadual rasmi institutes?';

            if (!confirm(promptMsg)) return;

            const fd = new FormData();
            fd.append('action', 'transfer_temp_institutes');
            if (isSingle) {
                fd.append('id', targetId);
            }

            const btn = document.getElementById('btnUpdateInstitute');
            const originalHtml = btn ? btn.innerHTML : '';
            if (btn && !isSingle) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Sedang Memindahkan...';
            }

            sendAjax('api.php', fd, (res) => {
                if (btn && !isSingle) {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
                loadTempInstitutes();
                loadInstitutes();
                loadAdminStats();
            });
        }

        function openEditTempInstitute(id, stateId, name, status) {
            document.getElementById('edit_temp_id').value = id;
            document.getElementById('edit_temp_state_id').value = stateId;
            document.getElementById('edit_temp_institute_name').value = name;
            document.getElementById('edit_temp_status').value = status;
            openModal('modalEditTempInstitute');
        }

        function deleteTempInstitute(id, name) {
            if (!confirm(`Adakah anda pasti ingin memadam permohonan institusi "${name}" dari temp_institutes?`)) {
                return;
            }
            const fd = new FormData();
            fd.append('action', 'delete_temp_institute');
            fd.append('id', id);
            sendAjax('api.php', fd, () => {
                loadTempInstitutes();
                loadAdminStats();
            });
        }

        // ==========================================
        // TEMP COURSES CRUD & UPDATE FUNCTION
        // ==========================================
        function loadTempCourses() {
            const tbody = document.getElementById('tempCoursesTableBody');
            if (!tbody) return;
            const stateId = document.getElementById('filterTempCourseState').value;
            const instId = document.getElementById('filterTempCourseInstitute').value;
            const level = document.getElementById('filterTempCourseLevel').value;
            const status = document.getElementById('filterTempCourseStatus').value;
            const search = document.getElementById('searchTempCourseInput').value.trim();

            tbody.innerHTML = '<tr><td colspan="8" style="text-align: center; color: var(--text-muted); padding: 2rem;"><i class="fa-solid fa-spinner fa-spin"></i> Memuatkan senarai permohonan kursus...</td></tr>';

            const params = new URLSearchParams({
                action: 'get_temp_courses_admin',
                state_id: stateId,
                institute_id: instId,
                qualification_level: level,
                status: status,
                search: search
            });

            fetch(`api.php?${params.toString()}`)
                .then(r => r.json())
                .then(res => {
                    if (res.status === 'success' && res.data) {
                        tempCoursesList = res.data;
                        renderTempCoursesTable(tempCoursesList);
                    } else {
                        tbody.innerHTML = `<tr><td colspan="8" style="text-align: center; color: #ef4444; padding: 2rem;">${escapeHtml(res.message || 'Gagal memuatkan data kursus.')}</td></tr>`;
                    }
                })
                .catch(err => {
                    tbody.innerHTML = `<tr><td colspan="8" style="text-align: center; color: #ef4444; padding: 2rem;">Ralat: ${escapeHtml(err.message)}</td></tr>`;
                });
        }

        function syncTempCourseInstituteFilter() {
            const stateSelect = document.getElementById('filterTempCourseState');
            const instSelect = document.getElementById('filterTempCourseInstitute');
            if (!stateSelect || !instSelect) return;

            const selectedState = parseInt(stateSelect.value) || 0;
            const options = instSelect.querySelectorAll('option');

            options.forEach(opt => {
                if (!opt.value || opt.value === '0') return;
                const optState = parseInt(opt.getAttribute('data-state')) || 0;
                if (selectedState === 0 || optState === selectedState) {
                    opt.style.display = '';
                } else {
                    opt.style.display = 'none';
                }
            });

            const currentSelected = instSelect.selectedOptions[0];
            if (currentSelected && currentSelected.style.display === 'none') {
                instSelect.value = '0';
            }
        }

        function filterTempCoursesTable() {
            const search = document.getElementById('searchTempCourseInput').value.toLowerCase().trim();
            if (!search) {
                renderTempCoursesTable(tempCoursesList);
                return;
            }
            const filtered = tempCoursesList.filter(item => {
                return item.course_name.toLowerCase().includes(search) ||
                    (item.institute_name && item.institute_name.toLowerCase().includes(search)) ||
                    (item.state_name && item.state_name.toLowerCase().includes(search)) ||
                    (item.campus_branch && item.campus_branch.toLowerCase().includes(search));
            });
            renderTempCoursesTable(filtered);
        }

        function renderTempCoursesTable(list) {
            const tbody = document.getElementById('tempCoursesTableBody');
            if (!tbody) return;

            if (list.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" style="text-align: center; color: var(--text-muted); padding: 2.5rem;">Tiada permohonan kursus dijumpai bagi tapisan semasa.</td></tr>';
                return;
            }

            tbody.innerHTML = list.map((c, idx) => {
                const isKiv = c.status === 'KIV';
                const statusBadge = isKiv
                    ? `<span class="badge-status-kiv"><i class="fa-solid fa-hourglass-half"></i> KIV</span>`
                    : `<span class="badge-status-approve"><i class="fa-solid fa-circle-check"></i> Approve</span>`;

                const levelClass = getLevelBadgeClass(c.qualification_level);

                return `
                    <tr>
                        <td style="color:var(--text-muted); font-size:0.85rem;">${idx + 1}</td>
                        <td>
                            <strong style="color:#ffffff; font-size:0.95rem;">${escapeHtml(c.course_name)}</strong>
                        </td>
                        <td>
                            <div style="font-weight:600; color:#e2e8f0; font-size:0.88rem;">${escapeHtml(c.institute_name)}</div>
                            <small style="color:var(--text-muted);"><i class="fa-solid fa-map-pin"></i> ${escapeHtml(c.state_name || '')}</small>
                        </td>
                        <td style="text-align:center;">
                            <span class="level-badge ${levelClass}">${escapeHtml(c.qualification_level)}</span>
                        </td>
                        <td>
                            <small style="color:#cbd5e1;">${escapeHtml(c.campus_branch || 'Kampus Utama')}</small>
                        </td>
                        <td style="text-align:center; font-size:0.82rem; color:var(--text-muted);">
                            ${escapeHtml(c.created_at)}
                        </td>
                        <td style="text-align:center;">
                            <div style="display:flex; flex-direction:column; align-items:center; gap:0.35rem;">
                                ${statusBadge}
                                <select class="status-quick-select" onchange="changeTempCourseStatus(${c.id}, this.value)" title="Tukar status permohonan kursus">
                                    <option value="KIV" ${isKiv ? 'selected' : ''}>KIV</option>
                                    <option value="Approve" ${!isKiv ? 'selected' : ''}>Approve</option>
                                </select>
                            </div>
                        </td>
                        <td style="text-align:center;">
                            <div style="display:inline-flex; gap:0.4rem; align-items:center; flex-wrap:wrap; justify-content:center;">
                                ${isKiv ? `
                                    <button type="button" class="btn-action-approve" onclick="changeTempCourseStatus(${c.id}, 'Approve')" title="Luluskan permohonan kursus ini">
                                        <i class="fa-solid fa-check"></i> Luluskan
                                    </button>
                                ` : `
                                    <button type="button" class="btn-action-transfer" onclick="transferApprovedCourses(${c.id})" title="Pindahkan sekarang ke jadual courses">
                                        <i class="fa-solid fa-cloud-arrow-up"></i> Pindahkan
                                    </button>
                                    <button type="button" class="btn-action-kiv" onclick="changeTempCourseStatus(${c.id}, 'KIV')" title="Kembalikan status ke KIV">
                                        <i class="fa-solid fa-rotate-left"></i> KIV
                                    </button>
                                `}
                                <button type="button" class="btn-action-edit" onclick="openEditTempCourse(${c.id}, ${c.institute_id}, '${escapeJs(c.course_name)}', '${escapeJs(c.qualification_level)}', '${escapeJs(c.campus_branch || '')}', '${c.status}')" title="Kemas kini maklumat">
                                    <i class="fa-solid fa-pen"></i> Edit
                                </button>
                                <button type="button" class="btn-action-delete" onclick="deleteTempCourse(${c.id}, '${escapeJs(c.course_name)}')" title="Padam permohonan">
                                    <i class="fa-solid fa-trash"></i> Padam
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function changeTempCourseStatus(id, newStatus) {
            const fd = new FormData();
            fd.append('action', 'update_temp_course_status');
            fd.append('id', id);
            fd.append('status', newStatus);
            sendAjax('api.php', fd, () => {
                loadTempCourses();
                loadAdminStats();
            });
        }

        // ========================================================
        // THE BUTTON FUNCTION: "Update Course"
        // Transfers data from table "temp_courses" into "courses"
        // ========================================================
        function transferApprovedCourses(targetId = 0) {
            const isSingle = targetId > 0;
            const promptMsg = isSingle
                ? 'Adakah anda pasti ingin memindahkan kursus yang diluluskan ini ke jadual rasmi courses?'
                : 'Fungsi [Update Course]: Adakah anda pasti ingin memindahkan SEMUA kursus berstatus "Approve" dari temp_courses ke jadual rasmi courses?';

            if (!confirm(promptMsg)) return;

            const fd = new FormData();
            fd.append('action', 'transfer_temp_courses');
            if (isSingle) {
                fd.append('id', targetId);
            }

            const btn = document.getElementById('btnUpdateCourse');
            const originalHtml = btn ? btn.innerHTML : '';
            if (btn && !isSingle) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Sedang Memindahkan...';
            }

            sendAjax('api.php', fd, (res) => {
                if (btn && !isSingle) {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
                loadTempCourses();
                loadCourses();
                loadAdminStats();
            });
        }

        function openEditTempCourse(id, instId, name, level, branch, status) {
            document.getElementById('edit_temp_course_id').value = id;
            document.getElementById('edit_temp_course_institute_id').value = instId;
            document.getElementById('edit_temp_course_name').value = name;
            document.getElementById('edit_temp_course_qualification_level').value = level || 'Diploma';
            document.getElementById('edit_temp_course_campus_branch').value = branch || 'Kampus Utama';
            document.getElementById('edit_temp_course_status').value = status;
            openModal('modalEditTempCourse');
        }

        function deleteTempCourse(id, name) {
            if (!confirm(`Adakah anda pasti ingin memadam permohonan kursus "${name}" dari temp_courses?`)) {
                return;
            }
            const fd = new FormData();
            fd.append('action', 'delete_temp_course');
            fd.append('id', id);
            sendAjax('api.php', fd, () => {
                loadTempCourses();
                loadAdminStats();
            });
        }

        // ==========================================
        // TEMP SKILLS CRUD & UPDATE FUNCTION
        // ==========================================
        function loadTempSkills() {
            const tbody = document.getElementById('tempSkillsTableBody');
            if (!tbody) return;
            const skillType = document.getElementById('filterTempSkillType').value;
            const level = document.getElementById('filterTempSkillLevel').value;
            const status = document.getElementById('filterTempSkillStatus').value;

            tbody.innerHTML = `
                <tr>
                    <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                        <i class="fa-solid fa-spinner fa-spin"></i> Memuatkan senarai permohonan kemahiran...
                    </td>
                </tr>
            `;

            let url = `api.php?action=get_temp_skills_admin&status=${status}`;
            if (skillType !== 'all') url += `&skill_type=${skillType}`;
            if (level !== 'all') url += `&qualification_level=${level}`;

            fetch(url)
                .then(r => r.json())
                .then(res => {
                    if (res.status === 'success') {
                        tempSkillsList = res.data || [];
                        renderTempSkillsTable(tempSkillsList);
                    } else {
                        tbody.innerHTML = `<tr><td colspan="8" style="text-align:center; color:#f87171;">${res.message || 'Gagal memuatkan data'}</td></tr>`;
                    }
                })
                .catch(() => {
                    tbody.innerHTML = `<tr><td colspan="8" style="text-align:center; color:#f87171;">Ralat sambungan database.</td></tr>`;
                });
        }

        function renderTempSkillsTable(data) {
            const tbody = document.getElementById('tempSkillsTableBody');
            if (!tbody) return;

            let approvedCount = 0;
            data.forEach(item => {
                if (item.status === 'Approve') approvedCount++;
            });

            const countBadge = document.getElementById('countTempSkillsApproved');
            if (countBadge) {
                countBadge.textContent = `${approvedCount} Sedia Dipindahkan`;
            }

            if (data.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 2.5rem;">
                            <i class="fa-solid fa-folder-open" style="font-size: 2rem; display:block; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                            Tiada rekod permohonan kemahiran dijumpai.
                        </td>
                    </tr>
                `;
                return;
            }

            let html = '';
            data.forEach((item, idx) => {
                const isKiv = item.status === 'KIV';
                const statusBadge = isKiv
                    ? '<span class="badge-status-kiv"><i class="fa-solid fa-clock"></i> KIV</span>'
                    : '<span class="badge-status-approve"><i class="fa-solid fa-check-circle"></i> Approve</span>';

                const toggleStatusBtn = isKiv
                    ? `<button type="button" class="btn-action-approve" onclick="updateTempSkillStatusDirect(${item.id}, 'Approve')"><i class="fa-solid fa-check"></i> Luluskan</button>`
                    : `<button type="button" class="btn-action-kiv" onclick="updateTempSkillStatusDirect(${item.id}, 'KIV')"><i class="fa-solid fa-clock"></i> KIV</button>`;

                const transferSingleBtn = (!isKiv)
                    ? `<button type="button" class="btn-action-transfer" title="Pindahkan kemahiran ini ke jadual rasmi" onclick="transferApprovedSkills(${item.id})"><i class="fa-solid fa-share-from-square"></i> Pindah</button>`
                    : '';

                const typeBadge = item.skill_type === 'technical'
                    ? '<span style="background: rgba(56, 189, 248, 0.15); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.3); padding: 0.2rem 0.55rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;"><i class="fa-solid fa-microchip"></i> Technical</span>'
                    : '<span style="background: rgba(168, 85, 247, 0.15); color: #c084fc; border: 1px solid rgba(168, 85, 247, 0.3); padding: 0.2rem 0.55rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;"><i class="fa-solid fa-users-gear"></i> Soft Skill</span>';

                const safeDesc = escapeHtml(item.skill_description || '-');
                const safeName = escapeHtml(item.skill_name);
                const safeDate = item.created_at ? item.created_at.substring(0, 16) : '-';

                html += `
                    <tr>
                        <td style="color: var(--text-muted); font-size: 0.8rem;">${idx + 1}</td>
                        <td style="font-weight: 600; color: #f8fafc;">${safeName}</td>
                        <td style="text-align: center;">${typeBadge}</td>
                        <td style="text-align: center;"><span style="background: rgba(255, 255, 255, 0.08); padding: 0.2rem 0.5rem; border-radius: 6px; font-size: 0.75rem;">${escapeHtml(item.qualification_level || 'Diploma')}</span></td>
                        <td style="font-size: 0.82rem; color: var(--text-muted); max-width: 250px;">${safeDesc}</td>
                        <td style="text-align: center; font-size: 0.78rem; color: var(--text-muted);">${safeDate}</td>
                        <td style="text-align: center;">${statusBadge}</td>
                        <td style="text-align: center;">
                            <div style="display: inline-flex; gap: 0.35rem; align-items: center; flex-wrap: wrap; justify-content: center;">
                                ${toggleStatusBtn}
                                ${transferSingleBtn}
                                <button type="button" class="btn-action-edit" onclick="openEditTempSkill(${item.id}, '${escapeJs(item.skill_name)}', '${escapeJs(item.skill_type)}', '${escapeJs(item.qualification_level || 'Diploma')}', '${escapeJs(item.skill_description || '')}', '${item.status}')" title="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button type="button" class="btn-action-delete" onclick="deleteTempSkill(${item.id}, '${escapeJs(item.skill_name)}')" title="Padam">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;
        }

        function filterTempSkillsTable() {
            const query = (document.getElementById('searchTempSkillInput')?.value || '').toLowerCase().trim();
            if (!query) {
                renderTempSkillsTable(tempSkillsList);
                return;
            }
            const filtered = tempSkillsList.filter(item => {
                const name = (item.skill_name || '').toLowerCase();
                const desc = (item.skill_description || '').toLowerCase();
                return name.includes(query) || desc.includes(query);
            });
            renderTempSkillsTable(filtered);
        }

        function updateTempSkillStatusDirect(id, newStatus) {
            const fd = new FormData();
            fd.append('action', 'update_temp_skill_status');
            fd.append('id', id);
            fd.append('status', newStatus);
            sendAjax('api.php', fd, () => {
                loadTempSkills();
                loadAdminStats();
            });
        }

        function transferApprovedSkills(targetId = 0) {
            const isSingle = targetId > 0;
            const promptMsg = isSingle
                ? 'Adakah anda pasti ingin memindahkan kemahiran yang diluluskan ini ke jadual rasmi skills?'
                : 'Fungsi [Update Kemahiran]: Adakah anda pasti ingin memindahkan SEMUA kemahiran berstatus "Approve" dari temp_skills ke jadual rasmi skills?';

            if (!confirm(promptMsg)) return;

            const fd = new FormData();
            fd.append('action', 'transfer_temp_skills');
            if (isSingle) {
                fd.append('id', targetId);
            }

            const btn = document.getElementById('btnUpdateSkill');
            const originalHtml = btn ? btn.innerHTML : '';
            if (btn && !isSingle) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Sedang Memindahkan...';
            }

            sendAjax('api.php', fd, (res) => {
                if (btn && !isSingle) {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
                loadTempSkills();
                loadAdminStats();
            });
        }

        function openEditTempSkill(id, name, type, qual, desc, status) {
            document.getElementById('edit_temp_skill_id').value = id;
            document.getElementById('edit_temp_skill_name').value = name;
            document.getElementById('edit_temp_skill_type').value = type;
            document.getElementById('edit_temp_skill_qualification').value = qual || 'Diploma';
            document.getElementById('edit_temp_skill_description').value = desc || '';
            document.getElementById('edit_temp_skill_status').value = status;
            openModal('modalEditTempSkill');
        }

        function deleteTempSkill(id, name) {
            if (!confirm(`Adakah anda pasti ingin memadam permohonan kemahiran "${name}" dari temp_skills?`)) {
                return;
            }
            const fd = new FormData();
            fd.append('action', 'delete_temp_skill');
            fd.append('id', id);
            sendAjax('api.php', fd, () => {
                loadTempSkills();
                loadAdminStats();
            });
        }

        // --- Users CRUD ---
        function loadAdminUsers() {
            const usersBody = document.getElementById('usersBody');
            fetch('api.php?action=get_users')
                .then(r => r.json())
                .then(res => {
                    if (res.status === 'success' && res.data) {
                        if (res.data.length === 0) {
                            usersBody.innerHTML = '<tr><td colspan="6" style="text-align: center; color: var(--text-muted);">Tiada pengguna pentadbir dijumpai.</td></tr>';
                            return;
                        }

                        usersBody.innerHTML = res.data.map((u, idx) => `
                            <tr>
                                <td>${idx + 1}</td>
                                <td><strong>${escapeHtml(u.username)}</strong></td>
                                <td>${escapeHtml(u.full_name)}</td>
                                <td>
                                    <span class="badge-step" style="font-size:0.75rem; text-transform:uppercase;">
                                        ${escapeHtml(u.role)}
                                    </span>
                                </td>
                                <td><small style="color:var(--text-muted);">${escapeHtml(u.created_at)}</small></td>
                                <td style="text-align:center;">
                                    ${(u.id == currentUserId)
                                ? '<small style="color:var(--text-muted); font-style:italic;">Akaun Semasa</small>'
                                : `<button type="button" class="btn-action-delete" onclick="deleteUser(${u.id}, '${escapeJs(u.username)}')"><i class="fa-solid fa-trash"></i> Padam</button>`
                            }
                                </td>
                            </tr>
                        `).join('');
                    }
                })
                .catch(err => {
                    usersBody.innerHTML = `<tr><td colspan="6" style="text-align: center; color: #ef4444;">Gagal memuatkan: ${escapeHtml(err.message)}</td></tr>`;
                });
        }

        function deleteUser(userId, username) {
            if (!confirm(`Adakah anda pasti ingin memadam pengguna pentadbir "${username}"?`)) return;

            const fd = new FormData();
            fd.append('action', 'delete_user');
            fd.append('user_id', userId);
            sendAjax('api.php', fd, () => {
                loadAdminUsers();
                loadAdminStats();
            });
        }

        // ==========================================
        // UTILITIES & MODAL HELPERS
        // ==========================================
        function sendAjax(url, formData, onSuccess = null) {
            fetch(url, { method: 'POST', body: formData })
                .then(r => r.json())
                .then(res => {
                    if (res.status === 'success') {
                        showAlert(res.message, 'success');
                        if (typeof onSuccess === 'function') onSuccess(res);
                    } else {
                        showAlert(res.message || 'Ralat berlaku semasa memproses permintaan.', 'error');
                    }
                })
                .catch(err => {
                    showAlert('Ralat sambungan: ' + err.message, 'error');
                });
        }

        function showAlert(msg, type = 'success') {
            const alertBox = document.getElementById('adminAlert');
            alertBox.className = `alert-box alert-${type}`;
            alertBox.innerHTML = `
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span><i class="fa-solid fa-${type === 'success' ? 'circle-check' : 'triangle-exclamation'}"></i> ${escapeHtml(msg)}</span>
                    <button type="button" onclick="this.parentElement.parentElement.style.display='none'" style="background:none; border:none; color:inherit; cursor:pointer; font-size:1.1rem;">&times;</button>
                </div>
            `;
            alertBox.style.display = 'block';
            window.scrollTo({ top: 0, behavior: 'smooth' });
            setTimeout(() => { alertBox.style.display = 'none'; }, 6000);
        }

        function openModal(modalId) {
            const m = document.getElementById(modalId);
            if (m) m.classList.add('active');
        }

        function closeModal(modalId) {
            const m = document.getElementById(modalId);
            if (m) m.classList.remove('active');
        }

        // Close modal when clicking on overlay background
        document.querySelectorAll('.modal-overlay').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.remove('active');
                }
            });
        });

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function escapeJs(text) {
            if (!text) return '';
            return text.replace(/'/g, "\\'").replace(/"/g, '&quot;');
        }
    </script>
</body>

</html>