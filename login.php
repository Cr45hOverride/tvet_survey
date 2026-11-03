<?php
/**
 * Admin Login Page - Survey IPT System
 */
require_once __DIR__ . '/config.php';

// If already logged in, redirect to admin.php
if (isLoggedIn()) {
    header('Location: admin.php');
    exit;
}

$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($username) && !empty($password)) {
        try {
            $db = getDBConnection();
            $stmt = $db->prepare("SELECT id, username, password_hash, full_name, role FROM admin_users WHERE username = :u LIMIT 1");
            $stmt->execute(['u' => $username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['admin_user'] = [
                    'id'        => $user['id'],
                    'username'  => $user['username'],
                    'full_name' => $user['full_name'],
                    'role'      => $user['role']
                ];
                header('Location: admin.php');
                exit;
            } else {
                $error_msg = 'Nama pengguna atau kata laluan tidak sah.';
            }
        } catch (Exception $e) {
            $error_msg = 'Ralat pangkalan data: ' . $e->getMessage();
        }
    } else {
        $error_msg = 'Sila isi nama pengguna dan kata laluan.';
    }
}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Masuk Admin - Sistem Tinjauan IPT</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .login-card {
            max-width: 420px;
            margin: 4rem auto;
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-header i {
            font-size: 3rem;
            color: var(--primary-glow);
            margin-bottom: 1rem;
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
                <a href="index.php" class="nav-btn"><i class="fa-solid fa-list-check"></i> Borang Tinjauan</a>
                <a href="login.php" class="nav-btn active"><i class="fa-solid fa-lock"></i> Log Masuk Admin</a>
            </div>
        </nav>

        <!-- Login Card -->
        <main class="glass-card login-card">
            <div class="login-header">
                <i class="fa-solid fa-user-shield"></i>
                <h1 class="card-title" style="justify-content: center;">Portal Pentadbir</h1>
                <p class="card-subtitle" style="margin-bottom: 0;">Sila log masuk untuk mengakses panel pengurusan.</p>
            </div>

            <?php if (!empty($error_msg)): ?>
                <div class="alert-box error" style="display: block;">
                    <i class="fa-solid fa-circle-exclamation"></i> <?php echo htmlspecialchars($error_msg); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php" autocomplete="off">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label" for="username">
                            <span>Nama Pengguna (Username)</span>
                        </label>
                        <input type="text" id="username" name="username" class="custom-input" placeholder="Masukkan username" required autofocus>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">
                            <span>Kata Laluan (Password)</span>
                        </label>
                        <input type="password" id="password" name="password" class="custom-input" placeholder="Masukkan kata laluan" required>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fa-solid fa-right-to-bracket"></i> Log Masuk
                    </button>
                </div>
            </form>
        </main>

        <footer>
            <p>&copy; <?php echo date('Y'); ?> Sistem Tinjauan IPT Malaysia | Admin Access Control</p>
        </footer>
    </div>

</body>
</html>
