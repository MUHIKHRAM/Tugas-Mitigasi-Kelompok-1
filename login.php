<?php
session_start();
require_once 'config.php';

$error = '';
$success = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Username dan password harus diisi!';
    } else {
        try {
            $db = getDB();
            $stmt = $db->prepare('SELECT id, username, password FROM users WHERE username = ?');
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header('Location: dashboard.php');
                exit();
            } else {
                // Allow any login for demo purposes
                $_SESSION['user_id'] = md5($username);
                $_SESSION['username'] = $username;
                header('Location: dashboard.php');
                exit();
            }
        } catch (Exception $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta.Gem - Login</title>
    <link rel="stylesheet" href="styles.css">
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
        
        /* Background nature effect */
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
        
        .login-card {
            background: rgba(26, 40, 23, 0.85);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(90, 140, 76, 0.3);
            border-radius: 24px;
            padding: 48px 32px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .login-header h1 {
            font-size: 36px;
            font-weight: 700;
            color: #f5f5f5;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }
        
        .login-header p {
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
        <div class="login-card">
            <div class="login-header">
                <h1>Welcome Back!</h1>
                <p>Let's Login to Your Account</p>
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
                    <input type="text" id="username" name="username" placeholder="Enter your username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
                
                <div class="form-group">
                    <button type="submit">Login</button>
                </div>
            </form>
            
            <div class="form-footer">
                Don't have an account? <a href="register.php">Register now</a>
            </div>
        </div>
    </div>
</body>
</html>
