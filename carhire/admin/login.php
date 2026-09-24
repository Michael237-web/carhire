<?php
// ===================== DEBUG (remove after fixing) =====================
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ===================== CONFIG =====================
require_once '../config.php';
$error = '';

// ===================== HANDLE LOGIN =====================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $conn = getDBConnection();

        $stmt = $conn->prepare("SELECT id, username, password_hash FROM admin_users WHERE username = ? LIMIT 1");
        if (!$stmt) {
            $error = 'Database error: ' . $conn->error;
        } else {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            $conn->close();

            if ($user) {
                // Accept either a correctly hashed password OR the demo password
                $passwordOk = password_verify($password, $user['password_hash'])
                           || $password === 'admin123';

                if ($passwordOk) {
                    // Prevent session fixation
                    session_regenerate_id(true);

                    $_SESSION['admin_id']       = (int) $user['id'];
                    $_SESSION['admin_username'] = $user['username'];

                    header('Location: dashboard.php');
                    exit;
                } else {
                    $error = 'Invalid username or password.';
                }
            } else {
                $error = 'Invalid username or password.';
            }
        }
    } else {
        $error = 'Please fill in both fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | DriveElite</title>
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: var(--gray-50); min-height: 100vh; }
        .login-wrapper {
            max-width: 420px;
            margin: 6rem auto;
            padding: 3rem;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(15, 36, 56, 0.15);
        }
        .login-wrapper h1 {
            text-align: center;
            color: var(--navy-800);
            margin-bottom: 2rem;
            font-size: 1.6rem;
        }
        .login-wrapper h1 i { color: var(--blue-500); margin-right: 0.5rem; }
        .login-wrapper .form-group { margin-bottom: 1.2rem; }
        .login-wrapper label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 0.4rem;
        }
        .login-wrapper input {
            width: 100%;
            padding: 0.9rem 1rem;
            border: 1.5px solid var(--gray-300);
            border-radius: 10px;
            font-size: 1rem;
            font-family: inherit;
            transition: all 0.2s;
        }
        .login-wrapper input:focus {
            outline: none;
            border-color: var(--blue-500);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
        }
        .login-wrapper .btn-submit {
            width: 100%;
            margin-top: 1rem;
            padding: 1rem;
            background: linear-gradient(135deg, var(--navy-700), var(--navy-600));
            color: #fff;
            border: none;
            border-radius: 40px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.25s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .login-wrapper .btn-submit:hover {
            background: linear-gradient(135deg, var(--blue-500), var(--blue-600));
            box-shadow: 0 12px 26px rgba(59, 130, 246, 0.4);
            transform: translateY(-2px);
        }
        .login-wrapper .alert {
            padding: 1rem 1.2rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-weight: 500;
            font-size: 0.92rem;
        }
        .login-wrapper .alert.error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }
        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--gray-500);
            font-size: 0.85rem;
        }
        .login-footer code {
            background: var(--gray-100);
            padding: 0.15rem 0.4rem;
            border-radius: 4px;
            font-size: 0.8rem;
            color: var(--navy-800);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-wrapper">
            <h1><i class="fas fa-lock"></i> Admin Login</h1>

            <?php if ($error): ?>
                <div class="alert error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" autocomplete="off">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required autofocus>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>

            <p class="login-footer">
                Demo credentials: <code>admin</code> / <code>admin123</code>
            </p>
        </div>
    </div>
</body>
</html>