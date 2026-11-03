<?php
/**
 * Main User Page - Survey IPT Cascading 4-Dropdown System
 */
require_once __DIR__ . '/config.php';
$db = getDBConnection();

// Fetch only enabled states, institutes, and courses for the survey form dropdowns
$enabledStates = $db->query("SELECT id, state_name FROM states WHERE status = 'enable' ORDER BY state_name ASC")->fetchAll(PDO::FETCH_ASSOC);
$enabledInstitutes = $db->query("SELECT id, state_id, institute_name FROM institutes WHERE status = 'enable' ORDER BY institute_name ASC")->fetchAll(PDO::FETCH_ASSOC);
$enabledCourses = $db->query("SELECT id, institute_id, course_name, campus_branch, qualification_level FROM courses WHERE status = 'enable' ORDER BY course_name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ms">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Tinjauan IPT - Cascading Dropdown</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .skills-chips-grid,
        #techSkillsWrapper,
        #softSkillsWrapper {
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 0.5rem !important;
            max-height: none !important;
            height: auto !important;
            overflow: visible !important;
            overflow-y: visible !important;
        }

        .skills-chips-grid::-webkit-scrollbar,
        #techSkillsWrapper::-webkit-scrollbar,
        #softSkillsWrapper::-webkit-scrollbar {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }
    </style>
</head>

