<?php
// index.php - Entry point: redirect logged-in users to dashboard, others to login
require_once 'includes/auth.php';
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
} else {
    header('Location: login.php');
}
exit;
?>