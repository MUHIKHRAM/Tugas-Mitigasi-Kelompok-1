<?php
session_start();
require_once 'config.php';

$error = '';
$success = '';

// Redirect ke dashboard jika sudah login
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

// Handle register form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Semua field harus diisi!';
    } elseif (strlen($password) < 6) {
        $error = 'Password harus minimal 6 karakter!';
    } elseif ($password !== $confirm_password) {
        $error = 'Password tidak cocok!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid!';
    } else {
        try {
            $db = getDB();
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $db->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
            $stmt->execute([$username, $email, $hashed_password]);
            
            $success = 'Registrasi berhasil! Silakan login.';
            $_SESSION['user_id'] = $db->lastInsertId();
            $_SESSION['username'] = $username;
            header('Refresh: 2; url=dashboard.php');
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'UNIQUE constraint failed: users.username') !== false) {
                $error = 'Username sudah terdaftar!';
            } elseif (strpos($e->getMessage(), 'UNIQUE constraint failed: users.email') !== false) {
                $error = 'Email sudah terdaftar!';
            } else {
                $error = 'Error: ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta.Gem - Register</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Geist', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #0a0f08 0%, #1a2817 50%, #0a0f08 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 800"><defs><linearGradient id="g1" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:%235a8c4c;stop-opacity:0.1"/><stop offset="100%" style="stop-color:%23d4a574;stop-opacity:0.05"/></linearGradient></defs><rect fill="url(%23g1)" width="1200" height="800"/><path d="M0 400 Q300 200 600 350 T1200 400 L1200 800 L0 800 Z" fill="%23243522" opacity="0.3"/><path d="M0 500 Q400 350 800 450 T1200 500 L1200 800 L0 800 Z" fill="%231a2817" opacity="0.2"/></svg>');
            background-size: cover;
            background-position: center;
            opacity: 0.4;
            z-index: 0;
        }
        
        .container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 450px;
            padding: 20px;
        }
        
        .register-card {
            background: rgba(26, 40, 23, 0.85);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(90, 140, 76, 0.3);
            border-radius: 24px;
            padding: 48px 32px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }
        
        .register-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .register-header h1 {
            font-size: 36px;
            font-weight: 700;
            color: #f5f5f5;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }
        
        .register-header p {
            color: #b0b8a8;
            font-size: 14px;
            font-weight: 500;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            color: #d4a574;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        
        input[type="text"],
        input[type="password"],
        input[type="email"] {
            width: 100%;
            padding: 12px 16px;
            background: rgba(245, 245, 245, 0.08);
            border: 1px solid rgba(90, 140, 76, 0.2);
            border-radius: 12px;
            color: #f5f5f5;
            font-size: 14px;
            transition: all 0.3s ease;
            outline: none;
        }
        
        input[type="text"]:focus,
        input[type="password"]:focus,
        input[type="email"]:focus {
            background: rgba(245, 245, 245, 0.12);
            border-color: rgba(90, 140, 76, 0.5);
            box-shadow: 0 0 0 3px rgba(90, 140, 76, 0.1);
        }
        
        input::placeholder {
            color: rgba(245, 245, 245, 0.4);
        }
        
        .form-group button {
            width: 100%;
            padding: 12px 24px;
            background: linear-gradient(135deg, #5a8c4c 0%, #7aa85f 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 24px;
            box-shadow: 0 4px 15px rgba(90, 140, 76, 0.3);
        }
        
        .form-group button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(90, 140, 76, 0.4);
        }
        
        .form-group button:active {
            transform: translateY(0);
        }
        
        .form-footer {
            text-align: center;
            margin-top: 24px;
            font-size: 14px;
            color: #b0b8a8;
        }
        
        .form-footer a {
            color: #d4a574;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        
        .form-footer a:hover {
            color: #f5f5f5;
        }
        
        .error-message {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
        }
        
        .success-message {
            background: rgba(34, 197, 94, 0.15);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #86efac;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="register-card">
            <div class="register-header">
                <h1>Hello!</h1>
                <p>Let's Register Your Account</p>
            </div>
            
            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Enter username" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter password (min 6 chars)" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm password" required>
                </div>
                
                <div class="form-group">
                    <button type="submit">Register</button>
                </div>
            </form>
            
            <div class="form-footer">
                Already have an account? <a href="login.php">Login now</a>
            </div>
        </div>
    </div>
</body>
</html>
