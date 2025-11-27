<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "login.php");
    exit();
}

require_once 'config.php';

$error = '';
$success = '';

// Handle habit creation
if ($_POST && isset($_POST['create_habit'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category = trim($_POST['category']);
    $color = $_POST['color'];
    $frequency = $_POST['frequency'];
    
    if (empty($title)) {
        $error = "Habit title is required";
    } else {
        $stmt = $pdo->prepare("INSERT INTO habits (user_id, title, description, category, color, target_frequency) VALUES (?, ?, ?, ?, ?, ?)");
        
        if ($stmt->execute([$_SESSION['user_id'], $title, $description, $category, $color, $frequency])) {
            $success = "Habit created successfully!";
        } else {
            $error = "Failed to create habit. Please try again.";
        }
    }
}

// Handle habit deletion
if ($_POST && isset($_POST['delete_habit'])) {
    $habit_id = $_POST['habit_id'];
    
    // Verify the habit belongs to the current user
    $stmt = $pdo->prepare("SELECT id FROM habits WHERE id = ? AND user_id = ?");
    $stmt->execute([$habit_id, $_SESSION['user_id']]);
    
    if ($stmt->fetch()) {
        $stmt = $pdo->prepare("UPDATE habits SET is_active = 0 WHERE id = ?");
        if ($stmt->execute([$habit_id])) {
            $success = "Habit deleted successfully!";
        } else {
            $error = "Failed to delete habit.";
        }
    } else {
        $error = "Invalid habit ID.";
    }
}

// Get user's habits
$stmt = $pdo->prepare("SELECT id, title, description, category, color, target_frequency, created_at FROM habits WHERE user_id = ? AND is_active = 1 ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$habits = $stmt->fetchAll();

// Get user info for navigation
$stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Manage Habits - Daily Habit Tracker</title>
        <link rel="icon" type="image/svg+xml" href="assets/favicon.svg">
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
        
        .btn-success {
            background: var(--success-gradient);
            box-shadow: 0 5px 15px rgba(79, 172, 254, 0.4);
        }
        
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 172, 254, 0.5);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.4);
        }
        
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 107, 107, 0.5);
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
        
        .form-select {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 15px 20px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            background: white;
        }
        
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }
        
        .habit-item {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border-left: 5px solid #667eea;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        
        .habit-item:hover {
            transform: translateX(5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
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
        
        .alert {
            border: none;
            border-radius: 15px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
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
                        <a class="nav-link" href="dashboard.php">
                            <i class="bi bi-house-fill me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="habits.php">
                            <i class="bi bi-list-check me-1"></i>Habits
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="track.php">
                            <i class="bi bi-calendar-check me-1"></i>Track
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="progress.php">
                            <i class="bi bi-graph-up me-1"></i>Progress
                        </a>
                    </li>
                </ul>
                
                <div class="navbar-nav">
                    <span class="navbar-text me-3">
                        <i class="bi bi-person-circle me-2"></i>
                        Welcome, <?php echo htmlspecialchars($user['username']); ?>!
                    </span>
                    <a href="logout.php" class="btn btn-outline-light">
                        <i class="bi bi-box-arrow-right me-1"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <!-- Create New Habit Form -->
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="bi bi-plus-circle"></i> Create New Habit</h5>
                        </div>
                        <div class="card-body">
                            <?php if ($error): ?>
                                <div class="alert alert-danger">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <?php echo htmlspecialchars($error); ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($success): ?>
                                <div class="alert alert-success">
                                    <i class="bi bi-check-circle-fill me-2"></i>
                                    <?php echo htmlspecialchars($success); ?>
                                </div>
                            <?php endif; ?>
                            
                            <form method="POST">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Habit Title *</label>
                                    <input type="text" class="form-control" id="title" name="title" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="category" class="form-label">Category</label>
                                    <select class="form-select" id="category" name="category">
                                        <option value="Health & Fitness">Health & Fitness</option>
                                        <option value="Education & Learning">Education & Learning</option>
                                        <option value="Wellness & Mindfulness">Wellness & Mindfulness</option>
                                        <option value="Personal Development">Personal Development</option>
                                        <option value="Social & Relationships">Social & Relationships</option>
                                        <option value="Home & Organization">Home & Organization</option>
                                        <option value="Finance">Finance</option>
                                        <option value="Hobbies & Creativity">Hobbies & Creativity</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="color" class="form-label">Color</label>
                                    <input type="color" class="form-control form-control-color" id="color" name="color" value="#007bff">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="frequency" class="form-label">Target Frequency</label>
                                    <select class="form-select" id="frequency" name="frequency">
                                        <option value="daily">Daily</option>
                                        <option value="weekly">Weekly</option>
                                    </select>
                                </div>
                                
                                <button type="submit" name="create_habit" class="btn btn-primary w-100">
                                    <i class="bi bi-plus-circle me-2"></i> Create Habit
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-8">
                    <!-- Habits List -->
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="bi bi-list-check"></i> Your Habits</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($habits)): ?>
                                <div class="text-center py-4">
                                    <i class="bi bi-list-check display-1 text-muted"></i>
                                    <p class="text-muted mt-3">No habits created yet. Start building your daily routine!</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($habits as $habit): ?>
                                    <div class="habit-item">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="mb-0"><?php echo htmlspecialchars($habit['title']); ?></h6>
                                            <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this habit?')">
                                                <input type="hidden" name="habit_id" value="<?php echo $habit['id']; ?>">
                                                <button type="submit" name="delete_habit" class="btn btn-danger btn-sm">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                        
                                        <?php if ($habit['description']): ?>
                                            <p class="text-muted mb-2">
                                                <?php echo htmlspecialchars($habit['description']); ?>
                                            </p>
                                        <?php endif; ?>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-secondary"><?php echo htmlspecialchars($habit['category']); ?></span>
                                            <span class="badge bg-info"><?php echo ucfirst($habit['target_frequency']); ?></span>
                                        </div>
                                        
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                Created: <?php echo date('M j, Y', strtotime($habit['created_at'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
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