<body>

    <div class="container">
        <!-- Navigation Header -->
        <nav class="navbar">
            <div class="brand">
                <i class="fa-solid fa-graduation-cap brand-icon"></i>
                <span>Sistem Tinjauan IPT</span>
            </div>
            <div class="nav-links">
                <a href="index.php" class="nav-btn active"><i class="fa-solid fa-list-check"></i> Borang Tinjauan</a>
                <!-- a href="cadang_institusi.php" class="nav-btn"><i class="fa-solid fa-school-flag"></i> Cadang IPT</a -->
                <!--a href="cadang_kursus.php" class="nav-btn"><i class="fa-solid fa-graduation-cap"></i> Cadang Kursus</a -->
                <a href="admin.php" class="nav-btn"><i class="fa-solid fa-sliders"></i> Panel Admin</a>
            </div>
        </nav>

        <nav class="navbar">
            <div class="brand">
                <p class="card-subtitle">
                    Soal selidik ini merupakan satu inisiatif di bawah Kedah TVET Strategic Implementation Roadmap yang
                    bertujuan untuk mengenalpasti jurang antara kemahiran teknikal serta insaniah yang diperoleh graduan
                    kejuruteraan di IPT dengan keperluan sebenar industri semasa. Maklum balas daripada pihak anda amat
                    penting bagi membantu pihak kami merangka program intervensi yang bersesuaian, berimpak tinggi,
                    serta berupaya meningkatkan peluang kebolehpasaran pekerjaan para graduan. Segala maklumat yang
                    diberikan adalah sulit dan hanya akan digunakan bagi tujuan penyelidikan dan pelaksanaan inisiatif
                    ini sahaja. Terima kasih atas masa dan kerjasama anda.
                </p>
            </div>
        </nav>

        <!-- Main Form Card -->
        <main class="glass-card">
            <h1 class="card-title">
                Pilihan Pengajian IPT
            </h1>
            <p class="card-subtitle">
                Sila pilih negeri, institusi, dan kursus yang ditawarkan melalui 3 dropdown dinamik di bawah.
            </p>

            <div id="alertBox" class="alert-box"></div>

            <form id="surveyForm" autocomplete="off">
                <div class="form-grid">

                    <!-- Respondent Name -->
                    <div class="form-group">
                        <label class="form-label" for="respondent_name">
                            <span>Nama Responden</span>
                            <span class="badge-step">Wajib</span>
                        </label>
                        <input type="text" id="respondent_name" name="respondent_name" class="custom-input"
                            placeholder="Contoh: Ahmad Bin Razak" required>
                    </div>

                    <!-- Respondent Position -->
                    <div class="form-group">
                        <label class="form-label" for="respondent_position">
                            <span>Jawatan Responden</span>
                            <span class="badge-step">Wajib</span>
                        </label>
                        <input type="text" id="respondent_position" name="respondent_position" class="custom-input"
                            placeholder="Contoh: Ketua Jabatan / Pensyarah / Penyelaras Program" required>
                    </div>

                    <!-- Respondent Email -->
                    <div class="form-group">
                        <label class="form-label" for="respondent_email">
                            <span>Alamat E-mel Responden</span>
                            <span class="badge-step">Wajib</span>
                        </label>
                        <input type="email" id="respondent_email" name="respondent_email" class="custom-input"
                            placeholder="Contoh: ahmad@email.com" required>
                    </div>

                    <!-- Select State -->
                    <div class="form-group">
                        <label class="form-label" for="drop_state">
                            <span>1. Pilih Negeri</span>
                        </label>
                        <div class="select-wrapper">
                            <select id="drop_state" name="state_id" class="custom-select" disabled>
                                <option value="">-- Memuatkan Negeri... --</option>
                            </select>
                            <i class="fa-solid fa-chevron-down select-arrow"></i>
                            <div class="spinner-inline"></div>
                        </div>
                    </div>

                    <!-- Select Institute -->
                    <div class="form-group">
                        <label class="form-label" for="drop_institute">
                            <span>2. Pilih Institusi Pengajian Tinggi (IPT)</span>
                        </label>
                        <div class="select-wrapper">
                            <select id="drop_institute" name="institute_id" class="custom-select" disabled>
                                <option value="">-- Pilih Negeri Terlebih Dahulu --</option>
                            </select>
                            <i class="fa-solid fa-chevron-down select-arrow"></i>
                            <div class="spinner-inline"></div>
                        </div>

                        <div class="suggest-ipt-prompt"
                            style="margin-top: 0.5rem; font-size: 0.82rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem; padding: 0.5rem 0.75rem; background: rgba(56, 189, 248, 0.07); border: 1px dashed rgba(56, 189, 248, 0.25); border-radius: 8px;">
                            <span style="color: #94a3b8;">
                                <i class="fa-solid fa-circle-question" style="color: var(--primary-glow, #38bdf8);"></i>
                                IPT
                                anda tiada dalam senarai?
                            </span>
                            <button type="button" id="btnOpenSuggestModal"
                                style="background: none; border: none; color: var(--primary-glow, #38bdf8); font-weight: 600; cursor: pointer; text-decoration: underline; font-size: 0.82rem; display: inline-flex; align-items: center; gap: 0.35rem; padding: 0;">
                                <i class="fa-solid fa-plus-circle"></i> Cadang Institusi Baharu
                            </button>
                        </div>
                    </div>

                    <!-- Select Course Offered -->
                    <div class="form-group">
                        <label class="form-label" for="drop_course">
                            <span>3. Pilih Kursus / Program Ditawarkan</span>
                        </label>
                        <div class="select-wrapper">
                            <select id="drop_course" name="course_id" class="custom-select" disabled>
                                <option value="">-- Pilih Institusi Terlebih Dahulu --</option>
                            </select>
                            <i class="fa-solid fa-chevron-down select-arrow"></i>
                            <div class="spinner-inline"></div>
                        </div>
                        <div class="suggest-ipt-prompt"
                            style="margin-top: 0.5rem; font-size: 0.82rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem; padding: 0.5rem 0.75rem; background: rgba(16, 185, 129, 0.07); border: 1px dashed rgba(16, 185, 129, 0.25); border-radius: 8px;">
                            <span style="color: #94a3b8;">
                                <i class="fa-solid fa-circle-question" style="color: #10b981;"></i> Kursus anda tiada
                                dalam
                                senarai?
                            </span>
                            <button type="button" id="btnOpenSuggestCourseModal"
                                style="background: none; border: none; color: #10b981; font-weight: 600; cursor: pointer; text-decoration: underline; font-size: 0.82rem; display: inline-flex; align-items: center; gap: 0.35rem; padding: 0;">
                                <i class="fa-solid fa-plus-circle"></i> Cadang Kursus Baharu
                            </button>
                        </div>
                    </div>

                    <!-- Annual Student Intake -->
                    <div class="form-group">
                        <label class="form-label" for="annual_intake">
                            <span>4. Bilangan Kemasukan Pelajar Setahun</span>
                            <span class="badge-step">Wajib</span>
                        </label>
                        <input type="number" id="annual_intake" name="annual_intake" class="custom-input" min="0"
                            placeholder="Contoh: 150" required>
                    </div>

                </div>

                <!-- Dynamic Skills Section -->
                <div id="skillsContainer" class="skills-card" style="display: none;">
                    <div class="skills-card-header">
                        <h3><i class="fa-solid fa-gears" style="color: var(--primary-glow);"></i> Keperluan
                            Kemahiran (Skills Requirements)</h3>
                        <p class="skills-subtitle">Sila pilih kemahiran teknikal &amp; insaniah yang pelajar perolehi
                            setelah tamat pengajian.</p>
                        <div class="suggest-ipt-prompt"
                            style="margin-top: 0.75rem; font-size: 0.82rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem; padding: 0.5rem 0.75rem; background: rgba(168, 85, 247, 0.08); border: 1px dashed rgba(168, 85, 247, 0.3); border-radius: 8px;">
                            <span style="color: #94a3b8;">
                                <i class="fa-solid fa-circle-question" style="color: #c084fc;"></i> Kemahiran yang anda
                                cari tiada dalam senarai?
                            </span>
                            <button type="button" id="btnOpenSuggestSkillModal"
                                style="background: none; border: none; color: #c084fc; font-weight: 600; cursor: pointer; text-decoration: underline; font-size: 0.82rem; display: inline-flex; align-items: center; gap: 0.35rem; padding: 0;">
                                <i class="fa-solid fa-plus-circle"></i> Cadang Kemahiran Baharu
                            </button>
                        </div>
                    </div>

                    <!-- Technical Skills -->
                    <div class="skills-group">
                        <div class="skills-group-header">
                            <label class="skills-group-title"><i class="fa-solid fa-microchip"></i> Kemahiran
                                Teknikal (Technical Skills)</label>
                            <input type="text" id="techSearch" class="skill-search-input"
                                placeholder="Cari kemahiran teknikal...">
                        </div>
                        <div id="techSkillsWrapper" class="skills-chips-grid"></div>
                    </div>

                    <!-- Soft Skills -->
                    <div class="skills-group" style="margin-top: 1.5rem;">
                        <div class="skills-group-header">
                            <label class="skills-group-title"><i class="fa-solid fa-users-gear"></i> Kemahiran
                                Insaniah (Soft Skills)</label>
                        </div>
                        <div id="softSkillsWrapper" class="skills-chips-grid"></div>
                    </div>
                </div>

                <!-- Live Selection Summary -->
                <div class="summary-card">
                    <div class="summary-title"><i class="fa-solid fa-receipt"></i> RINGKASAN PILIHAN ANDA</div>
                    <div class="summary-list">
                        <div class="summary-item">
                            <span class="summary-label">Negeri:</span>
                            <span class="summary-value" id="sum_state">- Belum Dipilih -</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Institusi (IPT):</span>
                            <span class="summary-value" id="sum_institute">- Belum Dipilih -</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Kursus Ditawarkan:</span>
                            <span class="summary-value" id="sum_course">- Belum Dipilih -</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Kemahiran Dipilih:</span>
                            <span class="summary-value" id="sum_skills">0 Kemahiran</span>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" id="submitBtn" class="btn-submit" disabled>
                    <i class="fa-solid fa-paper-plane"></i> Hantar Maklumat Tinjauan
                </button>

            </form>
        </main>

        <footer>
            <p>&copy;
                <?php echo date('Y'); ?> Sistem Tinjauan Program Kejuruteraan IPT Malaysia | Develop by : TD9
                KISMEC
            </p>
        </footer>
    </div>

    <!-- MODAL: CADANG INSTITUSI BAHARU (NORMAL USER) -->
    <div id="modalSuggestInstitute" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-header">
                <div class="modal-title">
                    <i class="fa-solid fa-school-flag" style="color: var(--primary-glow);"></i> Maklum / Cadang
                    Institusi Baharu
                </div>
                <button type="button" class="modal-close" id="btnCloseSuggestModal">&times;</button>
            </div>
            <form id="modalSuggestForm">
                <div class="modal-body">
                    <p style="font-size: 0.85rem; color: #94a3b8; margin-bottom: 1.25rem; line-height: 1.5;">
                        Sila masukkan maklumat institusi yang ingin dicadangkan. Permohonan ini akan disemak oleh
                        Pentadbir Sistem sebelum dimasukkan ke dalam senarai pilihan rasmi. Ia mengambil masa 3 hari bekerja untuk pengesahan.
                    </p>

                    <div id="modalSuggestAlert" class="alert-box" style="display: none; margin-bottom: 1rem;"></div>

                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label class="form-label" for="modal_suggest_state_id">1. Pilih Negeri Institusi</label>
                        <div class="select-wrapper">
                            <select id="modal_suggest_state_id" name="state_id" class="custom-select" required>
                                <option value="">-- Pilih Negeri --</option>
                            </select>
                            <i class="fa-solid fa-chevron-down select-arrow"></i>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label class="form-label" for="modal_suggest_institute_name">2. Nama Institusi / IPT</label>
                        <input type="text" id="modal_suggest_institute_name" name="institute_name" class="custom-input"
                            placeholder="Contoh: Universiti Malaysia Kelantan (UMK)" required>
                        <small
                            style="color: var(--text-muted); font-size: 0.78rem; margin-top: 0.3rem; display: block;">
                            Sila masukkan nama penuh berserta singkatan.
                        </small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-secondary" id="btnCancelSuggestModal">Batal</button>
                    <button type="submit" id="btnSubmitModalSuggest" class="btn-submit">
                        <i class="fa-solid fa-paper-plane"></i> Hantar Cadangan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: CADANG KURSUS BAHARU (NORMAL USER) -->
    <div id="modalSuggestCourse" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-header">
                <div class="modal-title">
                    <i class="fa-solid fa-graduation-cap" style="color: #10b981;"></i> Maklum / Cadang Kursus Baharu
                </div>
                <button type="button" class="modal-close" id="btnCloseSuggestCourseModal">&times;</button>
            </div>
            <form id="modalSuggestCourseForm">
                <div class="modal-body">
                    <p style="font-size: 0.85rem; color: #94a3b8; margin-bottom: 1.25rem; line-height: 1.5;">
                        Sila masukkan maklumat kursus yang ingin dicadangkan. Permohonan ini akan disemak oleh Pentadbir
                        Sistem sebelum dimasukkan ke dalam senarai kursus rasmi. Ia mengambil masa 3 hari bekerja untuk pengesahan.
                    </p>

                    <div id="modalSuggestCourseAlert" class="alert-box" style="display: none; margin-bottom: 1rem;">
                    </div>

                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label class="form-label" for="modal_suggest_course_state_id">1. Pilih Negeri IPT</label>
                        <div class="select-wrapper">
                            <select id="modal_suggest_course_state_id" class="custom-select" required>
                                <option value="">-- Pilih Negeri Terlebih Dahulu --</option>
                            </select>
                            <i class="fa-solid fa-chevron-down select-arrow"></i>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label class="form-label" for="modal_suggest_course_institute_id">2. Pilih Institusi
                            (IPT)</label>
                        <div class="select-wrapper">
                            <select id="modal_suggest_course_institute_id" name="institute_id" class="custom-select"
                                required disabled>
                                <option value="">-- Pilih Negeri Terlebih Dahulu --</option>
                            </select>
                            <i class="fa-solid fa-chevron-down select-arrow"></i>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label class="form-label" for="modal_suggest_course_name">3. Nama Kursus / Program
                            Akademik</label>
                        <input type="text" id="modal_suggest_course_name" name="course_name" class="custom-input"
                            placeholder="Contoh: Diploma Kejuruteraan Mekatronik" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 0.5rem;">
                        <div class="form-group">
                            <label class="form-label" for="modal_suggest_qualification_level">4. Tahap Kelayakan</label>
                            <div class="select-wrapper">
                                <select id="modal_suggest_qualification_level" name="qualification_level"
                                    class="custom-select" required>
                                    <option value="Diploma" selected>Diploma</option>
                                    <option value="Degree">Degree / Sarjana Muda</option>
                                    <option value="Certificate">Certificate / Sijil</option>
                                    <option value="SKM">SKM</option>
                                    <option value="Master">Master</option>
                                </select>
                                <i class="fa-solid fa-chevron-down select-arrow"></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="modal_suggest_campus_branch">5. Cawangan</label>
                            <input type="text" id="modal_suggest_campus_branch" name="campus_branch"
                                class="custom-input" value="Kampus Utama" placeholder="Kampus Utama">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-secondary" id="btnCancelSuggestCourseModal">Batal</button>
                    <button type="submit" id="btnSubmitModalSuggestCourse" class="btn-submit"
                        style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <i class="fa-solid fa-paper-plane"></i> Hantar Cadangan Kursus
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: CADANG KEMAHIRAN BAHARU (NORMAL USER) -->
    <div id="modalSuggestSkill" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-header">
                <div class="modal-title">
                    <i class="fa-solid fa-brain" style="color: #c084fc;"></i> Maklum / Cadang Kemahiran Baharu
                </div>
                <button type="button" class="modal-close" id="btnCloseSuggestSkillModal">&times;</button>
            </div>
            <form id="modalSuggestSkillForm">
                <div class="modal-body">
                    <p style="font-size: 0.85rem; color: #94a3b8; margin-bottom: 1.25rem; line-height: 1.5;">
                        Sila masukkan maklumat kemahiran yang ingin dicadangkan. Permohonan ini akan disemak oleh
                        Pentadbir Sistem sebelum dimasukkan ke dalam senarai kemahiran rasmi. Ia mengambil masa 3 hari bekerja untuk pengesahan.
                    </p>

                    <div id="modalSuggestSkillAlert" class="alert-box" style="display: none; margin-bottom: 1rem;">
                    </div>

                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label class="form-label" for="modal_suggest_skill_name">1. Nama Kemahiran</label>
                        <input type="text" id="modal_suggest_skill_name" name="skill_name" class="custom-input"
                            placeholder="Contoh: Analisis Data / PLC Programming" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div class="form-group">
                            <label class="form-label" for="modal_suggest_skill_type">2. Jenis Kemahiran</label>
                            <div class="select-wrapper">
                                <select id="modal_suggest_skill_type" name="skill_type" class="custom-select" required>
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="technical">Technical (Teknikal)</option>
                                    <option value="soft">Soft (Insaniah)</option>
                                </select>
                                <i class="fa-solid fa-chevron-down select-arrow"></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="modal_suggest_skill_qualification">3. Tahap Pengajian</label>
                            <div class="select-wrapper">
                                <select id="modal_suggest_skill_qualification" name="qualification_level"
                                    class="custom-select" required>
                                    <option value="SKM">SKM</option>
                                    <option value="SPM">SPM</option>
                                    <option value="Diploma" selected>Diploma</option>
                                    <option value="Degree">Degree / Sarjana Muda</option>
                                    <option value="Master">Master</option>
                                </select>
                                <i class="fa-solid fa-chevron-down select-arrow"></i>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 0.5rem;">
                        <label class="form-label" for="modal_suggest_skill_description">4. Keterangan / Penerangan
                            (Pilihan)</label>
                        <textarea id="modal_suggest_skill_description" name="skill_description" class="custom-input"
                            rows="3" placeholder="Berikan penerangan ringkas mengenai skop kemahiran ini..."></textarea>
                    </div>

                    <!-- NOTE: Status is completely HIDDEN from normal users (defaults to KIV) -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" id="btnCancelSuggestSkillModal">Batal</button>
                    <button type="submit" id="btnSubmitModalSuggestSkill" class="btn-submit"
                        style="background: linear-gradient(135deg, #a855f7 0%, #7e22ce 100%);">
                        <i class="fa-solid fa-paper-plane"></i> Hantar Cadangan Kemahiran
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Filter: Ensure Section 1 (States), Section 2 (Institutes), and Section 3 (Courses) only list records with status = 'enable' -->
    <script>
        (function() {
            const enabledStates = <?php echo json_encode($enabledStates, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
            const enabledInstitutes = <?php echo json_encode($enabledInstitutes, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
            const enabledCourses = <?php echo json_encode($enabledCourses, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

            const originalFetch = window.fetch;
            window.fetch = function(resource, init) {
                const url = typeof resource === 'string' ? resource : (resource && resource.url ? resource.url : '');

                // Intercept get_states: return only states with status = 'enable'
                if (url.includes('action=get_states')) {
                    return Promise.resolve(new Response(JSON.stringify({
                        status: 'success',
                        data: enabledStates
                    }), {
                        status: 200,
                        headers: { 'Content-Type': 'application/json' }
                    }));
                }

                // Intercept get_institutes: return only institutes with status = 'enable'
                if (url.includes('action=get_institutes')) {
                    const urlObj = new URL(url, window.location.origin);
                    const stateId = parseInt(urlObj.searchParams.get('state_id') || '0', 10);
                    const filtered = enabledInstitutes.filter(inst => parseInt(inst.state_id, 10) === stateId);
                    return Promise.resolve(new Response(JSON.stringify({
                        status: 'success',
                        data: filtered
                    }), {
                        status: 200,
                        headers: { 'Content-Type': 'application/json' }
                    }));
                }

                // Intercept get_courses: return only courses with status = 'enable'
                if (url.includes('action=get_courses')) {
                    const urlObj = new URL(url, window.location.origin);
                    const instId = parseInt(urlObj.searchParams.get('institute_id') || '0', 10);
                    const filtered = enabledCourses.filter(c => parseInt(c.institute_id, 10) === instId);
                    return Promise.resolve(new Response(JSON.stringify({
                        status: 'success',
                        data: filtered
                    }), {
                        status: 200,
                        headers: { 'Content-Type': 'application/json' }
                    }));
                }

                return originalFetch.apply(this, arguments);
            };
        })();
    </script>
    <script src="script.js"></script>
</body>

</html>