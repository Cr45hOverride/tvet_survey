<?php
/**
 * Industry Skills Framework & Respondent Gap Analysis
 * Maps survey respondent skills to industry job positions and analyzes skill gaps.
 * Based on skill_Industries.csv (IndSF Semiconductor & Engineering Framework)
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/industry_skills_helper.php';

$isLoggedIn = isLoggedIn();
$currentUser = getCurrentUser();

// Fetch all survey submissions with their selected skills
$allSubmissions = IndustrySkillsHelper::getAllSubmissionsWithSkills();

// Fetch all 36 industry jobs
$allJobs = IndustrySkillsHelper::getAllJobs();

// Fetch states, institutes, and courses for hierarchical cascading filters
$db = getDBConnection();
$states = $db->query("SELECT id, state_name FROM states ORDER BY state_name ASC")->fetchAll();
$institutes = $db->query("SELECT id, state_id, institute_name FROM institutes ORDER BY institute_name ASC")->fetchAll();
$courses = $db->query("SELECT id, institute_id, course_name, qualification_level FROM courses ORDER BY course_name ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ms">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemetaan Kemahiran Responden & Analisis Jurang Industri (IndSF)</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .container {
            width: 95% !important;
            max-width: 1320px !important;
        }

        /* Respondent Profile Card */
        .respondent-card {
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.85) 0%, rgba(15, 23, 42, 0.95) 100%);
            border: 1px solid rgba(56, 189, 248, 0.35);
            border-radius: 20px;
            padding: 1.75rem;
            margin-bottom: 2rem;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            position: relative;
        }

        .respondent-meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
            margin-bottom: 1.25rem;
            padding-bottom: 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .meta-item {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .meta-label {
            font-size: 0.78rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
        }

        .meta-val {
            font-size: 0.95rem;
            color: #f8fafc;
            font-weight: 600;
        }

        /* Match highlights & Gap Badges */
        .skill-tag-matched {
            background: rgba(16, 185, 129, 0.25);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.4);
            font-weight: 700;
            padding: 0.25rem 0.65rem;
            border-radius: 8px;
            font-size: 0.82rem;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }

        .skill-tag-gap {
            background: rgba(239, 68, 68, 0.18);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.35);
            font-weight: 600;
            padding: 0.25rem 0.65rem;
            border-radius: 8px;
            font-size: 0.82rem;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }

        .skill-tag-unmatched-neutral {
            color: #94a3b8;
            opacity: 0.85;
            padding: 0.15rem 0.3rem;
            font-size: 0.85rem;
        }

        /* Mapping Result Cards */
        .mapping-card {
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 18px;
            padding: 1.5rem;
            margin-bottom: 1.25rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .mapping-card:hover {
            border-color: rgba(56, 189, 248, 0.4);
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4);
        }

        .mapping-card.tier-high {
            border-left: 5px solid #10b981;
        }

        .mapping-card.tier-moderate {
            border-left: 5px solid #fbbf24;
        }

        .mapping-card.tier-low {
            border-left: 5px solid #64748b;
        }

        .mapping-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .mapping-job-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #f8fafc;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .match-score-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 0.9rem;
            border-radius: 9999px;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .match-score-badge.high {
            background: rgba(16, 185, 129, 0.2);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.4);
        }

        .match-score-badge.moderate {
            background: rgba(245, 158, 11, 0.2);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.4);
        }

        .match-score-badge.low {
            background: rgba(100, 116, 139, 0.2);
            color: #94a3b8;
            border: 1px solid rgba(100, 116, 139, 0.4);
        }

        /* Ratio Bar */
        .ratio-bar-wrap {
            width: 100%;
            height: 8px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 9999px;
            overflow: hidden;
            margin-top: 0.4rem;
        }

        .ratio-bar-fill {
            height: 100%;
            border-radius: 9999px;
            transition: width 0.5s ease;
        }

        /* Tabs Navigation */
        .view-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 0.5rem;
            flex-wrap: wrap;
        }

        .view-tab-btn {
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 0.92rem;
            font-weight: 600;
            padding: 0.65rem 1.25rem;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .view-tab-btn:hover {
            color: var(--text-main);
            background: rgba(255, 255, 255, 0.05);
        }

        .view-tab-btn.active {
            background: rgba(56, 189, 248, 0.18);
            color: var(--primary-glow);
            border: 1px solid rgba(56, 189, 248, 0.35);
        }

        .view-pane {
            display: none;
        }

        .view-pane.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        /* Deep dive table */
        .gap-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .gap-table th {
            background: rgba(15, 23, 42, 0.85);
            padding: 0.9rem 1rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--primary-glow);
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .gap-table td {
            padding: 0.9rem 1rem;
            font-size: 0.88rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            color: #cbd5e1;
            vertical-align: middle;
        }

        .btn-inspect {
            background: rgba(56, 189, 248, 0.15);
            border: 1px solid rgba(56, 189, 248, 0.35);
            color: #38bdf8;
            padding: 0.4rem 0.85rem;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }

        .btn-inspect:hover {
            background: rgba(56, 189, 248, 0.85);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(56, 189, 248, 0.4);
        }

        .btn-analisa-ai {
            background: linear-gradient(135deg, #a855f7 0%, #6366f1 100%);
            color: #ffffff;
            border: 1px solid rgba(168, 85, 247, 0.45);
            padding: 0.5rem 1.1rem;
            border-radius: 10px;
            font-size: 0.84rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 4px 15px rgba(168, 85, 247, 0.35);
        }

        .btn-analisa-ai:hover:not(:disabled) {
            background: linear-gradient(135deg, #9333ea 0%, #4f46e5 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(168, 85, 247, 0.5);
            color: #ffffff;
        }

        .btn-analisa-ai:disabled {
            opacity: 0.45;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        @media print {

            .navbar,
            .view-tabs,
            .form-grid,
            .btn-submit,
            footer {
                display: none !important;
            }

            body {
                background: #ffffff !important;
                color: #000000 !important;
            }

            .mapping-card,
            .respondent-card {
                border: 1px solid #ccc !important;
                box-shadow: none !important;
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <!-- Navigation Header -->
        <?php include __DIR__ . '/menu.php'; ?>

        <!-- SECTION 1: RESPONDENT FILTER & SELECTION (RESPONDEN, NEGERI, IPT, KURSUS) -->
        <section class="glass-card" style="border-color: rgba(56, 189, 248, 0.35); padding: 1.75rem 2rem;">
            <div
                style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem;">
                <div>
                    <h2 class="card-title" style="margin-bottom: 0.2rem; font-size: 1.4rem;">
                        <i class="fa-solid fa-filter" style="color: var(--primary-glow);"></i>
                        Penapis Responden (Responden, Negeri, IPT & Kursus)
                    </h2>
                    <p class="card-subtitle" style="margin-bottom: 0;">
                        Pilih Responden dan tapis mengikut Negeri, Institusi (IPT), atau Kursus untuk memetakan
                        kemahiran dengan keperluan 36 jawatan industri semiconductor & kejuruteraan.
                    </p>
                </div>
                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                    <button type="button" id="btnResetAllFilters" class="btn-secondary"
                        style="font-size: 0.85rem; padding: 0.55rem 1rem;">
                        <i class="fa-solid fa-rotate-left"></i> Reset Penapis
                    </button>
                    <button type="button" class="btn-secondary" onclick="window.print()"
                        style="font-size: 0.85rem; padding: 0.55rem 1rem;">
                        <i class="fa-solid fa-print"></i> Cetak Laporan
                    </button>
                </div>
            </div>

            <!-- 4-Column Responsive Grid Filters: Responden, Negeri, IPT, Kursus -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;">
                <!-- Filter 1: Responden -->
                <div class="form-group">
                    <label class="form-label" for="selectRespondent">
                        <span><i class="fa-solid fa-users" style="color: var(--primary-glow);"></i> 1. Filter
                            Responden</span>
                        <span id="respCountBadge" class="badge-step" style="font-size: 0.72rem;">0 Responden</span>
                    </label>
                    <div class="select-wrapper">
                        <select id="selectRespondent" class="custom-select" style="font-weight: 600; color: #38bdf8;">
                            <option value="">-- Memuatkan Responden... --</option>
                        </select>
                        <i class="fa-solid fa-chevron-down select-arrow"></i>
                    </div>
                </div>

                <!-- Filter 2: Negeri -->
                <div class="form-group">
                    <label class="form-label" for="filterNegeri">
                        <span><i class="fa-solid fa-map-location-dot" style="color: #38bdf8;"></i> 2. Filter
                            Negeri</span>
                    </label>
                    <div class="select-wrapper">
                        <select id="filterNegeri" class="custom-select">
                            <option value="all">-- Semua Negeri --</option>
                            <?php foreach ($states as $st): ?>
                                <option value="<?php echo $st['id']; ?>"><?php echo htmlspecialchars($st['state_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <i class="fa-solid fa-chevron-down select-arrow"></i>
                    </div>
                </div>

                <!-- Filter 3: IPT / Institusi -->
                <div class="form-group">
                    <label class="form-label" for="filterIPT">
                        <span><i class="fa-solid fa-building-columns" style="color: #fbbf24;"></i> 3. Filter Institusi
                            (IPT)</span>
                    </label>
                    <div class="select-wrapper">
                        <select id="filterIPT" class="custom-select">
                            <option value="all">-- Semua Institusi (IPT) --</option>
                            <?php foreach ($institutes as $inst): ?>
                                <option value="<?php echo $inst['id']; ?>" data-state="<?php echo $inst['state_id']; ?>">
                                    <?php echo htmlspecialchars($inst['institute_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <i class="fa-solid fa-chevron-down select-arrow"></i>
                    </div>
                </div>

                <!-- Filter 4: Kursus -->
                <div class="form-group">
                    <label class="form-label" for="filterKursus">
                        <span><i class="fa-solid fa-graduation-cap" style="color: #c084fc;"></i> 4. Filter Kursus</span>
                    </label>
                    <div class="select-wrapper">
                        <select id="filterKursus" class="custom-select">
                            <option value="all">-- Semua Kursus --</option>
                            <?php foreach ($courses as $c): ?>
                                <option value="<?php echo $c['id']; ?>" data-inst="<?php echo $c['institute_id']; ?>">
                                    <?php echo htmlspecialchars($c['course_name'] . ' (' . $c['qualification_level'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <i class="fa-solid fa-chevron-down select-arrow"></i>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECTION 2: RESPONDENT DETAIL & ACQUIRED SKILLS CARD -->
        <div id="respondentProfileArea" class="respondent-card">
            <div
                style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem; padding-bottom: 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                <div style="display: flex; align-items: center; gap: 0.85rem;">
                    <div
                        style="width: 52px; height: 52px; border-radius: 14px; background: linear-gradient(135deg, rgba(56, 189, 248, 0.25), rgba(99, 102, 241, 0.25)); border: 1px solid rgba(56, 189, 248, 0.4); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: #38bdf8;">
                        <i class="fa-solid fa-id-card-clip"></i>
                    </div>
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.2rem;">
                            <span
                                style="font-size: 0.78rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700;">
                                Responden Detail
                            </span>
                            <span id="dispRespIdBadge" class="badge-step"
                                style="font-size: 0.72rem; padding: 0.15rem 0.55rem; background: rgba(56, 189, 248, 0.15); border-color: rgba(56, 189, 248, 0.35); color: #38bdf8;">
                                Responden #-
                            </span>
                        </div>
                        <h3 id="dispRespHeaderTitle"
                            style="font-size: 1.4rem; font-weight: 800; color: #f8fafc; margin: 0; display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                            <span style="color: var(--text-muted); font-size: 0.95rem; font-weight: 500;">Nama
                                Responden:</span>
                            <span id="dispRespHeaderNameVal" style="color: #38bdf8;">-</span>
                        </h3>
                    </div>
                </div>
                <div>
                    <span id="dispRespTotalSkillsBadge" class="match-score-badge high"
                        style="font-size: 0.85rem; padding: 0.4rem 0.9rem;">
                        <i class="fa-solid fa-check-double"></i> 0 Kemahiran Dipilih
                    </span>
                </div>
            </div>

            <!-- Responden Detail Metadata Grid -->
            <div class="respondent-meta-grid">
                <div class="meta-item"
                    style="background: rgba(56, 189, 248, 0.08); padding: 0.75rem 1rem; border-radius: 12px; border: 1px solid rgba(56, 189, 248, 0.25);">
                    <span class="meta-label" style="color: #38bdf8; font-weight: 700;"><i class="fa-solid fa-user"></i>
                        Nama Responden</span>
                    <span class="meta-val" id="dispRespName"
                        style="color: #ffffff; font-size: 1.15rem; font-weight: 800;">-</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label"><i class="fa-solid fa-briefcase"></i> Jawatan Responden</span>
                    <span class="meta-val" id="dispRespPosition">-</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label"><i class="fa-solid fa-envelope"></i> Alamat E-mel</span>
                    <span class="meta-val" id="dispRespEmail">-</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label"><i class="fa-solid fa-map-location-dot"></i> Negeri Tempat IPT</span>
                    <span class="meta-val" id="dispRespState">-</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label"><i class="fa-solid fa-building-columns"></i> Institusi (IPT)</span>
                    <span class="meta-val" id="dispRespInst">-</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label"><i class="fa-solid fa-graduation-cap"></i> Kursus / Program</span>
                    <span class="meta-val" id="dispRespCourse">-</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label"><i class="fa-solid fa-certificate"></i> Tahap Kelayakan</span>
                    <span class="meta-val" id="dispRespLevel">-</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label"><i class="fa-solid fa-users"></i> Kemasukan Pelajar Setahun</span>
                    <span class="meta-val" id="dispRespIntake">-</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label"><i class="fa-solid fa-clock"></i> Tarikh & Masa Hantar</span>
                    <span class="meta-val" id="dispRespDate">-</span>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;">
                <!-- Acquired Technical Skills -->
                <div>
                    <div
                        style="font-size: 0.88rem; font-weight: 700; color: #38bdf8; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.4rem;">
                        <i class="fa-solid fa-microchip"></i> Kemahiran Teknikal yang Dimiliki (<span
                            id="dispCountTech">0</span>)
                    </div>
                    <div id="dispTechSkillsWrapper" style="display: flex; flex-wrap: wrap; gap: 0.4rem;"></div>
                </div>

                <!-- Acquired Soft Skills -->
                <div>
                    <div
                        style="font-size: 0.88rem; font-weight: 700; color: #c084fc; margin-bottom: 0.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                        <span style="display: flex; align-items: center; gap: 0.4rem;">
                            <i class="fa-solid fa-users-gear"></i> Kemahiran Insaniah yang Dimiliki (<span
                                id="dispCountSoft">0</span>)
                        </span>
                    </div>
                    <div id="dispSoftSkillsWrapper" style="display: flex; flex-wrap: wrap; gap: 0.4rem; margin-bottom: 0.85rem;"></div>
                    <div>
                        <button type="button" id="btnAnalisaAi" class="btn-analisa-ai" title="Jana Analisa AI bagi responden ini">
                            <i class="fa-solid fa-wand-magic-sparkles"></i> Analisa AI (Kesesuaian Industri)
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Tersembunyi Penghantaran Data ke analisa_ai.php -->
        <form id="formToAnalisaAi" method="POST" action="analisa_ai.php" style="display: none;">
            <input type="hidden" name="nama_institusi" id="aiInputInstitusi">
            <input type="hidden" name="nama_kursus" id="aiInputKursus">
            <input type="hidden" name="tahap_kelayakan" id="aiInputTahap">
            <input type="hidden" name="kemahiran_teknikal" id="aiInputTech">
            <input type="hidden" name="kemahiran_insaniah" id="aiInputSoft">
            <input type="hidden" name="nama_responden" id="aiInputResponden">
        </form>

        <!-- EXECUTIVE KPI SUMMARY CARDS -->
        <div class="stats-grid">
            <div class="stat-card" style="--stat-accent: #10b981; --stat-icon-bg: rgba(16, 185, 129, 0.15);">
                <div class="stat-icon"><i class="fa-solid fa-trophy"></i></div>
                <div class="stat-content">
                    <span class="stat-number" id="statTopJobTitle" style="font-size: 1.15rem;">-</span>
                    <span class="stat-label">Jawatan Paling Padan (Top Match)</span>
                </div>
            </div>

            <div class="stat-card" style="--stat-accent: #38bdf8; --stat-icon-bg: rgba(56, 189, 248, 0.15);">
                <div class="stat-icon"><i class="fa-solid fa-chart-pie"></i></div>
                <div class="stat-content">
                    <span class="stat-number" id="statTopScore">-</span>
                    <span class="stat-label">Skor Padanan Tertinggi</span>
                </div>
            </div>

            <div class="stat-card" style="--stat-accent: #fbbf24; --stat-icon-bg: rgba(245, 158, 11, 0.15);">
                <div class="stat-icon"><i class="fa-solid fa-circle-check"></i></div>
                <div class="stat-content">
                    <span class="stat-number" id="statHighMatchCount">0</span>
                    <span class="stat-label">Jawatan Padanan Tinggi (&ge;50%)</span>
                </div>
            </div>

            <div class="stat-card" style="--stat-accent: #ef4444; --stat-icon-bg: rgba(239, 68, 68, 0.15);">
                <div class="stat-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
                <div class="stat-content">
                    <span class="stat-number" id="statCriticalGapsCount">0</span>
                    <span class="stat-label">Jurang Kemahiran Industri</span>
                </div>
            </div>
        </div>

        <!-- MAIN TABS FOR GAP ANALYSIS & MAPPING -->
        <div class="view-tabs">
            <button type="button" class="view-tab-btn active" data-tab="all-mappings">
                <i class="fa-solid fa-ranking-star"></i> 1. Kedudukan Kesemua 36 Jawatan Industri (All Job Rankings)
            </button>
            <button type="button" class="view-tab-btn" data-tab="deep-dive">
                <i class="fa-solid fa-magnifying-glass-chart"></i> 2. Analisis Terperinci Jurang Jawatan Tertentu
                (Deep-Dive Gap)
            </button>
            <button type="button" class="view-tab-btn" data-tab="catalog">
                <i class="fa-solid fa-book-bookmark"></i> 3. Katalog Rangka Kerja Industri (IndSF Reference)
            </button>
        </div>

        <!-- PANE 1: ALL MAPPINGS RANKED -->
        <div id="pane-all-mappings" class="view-pane active">
            <!-- Filter Toolbar for Job Rankings -->
            <div class="filter-toolbar">
                <div class="filter-left">
                    <div class="filter-search-wrap">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="mapSearchInput" class="filter-search-input"
                            placeholder="Cari jawatan industri atau kemahiran...">
                    </div>

                    <select id="mapFilterTier" class="filter-select">
                        <option value="all">Semua Tahap Padanan</option>
                        <option value="high">Padanan Tinggi (&ge; 50%)</option>
                        <option value="moderate">Padanan Sederhana (25% - 49%)</option>
                        <option value="low">Padanan Rendah (< 25%)</option>
                    </select>

                    <select id="mapFilterFocus" class="filter-select">
                        <option value="all">Semua Focus Area</option>
                        <option value="Technical">Technical Sahaja (7)</option>
                        <option value="Engineering">Engineering Sahaja (29)</option>
                    </select>
                </div>
                <div class="filter-right">
                    <span id="mapCounter" class="count-indicator">Memuatkan padanan...</span>
                </div>
            </div>

            <!-- List of mapped job cards -->
            <div id="mappedJobsContainer"></div>
        </div>

        <!-- PANE 2: DEEP DIVE GAP ANALYSIS FOR A TARGET JOB -->
        <div id="pane-deep-dive" class="view-pane">
            <section class="glass-card">
                <div
                    style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
                    <div>
                        <h2 class="card-title" style="margin-bottom: 0.2rem;">
                            <i class="fa-solid fa-sliders" style="color: var(--primary-glow);"></i>
                            Analisis Jurang & Pelan Intervensi Mengikut Jawatan
                        </h2>
                        <p class="card-subtitle" style="margin-bottom: 0;">
                            Pilih jawatan sasaran untuk melihat perbandingan terperinci antara kemahiran yang dimiliki
                            responden dengan keperluan industri.
                        </p>
                    </div>

                    <div style="min-width: 300px;">
                        <label class="form-label" for="deepJobSelect" style="margin-bottom: 0.3rem;">Pilih Jawatan
                            Sasaran Industri:</label>
                        <div class="select-wrapper">
                            <select id="deepJobSelect" class="custom-select">
                                <?php foreach ($allJobs as $job): ?>
                                    <option value="<?php echo $job['id']; ?>">
                                        <?php echo htmlspecialchars($job['job_position'] . ' — ' . $job['job_area'] . ' (' . $job['focus_area'] . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <i class="fa-solid fa-chevron-down select-arrow"></i>
                        </div>
                    </div>
                </div>

                <!-- Deep Dive Result Box -->
                <div id="deepDiveBox"></div>
            </section>
        </div>

        <!-- PANE 3: CATALOG REFERENCE OF ALL 36 JOBS -->
        <div id="pane-catalog" class="view-pane">
            <section class="glass-card">
                <h2 class="card-title">
                    <i class="fa-solid fa-briefcase" style="color: var(--primary-glow);"></i>
                    Rujukan Piawaian Industri Semikonduktor (IndSF)
                </h2>
                <p class="card-subtitle">
                    Senarai piawaian lengkap 36 jawatan industri bersama deskripsi kerja dan kemahiran prasyarat.
                </p>

                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Jawatan Industri</th>
                                <th>Focus & Job Area</th>
                                <th>Deskripsi Tugas</th>
                                <th>Kemahiran Insaniah Diperlukan</th>
                                <th>Kemahiran Teknikal Diperlukan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allJobs as $idx => $job): ?>
                                <tr>
                                    <td><?php echo $idx + 1; ?></td>
                                    <td><strong><?php echo htmlspecialchars($job['job_position']); ?></strong></td>
                                    <td>
                                        <span class="badge-step"
                                            style="font-size: 0.72rem;"><?php echo htmlspecialchars($job['focus_area']); ?></span><br>
                                        <small
                                            style="color: var(--text-muted);"><?php echo htmlspecialchars($job['job_area']); ?></small>
                                    </td>
                                    <td><small><?php echo htmlspecialchars($job['job_description']); ?></small></td>
                                    <td>
                                        <?php
                                        $sArr = array_map('trim', explode(',', $job['soft_skills_raw']));
                                        foreach ($sArr as $s) {
                                            echo '<span class="skill-tag soft" style="font-size:0.75rem;">' . htmlspecialchars($s) . '</span> ';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $tArr = array_map('trim', explode(',', $job['technical_skills_raw']));
                                        foreach ($tArr as $t) {
                                            echo '<span class="skill-tag tech" style="font-size:0.75rem;">' . htmlspecialchars($t) . '</span> ';
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <footer>
            <p>&copy; <?php echo date('Y'); ?> Sistem Tinjauan IPT Malaysia | Inisiatif Pemetaan Kemahiran TVET Kedah
            </p>
        </footer>
    </div>

    <!-- Client-side Logic Script -->
    <script>
        const submissionsData = <?php echo json_encode($allSubmissions, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
        const jobsData = <?php echo json_encode($allJobs, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
        const statesData = <?php echo json_encode($states, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
        const institutesData = <?php echo json_encode($institutes, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
        const coursesData = <?php echo json_encode($courses, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

        document.addEventListener('DOMContentLoaded', () => {
            const filterNegeri = document.getElementById('filterNegeri');
            const filterIPT = document.getElementById('filterIPT');
            const filterKursus = document.getElementById('filterKursus');
            const selectRespondent = document.getElementById('selectRespondent');
            const respCountBadge = document.getElementById('respCountBadge');
            const btnResetAllFilters = document.getElementById('btnResetAllFilters');

            const deepJobSelect = document.getElementById('deepJobSelect');
            const mappedJobsContainer = document.getElementById('mappedJobsContainer');
            const deepDiveBox = document.getElementById('deepDiveBox');

            // Tab handling
            document.querySelectorAll('.view-tab-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('.view-tab-btn').forEach(b => b.classList.remove('active'));
                    document.querySelectorAll('.view-pane').forEach(p => p.classList.remove('active'));
                    btn.classList.add('active');
                    const tabId = btn.getAttribute('data-tab');
                    document.getElementById(`pane-${tabId}`).classList.add('active');
                });
            });

            // Cascading Dropdown Listeners
            filterNegeri.addEventListener('change', () => {
                updateIPTDropdown();
                updateKursusDropdown();
                updateRespondentDropdown();
            });

            filterIPT.addEventListener('change', () => {
                updateKursusDropdown();
                updateRespondentDropdown();
            });

            filterKursus.addEventListener('change', () => {
                updateRespondentDropdown();
            });

            selectRespondent.addEventListener('change', updateRespondentAnalysis);
            deepJobSelect.addEventListener('change', updateDeepDive);

            // Reset All Filters
            if (btnResetAllFilters) {
                btnResetAllFilters.addEventListener('click', () => {
                    filterNegeri.value = 'all';
                    updateIPTDropdown();
                    filterIPT.value = 'all';
                    updateKursusDropdown();
                    filterKursus.value = 'all';
                    updateRespondentDropdown();
                });
            }

            // AI Analysis Button Handler
            const btnAnalisaAi = document.getElementById('btnAnalisaAi');
            if (btnAnalisaAi) {
                btnAnalisaAi.addEventListener('click', () => {
                    const resp = getSelectedRespondent();
                    if (!resp) {
                        alert('Sila pilih responden terlebih dahulu dalam senarai tapisan.');
                        return;
                    }

                    const techSkillsStr = (resp.tech_skills && resp.tech_skills.length > 0)
                        ? resp.tech_skills.join(', ')
                        : 'Tiada kemahiran teknikal direkodkan';

                    const softSkillsStr = (resp.soft_skills && resp.soft_skills.length > 0)
                        ? resp.soft_skills.join(', ')
                        : 'Tiada kemahiran insaniah direkodkan';

                    document.getElementById('aiInputInstitusi').value = resp.institute_name || '';
                    document.getElementById('aiInputKursus').value = resp.course_name || '';
                    document.getElementById('aiInputTahap').value = resp.qualification_level || '';
                    document.getElementById('aiInputTech').value = techSkillsStr;
                    document.getElementById('aiInputSoft').value = softSkillsStr;
                    document.getElementById('aiInputResponden').value = resp.respondent_name || '';

                    document.getElementById('formToAnalisaAi').submit();
                });
            }

            // Filter inputs for mapped jobs
            document.getElementById('mapSearchInput').addEventListener('input', filterMappedCards);
            document.getElementById('mapFilterTier').addEventListener('change', filterMappedCards);
            document.getElementById('mapFilterFocus').addEventListener('change', filterMappedCards);

            // Populate IPT Dropdown based on selected state
            function updateIPTDropdown() {
                const stateId = filterNegeri.value;
                const prevVal = filterIPT.value;

                let filteredInst = institutesData;
                if (stateId !== 'all') {
                    filteredInst = institutesData.filter(i => parseInt(i.state_id) === parseInt(stateId));
                }

                filterIPT.innerHTML = '<option value="all">-- Semua Institusi (IPT) --</option>' +
                    filteredInst.map(i => `<option value="${i.id}">${escapeHtml(i.institute_name)}</option>`).join('');

                if (filteredInst.some(i => i.id == prevVal)) {
                    filterIPT.value = prevVal;
                } else {
                    filterIPT.value = 'all';
                }
            }

            // Populate Kursus Dropdown based on selected IPT or State
            function updateKursusDropdown() {
                const instId = filterIPT.value;
                const stateId = filterNegeri.value;
                const prevVal = filterKursus.value;

                let filteredCourses = coursesData;

                if (instId !== 'all') {
                    filteredCourses = coursesData.filter(c => parseInt(c.institute_id) === parseInt(instId));
                } else if (stateId !== 'all') {
                    const validInstIds = new Set(institutesData.filter(i => parseInt(i.state_id) === parseInt(stateId)).map(i => parseInt(i.id)));
                    filteredCourses = coursesData.filter(c => validInstIds.has(parseInt(c.institute_id)));
                }

                filterKursus.innerHTML = '<option value="all">-- Semua Kursus --</option>' +
                    filteredCourses.map(c => `<option value="${c.id}">${escapeHtml(c.course_name)} (${escapeHtml(c.qualification_level)})</option>`).join('');

                if (filteredCourses.some(c => c.id == prevVal)) {
                    filterKursus.value = prevVal;
                } else {
                    filterKursus.value = 'all';
                }
            }

            // Populate Respondent Dropdown based on State, IPT, and Course filters
            function updateRespondentDropdown() {
                const stateId = filterNegeri.value;
                const instId = filterIPT.value;
                const courseId = filterKursus.value;
                const prevSelectedId = selectRespondent.value;

                const filteredSubs = submissionsData.filter(sub => {
                    const matchState = (stateId === 'all') || (sub.state_id && parseInt(sub.state_id) === parseInt(stateId));
                    const matchInst = (instId === 'all') || (sub.institute_id && parseInt(sub.institute_id) === parseInt(instId));
                    const matchCourse = (courseId === 'all') || (sub.course_id && parseInt(sub.course_id) === parseInt(courseId));
                    return matchState && matchInst && matchCourse;
                });

                respCountBadge.textContent = `${filteredSubs.length} Responden`;

                if (filteredSubs.length === 0) {
                    selectRespondent.innerHTML = '<option value="">-- Tiada Responden Sepadan --</option>';
                    showEmptyRespondentState();
                    return;
                }

                selectRespondent.innerHTML = filteredSubs.map(s => `
                    <option value="${s.id}">
                        Responden #${s.id} &bull; ${escapeHtml(s.course_name)} (${escapeHtml(s.institute_name)}) [${(s.all_skills || []).length} Kemahiran]
                    </option>
                `).join('');

                // Preserve previous selection if still in filtered list, else choose first
                if (filteredSubs.some(s => s.id == prevSelectedId)) {
                    selectRespondent.value = prevSelectedId;
                } else {
                    selectRespondent.value = filteredSubs[0].id;
                }

                updateRespondentAnalysis();
            }

            function getSelectedRespondent() {
                const subId = parseInt(selectRespondent.value);
                if (!subId) return null;
                return submissionsData.find(s => parseInt(s.id) === subId);
            }

            function showEmptyRespondentState() {
                document.getElementById('dispRespHeaderNameVal').textContent = 'Tiada Responden Dipilih';
                document.getElementById('dispRespIdBadge').textContent = 'Responden #-';
                document.getElementById('dispRespName').textContent = '-';
                document.getElementById('dispRespPosition').textContent = '-';
                document.getElementById('dispRespEmail').textContent = '-';
                document.getElementById('dispRespInst').textContent = '-';
                document.getElementById('dispRespState').textContent = '-';
                document.getElementById('dispRespCourse').textContent = '-';
                document.getElementById('dispRespLevel').textContent = '-';
                document.getElementById('dispRespIntake').textContent = '-';
                document.getElementById('dispRespDate').textContent = '-';
                document.getElementById('dispRespTotalSkillsBadge').innerHTML = '<i class="fa-solid fa-circle-xmark"></i> 0 Kemahiran';
                document.getElementById('dispCountTech').textContent = '0';
                document.getElementById('dispCountSoft').textContent = '0';
                document.getElementById('dispTechSkillsWrapper').innerHTML = '<small style="color:var(--text-muted);">-</small>';
                document.getElementById('dispSoftSkillsWrapper').innerHTML = '<small style="color:var(--text-muted);">-</small>';
                const btnAi = document.getElementById('btnAnalisaAi');
                if (btnAi) btnAi.disabled = true;
                mappedJobsContainer.innerHTML = '<div style="text-align:center; padding:3rem; color:var(--text-muted);">Sila pilih responden untuk melihat pemetaan jawatan industri.</div>';
                deepDiveBox.innerHTML = '<div style="text-align:center; padding:3rem; color:var(--text-muted);">Sila pilih responden untuk memulakan analisis jurang.</div>';
            }

            function updateRespondentAnalysis() {
                const resp = getSelectedRespondent();
                if (!resp) {
                    showEmptyRespondentState();
                    return;
                }

                const btnAi = document.getElementById('btnAnalisaAi');
                if (btnAi) btnAi.disabled = false;

                // 1. Populate Respondent Detail Card
                document.getElementById('dispRespHeaderNameVal').textContent = resp.respondent_name;
                document.getElementById('dispRespIdBadge').textContent = `Responden #${resp.id}`;
                document.getElementById('dispRespName').textContent = resp.respondent_name;
                document.getElementById('dispRespPosition').textContent = resp.respondent_position || '-';
                document.getElementById('dispRespEmail').textContent = resp.respondent_email || '-';
                document.getElementById('dispRespInst').textContent = resp.institute_name;
                document.getElementById('dispRespState').textContent = resp.state_name;
                document.getElementById('dispRespCourse').textContent = resp.course_name;
                document.getElementById('dispRespLevel').textContent = resp.qualification_level;
                document.getElementById('dispRespIntake').textContent = resp.annual_intake ? `${resp.annual_intake} Pelajar / Tahun` : '0 Pelajar';
                document.getElementById('dispRespDate').textContent = resp.submitted_at || '-';

                const totalSkillsCount = (resp.all_skills || []).length;
                document.getElementById('dispRespTotalSkillsBadge').innerHTML = `<i class="fa-solid fa-check-double"></i> ${totalSkillsCount} Kemahiran Dipilih`;

                // Render acquired skills
                const techChipsWrapper = document.getElementById('dispTechSkillsWrapper');
                const softChipsWrapper = document.getElementById('dispSoftSkillsWrapper');

                document.getElementById('dispCountTech').textContent = (resp.tech_skills || []).length;
                document.getElementById('dispCountSoft').textContent = (resp.soft_skills || []).length;

                techChipsWrapper.innerHTML = (resp.tech_skills && resp.tech_skills.length > 0)
                    ? resp.tech_skills.map(s => `<span class="skill-tag tech"><i class="fa-solid fa-wrench"></i> ${escapeHtml(s)}</span>`).join('')
                    : '<small style="color:var(--text-muted);">Tiada kemahiran teknikal dipilih</small>';

                softChipsWrapper.innerHTML = (resp.soft_skills && resp.soft_skills.length > 0)
                    ? resp.soft_skills.map(s => `<span class="skill-tag soft"><i class="fa-solid fa-brain"></i> ${escapeHtml(s)}</span>`).join('')
                    : '<small style="color:var(--text-muted);">Tiada kemahiran insaniah dipilih</small>';

                // 2. Perform Mapping against all 36 jobs
                const respondentAllSkills = resp.all_skills || [];
                const evaluatedJobs = evaluateAllJobsForSkills(respondentAllSkills);

                // 3. Update KPI summary cards
                const topJob = evaluatedJobs[0];
                if (topJob) {
                    document.getElementById('statTopJobTitle').textContent = topJob.job_position;
                    document.getElementById('statTopScore').textContent = `${topJob.overall_percent}% (${topJob.overall_ratio})`;
                }

                const highMatches = evaluatedJobs.filter(j => j.overall_percent >= 50);
                document.getElementById('statHighMatchCount').textContent = highMatches.length;

                // Count total distinct missing skills for top 3 matching jobs
                let totalMissingInTop = 0;
                evaluatedJobs.slice(0, 3).forEach(j => {
                    totalMissingInTop += (j.soft_missing_list.length + j.tech_missing_list.length);
                });
                document.getElementById('statCriticalGapsCount').textContent = `${totalMissingInTop} Jurang`;

                // 4. Render Mapped Cards in Tab 1
                renderMappedCards(evaluatedJobs);

                // 5. Render Deep Dive in Tab 2
                updateDeepDive();
            }

            // Initial setup of cascading dropdowns
            updateIPTDropdown();
            updateKursusDropdown();
            updateRespondentDropdown();

            function evaluateAllJobsForSkills(respondentSkillsList) {
                const respMap = {};
                respondentSkillsList.forEach(s => {
                    if (s) respMap[s.toLowerCase().trim()] = true;
                });

                const results = jobsData.map(job => {
                    // Soft Skills
                    const softList = job.soft_skills_raw.split(',').map(s => s.trim()).filter(Boolean);
                    const softMatched = [];
                    const softMissing = [];

                    softList.forEach(skill => {
                        if (isSkillMatch(skill, respMap)) {
                            softMatched.push(skill);
                        } else {
                            softMissing.push(skill);
                        }
                    });

                    // Tech Skills
                    const techList = job.technical_skills_raw.split(',').map(s => s.trim()).filter(Boolean);
                    const techMatched = [];
                    const techMissing = [];

                    techList.forEach(skill => {
                        if (isSkillMatch(skill, respMap)) {
                            techMatched.push(skill);
                        } else {
                            techMissing.push(skill);
                        }
                    });

                    const totalReq = softList.length + techList.length;
                    const totalMatched = softMatched.length + techMatched.length;
                    const pct = totalReq > 0 ? Math.round((totalMatched / totalReq) * 100) : 0;

                    let tier = 'low';
                    let tierLabel = 'Padanan Rendah';
                    if (pct >= 50) {
                        tier = 'high';
                        tierLabel = 'Sangat Sesuai';
                    } else if (pct >= 25) {
                        tier = 'moderate';
                        tierLabel = 'Sederhana';
                    }

                    return {
                        ...job,
                        soft_list: softList,
                        soft_matched_list: softMatched,
                        soft_missing_list: softMissing,
                        soft_ratio: `${softMatched.length}/${softList.length}`,
                        tech_list: techList,
                        tech_matched_list: techMatched,
                        tech_missing_list: techMissing,
                        tech_ratio: `${techMatched.length}/${techList.length}`,
                        total_required: totalReq,
                        total_matched: totalMatched,
                        overall_percent: pct,
                        overall_ratio: `${totalMatched}/${totalReq}`,
                        tier: tier,
                        tier_label: tierLabel
                    };
                });

                // Sort descending by percentage, then by tech match count
                results.sort((a, b) => {
                    if (b.overall_percent !== a.overall_percent) {
                        return b.overall_percent - a.overall_percent;
                    }
                    return b.tech_matched_list.length - a.tech_matched_list.length;
                });

                return results;
            }

            function isSkillMatch(needle, haystackMap) {
                const n = needle.toLowerCase().trim();
                if (haystackMap[n]) return true;

                const nSingular = n.replace(/s$/, '');
                if (haystackMap[nSingular]) return true;

                for (let key in haystackMap) {
                    if (n === key || nSingular === key.replace(/s$/, '')) return true;
                    if (key.length > 4 && (n.includes(key) || key.includes(n))) return true;
                }
                return false;
            }

            function renderMappedCards(jobsList) {
                mappedJobsContainer.innerHTML = jobsList.map((job, idx) => {
                    const barColor = job.tier === 'high' ? '#10b981' : (job.tier === 'moderate' ? '#fbbf24' : '#64748b');

                    // Format soft skills with bold/highlight for matched
                    const softDisplay = job.soft_list.map(s => {
                        const isMatch = job.soft_matched_list.includes(s);
                        return isMatch
                            ? `<span class="skill-tag-matched"><i class="fa-solid fa-check"></i> <strong>${escapeHtml(s)}</strong></span>`
                            : `<span class="skill-tag-unmatched-neutral">${escapeHtml(s)}</span>`;
                    }).join(' ');

                    // Format tech skills with bold/highlight for matched
                    const techDisplay = job.tech_list.map(s => {
                        const isMatch = job.tech_matched_list.includes(s);
                        return isMatch
                            ? `<span class="skill-tag-matched"><i class="fa-solid fa-check"></i> <strong>${escapeHtml(s)}</strong></span>`
                            : `<span class="skill-tag-unmatched-neutral">${escapeHtml(s)}</span>`;
                    }).join(' ');

                    // Format skill gap pills
                    const gapTechHtml = job.tech_missing_list.length > 0
                        ? job.tech_missing_list.map(g => `<span class="skill-tag-gap"><i class="fa-solid fa-circle-exclamation"></i> ${escapeHtml(g)}</span>`).join(' ')
                        : '<span style="color:#34d399; font-size:0.8rem;"><i class="fa-solid fa-circle-check"></i> Tiada jurang teknikal!</span>';

                    const gapSoftHtml = job.soft_missing_list.length > 0
                        ? job.soft_missing_list.map(g => `<span class="skill-tag-gap"><i class="fa-solid fa-circle-exclamation"></i> ${escapeHtml(g)}</span>`).join(' ')
                        : '<span style="color:#34d399; font-size:0.8rem;"><i class="fa-solid fa-circle-check"></i> Tiada jurang insaniah!</span>';

                    return `
                        <div class="mapping-card tier-${job.tier} job-mapped-item"
                             data-title="${escapeHtml(job.job_position.toLowerCase())}"
                             data-focus="${escapeHtml(job.focus_area)}"
                             data-tier="${job.tier}"
                             data-skills="${escapeHtml((job.soft_skills_raw + ' ' + job.technical_skills_raw).toLowerCase())}">

                            <div class="mapping-header">
                                <div>
                                    <div class="mapping-job-title">
                                        <span style="font-size: 0.9rem; color: var(--text-muted);">#${idx + 1}</span>
                                        ${escapeHtml(job.job_position)}
                                        <span class="badge-step" style="font-size: 0.72rem;">${escapeHtml(job.focus_area)}</span>
                                        <span class="badge-step" style="font-size: 0.72rem; background:rgba(168,85,247,0.15); color:#c084fc; border-color:rgba(168,85,247,0.3);">${escapeHtml(job.job_area)}</span>
                                    </div>
                                    <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;">
                                        ${escapeHtml(job.job_description)}
                                    </div>
                                </div>

                                <div style="text-align: right;">
                                    <div class="match-score-badge ${job.tier}">
                                        <i class="fa-solid ${job.tier === 'high' ? 'fa-circle-check' : (job.tier === 'moderate' ? 'fa-triangle-exclamation' : 'fa-circle-xmark')}"></i>
                                        ${job.overall_percent}% (${job.overall_ratio}) &bull; ${job.tier_label}
                                    </div>
                                    <div class="ratio-bar-wrap" style="width: 160px; margin-left: auto;">
                                        <div class="ratio-bar-fill" style="width: ${job.overall_percent}%; background: ${barColor};"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="skills-spec-grid" style="margin-bottom: 0.75rem;">
                                <!-- Soft Skills Analysis -->
                                <div>
                                    <div class="spec-section-title" style="color: #c084fc;">
                                        <i class="fa-solid fa-users-gear"></i> Kemahiran Insaniah (Soft Skills) 
                                        <span class="ratio-pill" style="background:rgba(168,85,247,0.2); color:#c084fc; border-color:rgba(168,85,247,0.4);">${job.soft_ratio}</span>
                                    </div>
                                    <div style="line-height: 1.8;">
                                        ${softDisplay}
                                    </div>
                                </div>

                                <!-- Technical Skills Analysis -->
                                <div>
                                    <div class="spec-section-title" style="color: #38bdf8;">
                                        <i class="fa-solid fa-microchip"></i> Kemahiran Teknikal (Technical Skills) 
                                        <span class="ratio-pill">${job.tech_ratio}</span>
                                    </div>
                                    <div style="line-height: 1.8;">
                                        ${techDisplay}
                                    </div>
                                </div>
                            </div>

                            <!-- Skill Gap Box -->
                            <div style="background: rgba(239, 68, 68, 0.08); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: 12px; padding: 0.75rem 1rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
                                <div style="display: flex; flex-direction: column; gap: 0.35rem; flex: 1;">
                                    <div style="font-size: 0.78rem; font-weight: 700; color: #fca5a5; text-transform: uppercase; letter-spacing: 0.04em;">
                                        <i class="fa-solid fa-triangle-exclamation"></i> Jurang Kemahiran yang Diperlukan Industri (Skill Gap):
                                    </div>
                                    <div style="display: flex; flex-wrap: wrap; gap: 0.35rem;">
                                        ${gapTechHtml} ${gapSoftHtml}
                                    </div>
                                </div>

                                <button type="button" class="btn-inspect" onclick="inspectJobInDeepDive(${job.id})">
                                    <i class="fa-solid fa-magnifying-glass-plus"></i> Perincian Jurang
                                </button>
                            </div>
                        </div>
                    `;
                }).join('');

                filterMappedCards();
            }

            function filterMappedCards() {
                const query = document.getElementById('mapSearchInput').value.toLowerCase().trim();
                const tier = document.getElementById('mapFilterTier').value;
                const focus = document.getElementById('mapFilterFocus').value;

                const items = document.querySelectorAll('.job-mapped-item');
                let count = 0;

                items.forEach(item => {
                    const title = item.getAttribute('data-title');
                    const skills = item.getAttribute('data-skills');
                    const itemTier = item.getAttribute('data-tier');
                    const itemFocus = item.getAttribute('data-focus');

                    const matchQ = !query || title.includes(query) || skills.includes(query);
                    const matchTier = (tier === 'all') || (itemTier === tier);
                    const matchFocus = (focus === 'all') || (itemFocus === focus);

                    if (matchQ && matchTier && matchFocus) {
                        item.style.display = 'block';
                        count++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                document.getElementById('mapCounter').textContent = `Menunjukkan ${count} daripada ${items.length} jawatan`;
            }

            // Window helper to jump to deep dive tab
            window.inspectJobInDeepDive = function (jobId) {
                deepJobSelect.value = jobId;
                updateDeepDive();
                document.querySelector('.view-tab-btn[data-tab="deep-dive"]').click();
                deepDiveBox.scrollIntoView({ behavior: 'smooth', block: 'start' });
            };

            function updateDeepDive() {
                const resp = getSelectedRespondent();
                if (!resp) return;

                const targetJobId = parseInt(deepJobSelect.value);
                const job = jobsData.find(j => parseInt(j.id) === targetJobId);
                if (!job) return;

                const respMap = {};
                (resp.all_skills || []).forEach(s => {
                    if (s) respMap[s.toLowerCase().trim()] = true;
                });

                const softList = job.soft_skills_raw.split(',').map(s => s.trim()).filter(Boolean);
                const techList = job.technical_skills_raw.split(',').map(s => s.trim()).filter(Boolean);

                // Build Table Rows
                const rows = [];

                // Technical skills comparison rows
                techList.forEach(s => {
                    const isOwned = isSkillMatch(s, respMap);
                    rows.push({
                        category: 'Teknikal',
                        skill: s,
                        status: isOwned,
                        importance: 'Tinggi (Wajib)',
                        intervention: isOwned
                            ? '<span style="color:#34d399;"><i class="fa-solid fa-check"></i> Kompeten (Telah dicapai oleh responden)</span>'
                            : `<strong style="color:#fbbf24;"><i class="fa-solid fa-graduation-cap"></i> Modul Tambahan TVET:</strong> Kursus intensif / latihan makmal industri bagi <em>${escapeHtml(s)}</em>`
                    });
                });

                // Soft skills comparison rows
                softList.forEach(s => {
                    const isOwned = isSkillMatch(s, respMap);
                    rows.push({
                        category: 'Insaniah',
                        skill: s,
                        status: isOwned,
                        importance: 'Penting',
                        intervention: isOwned
                            ? '<span style="color:#34d399;"><i class="fa-solid fa-check"></i> Kompeten (Telah dicapai oleh responden)</span>'
                            : `<strong style="color:#c084fc;"><i class="fa-solid fa-lightbulb"></i> Bengkel Intervensi:</strong> Program pembangunan sahsiah & simulasi industri bagi <em>${escapeHtml(s)}</em>`
                    });
                });

                const matchedCount = rows.filter(r => r.status).length;
                const totalCount = rows.length;
                const pct = Math.round((matchedCount / totalCount) * 100);

                deepDiveBox.innerHTML = `
                    <div style="background: rgba(15, 23, 42, 0.85); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 16px; padding: 1.5rem; margin-bottom: 1.5rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                            <div>
                                <h3 style="font-size: 1.35rem; color: #f8fafc; margin-bottom: 0.25rem;">
                                    ${escapeHtml(job.job_position)}
                                </h3>
                                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0;">
                                    ${escapeHtml(job.job_area)} &bull; ${escapeHtml(job.focus_area)} &bull; ${escapeHtml(job.job_description)}
                                </p>
                            </div>
                            <div class="match-score-badge ${pct >= 50 ? 'high' : (pct >= 25 ? 'moderate' : 'low')}" style="font-size: 1rem; padding: 0.5rem 1.2rem;">
                                Kesesuaian: ${pct}% (${matchedCount}/${totalCount})
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="gap-table">
                            <thead>
                                <tr>
                                    <th style="width: 120px;">Kategori</th>
                                    <th>Kemahiran Diperlukan Industri</th>
                                    <th style="width: 180px; text-align: center;">Status Responden</th>
                                    <th>Kepentingan</th>
                                    <th>Cadangan Pelan Intervensi / Modul Tambahan IPT</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${rows.map(r => `
                                    <tr style="${!r.status ? 'background: rgba(239, 68, 68, 0.04);' : ''}">
                                        <td>
                                            <span class="skill-tag ${r.category === 'Teknikal' ? 'tech' : 'soft'}">${r.category}</span>
                                        </td>
                                        <td>
                                            <strong>${escapeHtml(r.skill)}</strong>
                                        </td>
                                        <td style="text-align: center;">
                                            ${r.status
                        ? '<span class="skill-tag-matched"><i class="fa-solid fa-check"></i> Memiliki</span>'
                        : '<span class="skill-tag-gap"><i class="fa-solid fa-xmark"></i> Tiada</span>'
                    }
                                        </td>
                                        <td>
                                            <small style="color: var(--text-muted);">${r.importance}</small>
                                        </td>
                                        <td>
                                            ${r.intervention}
                                        </td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                `;
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