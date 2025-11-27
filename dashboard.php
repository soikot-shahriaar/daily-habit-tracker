<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "login.php");
    exit();
}

require_once 'config.php';

// Get user info
$stmt = $pdo->prepare("SELECT username, email, first_name, last_name FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Get user's habits
$stmt = $pdo->prepare("SELECT id, title, description, category, color FROM habits WHERE user_id = ? AND is_active = 1");
$stmt->execute([$_SESSION['user_id']]);
$habits = $stmt->fetchAll();

// Handle logout
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: " . BASE_URL . "login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Dashboard - Daily Habit Tracker</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar {
            background: var(--primary-gradient) !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 1rem 0;
        }
        
        .navbar-brand {
            font-size: 1.8rem;
            font-weight: 700;
            color: white !important;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .navbar-brand i {
            font-size: 2rem;
            color: #ffd700;
        }
        
        .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 600;
            padding: 0.5rem 1rem !important;
            margin: 0 0.25rem;
            border-radius: 25px;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: white !important;
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }
        
        .navbar-nav .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 20px;
            height: 3px;
            background: #ffd700;
            border-radius: 2px;
        }
        
        .navbar-text {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
        }
        
        .btn-outline-light {
            border: 2px solid rgba(255, 255, 255, 0.5);
            border-radius: 25px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-outline-light:hover {
            background: white;
            color: #667eea;
            border-color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .main-content {
            padding: 2rem 0;
            min-height: calc(100vh - 200px);
        }
        
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .card-header {
            background: var(--primary-gradient);
            color: white;
            border: none;
            padding: 1.5rem;
            font-weight: 600;
            font-size: 1.2rem;
        }
        
        .card-header h5 {
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .card-body {
            padding: 2rem;
        }
        
        .btn {
            border-radius: 15px;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
            border: none;
        }
        
        .btn-primary {
            background: var(--primary-gradient);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
        }
        
        .btn-outline-primary {
            border: 2px solid #667eea;
            color: #667eea;
            background: transparent;
        }
        
        .btn-outline-primary:hover {
            background: var(--primary-gradient);
            border-color: transparent;
            transform: translateY(-2px);
        }
        
        .habit-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border-left: 5px solid #667eea;
            transition: all 0.3s ease;
        }
        
        .habit-card:hover {
            transform: translateX(5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .badge {
            border-radius: 20px;
            padding: 0.5rem 1rem;
            font-weight: 500;
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
        
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .stats-card h3 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .stats-card p {
            margin: 0;
            opacity: 0.9;
        }
        
        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.5rem;
            }
            
            .navbar-brand i {
                font-size: 1.5rem;
            }
            
            .card-body {
                padding: 1.5rem;
            }
            
            .stats-card {
                padding: 1.5rem;
            }
            
            .stats-card h3 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Enhanced Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-calendar-check"></i>
                Daily Habit Tracker
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                                               <li class="nav-item">
                               <a class="nav-link active" href="dashboard.php">
                                   <i class="fas fa-home me-1"></i>Dashboard
                               </a>
                           </li>
                           <li class="nav-item">
                               <a class="nav-link" href="habits.php">
                                   <i class="fas fa-list-check me-1"></i>Habits
                               </a>
                           </li>
                           <li class="nav-item">
                               <a class="nav-link" href="track.php">
                                   <i class="fas fa-calendar-check me-1"></i>Track
                               </a>
                           </li>
                           <li class="nav-item">
                               <a class="nav-link" href="progress.php">
                                   <i class="fas fa-chart-line me-1"></i>Progress
                               </a>
                           </li>
                </ul>
                
                <div class="navbar-nav">
                                               <span class="navbar-text me-3">
                               <i class="fas fa-user-circle me-2"></i>
                               Welcome, <?php echo htmlspecialchars($user['username']); ?>!
                           </span>
                           <a href="logout.php" class="btn btn-outline-light">
                               <i class="fas fa-sign-out-alt me-1"></i>Logout
                           </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <!-- Welcome Stats -->
            <div class="row mb-4">
                                       <div class="col-md-4">
                           <div class="stats-card">
                               <i class="fas fa-list-check display-4 mb-3"></i>
                               <h3><?php echo count($habits); ?></h3>
                               <p>Active Habits</p>
                           </div>
                       </div>
                       <div class="col-md-4">
                           <div class="stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                               <i class="fas fa-calendar-day display-4 mb-3"></i>
                               <h3><?php echo date('j'); ?></h3>
                               <p>Day of Month</p>
                           </div>
                       </div>
                       <div class="col-md-4">
                           <div class="stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                               <i class="fas fa-trophy display-4 mb-3"></i>
                               <h3><?php echo date('W'); ?></h3>
                               <p>Week of Year</p>
                           </div>
                       </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                                                       <div class="card-header">
                                   <h5><i class="fas fa-list-check"></i>Your Habits</h5>
                               </div>
                        <div class="card-body">
                            <?php if (empty($habits)): ?>
                                                                       <div class="text-center py-4">
                                           <i class="fas fa-list-check display-1 text-muted"></i>
                                           <h5 class="text-muted mt-3">No habits created yet</h5>
                                           <p class="text-muted">Start building your daily routine!</p>
                                           <a href="habits.php" class="btn btn-primary">
                                               <i class="fas fa-plus-circle me-2"></i>Create Your First Habit
                                           </a>
                                       </div>
                            <?php else: ?>
                                <div class="row">
                                    <?php foreach ($habits as $habit): ?>
                                        <div class="col-md-6 mb-3">
                                            <div class="habit-card">
                                                <h6 class="card-title"><?php echo htmlspecialchars($habit['title']); ?></h6>
                                                <?php if ($habit['description']): ?>
                                                    <p class="card-text small text-muted mb-2">
                                                        <?php echo htmlspecialchars($habit['description']); ?>
                                                    </p>
                                                <?php endif; ?>
                                                <span class="badge bg-secondary"><?php echo htmlspecialchars($habit['category']); ?></span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                                                       <div class="card-header">
                                   <h5><i class="fas fa-bolt"></i>Quick Actions</h5>
                               </div>
                        <div class="card-body">
                                                               <a href="habits.php" class="btn btn-primary w-100 mb-3">
                                       <i class="fas fa-plus-circle me-2"></i>Add New Habit
                                   </a>
                                   <a href="track.php" class="btn btn-outline-primary w-100 mb-3">
                                       <i class="fas fa-calendar-check me-2"></i>Track Progress
                                   </a>
                                   <a href="progress.php" class="btn btn-outline-primary w-100">
                                       <i class="fas fa-chart-line me-2"></i>View Reports
                                   </a>
                        </div>
                    </div>
                    
                                               <div class="card mt-3">
                               <div class="card-header">
                                   <h5><i class="fas fa-user-circle"></i>Account Info</h5>
                               </div>
                        <div class="card-body">
                            <p class="mb-2"><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                            <p class="mb-2"><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                            <p class="mb-0"><strong>Name:</strong> <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
                        </div>
                    </div>
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
</body>
</html>
