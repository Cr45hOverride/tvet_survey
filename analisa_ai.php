<?php
/**
 * Analisis Kesesuaian Kerjaya AI (Google Gemini API)
 * Menganalisis profil graduan & kemahiran teknikal/insaniah responden
 * terhadap jawatan dan syarikat sasaran dalam sektor perkilangan elektrik dan elektronik (E&E).
 */

require_once __DIR__ . '/config.php';

// Ambil konfigurasi API Key dan Model dari config.php
$configGeminiKey = defined('GEMINI_API_KEY') ? GEMINI_API_KEY : '';
$configGeminiModel = defined('GEMINI_MODEL') ? GEMINI_MODEL : 'gemini-3.6-flash';

// =========================================================================
// 1. AJAX BACKEND HANDLER (Proxy ke Google Gemini API)
// =========================================================================
if (isset($_GET['action']) && $_GET['action'] === 'generate_ai') {
    header('Content-Type: application/json; charset=utf-8');

    // Pastikan kaedah POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Kaedah permintaan tidak sah. Sila gunakan POST.']);
        exit;
    }

    $rawInput = (php_sapi_name() !== 'cli') ? file_get_contents('php://input') : '';
    $payloadData = !empty($rawInput) ? json_decode($rawInput, true) : $_POST;

    $namaInstitusi = trim($payloadData['nama_institusi'] ?? '');
    $namaKursus = trim($payloadData['nama_kursus'] ?? '');
    $tahapKelayakan = trim($payloadData['tahap_kelayakan'] ?? '');
    $kemahiranTeknikal = trim($payloadData['kemahiran_teknikal'] ?? '');
    $kemahiranInsaniah = trim($payloadData['kemahiran_insaniah'] ?? '');
    $namaJawatan = trim($payloadData['nama_jawatan'] ?? '');
    $namaSyarikat = trim($payloadData['nama_syarikat'] ?? '');

    // Tetapan API Key & Model: Utamakan nilai daripada config.php
    $apiKey = !empty($configGeminiKey) ? $configGeminiKey : trim($payloadData['api_key'] ?? '');
    $model = !empty($configGeminiModel) ? $configGeminiModel : trim($payloadData['model'] ?? 'gemini-3.6-flash');

    if (empty($apiKey)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gemini API Key tidak ditemui. Sila tetapkan pemalar GEMINI_API_KEY di dalam config.php atau masukkan melalui borang antaramuka.'
        ]);
        exit;
    }

    if (empty($namaJawatan)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Sila masukkan Nama Jawatan sasaran.'
        ]);
        exit;
    }

    if (empty($namaSyarikat)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Sila masukkan Nama Penuh Syarikat sasaran.'
        ]);
        exit;
    }

    // Bina Prompt mengikut spesifikasi tepat
    $prompt = "Anda adalah pengurus human recourses yang memunya i pengalam kerja selama 30 tahun dalam sektor perkilangan elektrik dan elektronik .\n\n"
        . "Saya adalah graduan Bagi institute " . ($namaInstitusi ?: '[Nama Institusi]')
        . " bidang kursus " . ($namaKursus ?: '[Nama Kursus]')
        . ", tahap " . ($tahapKelayakan ?: '[Tahap Kelayakan]')
        . ", dan mempunyai kemahiran teknikal " . ($kemahiranTeknikal ?: 'tiada kemahiran teknikal direkodkan')
        . " dan kemahiran insaniah: " . ($kemahiranInsaniah ?: 'tiada kemahiran insaniah direkodkan') . ". (maklumat yang di hantar)\n\n"
        . "Jika saya ingin bekerja di syarikat " . $namaSyarikat . ", sebagai " . $namaJawatan . " (dari maklumat yang di masukan)\n\n"
        . "Barikan pendapat anda apakah \n"
        . "Kemahiran Teknikal  dan Kemahiran Insaniah yang di perlukan oleh syarikat tersebut.\n\n"
        . "apakah kekurangan kemahiran skill dan Kemahiran Insaniah yang saya perlu ada untuk bekerja di syarikat tersebut.\n\n"
        . "Buat cadangan kepada saya apa yang harus lakukan untu saya mempunya peluang yang tinggi untuk bekerja di syarikat tersebut.\n\n"
        . "---\n"
        . "Panduan format jawapan:\n"
        . "1. Gunakan Bahasa Melayu yang profesional, bernada mentor/pengurus HR berpengalaman 30 tahun.\n"
        . "2. Susun menggunakan Markdown dengan tajuk kecil yang kemas (#, ##, ###), senarai bertitik, dan penegasan tebal (bold).\n"
        . "3. Berikan ulasan mendalam mengenai industri perkilangan E&E dan keperluan khusus syarikat {$namaSyarikat} bagi jawatan {$namaJawatan}.";

    $apiPayload = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ],
        'generationConfig' => [
            'temperature' => 0.7,
            'topK' => 40,
            'topP' => 0.95,
            'maxOutputTokens' => 4096
        ]
    ];

    // Fungsi pembantu cURL ke Gemini
    function callGeminiEndpoint($selectedModel, $key, $body)
    {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/" . urlencode($selectedModel) . ":generateContent?key=" . urlencode($key);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 75);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        return [$httpCode, $response, $curlError];
    }

    // Panggilan pertama dengan model pilihan dari config.php (cth: gemini-3.6-flash)
    list($httpCode, $response, $curlError) = callGeminiEndpoint($model, $apiKey, $apiPayload);

    // Sekiranya model 404 (model belum diaktifkan pada kunci tertentu), cuba model sandaran alternatif
    if ($httpCode === 404) {
        $fallbacks = ['gemini-2.5-flash', 'gemini-2.0-flash', 'gemini-1.5-flash'];
        foreach ($fallbacks as $fbModel) {
            if ($fbModel === $model)
                continue;
            list($fbHttpCode, $fbResponse, $fbCurlError) = callGeminiEndpoint($fbModel, $apiKey, $apiPayload);
            if ($fbHttpCode === 200) {
                $httpCode = $fbHttpCode;
                $response = $fbResponse;
                $curlError = $fbCurlError;
                $model = $fbModel;
                break;
            }
        }
    }

    if ($httpCode !== 200 || empty($response)) {
        $errorMsg = 'Ralat sambungan ke Google Gemini API (HTTP ' . $httpCode . ')';
        if (!empty($curlError)) {
            $errorMsg .= ': ' . $curlError;
        } elseif (!empty($response)) {
            $errJson = json_decode($response, true);
            if (!empty($errJson['error']['message'])) {
                $errorMsg = 'Gemini API Error: ' . $errJson['error']['message'];
            }
        }
        echo json_encode([
            'status' => 'error',
            'message' => $errorMsg,
            'http_code' => $httpCode,
            'raw' => $response
        ]);
        exit;
    }

    $resJson = json_decode($response, true);
    $generatedText = $resJson['candidates'][0]['content']['parts'][0]['text'] ?? '';

    if (empty($generatedText)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gemini API tidak mengembalikan sebarang teks ulasan. Sila cuba sebentar lagi.',
            'raw' => $resJson
        ]);
        exit;
    }

    echo json_encode([
        'status' => 'success',
        'report' => $generatedText,
        'model' => $model,
        'prompt' => $prompt
    ]);
    exit;
}

