<?php
// auth.php - Session handling & authentication helpers (Requirement 1f)
// Uses $_SESSION to track logged-in users across requests.

require_once 'db.php';

// Start or resume the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Require the user to be logged in; redirect to login otherwise.
 */
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        // Remember where they were trying to go
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        header('Location: login.php');
        exit;
    }
}

/**
 * Require the user to be logged out (guest only); redirect to dashboard otherwise.
 */
function requireGuest() {
    if (isset($_SESSION['user_id'])) {
        header('Location: dashboard.php');
        exit;
    }
}

/**
 * Get the currently logged-in user record from the database.
 * Returns null if not logged in or user not found.
 */
function getCurrentUser($pdo) {
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

/**
 * Log the current user in by storing their id in the session.
 */
function loginUser($userId) {
    $_SESSION['user_id'] = (int) $userId;
    $_SESSION['logged_in'] = true;
    // Regenerate session ID to prevent session fixation attacks
    session_regenerate_id(true);
}

/**
 * Log the user out by destroying the session.
 */
function logoutUser() {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}

/**
 * Check whether a given user id belongs to the logged-in user.
 */
function isCurrentUser($userId) {
    return isset($_SESSION['user_id']) && $_SESSION['user_id'] == $userId;
}
?>