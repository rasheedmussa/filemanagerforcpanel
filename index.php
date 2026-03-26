<?php
// index.php - Main entry point
require_once 'config.php';

// Go directly to file manager (no authentication needed)
header('Location: dashboard.php');
exit;
?>