<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "login.php");
    exit();
}

require_once 'config.php';

// Get date range (default: last 30 days)
$end_date = date('Y-m-d');
$start_date = date('Y-m-d', strtotime('-30 days'));

if (isset($_GET['days'])) {
    $days = (int)$_GET['days'];
    $start_date = date('Y-m-d', strtotime("-$days days"));
}

// Get user's habits with completion stats
$stmt = $pdo->prepare("
    SELECT h.id, h.title, h.category, h.target_frequency,
           COUNT(ht.id) as total_days,
           SUM(ht.completed) as completed_days,
           ROUND((SUM(ht.completed) / COUNT(ht.id)) * 100, 1) as completion_rate
    FROM habits h
    LEFT JOIN habit_tracking ht ON h.id = ht.habit_id 
        AND ht.tracking_date BETWEEN ? AND ?
    WHERE h.user_id = ? AND h.is_active = 1
    GROUP BY h.id
    ORDER BY completion_rate DESC
");
$stmt->execute([$start_date, $end_date, $_SESSION['user_id']]);
$habits_stats = $stmt->fetchAll();

// Get overall completion rate
$stmt = $pdo->prepare("
    SELECT 
        COUNT(*) as total_entries,
        SUM(completed) as total_completed,
        ROUND((SUM(completed) / COUNT(*)) * 100, 1) as overall_rate
    FROM habit_tracking ht
    JOIN habits h ON ht.habit_id = h.id
    WHERE h.user_id = ? AND ht.tracking_date BETWEEN ? AND ?
");
$stmt->execute([$_SESSION['user_id'], $start_date, $end_date]);
$overall_stats = $stmt->fetch();

// Get daily completion trend
$stmt = $pdo->prepare("
    SELECT 
        ht.tracking_date,
        COUNT(*) as total_habits,
        SUM(ht.completed) as completed_habits,
        ROUND((SUM(ht.completed) / COUNT(*)) * 100, 1) as daily_rate
    FROM habit_tracking ht
    JOIN habits h ON ht.habit_id = h.id
    WHERE h.user_id = ? AND ht.tracking_date BETWEEN ? AND ?
    GROUP BY ht.tracking_date
    ORDER BY ht.tracking_date
");
$stmt->execute([$_SESSION['user_id'], $start_date, $end_date]);
$daily_trend = $stmt->fetchAll();

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
            <title>Progress & Analytics - Daily Habit Tracker</title>
        <link rel="icon" type="image/svg+xml" href="assets/favicon.svg">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        
        .btn-outline-primary {
            border: 2px solid #667eea;
            color: #667eea;
            background: transparent;
            border-radius: 25px;
            padding: 0.5rem 1.5rem;
        }
        
        .btn-outline-primary:hover,
        .btn-outline-primary.active {
            background: var(--primary-gradient);
            border-color: transparent;
            transform: translateY(-2px);
        }
        
        .stats-card {
            background: var(--primary-gradient);
            color: white;
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
            transition: all 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(102, 126, 234, 0.4);
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
        
        .stats-card i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.8;
        }
        
        .progress {
            height: 20px;
            border-radius: 10px;
            background: #e9ecef;
        }
        
        .progress-bar {
            border-radius: 10px;
            font-weight: 600;
        }
        
        .table {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        
        .table thead th {
            background: var(--primary-gradient);
            color: white;
            border: none;
            padding: 1rem;
            font-weight: 600;
        }
        
        .table tbody td {
            padding: 1rem;
            border: none;
            border-bottom: 1px solid #f8f9fa;
        }
        
        .table tbody tr:hover {
            background: #f8f9fa;
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
            
            .stats-card {
                padding: 1.5rem;
            }
            
            .stats-card h3 {
                font-size: 2rem;
            }
            
            .stats-card i {
                font-size: 2.5rem;
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
                        <a class="nav-link" href="track.php">
                            <i class="bi bi-calendar-check me-1"></i>Track
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="progress.php">
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
            <!-- Date Range Selector -->
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="card-title fw-bold">Date Range</h6>
                    <div class="btn-group" role="group">
                        <a href="?days=7" class="btn btn-outline-primary <?php echo isset($_GET['days']) && $_GET['days'] == '7' ? 'active' : ''; ?>">Last 7 Days</a>
                        <a href="?days=30" class="btn btn-outline-primary <?php echo isset($_GET['days']) && $_GET['days'] == '30' ? 'active' : ''; ?>">Last 30 Days</a>
                        <a href="?days=90" class="btn btn-outline-primary <?php echo isset($_GET['days']) && $_GET['days'] == '90' ? 'active' : ''; ?>">Last 90 Days</a>
                    </div>
                    <small class="text-muted d-block mt-2">
                        Showing data from <?php echo date('M j, Y', strtotime($start_date)); ?> to <?php echo date('M j, Y', strtotime($end_date)); ?>
                    </small>
                </div>
            </div>

            <!-- Overall Statistics -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="stats-card">
                        <i class="bi bi-check-circle"></i>
                        <h3><?php echo $overall_stats['overall_rate'] ?? 0; ?>%</h3>
                        <p>Overall Completion Rate</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card" style="background: var(--secondary-gradient);">
                        <i class="bi bi-calendar-check"></i>
                        <h3><?php echo $overall_stats['total_completed'] ?? 0; ?></h3>
                        <p>Total Completed</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card" style="background: var(--success-gradient);">
                        <i class="bi bi-list-check"></i>
                        <h3><?php echo count($habits_stats); ?></h3>
                        <p>Active Habits</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Daily Trend Chart -->
                <div class="col-md-8 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="bi bi-graph-up"></i> Daily Completion Trend</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="dailyTrendChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Habit Performance -->
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="bi bi-trophy"></i> Habit Performance</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($habits_stats)): ?>
                                <p class="text-muted text-center">No data available</p>
                            <?php else: ?>
                                <?php foreach (array_slice($habits_stats, 0, 5) as $habit): ?>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <small class="fw-bold"><?php echo htmlspecialchars($habit['title']); ?></small>
                                            <br>
                                            <small class="text-muted"><?php echo $habit['completed_days']; ?>/<?php echo $habit['total_days']; ?> days</small>
                                        </div>
                                        <span class="badge bg-<?php echo $habit['completion_rate'] >= 80 ? 'success' : ($habit['completion_rate'] >= 60 ? 'warning' : 'danger'); ?>">
                                            <?php echo $habit['completion_rate']; ?>%
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Habit Statistics -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-bar-chart"></i> Detailed Habit Statistics</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($habits_stats)): ?>
                        <div class="empty-state">
                            <i class="bi bi-graph-up"></i>
                            <h5 class="text-muted mt-3">No habit data available for the selected period.</h5>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Habit</th>
                                        <th>Category</th>
                                        <th>Frequency</th>
                                        <th>Completed</th>
                                        <th>Total Days</th>
                                        <th>Completion Rate</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($habits_stats as $habit): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($habit['title']); ?></strong></td>
                                            <td><span class="badge bg-secondary"><?php echo htmlspecialchars($habit['category']); ?></span></td>
                                            <td><span class="badge bg-info"><?php echo ucfirst($habit['target_frequency']); ?></span></td>
                                            <td><?php echo $habit['completed_days']; ?></td>
                                            <td><?php echo $habit['total_days']; ?></td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar bg-<?php echo $habit['completion_rate'] >= 80 ? 'success' : ($habit['completion_rate'] >= 60 ? 'warning' : 'danger'); ?>" 
                                                         style="width: <?php echo $habit['completion_rate']; ?>%">
                                                        <?php echo $habit['completion_rate']; ?>%
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo $habit['completion_rate'] >= 80 ? 'success' : ($habit['completion_rate'] >= 60 ? 'warning' : 'danger'); ?>">
                                                    <?php echo $habit['completion_rate'] >= 80 ? 'Excellent' : ($habit['completion_rate'] >= 60 ? 'Good' : 'Needs Improvement'); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
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
        // Daily Trend Chart
        const ctx = document.getElementById('dailyTrendChart').getContext('2d');
        const dailyTrendChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($daily_trend, 'tracking_date')); ?>,
                datasets: [{
                    label: 'Completion Rate (%)',
                    data: <?php echo json_encode(array_column($daily_trend, 'daily_rate')); ?>,
                    borderColor: 'rgb(102, 126, 234)',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: 'rgb(102, 126, 234)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                },
                elements: {
                    point: {
                        hoverRadius: 8
                    }
                }
            }
        });
    </script>
</body>
</html>

