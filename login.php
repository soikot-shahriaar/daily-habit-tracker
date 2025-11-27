<?php
session_start();

require_once 'config.php';

$error = '';
$success = '';

// Handle login
if ($_POST && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields";
    } else {
        // Find user
        $stmt = $pdo->prepare("SELECT id, username, password_hash FROM users WHERE username = ? AND is_active = 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            // Login successful
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $success = "Login successful! Redirecting...";
            
            // Redirect after 2 seconds
            header("refresh:2;url=" . BASE_URL . "dashboard.php");
        } else {
            $error = "Invalid username or password";
        }
    }
}

// Handle registration
if ($_POST && isset($_POST['register'])) {
    $username = trim($_POST['reg_username']);
    $email = trim($_POST['reg_email']);
    $password = $_POST['reg_password'];
    $confirm_password = $_POST['reg_confirm_password'];
    
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Please fill in all fields";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters";
    } else {
        // Check if username exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = "Username already exists";
        } else {
            // Check if email exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = "Email already exists";
            } else {
                // Create user
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, first_name, last_name) VALUES (?, ?, ?, ?, ?)");
                
                if ($stmt->execute([$username, $email, $password_hash, '', ''])) {
                    $success = "Registration successful! You can now login.";
                } else {
                    $error = "Registration failed. Please try again.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Daily Habit Tracker - Login</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .auth-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 900px;
            animation: slideUp 0.6s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .auth-header {
            background: var(--primary-gradient);
            color: white;
            padding: 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .auth-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            animation: float 20s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
        
        .auth-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }
        
        .auth-header p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin: 0;
            position: relative;
            z-index: 1;
        }
        
        .auth-body {
            padding: 40px;
        }
        
        .nav-tabs {
            border: none;
            margin-bottom: 30px;
            justify-content: center;
        }
        
        .nav-tabs .nav-link {
            border: none;
            background: none;
            color: #6c757d;
            font-weight: 600;
            padding: 15px 30px;
            margin: 0 10px;
            border-radius: 50px;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .nav-tabs .nav-link:hover {
            color: #667eea;
            background: rgba(102, 126, 234, 0.1);
        }
        
        .nav-tabs .nav-link.active {
            color: white;
            background: var(--primary-gradient);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .tab-content {
            display: none;
            animation: fadeIn 0.5s ease-in;
        }
        
        .tab-content.active {
            display: block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .form-floating {
            margin-bottom: 20px;
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 15px 20px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            background: white;
        }
        
        .form-label {
            font-weight: 600;
            color: #495057;
        }
        
        .btn {
            padding: 15px 30px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            border: none;
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary {
            background: var(--primary-gradient);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
        }
        
        .btn-success {
            background: var(--success-gradient);
            box-shadow: 0 5px 15px rgba(79, 172, 254, 0.4);
        }
        
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 172, 254, 0.5);
        }
        
        @keyframes shine {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        .alert {
            border: none;
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            color: white;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #51cf66 0%, #40c057 100%);
            color: white;
        }
        
        .form-check {
            margin-bottom: 20px;
        }
        
        .form-check-input {
            width: 20px;
            height: 20px;
            margin-top: 0.2em;
        }
        
        .form-check-label {
            font-weight: 500;
            color: #495057;
        }
        
        .floating-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }
        
        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float-shape 6s ease-in-out infinite;
        }
        
        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }
        
        .shape:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            right: 10%;
            animation-delay: 2s;
        }
        
        .shape:nth-child(3) {
            width: 60px;
            height: 60px;
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }
        
        @keyframes float-shape {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
        
        .footer {
            background: var(--primary-gradient);
            color: white;
            padding: 2rem 0;
            margin-top: 3rem;
        }
        
        .footer a {
            color: #ffd700;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .footer a:hover {
            color: white;
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .auth-card {
                margin: 20px;
                border-radius: 15px;
            }
            
            .auth-header {
                padding: 30px 20px;
            }
            
            .auth-header h1 {
                font-size: 2rem;
            }
            
            .auth-body {
                padding: 30px 20px;
            }
            
            .nav-tabs .nav-link {
                padding: 12px 20px;
                margin: 0 5px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <!-- Floating Background Shapes -->
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <div class="auth-container">
        <div class="auth-card">
            <!-- Header -->
            <div class="auth-header">
                <h1><i class="fas fa-calendar-check me-3"></i>Daily Habit Tracker</h1>
                <p>Build better habits, track your progress, achieve your goals</p>
                        </div>
                        
            <!-- Body -->
            <div class="auth-body">
                        <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                                <?php echo htmlspecialchars($success); ?>
                            </div>
                        <?php endif; ?>
                        
                <!-- Navigation Tabs -->
                                       <ul class="nav nav-tabs" id="authTabs">
                           <li class="nav-item">
                               <a class="nav-link active" onclick="showTab('login')">
                                   <i class="fas fa-sign-in-alt me-2"></i>Login
                               </a>
                           </li>
                           <li class="nav-item">
                               <a class="nav-link" onclick="showTab('register')">
                                   <i class="fas fa-user-plus me-2"></i>Register
                               </a>
                           </li>
                       </ul>
                
                <!-- Login Form -->
                <div id="login" class="tab-content active">
                    <form method="POST">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                            <label for="username">Username</label>
                            </div>
                        
                        <div class="form-floating">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                            <label for="password">Password</label>
                        </div>
                        
                                                       <button type="submit" name="login" class="btn btn-primary w-100">
                                   <i class="fas fa-sign-in-alt me-2"></i>Sign In
                               </button>
                    </form>                 
                    
                </div>
                
                <!-- Register Form -->
                <div id="register" class="tab-content">
                    <form method="POST">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="reg_username" name="reg_username" placeholder="Username" required>
                            <label for="reg_username">Username</label>
                        </div>
                        
                        <div class="form-floating">
                            <input type="email" class="form-control" id="reg_email" name="reg_email" placeholder="Email" required>
                            <label for="reg_email">Email</label>
                        </div>
                        
                        <div class="form-floating">
                            <input type="password" class="form-control" id="reg_password" name="reg_password" placeholder="Password" required>
                            <label for="reg_password">Password</label>
                        </div>
                        
                        <div class="form-floating">
                            <input type="password" class="form-control" id="reg_confirm_password" name="reg_confirm_password" placeholder="Confirm Password" required>
                            <label for="reg_confirm_password">Confirm Password</label>
                        </div>
                        
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="terms" required>
                            <label class="form-check-label" for="terms">
                                I agree to the <a href="#" class="text-decoration-none">Terms of Service</a> and <a href="#" class="text-decoration-none">Privacy Policy</a>
                            </label>
                    </div>
                        
                        <button type="submit" name="register" class="btn btn-success w-100">
                            <i class="fas fa-user-plus me-2"></i>Create Account
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="text-center my-2">
                <div>
                    <span>© 2025 . </span>
                    <span class="text-light">Developed by </span>
                    <a href="https://rivertheme.com" class="fw-bold text-decoration-none" target="_blank" rel="noopener">RiverTheme</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Remove active class from all nav links
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
            });
            
            // Show selected tab
            document.getElementById(tabName).classList.add('active');
            
            // Add active class to clicked nav link
            event.target.classList.add('active');
        }
        
        // Add some interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            // Add focus effects to form inputs
            const inputs = document.querySelectorAll('.form-control');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('focused');
                });
            });
        });
    </script>
</body>
</html>
