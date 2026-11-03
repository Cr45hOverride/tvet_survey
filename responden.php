<?php
/**
 * Respondent Submissions Record Page - Survey IPT System
 * Displays all survey submissions and provides Export to CSV functionality
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
    <title>Rekod Responden - Sistem Tinjauan IPT</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .container {
            width: 95% !important;
            max-width: 95% !important;
        }

        .page-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .search-box {
            position: relative;
            min-width: 280px;
        }

        .search-box input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: 12px;
            color: var(--text-main);
            font-size: 0.9rem;
            outline: none;
        }

        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-glow);
        }

        .btn-export {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            font-weight: 700;
            padding: 0.75rem 1.4rem;
            border-radius: 12px;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
            transition: all 0.3s ease;
        }

        .btn-export:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.5);
            color: #ffffff;
        }

        .btn-action-delete {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.4);
            color: #fca5a5;
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .btn-action-delete:hover {
            background: rgba(239, 68, 68, 0.85);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
            transform: translateY(-1px);
        }
    </style>
</head>

<body>

    <div class="container">
        <!-- Navigation Header -->
        <?php include __DIR__ . '/menu.php'; ?>

        <div id="adminAlert" class="alert-box"></div>

        <!-- Main Submissions Log Card -->
        <main class="glass-card">
            <h1 class="card-title">
                <i class="fa-solid fa-table-list" style="color: var(--primary-glow);"></i>
                Rekod Tinjauan Responden IPT
            </h1>
            <p class="card-subtitle">Senarai lengkap maklum balas tinjauan yang telah dihantar oleh pemohon.</p>

            <div class="page-actions">
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="submissionSearch" placeholder="Cari nama, negeri, institusi, atau kursus...">
                </div>

                <a href="api.php?action=export_csv" class="btn-export">
                    <i class="fa-solid fa-file-csv"></i> Eksport ke Fail CSV
                </a>
            </div>

            <div class="table-responsive">
                <table class="custom-table" id="submissionsTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Responden</th>
                            <th>E-mel Responden</th>
                            <th>Negeri</th>
                            <th>Institusi</th>
                            <th>Kursus Ditawarkan</th>
                            <th>Tahap Pengajian</th>
                            <th>Kemasukan Setahun</th>
                            <th>Kemahiran Teknikal</th>
                            <th>Kemahiran Insaniah</th>
                            <th>Masa Hantar</th>
                            <th style="text-align: center;">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody id="submissionsBody">
                        <tr>
                            <td colspan="12" style="text-align: center; color: var(--text-muted);">Memuatkan rekod
                                tinjauan...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>

        <footer>
            <p>&copy; <?php echo date('Y'); ?> Sistem Tinjauan IPT Malaysia | Respondent Submissions Record</p>
        </footer>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const submissionsBody = document.getElementById('submissionsBody');
            const submissionSearch = document.getElementById('submissionSearch');
            let rawSubmissionsData = [];

            loadSubmissions();

            function loadSubmissions() {
                fetch('api.php?action=get_submissions')
                    .then(r => r.json())
                    .then(res => {
                        if (res.status === 'success') {
                            rawSubmissionsData = res.data || [];
                            renderSubmissions(rawSubmissionsData);
                        }
                    })
                    .catch(() => {
                        submissionsBody.innerHTML = `<tr><td colspan="12" style="text-align:center; color:#fca5a5;">Gagal memuatkan rekod tinjauan.</td></tr>`;
                    });
            }

            function renderSubmissions(data) {
                if (data.length === 0) {
                    submissionsBody.innerHTML = `<tr><td colspan="12" style="text-align:center; color: var(--text-muted);">Tiada maklum balas tinjauan dijumpai.</td></tr>`;
                    return;
                }

                submissionsBody.innerHTML = data.map((row, idx) => {
                    const techTags = row.technical_skills
                        ? row.technical_skills.split(', ').map(s => `<span class="skill-tag tech">${escapeHtml(s)}</span>`).join('')
                        : '<small style="color:var(--text-muted);">-</small>';

                    const softTags = row.soft_skills
                        ? row.soft_skills.split(', ').map(s => `<span class="skill-tag soft">${escapeHtml(s)}</span>`).join('')
                        : '<small style="color:var(--text-muted);">-</small>';

                    const emailDisplay = row.respondent_email
                        ? `<a href="mailto:${escapeHtml(row.respondent_email)}" style="color:var(--primary-glow); text-decoration:none;"><i class="fa-regular fa-envelope"></i> ${escapeHtml(row.respondent_email)}</a>`
                        : '<small style="color:var(--text-muted);">-</small>';

                    const escapedName = escapeHtml(row.respondent_name).replace(/'/g, "\\'");

                    return `
                        <tr>
                            <td>${idx + 1}</td>
                            <td>
                                <strong>${escapeHtml(row.respondent_name)}</strong>
                                ${row.respondent_position ? `<br><small style="color:var(--text-muted); display:inline-flex; align-items:center; gap:0.25rem;"><i class="fa-solid fa-briefcase" style="font-size:0.75rem;"></i> ${escapeHtml(row.respondent_position)}</small>` : ''}
                            </td>
                            <td>${emailDisplay}</td>
                            <td>${escapeHtml(row.state_name)}</td>
                            <td>${escapeHtml(row.institute_name)}</td>
                            <td>${escapeHtml(row.course_name)}</td>
                            <td><span class="badge-step">${escapeHtml(row.qualification_level)}</span></td>
                            <td><strong>${row.annual_intake ? parseInt(row.annual_intake).toLocaleString() : 0}</strong></td>
                            <td>${techTags}</td>
                            <td>${softTags}</td>
                            <td><small>${row.submitted_at}</small></td>
                            <td style="text-align: center;">
                                <button class="btn-action-delete" onclick="deleteSubmission(${row.id}, '${escapedName}')">
                                    <i class="fa-solid fa-trash-can"></i> Padam
                                </button>
                            </td>
                        </tr>
                    `;
                }).join('');
            }

            // Live Search Filter
            if (submissionSearch) {
                submissionSearch.addEventListener('input', (e) => {
                    const query = e.target.value.toLowerCase().trim();
                    const filtered = rawSubmissionsData.filter(row => {
                        return (row.respondent_name && row.respondent_name.toLowerCase().includes(query)) ||
                            (row.respondent_position && row.respondent_position.toLowerCase().includes(query)) ||
                            (row.respondent_email && row.respondent_email.toLowerCase().includes(query)) ||
                            (row.state_name && row.state_name.toLowerCase().includes(query)) ||
                            (row.institute_name && row.institute_name.toLowerCase().includes(query)) ||
                            (row.course_name && row.course_name.toLowerCase().includes(query)) ||
                            (row.qualification_level && row.qualification_level.toLowerCase().includes(query)) ||
                            (row.annual_intake && row.annual_intake.toString().includes(query)) ||
                            (row.technical_skills && row.technical_skills.toLowerCase().includes(query)) ||
                            (row.soft_skills && row.soft_skills.toLowerCase().includes(query));
                    });
                    renderSubmissions(filtered);
                });
            }

            window.deleteSubmission = function(submissionId, name) {
                if (!confirm(`Adakah anda pasti mahu memadam rekod tinjauan untuk "${name}"?`)) {
                    return;
                }

                const formData = new FormData();
                formData.append('action', 'delete_submission');
                formData.append('submission_id', submissionId);

                fetch('api.php', {
                    method: 'POST',
                    body: formData
                })
                .then(r => r.json())
                .then(res => {
                    if (res.status === 'success') {
                        loadSubmissions();
                    } else {
                        alert(res.message || 'Gagal memadam rekod.');
                    }
                })
                .catch(() => alert('Ralat semasa memadam rekod.'));
            };

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
        });
    </script>
</body>

</html>