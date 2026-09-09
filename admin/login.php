<?php
/**
 * EnterF1.com - Admin Login
 */

session_start();
require_once '../config.php';

// =====================================================
// ADMIN CREDENTIALS - CHANGE THESE BEFORE GOING LIVE
// =====================================================
// To generate a new hash, run this in PHP:
// echo password_hash('your_password_here', PASSWORD_BCRYPT);
// Then paste the output as ADMIN_PASSWORD_HASH below.

define('ADMIN_USERNAME', 'enterf1admin');
define('ADMIN_PASSWORD_HASH', '$2y$12$QuthD9V38DIQBhx3nHAA7uD/CRqDn9Uc3jtv12/UnD/xh4uH0mdEi');

// Rate limiting: max attempts before lockout
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME', 300); // 5 minutes in seconds

// If already logged in, redirect to admin dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit;
}

$error = '';
$locked = false;

// Check for session timeout message
if (isset($_GET['timeout'])) {
    $error = 'Session expired. Please log in again.';
}

// Check rate limiting
if (isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] >= MAX_LOGIN_ATTEMPTS) {
    if (isset($_SESSION['lockout_time']) && (time() - $_SESSION['lockout_time']) < LOCKOUT_TIME) {
        $remaining = LOCKOUT_TIME - (time() - $_SESSION['lockout_time']);
        $error = "Too many failed attempts. Try again in " . ceil($remaining / 60) . " minute(s).";
        $locked = true;
    } else {
        // Lockout period expired, reset
        $_SESSION['login_attempts'] = 0;
        unset($_SESSION['lockout_time']);
    }
}

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$locked) {
    // CSRF check
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'Invalid form submission. Please try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === ADMIN_USERNAME && password_verify($password, ADMIN_PASSWORD_HASH)) {
            // Successful login
            session_regenerate_id(true);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $username;
            $_SESSION['last_activity'] = time();
            $_SESSION['login_attempts'] = 0;
            unset($_SESSION['lockout_time']);

            header('Location: index.php');
            exit;
        } else {
            // Failed login
            $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
            if ($_SESSION['login_attempts'] >= MAX_LOGIN_ATTEMPTS) {
                $_SESSION['lockout_time'] = time();
            }
            $error = 'Invalid username or password.';
        }
    }
}

// Generate CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin Login | <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700;900&display=swap" rel="stylesheet">
    <link href="<?php echo SITE_URL; ?>/css/style.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #15151E 0%, #1a1a24 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            max-width: 420px;
            width: 100%;
            border-top: 4px solid var(--f1-red);
        }
        .login-card:hover {
            transform: none;
        }
    </style>
</head>
<body>
    <div class="container px-3">
        <div class="login-card card mx-auto">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <h4 class="fw-bold">
                        <i class="bi bi-flag-fill text-danger"></i> <?php echo SITE_NAME; ?>
                    </h4>
                    <p class="text-muted mb-0">Admin Panel</p>
                </div>
                
                <?php if ($error): ?>
                <div class="alert alert-danger py-2 small">
                    <i class="bi bi-exclamation-triangle-fill"></i> <?php echo htmlspecialchars($error); ?>
                </div>
                <?php endif; ?>
                
                <form method="POST" action="login.php" autocomplete="off">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    
                    <div class="mb-3">
                        <label for="username" class="form-label fw-bold">Username</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                            <input type="text" class="form-control" id="username" name="username" 
                                   required autofocus <?php echo $locked ? 'disabled' : ''; ?>
                                   value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="form-label fw-bold">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" class="form-control" id="password" name="password" 
                                   required <?php echo $locked ? 'disabled' : ''; ?>>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-f1 w-100" <?php echo $locked ? 'disabled' : ''; ?>>
                        <i class="bi bi-box-arrow-in-right"></i> Log In
                    </button>
                </form>
                
                <div class="text-center mt-4">
                    <a href="<?php echo SITE_URL; ?>/" class="text-muted small text-decoration-none">
                        <i class="bi bi-arrow-left"></i> Back to site
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
