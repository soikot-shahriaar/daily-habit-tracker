<?php
session_start();

require_once 'config.php';

// Destroy all session data
session_destroy();

// Redirect to login page
header("Location: " . BASE_URL . "login.php");
exit();
?>
