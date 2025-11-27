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

// Handle habit tracking
if ($_POST && isset($_POST['track_habit'])) {
    $habit_id = $_POST['habit_id'];
    $tracking_date = $_POST['tracking_date'];
    $completed = isset($_POST['completed']) ? 1 : 0;
    $notes = trim($_POST['notes']);
    
    // Verify the habit belongs to the current user
    $stmt = $pdo->prepare("SELECT id FROM habits WHERE id = ? AND user_id = ? AND is_active = 1");
    $stmt->execute([$habit_id, $_SESSION['user_id']]);
    
    if ($stmt->fetch()) {
        // Check if tracking record already exists for this date
        $stmt = $pdo->prepare("SELECT id FROM habit_tracking WHERE habit_id = ? AND tracking_date = ?");
        $stmt->execute([$habit_id, $tracking_date]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            // Update existing record
            $stmt = $pdo->prepare("UPDATE habit_tracking SET completed = ?, notes = ?, updated_at = NOW() WHERE habit_id = ? AND tracking_date = ?");
            $stmt->execute([$completed, $notes, $habit_id, $tracking_date]);
        } else {
            // Create new record
            $stmt = $pdo->prepare("INSERT INTO habit_tracking (habit_id, user_id, tracking_date, completed, notes) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$habit_id, $_SESSION['user_id'], $tracking_date, $completed, $notes]);
        }
        
        $success = "Progress updated successfully!";
    } else {
        $error = "Invalid habit ID.";
    }
}

// Get user's active habits
$stmt = $pdo->prepare("SELECT id, title, description, category, color, target_frequency FROM habits WHERE user_id = ? AND is_active = 1 ORDER BY title");
$stmt->execute([$_SESSION['user_id']]);
$habits = $stmt->fetchAll();

// Get tracking data for today
$today = date('Y-m-d');
$tracking_data = [];
if (!empty($habits)) {
    $habit_ids = array_column($habits, 'id');
    $placeholders = str_repeat('?,', count($habit_ids) - 1) . '?';
    $sql = "SELECT habit_id, completed, notes FROM habit_tracking WHERE habit_id IN ($placeholders) AND tracking_date = ?";
    $params = array_merge($habit_ids, [$today]);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    while ($row = $stmt->fetch()) {
        $tracking_data[$row['habit_id']] = $row;
    }
}

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
            <title>Track Habits - Daily Habit Tracker</title>
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
        
        .habit-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border-left: 5px solid #667eea;
        }
        
        .habit-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        .habit-card.completed {
            border-left-color: #28a745;
            background: linear-gradient(135deg, #f8fff9 0%, #f0fff4 100%);
        }
        
        .habit-card.not-completed {
            border-left-color: #dc3545;
            background: linear-gradient(135deg, #fff8f8 0%, #fff0f0 100%);
        }
        
        .form-check-input {
            width: 20px;
            height: 20px;
            margin-top: 0.2em;
            border-radius: 50%;
            border: 2px solid #667eea;
            transition: all 0.3s ease;
        }
        
        .form-check-input:checked {
            background-color: #28a745;
            border-color: #28a745;
        }
        
        .form-check-label {
            font-weight: 600;
            color: #495057;
            cursor: pointer;
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
        
        .empty-state {
            text-align: center;
            padding: 3rem 0;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: #6c757d;
            margin-bottom: 1rem;
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
            
            .habit-card {
                padding: 1rem;
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
                        <a class="nav-link" href="habits.php">
                            <i class="bi bi-list-check me-1"></i>Habits
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="track.php">
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
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>
                                <i class="bi bi-calendar-check"></i> 
                                Track Your Habits - <?php echo date('l, F j, Y'); ?>
                            </h5>
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
                            
                            <?php if (empty($habits)): ?>
                                <div class="empty-state">
                                    <i class="bi bi-list-check"></i>
                                    <h5 class="text-muted mt-3">No habits to track yet</h5>
                                    <p class="text-muted">Create some habits first to start tracking your progress!</p>
                                    <a href="habits.php" class="btn btn-primary">
                                        <i class="bi bi-plus-circle me-2"></i>Create Your First Habit
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="row">
                                    <?php foreach ($habits as $habit): ?>
                                        <div class="col-md-6 col-lg-4 mb-3">
                                            <div class="habit-card <?php echo isset($tracking_data[$habit['id']]) && $tracking_data[$habit['id']]['completed'] ? 'completed' : 'not-completed'; ?>">
                                                <div class="d-flex justify-content-between align-items-start mb-3">
                                                    <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($habit['title']); ?></h6>
                                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($habit['category']); ?></span>
                                                </div>
                                                
                                                <?php if ($habit['description']): ?>
                                                    <p class="text-muted mb-3 small">
                                                        <?php echo htmlspecialchars($habit['description']); ?>
                                                    </p>
                                                <?php endif; ?>
                                                
                                                <form method="POST" class="tracking-form">
                                                    <input type="hidden" name="habit_id" value="<?php echo $habit['id']; ?>">
                                                    <input type="hidden" name="tracking_date" value="<?php echo $today; ?>">
                                                    
                                                    <div class="mb-3">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="completed" id="completed_<?php echo $habit['id']; ?>" 
                                                                   <?php echo isset($tracking_data[$habit['id']]) && $tracking_data[$habit['id']]['completed'] ? 'checked' : ''; ?>>
                                                            <label class="form-check-label" for="completed_<?php echo $habit['id']; ?>">
                                                                <strong>Completed Today</strong>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label for="notes_<?php echo $habit['id']; ?>" class="form-label">Notes (optional)</label>
                                                        <textarea class="form-control" id="notes_<?php echo $habit['id']; ?>" name="notes" rows="2" 
                                                                  placeholder="How did it go?"><?php echo isset($tracking_data[$habit['id']]) ? htmlspecialchars($tracking_data[$habit['id']]['notes']) : ''; ?></textarea>
                                                    </div>
                                                    
                                                    <button type="submit" name="track_habit" class="btn btn-primary w-100">
                                                        <i class="bi bi-save me-2"></i> Save Progress
                                                    </button>
                                                </form>
                                                
                                                <div class="mt-3 text-center">
                                                    <small class="text-muted">
                                                        <i class="bi bi-calendar-event me-1"></i>
                                                        <?php echo ucfirst($habit['target_frequency']); ?> habit
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
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

