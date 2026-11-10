<?php
// reset_password.php - Reset password using token (Requirement 1e)
require_once 'includes/auth.php';
requireGuest();

$token = $_GET['token'] ?? '';
$errors = [];

if ($token === '') {
    header('Location: forgot_password.php');
    exit;
}

// Verify token exists and has not expired
$stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expires > NOW()");
$stmt->execute([$token]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION['flash'] = ['msg' => 'Invalid or expired reset token.', 'type' => 'error'];
    header('Location: forgot_password.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $update = $pdo->prepare(
            "UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?"
        );
        $update->execute([$hash, $user['id']]);

        $_SESSION['flash'] = ['msg' => 'Password reset successful. Please log in.', 'type' => 'success'];
        header('Location: login.php');
        exit;
    }
}

$pageTitle = 'Reset Password';
$pageHeading = 'Reset Your Password';
include 'includes/header.php';
?>

<form action="reset_password.php?token=<?= h($token) ?>" method="POST" class="auth-form" novalidate>
    <div class="form-group">
        <label for="password">New Password <span class="required">*</span></label>
        <input type="password" id="password" name="password" required>
    </div>

    <div class="form-group">
        <label for="confirm_password">Confirm New Password <span class="required">*</span></label>
        <input type="password" id="confirm_password" name="confirm_password" required>
    </div>

    <?php foreach ($errors as $err): ?>
        <div class="flash flash-error"><?= h($err) ?></div>
    <?php endforeach; ?>

    <button type="submit" class="btn btn-primary">Reset Password</button>
    <p class="auth-switch">Back to <a href="login.php">Login</a></p>
</form>

<?php include 'includes/footer.php'; ?>