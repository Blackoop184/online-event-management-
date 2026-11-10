<?php
// forgot_password.php - Forgot password functionality (Requirement 1e)
require_once 'includes/auth.php';
requireGuest();

$message = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email'] ?? '');

    if (!validateEmail($email)) {
        $errors[] = 'Please enter a valid email address.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Generate a secure reset token valid for 1 hour
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $update = $pdo->prepare(
                "UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?"
            );
            $update->execute([$token, $expires, $user['id']]);

            // NOTE: In production send this link via email. For this assignment
            // we display it directly so the reset flow can be demonstrated.
            $message = 'A reset link has been generated for this account: ' .
                '<a href="reset_password.php?token=' . h($token) . '">' .
                'reset_password.php?token=' . h($token) . '</a>';
        } else {
            // Don't reveal whether email exists (security best practice)
            $message = 'If an account exists for that email, a reset link has been generated.';
        }
    }
}

$pageTitle = 'Forgot Password';
$pageHeading = 'Forgot Password';
include 'includes/header.php';
?>

<form action="forgot_password.php" method="POST" class="auth-form" novalidate>
    <div class="form-group">
        <label for="email">Email Address <span class="required">*</span></label>
        <input type="email" id="email" name="email" required>
    </div>

    <?php foreach ($errors as $err): ?>
        <div class="flash flash-error"><?= h($err) ?></div>
    <?php endforeach; ?>

    <?php if ($message): ?>
        <div class="flash flash-success"><?= $message ?></div>
    <?php endif; ?>

    <button type="submit" class="btn btn-primary">Send Reset Link</button>
    <p class="auth-switch">Remember your password? <a href="login.php">Log in</a></p>
</form>

<?php include 'includes/footer.php'; ?>