// =========================================================================
// 2. AMBIL VARIABLE YANG DIHANTAR (POST / GET)
// =========================================================================
$namaInstitusi = $_POST['nama_institusi'] ?? $_GET['nama_institusi'] ?? '';
$namaKursus = $_POST['nama_kursus'] ?? $_GET['nama_kursus'] ?? '';
$tahapKelayakan = $_POST['tahap_kelayakan'] ?? $_GET['tahap_kelayakan'] ?? '';
$kemahiranTeknikal = $_POST['kemahiran_teknikal'] ?? $_GET['kemahiran_teknikal'] ?? '';
$kemahiranInsaniah = $_POST['kemahiran_insaniah'] ?? $_GET['kemahiran_insaniah'] ?? '';
$namaResponden = $_POST['nama_responden'] ?? $_GET['nama_responden'] ?? '';

// Pisahkan kemahiran kepada array untuk paparan tag kemas
$arrTech = [];
if (!empty($kemahiranTeknikal) && $kemahiranTeknikal !== 'Tiada kemahiran teknikal direkodkan') {
    $arrTech = array_filter(array_map('trim', explode(',', $kemahiranTeknikal)));
}

$arrSoft = [];
if (!empty($kemahiranInsaniah) && $kemahiranInsaniah !== 'Tiada kemahiran insaniah direkodkan') {
    $arrSoft = array_filter(array_map('trim', explode(',', $kemahiranInsaniah)));
}

