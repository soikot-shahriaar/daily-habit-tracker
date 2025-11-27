<?php
// Base URL configuration - always use localhost without port
// Detect project name from script directory path
$script_dir = dirname($_SERVER['SCRIPT_NAME']);
$project_path = trim($script_dir, '/');
// Get the last segment of the path as project name
$project_name = !empty($project_path) ? basename($project_path) : 'daily-habit-tracker';
// Always use http://localhost (no port, no https detection) - forces localhost without port
define('BASE_URL', 'http://localhost/' . $project_name . '/');

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'habit_tracker_cms');
define('DB_USER', 'root');
define('DB_PASS', '');

// Database connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
