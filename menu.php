<?php
/**
 * Shared Navigation Menu Component - Sistem Tinjauan IPT
 * Included by admin.php, industry_framework.php, skills.php, and responden.php
 */

require_once __DIR__ . '/config.php';

// Detect current active script name
$currentScript = basename($_SERVER['PHP_SELF'] ?? '');

// Resolve current user if logged in
$menuUser = null;
if (function_exists('isLoggedIn') && isLoggedIn()) {
    $menuUser = function_exists('getCurrentUser') ? getCurrentUser() : null;
}

// Brand mapping based on active page (can be overridden by $pageBrandTitle and $pageBrandIcon)
$brandConfig = [
    'admin.php' => [
        'icon'  => 'fa-solid fa-sliders',
        'title' => 'Panel Pengurusan Data'
    ],
    'responden.php' => [
        'icon'  => 'fa-solid fa-users-rectangle',
        'title' => 'Rekod Tinjauan Responden'
    ],
    'skills.php' => [
        'icon'  => 'fa-solid fa-brain',
        'title' => 'Pengurusan Kemahiran'
    ],
    'industry_framework.php' => [
        'icon'  => 'fa-solid fa-industry',
        'title' => 'Pemetaan Kemahiran & Analisis Jurang (IndSF)'
    ]
];

$activeBrandIcon = $pageBrandIcon ?? ($brandConfig[$currentScript]['icon'] ?? 'fa-solid fa-sliders');
$activeBrandTitle = $pageBrandTitle ?? ($brandConfig[$currentScript]['title'] ?? 'Sistem Tinjauan IPT');
?>
<!-- Navigation Header Styling (Compact & Clean) -->
<style>
    .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 1.75rem;
    }

    .brand {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        font-size: 1.15rem;
        font-weight: 700;
        color: #f8fafc;
    }

    .brand .brand-icon {
        color: var(--primary-glow, #38bdf8);
        font-size: 1.25rem;
    }

    .nav-links {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.4rem;
    }

    .nav-btn {
        padding: 0.38rem 0.8rem !important;
        font-size: 0.8rem !important;
        border-radius: 8px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 0.38rem !important;
        font-weight: 500 !important;
        white-space: nowrap !important;
        text-decoration: none !important;
        color: var(--text-main, #f8fafc) !important;
        background: rgba(255, 255, 255, 0.08) !important;
        border: 1px solid var(--card-border, rgba(255, 255, 255, 0.12)) !important;
        transition: all 0.2s ease !important;
    }

    .nav-btn i {
        font-size: 0.78rem !important;
    }

    .nav-btn:hover,
    .nav-btn.active {
        background: var(--primary-accent, #0284c7) !important;
        border-color: transparent !important;
        color: #ffffff !important;
        box-shadow: 0 0 12px rgba(56, 189, 248, 0.35) !important;
    }

    .user-nav-badge {
        background: rgba(168, 85, 247, 0.15) !important;
        color: #c084fc !important;
        border: 1px solid rgba(168, 85, 247, 0.35) !important;
        padding: 0.38rem 0.8rem !important;
        font-size: 0.8rem !important;
        border-radius: 8px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 0.38rem !important;
        font-weight: 600 !important;
        white-space: nowrap !important;
    }

    .nav-btn-logout {
        background: rgba(239, 68, 68, 0.2) !important;
        border-color: rgba(239, 68, 68, 0.4) !important;
        color: #fca5a5 !important;
    }

    .nav-btn-logout:hover {
        background: rgba(239, 68, 68, 0.85) !important;
        color: #ffffff !important;
        box-shadow: 0 0 12px rgba(239, 68, 68, 0.4) !important;
    }

    @media (max-width: 768px) {
        .navbar {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
        .nav-links {
            width: 100%;
        }
    }
</style>

<!-- Reusable Navigation Bar -->
<nav class="navbar">
    <div class="brand">
        <i class="<?php echo htmlspecialchars($activeBrandIcon); ?> brand-icon"></i>
        <span><?php echo htmlspecialchars($activeBrandTitle); ?></span>
    </div>
    <div class="nav-links">
        <?php if (!empty($menuUser)): ?>
            <span class="user-nav-badge">
                <i class="fa-solid fa-user-check"></i> <?php echo htmlspecialchars($menuUser['full_name']); ?>
            </span>
        <?php endif; ?>

        <a href="index.php" class="nav-btn <?php echo ($currentScript === 'index.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-list-check"></i> Borang Tinjauan
        </a>
        <a href="cadang_institusi.php" class="nav-btn <?php echo ($currentScript === 'cadang_institusi.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-school-flag"></i> Cadang IPT
        </a>
        <a href="cadang_kursus.php" class="nav-btn <?php echo ($currentScript === 'cadang_kursus.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-graduation-cap"></i> Cadang Kursus
        </a>
        <a href="responden.php" class="nav-btn <?php echo ($currentScript === 'responden.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-table-list"></i> Rekod Responden
        </a>
        <a href="admin.php" class="nav-btn <?php echo ($currentScript === 'admin.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-sliders"></i> Panel Admin
        </a>
        <a href="skills.php" class="nav-btn <?php echo ($currentScript === 'skills.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-brain"></i> Pengurusan Kemahiran
        </a>
        <a href="industry_framework.php" class="nav-btn <?php echo ($currentScript === 'industry_framework.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-network-wired"></i> Analisis Jurang Industri
        </a>

        <?php if (!empty($menuUser)): ?>
            <a href="logout.php" class="nav-btn nav-btn-logout">
                <i class="fa-solid fa-right-from-bracket"></i> Log Keluar
            </a>
        <?php else: ?>
            <a href="login.php" class="nav-btn">
                <i class="fa-solid fa-right-to-bracket"></i> Log Masuk
            </a>
        <?php endif; ?>
    </div>
</nav>