$pageBrandIcon = 'fa-solid fa-wand-magic-sparkles';
$pageBrandTitle = 'Analisis Kesesuaian Kerjaya AI (Gemini)';
?>
<!DOCTYPE html>
<html lang="ms">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analisis Kesesuaian Kerjaya AI (Google Gemini) | Sistem Tinjauan IPT</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Marked.js untuk render Markdown ke HTML yang cantik -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

    <style>
        .container {
            width: 95% !important;
            max-width: 1100px !important;
        }

        /* Hero Header Banner */
        .ai-hero-card {
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.85) 0%, rgba(15, 23, 42, 0.95) 100%);
            border: 1px solid rgba(168, 85, 247, 0.35);
            border-radius: 20px;
            padding: 1.75rem 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
        }

        .ai-hero-card::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 180px;
            height: 180px;
            background: radial-gradient(circle, rgba(168, 85, 247, 0.25) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        /* Profile Details Grid */
        .profile-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.25rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .profile-item {
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
            background: rgba(15, 23, 42, 0.5);
            padding: 0.85rem 1.1rem;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .profile-label {
            font-size: 0.78rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .profile-val {
            font-size: 1rem;
            color: #f8fafc;
            font-weight: 700;
        }

        /* Skill Chips */
        .skills-display-box {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 1.25rem;
        }

        .skill-column {
            background: rgba(15, 23, 42, 0.4);
            border-radius: 14px;
            padding: 1rem 1.2rem;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .skill-column-title {
            font-size: 0.88rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .skill-column-title.tech {
            color: #38bdf8;
        }

        .skill-column-title.soft {
            color: #c084fc;
        }

        .skill-tag {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.35rem 0.75rem;
            border-radius: 8px;
            font-size: 0.82rem;
            font-weight: 600;
            margin: 0.2rem;
        }

        .skill-tag.tech {
            background: rgba(56, 189, 248, 0.15);
            color: #7dd3fc;
            border: 1px solid rgba(56, 189, 248, 0.3);
        }

        .skill-tag.soft {
            background: rgba(168, 85, 247, 0.15);
            color: #d8b4fe;
            border: 1px solid rgba(168, 85, 247, 0.3);
        }

        /* AI Form Styles */
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.25rem;
            margin-bottom: 1.25rem;
        }

        .btn-ai-generate {
            width: 100%;
            padding: 1.1rem;
            background: linear-gradient(135deg, #a855f7 0%, #3b82f6 100%);
            color: #ffffff;
            border: none;
            border-radius: 14px;
            font-size: 1.1rem;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.65rem;
            box-shadow: 0 4px 25px rgba(168, 85, 247, 0.4);
            letter-spacing: 0.02em;
        }

        .btn-ai-generate:hover:not(:disabled) {
            background: linear-gradient(135deg, #9333ea 0%, #2563eb 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(168, 85, 247, 0.6);
        }

        .btn-ai-generate:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* Result Report Card */
        .report-card {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.9) 0%, rgba(30, 41, 59, 0.95) 100%);
            border: 1px solid rgba(168, 85, 247, 0.4);
            border-radius: 20px;
            padding: 2.25rem;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.4);
            margin-top: 2rem;
            display: none;
            animation: fadeIn 0.4s ease;
            position: relative;
        }

        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            padding-bottom: 1.25rem;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .report-title-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 0.9rem;
            background: rgba(168, 85, 247, 0.15);
            color: #d8b4fe;
            border: 1px solid rgba(168, 85, 247, 0.35);
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .report-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .btn-action-sm {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #f8fafc;
            padding: 0.45rem 0.9rem;
            border-radius: 8px;
            font-size: 0.82rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .btn-action-sm:hover {
            background: rgba(255, 255, 255, 0.18);
            border-color: rgba(255, 255, 255, 0.3);
            color: #ffffff;
            transform: translateY(-1px);
        }

        /* Typography for AI Markdown Output */
        .markdown-output {
            color: #e2e8f0;
            line-height: 1.8;
            font-size: 1rem;
        }

        .markdown-output h1,
        .markdown-output h2,
        .markdown-output h3,
        .markdown-output h4 {
            color: #f8fafc;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
            font-weight: 700;
            line-height: 1.35;
        }

        .markdown-output h1 {
            font-size: 1.45rem;
            color: #38bdf8;
            border-bottom: 1px solid rgba(56, 189, 248, 0.25);
            padding-bottom: 0.4rem;
        }

        .markdown-output h2 {
            font-size: 1.25rem;
            color: #c084fc;
        }

        .markdown-output h3 {
            font-size: 1.1rem;
            color: #93c5fd;
        }

        .markdown-output p {
            margin-bottom: 1rem;
        }

        .markdown-output ul,
        .markdown-output ol {
            margin-left: 1.5rem;
            margin-bottom: 1.25rem;
        }

        .markdown-output li {
            margin-bottom: 0.45rem;
        }

        .markdown-output strong {
            color: #ffffff;
            font-weight: 700;
        }

        .markdown-output blockquote {
            background: rgba(56, 189, 248, 0.08);
            border-left: 4px solid #38bdf8;
            padding: 0.85rem 1.25rem;
            margin: 1.25rem 0;
            border-radius: 0 10px 10px 0;
            color: #cbd5e1;
            font-style: italic;
        }

        .markdown-output table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.25rem 0;
            font-size: 0.9rem;
        }

        .markdown-output th,
        .markdown-output td {
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 0.65rem 0.9rem;
            text-align: left;
        }

        .markdown-output th {
            background: rgba(168, 85, 247, 0.15);
            color: #f8fafc;
        }

        /* Loading Animation Box */
        .ai-loading-box {
            display: none;
            padding: 3rem 2rem;
            text-align: center;
            background: rgba(15, 23, 42, 0.7);
            border-radius: 20px;
            border: 1px solid rgba(168, 85, 247, 0.3);
            margin-top: 2rem;
        }

        .ai-pulse-circle {
            width: 70px;
            height: 70px;
            margin: 0 auto 1.5rem auto;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(168, 85, 247, 0.3), rgba(56, 189, 248, 0.3));
            border: 2px solid rgba(168, 85, 247, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #c084fc;
            animation: pulseGlow 1.8s infinite;
        }

        @keyframes pulseGlow {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(168, 85, 247, 0.6);
            }

            70% {
                transform: scale(1.05);
                box-shadow: 0 0 0 20px rgba(168, 85, 247, 0);
            }

            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(168, 85, 247, 0);
            }
        }

        /* Prompt Accordion Toggle */
        .prompt-preview-wrap {
            margin-top: 1rem;
            background: rgba(15, 23, 42, 0.45);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            overflow: hidden;
        }

        .prompt-preview-header {
            padding: 0.75rem 1rem;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 600;
        }

        .prompt-preview-header:hover {
            color: #f8fafc;
            background: rgba(255, 255, 255, 0.03);
        }

        .prompt-preview-body {
            display: none;
            padding: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(10, 15, 29, 0.6);
            font-family: monospace;
            font-size: 0.82rem;
            color: #94a3b8;
            white-space: pre-wrap;
            line-height: 1.6;
            max-height: 250px;
            overflow-y: auto;
        }

        @media print {

            .navbar,
            .ai-hero-card,
            .glass-card:not(.report-card),
            .btn-action-sm,
            footer {
                display: none !important;
            }

            body {
                background: #ffffff !important;
                color: #000000 !important;
            }

            .report-card {
                display: block !important;
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                color: #000000 !important;
            }

            .markdown-output {
                color: #000000 !important;
            }

            .markdown-output h1,
            .markdown-output h2,
            .markdown-output h3 {
                color: #000000 !important;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Navigation Header (Menu Bersama) -->
        <?php include __DIR__ . '/menu.php'; ?>

        <!-- Navigasi Pintar: Kembali ke Modul Analisis Jurang -->
        <div
            style="margin-bottom: 1.25rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
            <a href="industry_framework.php" class="nav-btn" style="text-decoration: none;">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Pemetaan Industri (IndSF)
            </a>
            <span class="badge-step"
                style="background: rgba(168, 85, 247, 0.15); color: #c084fc; border-color: rgba(168, 85, 247, 0.35);">
                <i class="fa-solid fa-robot"></i> Modul Analisis AI Eksekutif HR
            </span>
        </div>

        <!-- HERO SECTION: MAKLUMAT GRADUAN & KEMAHIRAN YANG DIHANTAR -->
        <div class="ai-hero-card">
            <div
                style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem;">
                <div style="display: flex; align-items: center; gap: 0.9rem;">
                    <div
                        style="width: 52px; height: 52px; border-radius: 14px; background: linear-gradient(135deg, rgba(168, 85, 247, 0.25), rgba(56, 189, 248, 0.25)); border: 1px solid rgba(168, 85, 247, 0.4); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: #c084fc;">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <div>
                        <div
                            style="font-size: 0.78rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700;">
                            Profil Graduan / Responden
                        </div>
                        <h2 style="font-size: 1.4rem; font-weight: 800; color: #f8fafc; margin: 0;">
                            <?php echo htmlspecialchars($namaResponden ?: ($namaKursus ?: 'Graduan Tanpa Nama')); ?>
                        </h2>
                    </div>
                </div>
                <div>
                    <span class="match-score-badge high" style="font-size: 0.82rem; padding: 0.35rem 0.85rem;">
                        <i class="fa-solid fa-circle-check"></i> Data Sedia Untuk Analisis AI
                    </span>
                </div>
            </div>

            <!-- Profile Metadata Grid -->
            <div class="profile-grid">
                <div class="profile-item">
                    <span class="profile-label"><i class="fa-solid fa-building-columns"></i> Nama Institusi</span>
                    <span class="profile-val"
                        id="dispInstitusi"><?php echo htmlspecialchars($namaInstitusi ?: 'Belum dinyatakan'); ?></span>
                </div>
                <div class="profile-item">
                    <span class="profile-label"><i class="fa-solid fa-book-open"></i> Nama Kursus</span>
                    <span class="profile-val"
                        id="dispKursus"><?php echo htmlspecialchars($namaKursus ?: 'Belum dinyatakan'); ?></span>
                </div>
                <div class="profile-item">
                    <span class="profile-label"><i class="fa-solid fa-certificate"></i> Tahap Kelayakan</span>
                    <span class="profile-val"
                        id="dispTahap"><?php echo htmlspecialchars($tahapKelayakan ?: 'Belum dinyatakan'); ?></span>
                </div>
                <?php if (!empty($namaResponden)): ?>
                    <div class="profile-item">
                        <span class="profile-label"><i class="fa-solid fa-user-tag"></i> Nama Penuh Responden</span>
                        <span class="profile-val"><?php echo htmlspecialchars($namaResponden); ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Skills Display Box -->
            <div class="skills-display-box">
                <!-- Technical Skills -->
                <div class="skill-column">
                    <div class="skill-column-title tech">
                        <i class="fa-solid fa-microchip"></i>
                        <span>Kemahiran Teknikal yang Dimiliki (<?php echo count($arrTech); ?>)</span>
                    </div>
                    <div id="wrapperTechChips">
                        <?php if (!empty($arrTech)): ?>
                            <?php foreach ($arrTech as $t): ?>
                                <span class="skill-tag tech"><i class="fa-solid fa-wrench"></i>
                                    <?php echo htmlspecialchars($t); ?></span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <small
                                style="color: var(--text-muted);"><?php echo htmlspecialchars($kemahiranTeknikal ?: 'Tiada kemahiran teknikal direkodkan'); ?></small>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Soft Skills -->
                <div class="skill-column">
                    <div class="skill-column-title soft">
                        <i class="fa-solid fa-users-gear"></i>
                        <span>Kemahiran Insaniah yang Dimiliki (<?php echo count($arrSoft); ?>)</span>
                    </div>
                    <div id="wrapperSoftChips">
                        <?php if (!empty($arrSoft)): ?>
                            <?php foreach ($arrSoft as $s): ?>
                                <span class="skill-tag soft"><i class="fa-solid fa-brain"></i>
                                    <?php echo htmlspecialchars($s); ?></span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <small
                                style="color: var(--text-muted);"><?php echo htmlspecialchars($kemahiranInsaniah ?: 'Tiada kemahiran insaniah direkodkan'); ?></small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- FORM INPUT: SASARAN PEKERJAAN & KONFIGURASI AI -->
        <main class="glass-card" style="border-color: rgba(168, 85, 247, 0.35); padding: 2rem 2.25rem;">
            <div
                style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 0.5rem;">
                <h2 class="card-title"
                    style="margin: 0; display: flex; align-items: center; gap: 0.65rem; font-size: 1.45rem;">
                    <i class="fa-solid fa-briefcase" style="color: #c084fc;"></i>
                    <span>Maklumat Sasaran Pekerjaan & Konfigurasi AI</span>
                </h2>
            </div>
            <p class="card-subtitle" style="margin-bottom: 1.5rem;">
                Sila masukkan jawatan yang disasarkan dan nama syarikat yang ingin dipohon untuk dinilai oleh Pengurus
                HR maya (30 tahun pengalaman sektor elektrik & elektronik).
            </p>

            <!-- Alert Box -->
            <div id="alertBox" class="alert-box"></div>

            <form id="formAiAnalysis" onsubmit="event.preventDefault(); triggerAiGeneration();">
                <!-- Hidden Data Passed From industry_framework.php -->
                <input type="hidden" id="inputNamaInstitusi" value="<?php echo htmlspecialchars($namaInstitusi); ?>">
                <input type="hidden" id="inputNamaKursus" value="<?php echo htmlspecialchars($namaKursus); ?>">
                <input type="hidden" id="inputTahapKelayakan" value="<?php echo htmlspecialchars($tahapKelayakan); ?>">
                <input type="hidden" id="inputKemahiranTeknikal"
                    value="<?php echo htmlspecialchars($kemahiranTeknikal); ?>">
                <input type="hidden" id="inputKemahiranInsaniah"
                    value="<?php echo htmlspecialchars($kemahiranInsaniah); ?>">
                <input type="hidden" id="inputNamaResponden" value="<?php echo htmlspecialchars($namaResponden); ?>">

                <!-- Row 1: Nama Jawatan & Nama Syarikat (Wajib) -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="nama_jawatan" class="form-label">
                            <span><i class="fa-solid fa-user-tie" style="color:#38bdf8;"></i> Nama Jawatan <span
                                    style="color:#ef4444;">*</span></span>
                            <span class="badge-step">Input Wajib</span>
                        </label>
                        <input type="text" id="nama_jawatan" name="nama_jawatan" class="custom-input"
                            placeholder="cth: Jurutera Ujian (Test Engineer) / Juruteknik SMT" required
                            autocomplete="off">
                        <small style="color: var(--text-muted); font-size: 0.78rem;">
                            Jawatan yang ingin dimohon atau dinilai kesesuaian kemahirannya.
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="nama_syarikat" class="form-label">
                            <span><i class="fa-solid fa-building" style="color:#c084fc;"></i> Nama Penuh Syarikat <span
                                    style="color:#ef4444;">*</span></span>
                            <span class="badge-step">Input Wajib</span>
                        </label>
                        <input type="text" id="nama_syarikat" name="nama_syarikat" class="custom-input"
                            placeholder="cth: Inari Technology Sdn Bhd / Intel Corporation / TF-AMD" required
                            autocomplete="off">
                        <small style="color: var(--text-muted); font-size: 0.78rem;">
                            Syarikat perkilangan elektrik & elektronik (E&E) atau sektor pembuatan berkaitan.
                        </small>
                    </div>
                </div>

                <!-- Prompt Live Preview Accordion -->
                <div class="prompt-preview-wrap">
                    <div class="prompt-preview-header" onclick="togglePromptPreview()">
                        <span><i class="fa-solid fa-terminal"></i> Papar Format Prompt Eksekutif HR</span>
                        <i class="fa-solid fa-chevron-down" id="promptChevron"></i>
                    </div>
                    <div class="prompt-preview-body" id="promptPreviewText">
                        Memuatkan pratonton prompt...
                    </div>
                </div>

                <!-- Butang Tindakan: Jana Laporan AI -->
                <button type="submit" id="btnJanaLaporan" class="btn-ai-generate">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                    <span>Jana Laporan AI</span>
                </button>
            </form>
        </main>

        <!-- LOADING INDICATOR BOX -->
        <div id="aiLoadingBox" class="ai-loading-box">
            <div class="ai-pulse-circle">
                <i class="fa-solid fa-brain"></i>
            </div>
            <h3 style="font-size: 1.35rem; color: #f8fafc; margin-bottom: 0.5rem; font-weight: 700;">
                Menghubungi AI Model ...
            </h3>
            <p style="color: var(--text-muted); font-size: 0.95rem; max-width: 600px; margin: 0 auto 1.5rem auto;">
                Pengurus HR Maya sedang menilai kesesuaian kemahiran teknikal dan insaniah responden bagi jawatan
                <strong id="loadingJobText" style="color:#38bdf8;">-</strong> di <strong id="loadingCompanyText"
                    style="color:#c084fc;">-</strong>.
            </p>
            <div
                style="display: inline-flex; align-items: center; gap: 0.5rem; color: #a855f7; font-size: 0.85rem; font-weight: 600;">
                <i class="fa-solid fa-spinner fa-spin"></i> Memproses sintesis jurang kemahiran & cadangan intervensi...
            </div>
        </div>

        <!-- AI REPORT RESULT CONTAINER -->
        <section id="aiReportCard" class="report-card">
            <div class="report-header">
                <div>
                    <div class="report-title-badge">
                        <i class="fa-solid fa-stamp"></i> Laporan Penilaian Pengurus Sumber Manusia (HR)
                    </div>
                    <h2 id="reportTargetTitle"
                        style="font-size: 1.5rem; font-weight: 800; color: #f8fafc; margin-top: 0.5rem; margin-bottom: 0.2rem;">
                        Penilaian Kesesuaian Kerjaya
                    </h2>
                    <div style="font-size: 0.84rem; color: var(--text-muted);">
                        Sektor: <strong style="color:#e2e8f0;">Perkilangan Elektrik & Elektronik (E&E)</strong> &bull;
                        Model: <span id="reportModelBadge" style="color:#38bdf8;">Gemini 1.5 Flash</span> &bull;
                        Tarikh: <span id="reportTimestamp" style="color:#cbd5e1;">-</span>
                    </div>
                </div>

                <div class="report-actions">
                    <button type="button" class="btn-action-sm" onclick="copyReportText()">
                        <i class="fa-solid fa-copy" id="iconCopy"></i> <span id="btnCopyText">Salin Teks</span>
                    </button>
                    <button type="button" class="btn-action-sm" onclick="window.print()">
                        <i class="fa-solid fa-print"></i> Cetak Laporan
                    </button>
                    <button type="button" class="btn-action-sm" onclick="triggerAiGeneration()">
                        <i class="fa-solid fa-arrows-rotate"></i> Jana Semula
                    </button>
                </div>
            </div>

            <!-- Formatted Markdown Content -->
            <div id="aiMarkdownContent" class="markdown-output">
                <!-- AI Generated Text Injected Here -->
            </div>
        </section>

        <footer
            style="text-align: center; margin-top: 3rem; margin-bottom: 1.5rem; color: var(--text-muted); font-size: 0.85rem;">
            <p>&copy; <?php echo date('Y'); ?> Sistem Tinjauan IPT Malaysia | Dikuasakan oleh Google Gemini AI &
                Pemetaan IndSF</p>
        </footer>
    </div>

    <!-- Client-Side Logic & API Orchestration -->
    <script>
        const configGeminiKey = <?php echo json_encode($configGeminiKey); ?>;
        const configGeminiModel = <?php echo json_encode($configGeminiModel); ?>;

        // Helper untuk parse Markdown asas sekiranya marked.js tidak dimuatkan
        function fallbackMarkdownParse(text) {
            if (!text) return '';
            let html = text
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');

            // Headers
            html = html.replace(/^### (.*$)/gim, '<h3>$1</h3>');
            html = html.replace(/^## (.*$)/gim, '<h2>$1</h2>');
            html = html.replace(/^# (.*$)/gim, '<h1>$1</h1>');

            // Bold & Italic
            html = html.replace(/\*\*(.*?)\*\*/gim, '<strong>$1</strong>');
            html = html.replace(/\*(.*?)\*/gim, '<em>$1</em>');

            // Blockquotes
            html = html.replace(/^\> (.*$)/gim, '<blockquote>$1</blockquote>');

            // Unordered list items
            html = html.replace(/^\s*[\-\*]\s+(.*$)/gim, '<li>$1</li>');
            html = html.replace(/(<li>.*<\/li>)/gims, '<ul>$1</ul>');

            // Paragraphs
            html = html.split(/\n\s*\n/).map(p => {
                p = p.trim();
                if (!p) return '';
                if (p.startsWith('<h') || p.startsWith('<ul') || p.startsWith('<ol') || p.startsWith('<blockquote')) {
                    return p;
                }
                return `<p>${p.replace(/\n/g, '<br>')}</p>`;
            }).join('');

            return html;
        }

        // Render Markdown selamat
        function renderMarkdown(md) {
            if (typeof marked !== 'undefined' && typeof marked.parse === 'function') {
                return marked.parse(md);
            }
            return fallbackMarkdownParse(md);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const inputJawatan = document.getElementById('nama_jawatan');
            const inputSyarikat = document.getElementById('nama_syarikat');

            inputJawatan.addEventListener('input', updatePromptPreview);
            inputSyarikat.addEventListener('input', updatePromptPreview);

            updatePromptPreview();
        });

        // Toggle Prompt Accordion
        function togglePromptPreview() {
            const body = document.getElementById('promptPreviewText');
            const chev = document.getElementById('promptChevron');
            if (body.style.display === 'block') {
                body.style.display = 'none';
                chev.classList.remove('fa-chevron-up');
                chev.classList.add('fa-chevron-down');
            } else {
                body.style.display = 'block';
                chev.classList.remove('fa-chevron-down');
                chev.classList.add('fa-chevron-up');
            }
        }

        // Kemas kini Pratonton Teks Prompt
        function updatePromptPreview() {
            const inst = document.getElementById('inputNamaInstitusi').value.trim() || '[Nama Institusi]';
            const kurs = document.getElementById('inputNamaKursus').value.trim() || '[Nama Kursus]';
            const tahap = document.getElementById('inputTahapKelayakan').value.trim() || '[Tahap Kelayakan]';
            const tech = document.getElementById('inputKemahiranTeknikal').value.trim() || 'tiada kemahiran teknikal direkodkan';
            const soft = document.getElementById('inputKemahiranInsaniah').value.trim() || 'tiada kemahiran insaniah direkodkan';
            const jawatan = document.getElementById('nama_jawatan').value.trim() || '[Nama Jawatan]';
            const syarikat = document.getElementById('nama_syarikat').value.trim() || '[Nama Penuh Syarikat]';

            const previewText = `Anda adalah pengurus human recourses yang memunya i pengalam kerja selama 30 tahun dalam sektor perkilangan elektrik dan elektronik . 

Saya adalah graduan Bagi institute ${inst} bidang kursus ${kurs}, tahap ${tahap}, dan mempunyai kemahiran teknikal ${tech} dan kemahiran insaniah: ${soft}. (maklumat yang di hantar)

Jika saya ingin bekerja di syarikat ${syarikat}, sebagai ${jawatan} (dari maklumat yang di masukan)

Barikan pendapat anda apakah 
Kemahiran Teknikal  dan Kemahiran Insaniah yang di perlukan oleh syarikat tersebut.

apakah kekurangan kemahiran skill dan Kemahiran Insaniah yang saya perlu ada untuk bekerja di syarikat tersebut.

Buat cadangan kepada saya apa yang harus lakukan untu saya mempunya peluang yang tinggi untuk bekerja di syarikat tersebut.`;

            document.getElementById('promptPreviewText').textContent = previewText;
            return previewText;
        }

        function showAlert(msg, type = 'error') {
            const alertBox = document.getElementById('alertBox');
            alertBox.className = `alert-box ${type}`;
            alertBox.style.display = 'block';
            alertBox.innerHTML = `<i class="fa-solid fa-circle-exclamation"></i> ${msg}`;
            alertBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function hideAlert() {
            const alertBox = document.getElementById('alertBox');
            alertBox.style.display = 'none';
        }

        // Panggilan API Utama (Backend Proxy dengan sokongan fallback client-side)
        async function triggerAiGeneration() {
            hideAlert();

            const namaJawatan = document.getElementById('nama_jawatan').value.trim();
            const namaSyarikat = document.getElementById('nama_syarikat').value.trim();
            const apiKey = (configGeminiKey || '').trim();
            const selectedModel = configGeminiModel || 'gemini-3.6-flash';

            if (!namaJawatan) {
                showAlert('Sila masukkan Nama Jawatan sasaran.');
                document.getElementById('nama_jawatan').focus();
                return;
            }

            if (!namaSyarikat) {
                showAlert('Sila masukkan Nama Penuh Syarikat sasaran.');
                document.getElementById('nama_syarikat').focus();
                return;
            }

            if (!apiKey) {
                showAlert('Gemini API Key belum ditetapkan. Sila masukkan kunci API anda di dalam fail <code>config.php</code> pada pemalar <code>GEMINI_API_KEY</code>.');
                return;
            }

            // Update UI State: Loading
            const btnSubmit = document.getElementById('btnJanaLaporan');
            const loadingBox = document.getElementById('aiLoadingBox');
            const reportCard = document.getElementById('aiReportCard');

            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menjana Laporan AI...';

            document.getElementById('loadingJobText').textContent = namaJawatan;
            document.getElementById('loadingCompanyText').textContent = namaSyarikat;
            loadingBox.style.display = 'block';
            reportCard.style.display = 'none';
            loadingBox.scrollIntoView({ behavior: 'smooth', block: 'start' });

            const payload = {
                nama_institusi: document.getElementById('inputNamaInstitusi').value.trim(),
                nama_kursus: document.getElementById('inputNamaKursus').value.trim(),
                tahap_kelayakan: document.getElementById('inputTahapKelayakan').value.trim(),
                kemahiran_teknikal: document.getElementById('inputKemahiranTeknikal').value.trim(),
                kemahiran_insaniah: document.getElementById('inputKemahiranInsaniah').value.trim(),
                nama_responden: document.getElementById('inputNamaResponden').value.trim(),
                nama_jawatan: namaJawatan,
                nama_syarikat: namaSyarikat,
                api_key: apiKey,
                model: selectedModel
            };

            let generatedReport = '';
            let modelUsed = selectedModel;

            try {
                // 1. Cuba melalui backend PHP proxy terlebih dahulu
                const response = await fetch('analisa_ai.php?action=generate_ai', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (data.status === 'success' && data.report) {
                    generatedReport = data.report;
                    modelUsed = data.model || selectedModel;
                } else {
                    // Sekiranya ada mesej ralat daripada backend, periksa jika boleh cuba client-side terus
                    throw new Error(data.message || 'Ralat tidak diketahui daripada pelayan.');
                }
            } catch (err) {
                console.warn('Backend proxy encounter:', err.message, '- Mencuba sambungan terus dari pelayar...');

                // 2. Sandaran terus melalui pelayar (Direct Client-Side Fetch ke Google Gemini API)
                try {
                    const clientPrompt = updatePromptPreview();
                    const directUrl = `https://generativelanguage.googleapis.com/v1beta/models/${encodeURIComponent(selectedModel)}:generateContent?key=${encodeURIComponent(apiKey)}`;

                    const directRes = await fetch(directUrl, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            contents: [{ parts: [{ text: clientPrompt }] }],
                            generationConfig: {
                                temperature: 0.7,
                                topK: 40,
                                topP: 0.95,
                                maxOutputTokens: 4096
                            }
                        })
                    });

                    const directData = await directRes.json();

                    if (directData.candidates && directData.candidates[0]?.content?.parts[0]?.text) {
                        generatedReport = directData.candidates[0].content.parts[0].text;
                        modelUsed = selectedModel;
                    } else if (directData.error) {
                        throw new Error(`Google API: ${directData.error.message || 'Ralat pengesahan API Key'}`);
                    } else {
                        throw new Error(err.message);
                    }
                } catch (fallbackErr) {
                    loadingBox.style.display = 'none';
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = '<i class="fa-solid fa-wand-magic-sparkles"></i> Jana Laporan AI';
                    showAlert(`Gagal menjana laporan AI: ${fallbackErr.message}`);
                    return;
                }
            }

            // Papar Laporan yang Berjaya Dijana
            loadingBox.style.display = 'none';
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="fa-solid fa-wand-magic-sparkles"></i> Jana Semula Laporan';

            document.getElementById('reportTargetTitle').textContent = `Penilaian Kesesuaian: ${namaJawatan} di ${namaSyarikat}`;
            const modelLabel = (modelUsed === 'gemini-3.6-flash') ? 'Google Gemini 3.6 Flash' : modelUsed;
            document.getElementById('reportModelBadge').textContent = `${modelLabel} (${modelUsed})`;
            document.getElementById('reportTimestamp').textContent = new Date().toLocaleString('ms-MY', {
                dateStyle: 'medium',
                timeStyle: 'short'
            });

            // Render Markdown ke HTML
            document.getElementById('aiMarkdownContent').innerHTML = renderMarkdown(generatedReport);
            window._lastRawReport = generatedReport;

            reportCard.style.display = 'block';
            reportCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        // Salin Teks Laporan ke Papan Keratan (Clipboard)
        function copyReportText() {
            const raw = window._lastRawReport || document.getElementById('aiMarkdownContent').innerText;
            if (!raw) return;

            navigator.clipboard.writeText(raw).then(() => {
                const icon = document.getElementById('iconCopy');
                const btnText = document.getElementById('btnCopyText');
                icon.className = 'fa-solid fa-check';
                btnText.textContent = 'Disalin!';
                setTimeout(() => {
                    icon.className = 'fa-solid fa-copy';
                    btnText.textContent = 'Salin Teks';
                }, 2500);
            }).catch(err => {
                alert('Gagal menyalin teks: ' + err);
            });
        }
    </script>
</body>

</html>