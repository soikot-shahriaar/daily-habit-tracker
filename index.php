<?php
// Main entry point - redirect directly to login
require_once 'config.php';
header("Location: " . BASE_URL . "login.php");
exit();
?>
