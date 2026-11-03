<?php
/**
 * AJAX API Endpoint for Survey IPT System
 * Returns JSON for dynamic cascaded dropdown requests & CRUD operations
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');

require_once __DIR__ . '/config.php';

$action = $_GET['action'] ?? ($_POST['action'] ?? '');

try {
    $db = getDBConnection();

    switch ($action) {
        case 'get_states':
            $stmt = $db->query("SELECT id, state_name FROM states ORDER BY state_name ASC");
            $states = $stmt->fetchAll();
            echo json_encode(['status' => 'success', 'data' => $states]);
            break;

        case 'get_institutes':
            $state_id = (int)($_GET['state_id'] ?? 0);
            if ($state_id <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid State ID']);
                exit;
            }
            $stmt = $db->prepare("SELECT id, institute_name FROM institutes WHERE state_id = :state_id ORDER BY institute_name ASC");
            $stmt->execute(['state_id' => $state_id]);
            $institutes = $stmt->fetchAll();
            echo json_encode(['status' => 'success', 'data' => $institutes]);
            break;

        case 'get_courses':
            $institute_id = (int)($_GET['institute_id'] ?? 0);
            if ($institute_id <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid Institute ID']);
                exit;
            }
            $stmt = $db->prepare("SELECT id, course_name, campus_branch, qualification_level FROM courses WHERE institute_id = :institute_id ORDER BY course_name ASC");
            $stmt->execute(['institute_id' => $institute_id]);
            $courses = $stmt->fetchAll();
            echo json_encode(['status' => 'success', 'data' => $courses]);
            break;

        case 'get_skills':
            $course_id = (int)($_GET['course_id'] ?? 0);
            $qualification_level = trim($_GET['qualification_level'] ?? '');

            if ($course_id > 0 && empty($qualification_level)) {
                $stmtCourse = $db->prepare("SELECT qualification_level FROM courses WHERE id = :cid");
                $stmtCourse->execute(['cid' => $course_id]);
                $cRow = $stmtCourse->fetch();
                if ($cRow) {
                    $qualification_level = $cRow['qualification_level'];
                }
            }

            if (empty($qualification_level)) {
                $qualification_level = 'Diploma';
            }

            $stmt = $db->prepare("SELECT id, skill_name, skill_type FROM skills WHERE qualification_level = :lvl OR qualification_level = 'All' ORDER BY skill_name ASC");
            $stmt->execute(['lvl' => $qualification_level]);
            $allSkills = $stmt->fetchAll();

            $technical = [];
            $soft = [];
            foreach ($allSkills as $s) {
                if ($s['skill_type'] === 'technical') {
                    $technical[] = $s;
                } else {
                    $soft[] = $s;
                }
            }

            echo json_encode([
                'status' => 'success',
                'qualification_level' => $qualification_level,
                'data' => [
                    'technical' => $technical,
                    'soft' => $soft
                ]
            ]);
            break;

        case 'submit_survey':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['status' => 'error', 'message' => 'POST method required']);
                exit;
            }

            $name = trim($_POST['respondent_name'] ?? '');
            $email = trim($_POST['respondent_email'] ?? '');
            $position = trim($_POST['respondent_position'] ?? '');
            $state_id = (int)($_POST['state_id'] ?? 0);
            $institute_id = (int)($_POST['institute_id'] ?? 0);
            $course_id = (int)($_POST['course_id'] ?? 0);
            $annual_intake = (int)($_POST['annual_intake'] ?? 0);
            
            $raw_skills = $_POST['skills'] ?? [];
            if (!is_array($raw_skills)) {
                $raw_skills = explode(',', $raw_skills);
            }

            if (empty($name) || empty($email) || $state_id <= 0 || $institute_id <= 0 || $course_id <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'Sila lengkapkan nama, e-mel, dan semua pilihan tinjauan.']);
                exit;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['status' => 'error', 'message' => 'Format alamat e-mel tidak sah.']);
                exit;
            }

            $stmt = $db->prepare("INSERT INTO survey_submissions (respondent_name, respondent_email, respondent_position, state_id, institute_id, course_id, annual_intake) VALUES (:name, :email, :position, :s, :i, :c, :intake)");
            $stmt->execute([
                'name'     => $name,
                'email'    => $email,
                'position' => $position,
                's'        => $state_id,
                'i'        => $institute_id,
                'c'        => $course_id,
                'intake'   => $annual_intake
            ]);
            $submission_id = $db->lastInsertId();

            if (!empty($raw_skills)) {
                $insSkill = $db->prepare("INSERT INTO survey_submission_skills (submission_id, skill_id) VALUES (:sub_id, :sk_id)");
                foreach ($raw_skills as $skId) {
                    $skIdInt = (int)$skId;
                    if ($skIdInt > 0) {
                        $insSkill->execute(['sub_id' => $submission_id, 'sk_id' => $skIdInt]);
                    }
                }
            }

            echo json_encode([
                'status' => 'success',
                'message' => 'Maklumat tinjauan berjaya disimpan! Terima kasih.',
                'submission_id' => $submission_id
            ]);
            break;

        case 'add_state':
            $state_name = trim($_POST['state_name'] ?? '');
            if (empty($state_name)) {
                echo json_encode(['status' => 'error', 'message' => 'Nama negeri diperlukan']);
                exit;
            }
            $stmt = $db->prepare("INSERT INTO states (state_name) VALUES (:name)");
            $stmt->execute(['name' => $state_name]);
            echo json_encode(['status' => 'success', 'message' => 'Negeri berjaya ditambah!']);
            break;

        case 'add_institute':
            $state_id = (int)($_POST['state_id'] ?? 0);
            $institute_name = trim($_POST['institute_name'] ?? '');

            if ($state_id <= 0 || empty($institute_name)) {
                echo json_encode(['status' => 'error', 'message' => 'Pilih negeri dan isi nama institusi']);
                exit;
            }
            $stmt = $db->prepare("INSERT INTO institutes (state_id, institute_name) VALUES (:s, :name)");
            $stmt->execute(['s' => $state_id, 'name' => $institute_name]);
            echo json_encode(['status' => 'success', 'message' => 'Institusi berjaya ditambah!']);
            break;

        case 'add_course':
            $institute_id = (int)($_POST['institute_id'] ?? 0);
            $course_name = trim($_POST['course_name'] ?? '');
            $campus_branch = trim($_POST['campus_branch'] ?? 'Kampus Utama');
            $qualification_level = trim($_POST['qualification_level'] ?? 'Degree');

            if ($institute_id <= 0 || empty($course_name)) {
                echo json_encode(['status' => 'error', 'message' => 'Pilih institusi dan isi nama kursus']);
                exit;
            }

            $stmt = $db->prepare("INSERT INTO courses (institute_id, course_name, campus_branch, qualification_level) VALUES (:inst, :name, :branch, :level)");
            $stmt->execute([
                'inst'  => $institute_id,
                'name'  => $course_name,
                'branch'=> $campus_branch,
                'level' => $qualification_level
            ]);

            echo json_encode(['status' => 'success', 'message' => 'Kursus & Tahap Pengajian berjaya ditambah!']);
            break;

        case 'get_submissions':
            $stmt = $db->query("
                SELECT sub.id, sub.respondent_name, sub.respondent_email, sub.respondent_position, s.state_name, i.institute_name, c.course_name, c.campus_branch, c.qualification_level, sub.annual_intake, sub.submitted_at,
                       GROUP_CONCAT(DISTINCT CASE WHEN sk.skill_type = 'technical' THEN sk.skill_name END ORDER BY sk.skill_name SEPARATOR ', ') AS technical_skills,
                       GROUP_CONCAT(DISTINCT CASE WHEN sk.skill_type = 'soft' THEN sk.skill_name END ORDER BY sk.skill_name SEPARATOR ', ') AS soft_skills
                FROM survey_submissions sub
                JOIN states s ON sub.state_id = s.id
                JOIN institutes i ON sub.institute_id = i.id
                JOIN courses c ON sub.course_id = c.id
                LEFT JOIN survey_submission_skills ssk ON sub.id = ssk.submission_id
                LEFT JOIN skills sk ON ssk.skill_id = sk.id
                GROUP BY sub.id
                ORDER BY sub.submitted_at DESC
                LIMIT 50
            ");
            echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll()]);
            break;

        case 'export_csv':
            requireLogin();
            $stmt = $db->query("
                SELECT sub.id, sub.respondent_name, sub.respondent_email, sub.respondent_position, s.state_name, i.institute_name, c.course_name, c.qualification_level, c.campus_branch, sub.annual_intake, sub.submitted_at,
                       GROUP_CONCAT(DISTINCT CASE WHEN sk.skill_type = 'technical' THEN sk.skill_name END ORDER BY sk.skill_name SEPARATOR '; ') AS technical_skills,
                       GROUP_CONCAT(DISTINCT CASE WHEN sk.skill_type = 'soft' THEN sk.skill_name END ORDER BY sk.skill_name SEPARATOR '; ') AS soft_skills
                FROM survey_submissions sub
                JOIN states s ON sub.state_id = s.id
                JOIN institutes i ON sub.institute_id = i.id
                JOIN courses c ON sub.course_id = c.id
                LEFT JOIN survey_submission_skills ssk ON sub.id = ssk.submission_id
                LEFT JOIN skills sk ON ssk.skill_id = sk.id
                GROUP BY sub.id
                ORDER BY sub.submitted_at DESC
            ");
            $rows = $stmt->fetchAll();

            $filename = "rekod_tinjauan_responden_" . date('Ymd_His') . ".csv";

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');

            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

            fputcsv($output, ['ID', 'Nama Responden', 'Jawatan Responden', 'E-mel Responden', 'Negeri', 'Institusi (IPT)', 'Kursus Ditawarkan', 'Tahap Pengajian', 'Kampus / Cawangan', 'Bilangan Kemasukan Setahun', 'Kemahiran Teknikal', 'Kemahiran Insaniah', 'Masa Hantar']);

            foreach ($rows as $row) {
                fputcsv($output, [
                    $row['id'],
                    $row['respondent_name'],
                    $row['respondent_position'] ?? '-',
                    $row['respondent_email'] ?? '-',
                    $row['state_name'],
                    $row['institute_name'],
                    $row['course_name'],
                    $row['qualification_level'],
                    $row['campus_branch'],
                    $row['annual_intake'] ?? 0,
                    $row['technical_skills'] ?? '-',
                    $row['soft_skills'] ?? '-',
                    $row['submitted_at']
                ]);
            }
            fclose($output);
            exit;
            break;

        case 'get_users':
            requireLogin();
            $stmt = $db->query("SELECT id, username, full_name, role, created_at FROM admin_users ORDER BY id ASC");
            echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll()]);
            break;

        case 'add_user':
            requireLogin();
            $username  = trim($_POST['username'] ?? '');
            $full_name = trim($_POST['full_name'] ?? '');
            $password  = trim($_POST['password'] ?? '');
            $role      = trim($_POST['role'] ?? 'admin');

            if (empty($username) || empty($full_name) || empty($password)) {
                echo json_encode(['status' => 'error', 'message' => 'Lengkapkan nama pengguna, nama penuh, dan kata laluan.']);
                exit;
            }

            // Check if username exists
            $stmtChk = $db->prepare("SELECT id FROM admin_users WHERE username = :u");
            $stmtChk->execute(['u' => $username]);
            if ($stmtChk->fetch()) {
                echo json_encode(['status' => 'error', 'message' => 'Nama pengguna ini telah wujud. Sila pilih nama pengguna lain.']);
                exit;
            }

            $pass_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmtIns = $db->prepare("INSERT INTO admin_users (username, password_hash, full_name, role) VALUES (:u, :ph, :fn, :role)");
            $stmtIns->execute(['u' => $username, 'ph' => $pass_hash, 'fn' => $full_name, 'role' => $role]);

            echo json_encode(['status' => 'success', 'message' => 'Pengguna admin baharu berjaya didaftarkan!']);
            break;

        case 'delete_user':
            requireLogin();
            $user_id = (int)($_POST['user_id'] ?? 0);
            $currentUser = getCurrentUser();

            if ($user_id <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'ID Pengguna tidak sah']);
                exit;
            }

            if ($currentUser && (int)$currentUser['id'] === $user_id) {
                echo json_encode(['status' => 'error', 'message' => 'Anda tidak boleh memadam akaun anda sendiri.']);
                exit;
            }

            $stmtDel = $db->prepare("DELETE FROM admin_users WHERE id = :id");
            $stmtDel->execute(['id' => $user_id]);

            echo json_encode(['status' => 'success', 'message' => 'Pengguna admin berjaya dipadam.']);
            break;

        case 'delete_submission':
            requireLogin();
            $submission_id = (int)($_POST['submission_id'] ?? 0);

            if ($submission_id <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'ID Tinjauan tidak sah.']);
                exit;
            }

            $stmtDel = $db->prepare("DELETE FROM survey_submissions WHERE id = :id");
            $stmtDel->execute(['id' => $submission_id]);

            echo json_encode(['status' => 'success', 'message' => 'Rekod tinjauan berjaya dipadam.']);
            break;

        case 'get_skills_admin':
            requireLogin();
            $level = trim($_GET['qualification_level'] ?? '');
            $type = trim($_GET['skill_type'] ?? '');
            $search = trim($_GET['search'] ?? '');

            $sql = "SELECT s.id, s.skill_name, s.skill_type, s.qualification_level, s.created_at,
                           COUNT(ssk.submission_id) AS usage_count
                    FROM skills s
                    LEFT JOIN survey_submission_skills ssk ON s.id = ssk.skill_id
                    WHERE 1=1";
            $params = [];

            if (!empty($level) && $level !== 'all') {
                $sql .= " AND s.qualification_level = :lvl";
                $params['lvl'] = $level;
            }

            if (!empty($type) && $type !== 'all') {
                $sql .= " AND s.skill_type = :type";
                $params['type'] = $type;
            }

            if (!empty($search)) {
                $sql .= " AND s.skill_name LIKE :search";
                $params['search'] = '%' . $search . '%';
            }

            $sql .= " GROUP BY s.id ORDER BY s.qualification_level ASC, s.skill_type ASC, s.skill_name ASC";

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $skills = $stmt->fetchAll();

            echo json_encode(['status' => 'success', 'data' => $skills]);
            break;

        case 'get_skills_stats':
            requireLogin();
            $stmt = $db->query("
                SELECT 
                    COUNT(*) as total_skills,
                    SUM(CASE WHEN qualification_level = 'SPM' THEN 1 ELSE 0 END) as spm_count,
                    SUM(CASE WHEN qualification_level = 'SKM' THEN 1 ELSE 0 END) as skm_count,
                    SUM(CASE WHEN qualification_level = 'Diploma' THEN 1 ELSE 0 END) as diploma_count,
                    SUM(CASE WHEN qualification_level = 'Degree' THEN 1 ELSE 0 END) as degree_count,
                    SUM(CASE WHEN qualification_level = 'Master' THEN 1 ELSE 0 END) as master_count,
                    SUM(CASE WHEN qualification_level = 'Diploma' AND skill_type = 'technical' THEN 1 ELSE 0 END) as diploma_tech,
                    SUM(CASE WHEN qualification_level = 'Diploma' AND skill_type = 'soft' THEN 1 ELSE 0 END) as diploma_soft,
                    SUM(CASE WHEN qualification_level = 'Degree' AND skill_type = 'technical' THEN 1 ELSE 0 END) as degree_tech,
                    SUM(CASE WHEN qualification_level = 'Degree' AND skill_type = 'soft' THEN 1 ELSE 0 END) as degree_soft
                FROM skills
            ");
            $stats = $stmt->fetch();
            echo json_encode(['status' => 'success', 'data' => $stats]);
            break;

        case 'add_skill':
            requireLogin();
            $skill_name = trim($_POST['skill_name'] ?? '');
            $skill_type = trim($_POST['skill_type'] ?? '');
            $qualification_level = trim($_POST['qualification_level'] ?? '');

            if (empty($skill_name)) {
                echo json_encode(['status' => 'error', 'message' => 'Sila masukkan nama kemahiran.']);
                exit;
            }

            if (!in_array($skill_type, ['technical', 'soft'])) {
                echo json_encode(['status' => 'error', 'message' => 'Jenis kemahiran mestilah "technical" atau "soft".']);
                exit;
            }

            $valid_levels = ['SPM', 'SKM', 'Diploma', 'Degree', 'Master', 'Both', 'All'];
            if (!in_array($qualification_level, $valid_levels)) {
                echo json_encode(['status' => 'error', 'message' => 'Tahap pengajian tidak sah (Pilih SPM, SKM, Diploma, Degree, Master, atau Kedua-duanya).']);
                exit;
            }

            $levels_to_insert = ($qualification_level === 'Both') 
                ? ['Diploma', 'Degree'] 
                : (($qualification_level === 'All') ? ['SPM', 'SKM', 'Diploma', 'Degree', 'Master'] : [$qualification_level]);
            $added = 0;
            $skipped = 0;

            $stmtChk = $db->prepare("SELECT id FROM skills WHERE LOWER(skill_name) = LOWER(:name) AND qualification_level = :lvl");
            $stmtIns = $db->prepare("INSERT INTO skills (skill_name, skill_type, qualification_level) VALUES (:name, :type, :lvl)");

            foreach ($levels_to_insert as $lvl) {
                $stmtChk->execute(['name' => $skill_name, 'lvl' => $lvl]);
                if ($stmtChk->fetch()) {
                    $skipped++;
                } else {
                    $stmtIns->execute(['name' => $skill_name, 'type' => $skill_type, 'lvl' => $lvl]);
                    $added++;
                }
            }

            if ($added === 0 && $skipped > 0) {
                echo json_encode(['status' => 'error', 'message' => "Kemahiran \"$skill_name\" telah wujud bagi tahap pengajian yang dipilih."]);
                exit;
            }

            $msg = ($added > 1) 
                ? "Kemahiran \"$skill_name\" ($skill_type) berjaya ditambah untuk Diploma & Degree!"
                : "Kemahiran \"$skill_name\" ($skill_type) berjaya ditambah untuk $qualification_level!";

            if ($skipped > 0) {
                $msg .= " (Satu rekod sedia ada diskip).";
            }

            echo json_encode(['status' => 'success', 'message' => $msg]);
            break;

        case 'bulk_add_skills':
            requireLogin();
            $skills_text = trim($_POST['skills_text'] ?? '');
            $skill_type = trim($_POST['skill_type'] ?? '');
            $qualification_level = trim($_POST['qualification_level'] ?? '');

            if (empty($skills_text)) {
                echo json_encode(['status' => 'error', 'message' => 'Sila masukkan senarai kemahiran.']);
                exit;
            }

            if (!in_array($skill_type, ['technical', 'soft'])) {
                echo json_encode(['status' => 'error', 'message' => 'Jenis kemahiran mestilah "technical" atau "soft".']);
                exit;
            }

            $valid_levels = ['SPM', 'SKM', 'Diploma', 'Degree', 'Master', 'Both', 'All'];
            if (!in_array($qualification_level, $valid_levels)) {
                echo json_encode(['status' => 'error', 'message' => 'Tahap pengajian tidak sah (Pilih SPM, SKM, Diploma, Degree, Master, atau Kedua-duanya).']);
                exit;
            }

            $levels_to_insert = ($qualification_level === 'Both') 
                ? ['Diploma', 'Degree'] 
                : (($qualification_level === 'All') ? ['SPM', 'SKM', 'Diploma', 'Degree', 'Master'] : [$qualification_level]);
            $lines = preg_split('/[\r\n,]+/', $skills_text);
            $added = 0;
            $skipped = 0;

            $stmtChk = $db->prepare("SELECT id FROM skills WHERE LOWER(skill_name) = LOWER(:name) AND qualification_level = :lvl");
            $stmtIns = $db->prepare("INSERT INTO skills (skill_name, skill_type, qualification_level) VALUES (:name, :type, :lvl)");

            foreach ($lines as $line) {
                $sName = trim($line);
                if (empty($sName)) continue;

                foreach ($levels_to_insert as $lvl) {
                    $stmtChk->execute(['name' => $sName, 'lvl' => $lvl]);
                    if ($stmtChk->fetch()) {
                        $skipped++;
                    } else {
                        $stmtIns->execute(['name' => $sName, 'type' => $skill_type, 'lvl' => $lvl]);
                        $added++;
                    }
                }
            }

            echo json_encode([
                'status' => 'success', 
                'message' => "Pukal selesai: $added kemahiran berjaya ditambah" . ($skipped > 0 ? " ($skipped pendua diskip)." : ".")
            ]);
            break;

        case 'delete_skill':
            requireLogin();
            $skill_id = (int)($_POST['skill_id'] ?? 0);

            if ($skill_id <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'ID Kemahiran tidak sah.']);
                exit;
            }

            $stmtInfo = $db->prepare("SELECT skill_name, qualification_level, skill_type FROM skills WHERE id = :id");
            $stmtInfo->execute(['id' => $skill_id]);
            $skInfo = $stmtInfo->fetch();

            if (!$skInfo) {
                echo json_encode(['status' => 'error', 'message' => 'Kemahiran tidak dijumpai atau telah dipadam.']);
                exit;
            }

            $stmtDel = $db->prepare("DELETE FROM skills WHERE id = :id");
            $stmtDel->execute(['id' => $skill_id]);

            echo json_encode([
                'status' => 'success', 
                'message' => "Kemahiran \"{$skInfo['skill_name']}\" ({$skInfo['qualification_level']} - {$skInfo['skill_type']}) berjaya dipadam."
            ]);
            break;

        case 'bulk_delete_skills':
            requireLogin();
            $raw_ids = $_POST['skill_ids'] ?? [];
            if (!is_array($raw_ids)) {
                $raw_ids = explode(',', $raw_ids);
            }

            $ids = array_filter(array_map('intval', $raw_ids), fn($id) => $id > 0);

            if (empty($ids)) {
                echo json_encode(['status' => 'error', 'message' => 'Tiada kemahiran yang dipilih untuk dipadam.']);
                exit;
            }

            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmtDel = $db->prepare("DELETE FROM skills WHERE id IN ($placeholders)");
            $stmtDel->execute(array_values($ids));
            $deletedCount = $stmtDel->rowCount();

            echo json_encode([
                'status' => 'success',
                'message' => "$deletedCount kemahiran terpilih berjaya dipadam."
            ]);
            break;

        case 'update_skill':
            requireLogin();
            $skill_id = (int)($_POST['skill_id'] ?? 0);
            $skill_name = trim($_POST['skill_name'] ?? '');
            $skill_type = trim($_POST['skill_type'] ?? '');
            $qualification_level = trim($_POST['qualification_level'] ?? '');

            if ($skill_id <= 0 || empty($skill_name)) {
                echo json_encode(['status' => 'error', 'message' => 'ID dan nama kemahiran diperlukan.']);
                exit;
            }

            if (!in_array($skill_type, ['technical', 'soft'])) {
                echo json_encode(['status' => 'error', 'message' => 'Jenis kemahiran mestilah "technical" atau "soft".']);
                exit;
            }

            if (!in_array($qualification_level, ['SPM', 'SKM', 'Diploma', 'Degree', 'Master'])) {
                echo json_encode(['status' => 'error', 'message' => 'Tahap pengajian mestilah SPM, SKM, Diploma, Degree, atau Master.']);
                exit;
            }

            // Check duplicate excluding this id
            $stmtChk = $db->prepare("SELECT id FROM skills WHERE LOWER(skill_name) = LOWER(:name) AND qualification_level = :lvl AND id != :id");
            $stmtChk->execute(['name' => $skill_name, 'lvl' => $qualification_level, 'id' => $skill_id]);
            if ($stmtChk->fetch()) {
                echo json_encode(['status' => 'error', 'message' => "Kemahiran \"$skill_name\" telah wujud bagi tahap $qualification_level."]);
                exit;
            }

            $stmtUpd = $db->prepare("UPDATE skills SET skill_name = :name, skill_type = :type, qualification_level = :lvl WHERE id = :id");
            $stmtUpd->execute([
                'name' => $skill_name,
                'type' => $skill_type,
                'lvl'  => $qualification_level,
                'id'   => $skill_id
            ]);

            echo json_encode(['status' => 'success', 'message' => 'Maklumat kemahiran berjaya dikemaskini!']);
            break;

        case 'get_admin_stats':
            requireLogin();
            $stats = $db->query("
                SELECT 
                    (SELECT COUNT(*) FROM states) as total_states,
                    (SELECT COUNT(*) FROM institutes) as total_institutes,
                    (SELECT COUNT(*) FROM courses) as total_courses,
                    (SELECT COUNT(*) FROM admin_users) as total_users,
                    (SELECT COUNT(*) FROM survey_submissions) as total_submissions,
                    (SELECT COUNT(*) FROM temp_institutes) as total_temp_institutes,
                    (SELECT COUNT(*) FROM temp_institutes WHERE status = 'KIV') as total_temp_kiv,
                    (SELECT COUNT(*) FROM temp_institutes WHERE status = 'Approve') as total_temp_approve,
                    (SELECT COUNT(*) FROM temp_courses) as total_temp_courses,
                    (SELECT COUNT(*) FROM temp_courses WHERE status = 'KIV') as total_temp_courses_kiv,
                    (SELECT COUNT(*) FROM temp_courses WHERE status = 'Approve') as total_temp_courses_approve,
                    (SELECT COUNT(*) FROM temp_skills) as total_temp_skills,
                    (SELECT COUNT(*) FROM temp_skills WHERE status = 'KIV') as total_temp_skills_kiv,
                    (SELECT COUNT(*) FROM temp_skills WHERE status = 'Approve') as total_temp_skills_approve
            ")->fetch();
            echo json_encode(['status' => 'success', 'data' => $stats]);
            break;

        case 'get_states_admin':
            requireLogin();
            $sql = "
                SELECT s.id, s.state_name, s.status, s.created_at,
                       COUNT(DISTINCT i.id) AS total_institutes,
                       COUNT(DISTINCT c.id) AS total_courses,
                       COUNT(DISTINCT sub.id) AS total_submissions
                FROM states s
                LEFT JOIN institutes i ON s.id = i.state_id
                LEFT JOIN courses c ON i.id = c.institute_id
                LEFT JOIN survey_submissions sub ON s.id = sub.state_id
                GROUP BY s.id, s.state_name, s.status, s.created_at
                ORDER BY s.state_name ASC
            ";
            $stmt = $db->query($sql);
            echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll()]);
            break;

        case 'update_state':
            requireLogin();
            $state_id = (int)($_POST['state_id'] ?? 0);
            $state_name = trim($_POST['state_name'] ?? '');
            $status = trim($_POST['status'] ?? 'enable');
            if (!in_array($status, ['enable', 'disable'])) {
                $status = 'enable';
            }

            if ($state_id <= 0 || empty($state_name)) {
                echo json_encode(['status' => 'error', 'message' => 'ID dan nama negeri diperlukan.']);
                exit;
            }
            $chk = $db->prepare("SELECT id FROM states WHERE LOWER(state_name) = LOWER(:name) AND id != :id");
            $chk->execute(['name' => $state_name, 'id' => $state_id]);
            if ($chk->fetch()) {
                echo json_encode(['status' => 'error', 'message' => "Negeri \"$state_name\" telah wujud."]);
                exit;
            }
            $stmt = $db->prepare("UPDATE states SET state_name = :name, status = :status WHERE id = :id");
            $stmt->execute([
                'name'   => $state_name,
                'status' => $status,
                'id'     => $state_id
            ]);
            echo json_encode(['status' => 'success', 'message' => 'Nama negeri berjaya dikemaskini!']);
            break;

        case 'delete_state':
            requireLogin();
            $state_id = (int)($_POST['state_id'] ?? 0);
            if ($state_id <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'ID Negeri tidak sah.']);
                exit;
            }
            $chkSub = $db->prepare("SELECT COUNT(*) FROM survey_submissions WHERE state_id = :id");
            $chkSub->execute(['id' => $state_id]);
            $subCount = (int)$chkSub->fetchColumn();
            if ($subCount > 0) {
                echo json_encode([
                    'status' => 'error', 
                    'message' => "Negeri ini tidak boleh dipadam kerana terdapat $subCount rekod tinjauan responden yang berdaftar di bawahnya."
                ]);
                exit;
            }
            $stmt = $db->prepare("DELETE FROM states WHERE id = :id");
            $stmt->execute(['id' => $state_id]);
            echo json_encode(['status' => 'success', 'message' => 'Negeri berjaya dipadam!']);
            break;

        case 'get_institutes_admin':
            requireLogin();
            $state_filter = (int)($_GET['state_id'] ?? 0);
            $search = trim($_GET['search'] ?? '');
            
            $sql = "
                SELECT i.id, i.state_id, i.institute_name, i.status, i.created_at, s.state_name,
                       COUNT(DISTINCT c.id) AS total_courses,
                       COUNT(DISTINCT sub.id) AS total_submissions
                FROM institutes i
                JOIN states s ON i.state_id = s.id
                LEFT JOIN courses c ON i.id = c.institute_id
                LEFT JOIN survey_submissions sub ON i.id = sub.institute_id
                WHERE 1=1
            ";
            $params = [];
            if ($state_filter > 0) {
                $sql .= " AND i.state_id = :sid";
                $params['sid'] = $state_filter;
            }
            if (!empty($search)) {
                $sql .= " AND i.institute_name LIKE :search";
                $params['search'] = '%' . $search . '%';
            }
            $sql .= " GROUP BY i.id, i.state_id, i.institute_name, i.status, i.created_at, s.state_name ORDER BY s.state_name ASC, i.institute_name ASC";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll()]);
            break;

        case 'update_institute':
            requireLogin();
            $institute_id = (int)($_POST['institute_id'] ?? 0);
            $state_id = (int)($_POST['state_id'] ?? 0);
            $institute_name = trim($_POST['institute_name'] ?? '');
            $status = trim($_POST['status'] ?? 'enable');
            if (!in_array($status, ['enable', 'disable'])) {
                $status = 'enable';
            }

            if ($institute_id <= 0 || $state_id <= 0 || empty($institute_name)) {
                echo json_encode(['status' => 'error', 'message' => 'Sila pilih negeri dan masukkan nama institusi.']);
                exit;
            }
            $chk = $db->prepare("SELECT id FROM institutes WHERE LOWER(institute_name) = LOWER(:name) AND state_id = :sid AND id != :id");
            $chk->execute(['name' => $institute_name, 'sid' => $state_id, 'id' => $institute_id]);
            if ($chk->fetch()) {
                echo json_encode(['status' => 'error', 'message' => "Institusi \"$institute_name\" telah wujud di bawah negeri tersebut."]);
                exit;
            }
            $stmt = $db->prepare("UPDATE institutes SET state_id = :sid, institute_name = :name, status = :status WHERE id = :id");
            $stmt->execute(['sid' => $state_id, 'name' => $institute_name, 'status' => $status, 'id' => $institute_id]);
            echo json_encode(['status' => 'success', 'message' => 'Maklumat institusi berjaya dikemaskini!']);
            break;

        case 'delete_institute':
            requireLogin();
            $institute_id = (int)($_POST['institute_id'] ?? 0);
            if ($institute_id <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'ID Institusi tidak sah.']);
                exit;
            }
            $chkSub = $db->prepare("SELECT COUNT(*) FROM survey_submissions WHERE institute_id = :id");
            $chkSub->execute(['id' => $institute_id]);
            $subCount = (int)$chkSub->fetchColumn();
            if ($subCount > 0) {
                echo json_encode([
                    'status' => 'error', 
                    'message' => "Institusi ini tidak boleh dipadam kerana terdapat $subCount rekod tinjauan responden yang berdaftar di bawahnya."
                ]);
                exit;
            }
            $stmt = $db->prepare("DELETE FROM institutes WHERE id = :id");
            $stmt->execute(['id' => $institute_id]);
            echo json_encode(['status' => 'success', 'message' => 'Institusi berjaya dipadam!']);
            break;

        case 'get_courses_admin':
            requireLogin();
            $state_filter = (int)($_GET['state_id'] ?? 0);
            $inst_filter = (int)($_GET['institute_id'] ?? 0);
            $level_filter = trim($_GET['qualification_level'] ?? '');
            $search = trim($_GET['search'] ?? '');
            
            $sql = "
                SELECT c.id, c.institute_id, c.course_name, c.campus_branch, c.qualification_level, c.status, c.created_at,
                       i.institute_name, i.state_id, s.state_name,
                       COUNT(DISTINCT sub.id) AS total_submissions
                FROM courses c
                JOIN institutes i ON c.institute_id = i.id
                JOIN states s ON i.state_id = s.id
                LEFT JOIN survey_submissions sub ON c.id = sub.course_id
                WHERE 1=1
            ";
            $params = [];
            if ($state_filter > 0) {
                $sql .= " AND i.state_id = :sid";
                $params['sid'] = $state_filter;
            }
            if ($inst_filter > 0) {
                $sql .= " AND c.institute_id = :iid";
                $params['iid'] = $inst_filter;
            }
            if (!empty($level_filter) && $level_filter !== 'all') {
                $sql .= " AND c.qualification_level = :lvl";
                $params['lvl'] = $level_filter;
            }
            if (!empty($search)) {
                $sql .= " AND (c.course_name LIKE :search OR i.institute_name LIKE :search)";
                $params['search'] = '%' . $search . '%';
            }
            $sql .= " GROUP BY c.id, c.institute_id, c.course_name, c.campus_branch, c.qualification_level, c.status, c.created_at, i.institute_name, i.state_id, s.state_name ORDER BY s.state_name ASC, i.institute_name ASC, c.course_name ASC";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll()]);
            break;

        case 'update_course':
            requireLogin();
            $course_id = (int)($_POST['course_id'] ?? 0);
            $institute_id = (int)($_POST['institute_id'] ?? 0);
            $course_name = trim($_POST['course_name'] ?? '');
            $campus_branch = trim($_POST['campus_branch'] ?? 'Kampus Utama');
            $qualification_level = trim($_POST['qualification_level'] ?? 'Diploma');
            $status = trim($_POST['status'] ?? 'enable');
            if (!in_array($status, ['enable', 'disable'])) {
                $status = 'enable';
            }

            if ($course_id <= 0 || $institute_id <= 0 || empty($course_name)) {
                echo json_encode(['status' => 'error', 'message' => 'Sila pilih institusi dan masukkan nama kursus.']);
                exit;
            }

            $stmt = $db->prepare("UPDATE courses SET institute_id = :inst, course_name = :name, campus_branch = :branch, qualification_level = :level, status = :status WHERE id = :id");
            $stmt->execute([
                'inst'   => $institute_id,
                'name'   => $course_name,
                'branch' => $campus_branch,
                'level'  => $qualification_level,
                'status' => $status,
                'id'     => $course_id
            ]);
            echo json_encode(['status' => 'success', 'message' => 'Maklumat kursus berjaya dikemaskini!']);
            break;

        case 'delete_course':
            requireLogin();
            $course_id = (int)($_POST['course_id'] ?? 0);
            if ($course_id <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'ID Kursus tidak sah.']);
                exit;
            }
            $chkSub = $db->prepare("SELECT COUNT(*) FROM survey_submissions WHERE course_id = :id");
            $chkSub->execute(['id' => $course_id]);
            $subCount = (int)$chkSub->fetchColumn();
            if ($subCount > 0) {
                echo json_encode([
                    'status' => 'error', 
                    'message' => "Kursus ini tidak boleh dipadam kerana terdapat $subCount rekod tinjauan responden yang berdaftar di bawahnya."
                ]);
                exit;
            }
            $stmt = $db->prepare("DELETE FROM courses WHERE id = :id");
            $stmt->execute(['id' => $course_id]);
            echo json_encode(['status' => 'success', 'message' => 'Kursus berjaya dipadam!']);
            break;

        case 'suggest_institute':
            // Public endpoint for normal users to key in/suggest a new institute.
            // Normal user cannot see or edit status - status is strictly saved as 'KIV'.
            $state_id = (int)($_POST['state_id'] ?? 0);
            $institute_name = trim($_POST['institute_name'] ?? '');

            if ($state_id <= 0 || empty($institute_name)) {
                echo json_encode(['status' => 'error', 'message' => 'Sila pilih negeri dan masukkan nama institusi yang sah.']);
                exit;
            }

            if (mb_strlen($institute_name) < 3) {
                echo json_encode(['status' => 'error', 'message' => 'Nama institusi mestilah sekurang-kurangnya 3 aksara.']);
                exit;
            }

            $stmtState = $db->prepare("SELECT state_name FROM states WHERE id = :id");
            $stmtState->execute(['id' => $state_id]);
            $state = $stmtState->fetch();
            if (!$state) {
                echo json_encode(['status' => 'error', 'message' => 'Negeri yang dipilih tidak sah.']);
                exit;
            }

            // Check if already exists in official institutes table
            $chkOfficial = $db->prepare("SELECT id FROM institutes WHERE state_id = :sid AND LOWER(TRIM(institute_name)) = LOWER(:name)");
            $chkOfficial->execute(['sid' => $state_id, 'name' => $institute_name]);
            if ($chkOfficial->fetch()) {
                echo json_encode([
                    'status' => 'warning',
                    'message' => "Institusi \"$institute_name\" sudah pun wujud dalam senarai rasmi bagi negeri {$state['state_name']}."
                ]);
                exit;
            }

            // Check if already exists in temp_institutes
            $chkTemp = $db->prepare("SELECT id, status FROM temp_institutes WHERE state_id = :sid AND LOWER(TRIM(institute_name)) = LOWER(:name)");
            $chkTemp->execute(['sid' => $state_id, 'name' => $institute_name]);
            $existingTemp = $chkTemp->fetch();
            if ($existingTemp) {
                if ($existingTemp['status'] === 'Approve') {
                    echo json_encode([
                        'status' => 'info',
                        'message' => "Institusi \"$institute_name\" telah diluluskan oleh Pentadbir dan sedia untuk dikemaskini ke senarai utama."
                    ]);
                } else {
                    echo json_encode([
                        'status' => 'info',
                        'message' => "Institusi \"$institute_name\" telah dicadangkan sebelum ini dan kini dalam proses semakan (KIV) pihak Pentadbir."
                    ]);
                }
                exit;
            }

            // Insert into temp_institutes with default status 'KIV'
            $stmt = $db->prepare("INSERT INTO temp_institutes (state_id, institute_name, status) VALUES (:sid, :name, 'KIV')");
            $stmt->execute(['sid' => $state_id, 'name' => $institute_name]);

            echo json_encode([
                'status' => 'success',
                'message' => 'Cadangan institusi berjaya dihantar kepada Pentadbir Sistem untuk semakan dan kelulusan. Terima kasih!'
            ]);
            break;

        case 'get_temp_institutes_admin':
            requireLogin();
            $state_filter = (int)($_GET['state_id'] ?? 0);
            $status_filter = trim($_GET['status'] ?? 'all');
            $search = trim($_GET['search'] ?? '');

            $sql = "
                SELECT t.id, t.state_id, t.institute_name, t.status, t.created_at,
                       s.state_name
                FROM temp_institutes t
                JOIN states s ON t.state_id = s.id
                WHERE 1=1
            ";
            $params = [];

            if ($state_filter > 0) {
                $sql .= " AND t.state_id = :sid";
                $params['sid'] = $state_filter;
            }
            if (!empty($status_filter) && $status_filter !== 'all') {
                $sql .= " AND t.status = :st";
                $params['st'] = $status_filter;
            }
            if (!empty($search)) {
                $sql .= " AND (t.institute_name LIKE :search OR s.state_name LIKE :search)";
                $params['search'] = '%' . $search . '%';
            }

            // Put KIV status first, then newest
            $sql .= " ORDER BY (t.status = 'KIV') DESC, t.created_at DESC";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll()]);
            break;

        case 'update_temp_institute_status':
            requireLogin();
            $id = (int)($_POST['id'] ?? 0);
            $status = trim($_POST['status'] ?? '');

            if ($id <= 0 || !in_array($status, ['KIV', 'Approve'])) {
                echo json_encode(['status' => 'error', 'message' => 'Status tidak sah. Nilai yang sah adalah "KIV" atau "Approve".']);
                exit;
            }

            $stmt = $db->prepare("UPDATE temp_institutes SET status = :status WHERE id = :id");
            $stmt->execute(['status' => $status, 'id' => $id]);
            echo json_encode([
                'status' => 'success',
                'message' => "Status institusi berjaya ditukar kepada \"$status\"!"
            ]);
            break;

        case 'update_temp_institute':
            requireLogin();
            $id = (int)($_POST['id'] ?? 0);
            $state_id = (int)($_POST['state_id'] ?? 0);
            $name = trim($_POST['institute_name'] ?? '');
            $status = trim($_POST['status'] ?? 'KIV');

            if ($id <= 0 || $state_id <= 0 || empty($name) || !in_array($status, ['KIV', 'Approve'])) {
                echo json_encode(['status' => 'error', 'message' => 'Sila lengkapkan semua maklumat dan pastikan status sah.']);
                exit;
            }

            $stmt = $db->prepare("UPDATE temp_institutes SET state_id = :sid, institute_name = :name, status = :st WHERE id = :id");
            $stmt->execute(['sid' => $state_id, 'name' => $name, 'st' => $status, 'id' => $id]);
            echo json_encode(['status' => 'success', 'message' => 'Maklumat cadangan institusi berjaya dikemaskini!']);
            break;

        case 'delete_temp_institute':
            requireLogin();
            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'ID tidak sah.']);
                exit;
            }
            $stmt = $db->prepare("DELETE FROM temp_institutes WHERE id = :id");
            $stmt->execute(['id' => $id]);
            echo json_encode(['status' => 'success', 'message' => 'Rekod permohonan institusi berjaya dipadam!']);
            break;

        case 'transfer_temp_institutes':
            requireLogin();
            // Function for button "Update Institure"
            // Transfers data from table "temp_institutes" into "institutes"
            $target_id = (int)($_POST['id'] ?? 0);

            if ($target_id > 0) {
                $stmt = $db->prepare("SELECT * FROM temp_institutes WHERE id = :id AND status = 'Approve'");
                $stmt->execute(['id' => $target_id]);
                $items = $stmt->fetchAll();
            } else {
                $stmt = $db->query("SELECT * FROM temp_institutes WHERE status = 'Approve' ORDER BY id ASC");
                $items = $stmt->fetchAll();
            }

            if (empty($items)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Tiada rekod institusi dengan status "Approve" untuk dipindahkan. Sila semak senarai dan ubah status kepada "Approve" terlebih dahulu sebelum menekan butang Update Institure.'
                ]);
                exit;
            }

            $db->beginTransaction();
            $transferredCount = 0;
            $mergedCount = 0;

            $insertStmt = $db->prepare("INSERT INTO institutes (state_id, institute_name, created_at) VALUES (:sid, :name, :created_at)");
            $checkExisting = $db->prepare("SELECT id FROM institutes WHERE state_id = :sid AND LOWER(TRIM(institute_name)) = LOWER(:name)");
            $deleteTemp = $db->prepare("DELETE FROM temp_institutes WHERE id = :id");

            foreach ($items as $item) {
                // Check if already in institutes table
                $checkExisting->execute(['sid' => $item['state_id'], 'name' => trim($item['institute_name'])]);
                $exists = $checkExisting->fetch();

                if (!$exists) {
                    $insertStmt->execute([
                        'sid'        => $item['state_id'],
                        'name'       => trim($item['institute_name']),
                        'created_at' => $item['created_at'] ?? date('Y-m-d H:i:s')
                    ]);
                    $transferredCount++;
                } else {
                    $mergedCount++;
                }

                // Remove from temp_institutes once transferred
                $deleteTemp->execute(['id' => $item['id']]);
            }

            $db->commit();

            $msg = "$transferredCount institusi baharu berjaya dipindahkan ke jadual institutes!";
            if ($mergedCount > 0) {
                $msg .= " ($mergedCount rekod sudah sedia wujud dalam jadual rasmi dan diselaraskan).";
            }

            echo json_encode([
                'status' => 'success',
                'message' => $msg,
                'transferred_count' => $transferredCount,
                'merged_count' => $mergedCount
            ]);
            break;

        case 'suggest_course':
            // Public endpoint for normal users to key in/suggest a new course.
            // Normal user cannot see or edit status - status is strictly saved as 'KIV'.
            $institute_id = (int)($_POST['institute_id'] ?? 0);
            $course_name = trim($_POST['course_name'] ?? '');
            $campus_branch = trim($_POST['campus_branch'] ?? 'Kampus Utama');
            $qualification_level = trim($_POST['qualification_level'] ?? 'Diploma');

            if ($institute_id <= 0 || empty($course_name)) {
                echo json_encode(['status' => 'error', 'message' => 'Sila pilih institusi dan masukkan nama kursus yang sah.']);
                exit;
            }

            if (mb_strlen($course_name) < 3) {
                echo json_encode(['status' => 'error', 'message' => 'Nama kursus mestilah sekurang-kurangnya 3 aksara.']);
                exit;
            }

            // Verify institute exists
            $stmtInst = $db->prepare("SELECT id, institute_name FROM institutes WHERE id = :id");
            $stmtInst->execute(['id' => $institute_id]);
            $inst = $stmtInst->fetch();
            if (!$inst) {
                echo json_encode(['status' => 'error', 'message' => 'Institusi yang dipilih tidak sah.']);
                exit;
            }

            // Check if already exists in official courses table for this institute
            $chkOfficial = $db->prepare("SELECT id FROM courses WHERE institute_id = :iid AND LOWER(TRIM(course_name)) = LOWER(:name)");
            $chkOfficial->execute(['iid' => $institute_id, 'name' => $course_name]);
            if ($chkOfficial->fetch()) {
                echo json_encode([
                    'status' => 'warning',
                    'message' => "Kursus \"$course_name\" sudah pun wujud dalam senarai rasmi bagi institusi {$inst['institute_name']}."
                ]);
                exit;
            }

            // Check if already in temp_courses table
            $chkTemp = $db->prepare("SELECT id, status FROM temp_courses WHERE institute_id = :iid AND LOWER(TRIM(course_name)) = LOWER(:name)");
            $chkTemp->execute(['iid' => $institute_id, 'name' => $course_name]);
            $existingTemp = $chkTemp->fetch();
            if ($existingTemp) {
                if ($existingTemp['status'] === 'Approve') {
                    echo json_encode([
                        'status' => 'info',
                        'message' => "Kursus \"$course_name\" telah diluluskan oleh Pentadbir dan sedia untuk dikemaskini ke senarai utama."
                    ]);
                } else {
                    echo json_encode([
                        'status' => 'info',
                        'message' => "Kursus \"$course_name\" telah dicadangkan sebelum ini dan kini dalam proses semakan (KIV) pihak Pentadbir."
                    ]);
                }
                exit;
            }

            // Insert into temp_courses with default status 'KIV'
            $stmt = $db->prepare("INSERT INTO temp_courses (institute_id, course_name, campus_branch, qualification_level, status) VALUES (:iid, :name, :branch, :level, 'KIV')");
            $stmt->execute([
                'iid'    => $institute_id,
                'name'   => $course_name,
                'branch' => !empty($campus_branch) ? $campus_branch : 'Kampus Utama',
                'level'  => !empty($qualification_level) ? $qualification_level : 'Diploma'
            ]);

            echo json_encode([
                'status' => 'success',
                'message' => 'Cadangan kursus baharu berjaya dihantar kepada Pentadbir Sistem untuk semakan dan kelulusan. Terima kasih!'
            ]);
            break;

        case 'get_temp_courses_admin':
            requireLogin();
            $state_filter = (int)($_GET['state_id'] ?? 0);
            $inst_filter = (int)($_GET['institute_id'] ?? 0);
            $level_filter = trim($_GET['qualification_level'] ?? '');
            $status_filter = trim($_GET['status'] ?? 'all');
            $search = trim($_GET['search'] ?? '');

            $sql = "
                SELECT tc.id, tc.institute_id, tc.course_name, tc.campus_branch, tc.qualification_level, tc.status, tc.created_at,
                       i.institute_name, i.state_id, s.state_name
                FROM temp_courses tc
                JOIN institutes i ON tc.institute_id = i.id
                JOIN states s ON i.state_id = s.id
                WHERE 1=1
            ";
            $params = [];

            if ($state_filter > 0) {
                $sql .= " AND i.state_id = :sid";
                $params['sid'] = $state_filter;
            }
            if ($inst_filter > 0) {
                $sql .= " AND tc.institute_id = :iid";
                $params['iid'] = $inst_filter;
            }
            if (!empty($level_filter) && $level_filter !== 'all') {
                $sql .= " AND tc.qualification_level = :lvl";
                $params['lvl'] = $level_filter;
            }
            if (!empty($status_filter) && $status_filter !== 'all') {
                $sql .= " AND tc.status = :st";
                $params['st'] = $status_filter;
            }
            if (!empty($search)) {
                $sql .= " AND (tc.course_name LIKE :search OR i.institute_name LIKE :search OR tc.campus_branch LIKE :search)";
                $params['search'] = '%' . $search . '%';
            }

            // Put KIV status first, then newest
            $sql .= " ORDER BY (tc.status = 'KIV') DESC, tc.created_at DESC";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll()]);
            break;

        case 'update_temp_course_status':
            requireLogin();
            $id = (int)($_POST['id'] ?? 0);
            $status = trim($_POST['status'] ?? '');

            if ($id <= 0 || !in_array($status, ['KIV', 'Approve'])) {
                echo json_encode(['status' => 'error', 'message' => 'Status tidak sah. Nilai yang sah adalah "KIV" atau "Approve".']);
                exit;
            }

            $stmt = $db->prepare("UPDATE temp_courses SET status = :status WHERE id = :id");
            $stmt->execute(['status' => $status, 'id' => $id]);
            echo json_encode([
                'status' => 'success',
                'message' => "Status kursus berjaya ditukar kepada \"$status\"!"
            ]);
            break;

        case 'update_temp_course':
            requireLogin();
            $id = (int)($_POST['id'] ?? 0);
            $institute_id = (int)($_POST['institute_id'] ?? 0);
            $course_name = trim($_POST['course_name'] ?? '');
            $campus_branch = trim($_POST['campus_branch'] ?? 'Kampus Utama');
            $qualification_level = trim($_POST['qualification_level'] ?? 'Diploma');
            $status = trim($_POST['status'] ?? 'KIV');

            if ($id <= 0 || $institute_id <= 0 || empty($course_name) || !in_array($status, ['KIV', 'Approve'])) {
                echo json_encode(['status' => 'error', 'message' => 'Sila lengkapkan semua maklumat dan pastikan status sah.']);
                exit;
            }

            $stmt = $db->prepare("UPDATE temp_courses SET institute_id = :iid, course_name = :name, campus_branch = :branch, qualification_level = :lvl, status = :st WHERE id = :id");
            $stmt->execute([
                'iid'    => $institute_id,
                'name'   => $course_name,
                'branch' => $campus_branch,
                'lvl'    => $qualification_level,
                'st'     => $status,
                'id'     => $id
            ]);
            echo json_encode(['status' => 'success', 'message' => 'Maklumat cadangan kursus berjaya dikemaskini!']);
            break;

        case 'delete_temp_course':
            requireLogin();
            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'ID tidak sah.']);
                exit;
            }
            $stmt = $db->prepare("DELETE FROM temp_courses WHERE id = :id");
            $stmt->execute(['id' => $id]);
            echo json_encode(['status' => 'success', 'message' => 'Rekod permohonan kursus berjaya dipadam!']);
            break;

        case 'transfer_temp_courses':
            requireLogin();
            // Function for button "Update Course"
            // Transfers data from table "temp_courses" into "courses"
            $target_id = (int)($_POST['id'] ?? 0);

            if ($target_id > 0) {
                $stmt = $db->prepare("SELECT * FROM temp_courses WHERE id = :id AND status = 'Approve'");
                $stmt->execute(['id' => $target_id]);
                $items = $stmt->fetchAll();
            } else {
                $stmt = $db->query("SELECT * FROM temp_courses WHERE status = 'Approve' ORDER BY id ASC");
                $items = $stmt->fetchAll();
            }

            if (empty($items)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Tiada rekod kursus dengan status "Approve" untuk dipindahkan. Sila semak senarai dan ubah status kepada "Approve" terlebih dahulu sebelum menekan butang Update Course.'
                ]);
                exit;
            }

            $db->beginTransaction();
            $transferredCount = 0;
            $mergedCount = 0;

            $insertStmt = $db->prepare("INSERT INTO courses (institute_id, course_name, campus_branch, qualification_level, created_at) VALUES (:iid, :name, :branch, :lvl, :created_at)");
            $checkExisting = $db->prepare("SELECT id FROM courses WHERE institute_id = :iid AND LOWER(TRIM(course_name)) = LOWER(:name)");
            $deleteTemp = $db->prepare("DELETE FROM temp_courses WHERE id = :id");

            foreach ($items as $item) {
                // Check if already in courses table
                $checkExisting->execute(['iid' => $item['institute_id'], 'name' => trim($item['course_name'])]);
                $exists = $checkExisting->fetch();

                if (!$exists) {
                    $insertStmt->execute([
                        'iid'        => $item['institute_id'],
                        'name'       => trim($item['course_name']),
                        'branch'     => $item['campus_branch'] ?? 'Kampus Utama',
                        'lvl'        => $item['qualification_level'] ?? 'Diploma',
                        'created_at' => $item['created_at'] ?? date('Y-m-d H:i:s')
                    ]);
                    $transferredCount++;
                } else {
                    $mergedCount++;
                }

                // Remove from temp_courses once transferred
                $deleteTemp->execute(['id' => $item['id']]);
            }

            $db->commit();

            $msg = "$transferredCount kursus baharu berjaya dipindahkan ke jadual courses!";
            if ($mergedCount > 0) {
                $msg .= " ($mergedCount rekod sudah sedia wujud dalam jadual rasmi dan diselaraskan).";
            }

            echo json_encode([
                'status' => 'success',
                'message' => $msg,
                'transferred_count' => $transferredCount,
                'merged_count' => $mergedCount
            ]);
            break;

        case 'suggest_skill':
            // Public endpoint for normal users to suggest a new skill.
            // Status is strictly saved as 'KIV'.
            $skill_name = trim($_POST['skill_name'] ?? '');
            $skill_type = trim($_POST['skill_type'] ?? '');
            $qualification_level = trim($_POST['qualification_level'] ?? 'Diploma');
            $skill_description = trim($_POST['skill_description'] ?? '');

            if (empty($skill_name) || !in_array($skill_type, ['technical', 'soft'])) {
                echo json_encode(['status' => 'error', 'message' => 'Sila masukkan nama kemahiran dan pilih jenis kemahiran yang sah.']);
                exit;
            }

            if (mb_strlen($skill_name) < 2) {
                echo json_encode(['status' => 'error', 'message' => 'Nama kemahiran mestilah sekurang-kurangnya 2 aksara.']);
                exit;
            }

            // Check if already in official skills table
            $chkOfficial = $db->prepare("SELECT id FROM skills WHERE LOWER(TRIM(skill_name)) = LOWER(:name) AND skill_type = :type");
            $chkOfficial->execute(['name' => $skill_name, 'type' => $skill_type]);
            if ($chkOfficial->fetch()) {
                $typeLabel = $skill_type === 'technical' ? 'Teknikal' : 'Insaniah';
                echo json_encode([
                    'status' => 'warning',
                    'message' => "Kemahiran \"$skill_name\" ($typeLabel) sudah pun wujud dalam senarai kemahiran rasmi."
                ]);
                exit;
            }

            // Check if already in temp_skills
            $chkTemp = $db->prepare("SELECT id, status FROM temp_skills WHERE LOWER(TRIM(skill_name)) = LOWER(:name) AND skill_type = :type");
            $chkTemp->execute(['name' => $skill_name, 'type' => $skill_type]);
            $existingTemp = $chkTemp->fetch();
            if ($existingTemp) {
                if ($existingTemp['status'] === 'Approve') {
                    echo json_encode([
                        'status' => 'info',
                        'message' => "Kemahiran \"$skill_name\" telah diluluskan oleh Pentadbir dan sedia untuk dikemaskini ke senarai utama."
                    ]);
                } else {
                    echo json_encode([
                        'status' => 'info',
                        'message' => "Kemahiran \"$skill_name\" telah dicadangkan sebelum ini dan kini dalam proses semakan (KIV) pihak Pentadbir."
                    ]);
                }
                exit;
            }

            // Insert into temp_skills with status 'KIV'
            $stmt = $db->prepare("INSERT INTO temp_skills (skill_name, skill_type, qualification_level, skill_description, status) VALUES (:name, :type, :qual, :desc, 'KIV')");
            $stmt->execute([
                'name' => $skill_name,
                'type' => $skill_type,
                'qual' => $qualification_level,
                'desc' => $skill_description
            ]);

            echo json_encode([
                'status' => 'success',
                'message' => 'Cadangan kemahiran berjaya dihantar kepada Pentadbir Sistem untuk semakan dan kelulusan. Terima kasih!'
            ]);
            break;

        case 'get_temp_skills_admin':
            requireLogin();
            $type_filter = trim($_GET['skill_type'] ?? 'all');
            $level_filter = trim($_GET['qualification_level'] ?? 'all');
            $status_filter = trim($_GET['status'] ?? 'all');
            $search = trim($_GET['search'] ?? '');

            $sql = "SELECT * FROM temp_skills WHERE 1=1";
            $params = [];

            if (!empty($type_filter) && $type_filter !== 'all') {
                $sql .= " AND skill_type = :type";
                $params['type'] = $type_filter;
            }
            if (!empty($level_filter) && $level_filter !== 'all') {
                $sql .= " AND qualification_level = :lvl";
                $params['lvl'] = $level_filter;
            }
            if (!empty($status_filter) && $status_filter !== 'all') {
                $sql .= " AND status = :st";
                $params['st'] = $status_filter;
            }
            if (!empty($search)) {
                $sql .= " AND (skill_name LIKE :search OR skill_description LIKE :search)";
                $params['search'] = '%' . $search . '%';
            }

            $sql .= " ORDER BY (status = 'KIV') DESC, created_at DESC";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll()]);
            break;

        case 'update_temp_skill_status':
            requireLogin();
            $id = (int)($_POST['id'] ?? 0);
            $status = trim($_POST['status'] ?? '');

            if ($id <= 0 || !in_array($status, ['KIV', 'Approve'])) {
                echo json_encode(['status' => 'error', 'message' => 'Status tidak sah. Nilai yang sah adalah "KIV" atau "Approve".']);
                exit;
            }

            $stmt = $db->prepare("UPDATE temp_skills SET status = :status WHERE id = :id");
            $stmt->execute(['status' => $status, 'id' => $id]);
            echo json_encode([
                'status' => 'success',
                'message' => "Status kemahiran berjaya ditukar kepada \"$status\"!"
            ]);
            break;

        case 'update_temp_skill':
            requireLogin();
            $id = (int)($_POST['id'] ?? 0);
            $skill_name = trim($_POST['skill_name'] ?? '');
            $skill_type = trim($_POST['skill_type'] ?? 'technical');
            $qualification_level = trim($_POST['qualification_level'] ?? 'Diploma');
            $skill_description = trim($_POST['skill_description'] ?? '');
            $status = trim($_POST['status'] ?? 'KIV');

            if ($id <= 0 || empty($skill_name) || !in_array($skill_type, ['technical', 'soft']) || !in_array($status, ['KIV', 'Approve'])) {
                echo json_encode(['status' => 'error', 'message' => 'Sila lengkapkan semua maklumat dan pastikan status sah.']);
                exit;
            }

            $stmt = $db->prepare("UPDATE temp_skills SET skill_name = :name, skill_type = :type, qualification_level = :lvl, skill_description = :desc, status = :st WHERE id = :id");
            $stmt->execute([
                'name' => $skill_name,
                'type' => $skill_type,
                'lvl'  => $qualification_level,
                'desc' => $skill_description,
                'st'   => $status,
                'id'   => $id
            ]);
            echo json_encode(['status' => 'success', 'message' => 'Maklumat cadangan kemahiran berjaya dikemaskini!']);
            break;

        case 'delete_temp_skill':
            requireLogin();
            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'ID tidak sah.']);
                exit;
            }
            $stmt = $db->prepare("DELETE FROM temp_skills WHERE id = :id");
            $stmt->execute(['id' => $id]);
            echo json_encode(['status' => 'success', 'message' => 'Rekod permohonan kemahiran berjaya dipadam!']);
            break;

        case 'transfer_temp_skills':
            requireLogin();
            // Function for button "Update Kemahiran" / bulk transfer
            // Transfers data from table "temp_skills" into "skills"
            $target_id = (int)($_POST['id'] ?? 0);

            if ($target_id > 0) {
                $stmt = $db->prepare("SELECT * FROM temp_skills WHERE id = :id AND status = 'Approve'");
                $stmt->execute(['id' => $target_id]);
                $items = $stmt->fetchAll();
            } else {
                $stmt = $db->query("SELECT * FROM temp_skills WHERE status = 'Approve' ORDER BY id ASC");
                $items = $stmt->fetchAll();
            }

            if (empty($items)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Tiada rekod kemahiran dengan status "Approve" untuk dipindahkan. Sila semak senarai dan ubah status kepada "Approve" terlebih dahulu sebelum menekan butang Update Kemahiran.'
                ]);
                exit;
            }

            $db->beginTransaction();
            $transferredCount = 0;
            $mergedCount = 0;

            $insertStmt = $db->prepare("INSERT INTO skills (skill_name, skill_type, qualification_level, created_at) VALUES (:name, :type, :lvl, :created_at)");
            $checkExisting = $db->prepare("SELECT id FROM skills WHERE LOWER(TRIM(skill_name)) = LOWER(:name) AND skill_type = :type");
            $deleteTemp = $db->prepare("DELETE FROM temp_skills WHERE id = :id");

            foreach ($items as $item) {
                $checkExisting->execute(['name' => trim($item['skill_name']), 'type' => $item['skill_type']]);
                $exists = $checkExisting->fetch();

                if (!$exists) {
                    $insertStmt->execute([
                        'name'       => trim($item['skill_name']),
                        'type'       => $item['skill_type'],
                        'lvl'        => $item['qualification_level'] ?? 'Diploma',
                        'created_at' => $item['created_at'] ?? date('Y-m-d H:i:s')
                    ]);
                    $transferredCount++;
                } else {
                    $mergedCount++;
                }

                $deleteTemp->execute(['id' => $item['id']]);
            }

            $db->commit();

            $msg = "$transferredCount kemahiran baharu berjaya dipindahkan ke jadual skills!";
            if ($mergedCount > 0) {
                $msg .= " ($mergedCount rekod sudah sedia wujud dalam jadual rasmi dan diselaraskan).";
            }

            echo json_encode([
                'status' => 'success',
                'message' => $msg,
                'transferred_count' => $transferredCount,
                'merged_count' => $mergedCount
            ]);
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Tindakan (action) tidak sah.']);
            break;
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Ralat Pangkalan Data: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Ralat: ' . $e->getMessage()]);
}
?>
