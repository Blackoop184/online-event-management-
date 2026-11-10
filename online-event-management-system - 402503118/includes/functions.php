<?php
// functions.php - Shared helper functions
// XSS protection via htmlspecialchars() on all output (Requirement 5b)
// Input sanitization & secure file uploads (Requirements 5c, 5e)

require_once 'db.php';

/**
 * Escape output for HTML to prevent XSS (Requirement 5b).
 */
function h($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Sanitize incoming input: trim, strip slashes, encode special chars.
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Validate an email address format.
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Securely handle profile picture uploads (Requirement 5e).
 * Validates extension, MIME type, and size before moving the file.
 * Returns ['success' => true, 'filename' => '...'] or ['error' => 'message'].
 */
function uploadProfileImage($file) {
    $allowedExts  = ['jpg', 'jpeg', 'png', 'gif'];
    $allowedMimes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxSize      = 2 * 1024 * 1024; // 2 MB

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowedExts)) {
        return ['error' => 'Invalid file type. Allowed: JPG, JPEG, PNG, GIF.'];
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowedMimes)) {
        return ['error' => 'Uploaded file is not a valid image.'];
    }

    if ($file['size'] > $maxSize) {
        return ['error' => 'File too large. Maximum size is 2 MB.'];
    }

    // Generate a unique filename to avoid collisions & overwrite attacks
    $filename = uniqid('profile_', true) . '.' . $ext;
    $destination = __DIR__ . '/../uploads/profile_pics/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return ['error' => 'Failed to save uploaded file.'];
    }

    return ['success' => true, 'filename' => $filename];
}

/**
 * Redirect helper with optional message stored in session.
 */
function redirect($url, $message = null, $type = 'info') {
    if ($message !== null) {
        $_SESSION['flash'] = ['msg' => $message, 'type' => $type];
    }
    header("Location: $url");
    exit;
}

/**
 * Display a flash message if one exists, then clear it.
 */
function showFlash() {
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        echo '<div class="flash flash-' . h($flash['type']) . '">' . h($flash['msg']) . '</div>';
    }
}
?>