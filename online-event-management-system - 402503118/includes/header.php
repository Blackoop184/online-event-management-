<?php
// header.php - Shared HTML header & navigation
// Session must already be started by auth.php on each page.

require_once 'auth.php';
$user = getCurrentUser($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? h($pageTitle) : 'Event Management' ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="site-header">
        <div class="container header-inner">
            <a href="dashboard.php" class="logo">Event<span>Manage</span></a>
            <nav class="main-nav">
                <?php if ($user): ?>
                    <a href="dashboard.php">Dashboard</a>
                    <a href="events.php">Events</a>
                    <a href="create_event.php">Create Event</a>
                    <a href="profile.php?id=<?= (int)$user['id'] ?>">Profile</a>
                    <a href="messages.php">Messages</a>
                    <a href="search.php">Search</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="register.php">Register</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="container">
        <?php showFlash(); ?>
        <?php if (isset($pageHeading)): ?>
            <h1><?= h($pageHeading) ?></h1>
        <?php endif; ?>