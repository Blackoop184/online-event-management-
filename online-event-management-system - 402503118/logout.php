<?php
// logout.php - Secure logout (Requirement 1d)
require_once 'includes/auth.php';
logoutUser();
header('Location: login.php');
exit;
?>