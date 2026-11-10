<?php
// login.php - Secure login & logout entry (Requirement 1d, 1f)
require_once 'includes/auth.php';
requireGuest();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $errors[] = 'Please enter both email and password.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // password_verify() checks against password_hash() (Requirement 1c)
        if ($user && password_verify($password, $user['password'])) {
            loginUser($user['id']);
            $redirect = $_SESSION['redirect_url'] ?? 'dashboard.php';
            unset($_SESSION['redirect_url']);
            header("Location: $redirect");
            exit;
        } else {
            $errors[] = 'Invalid email or password.';
        }
    }
}

$pageTitle = 'Login';
$pageHeading = 'Welcome Back';
include 'includes/header.php';
?>

<form action="login.php" method="POST" class="auth-form" novalidate>
    <div class="form-group">
        <label for="email">Email Address <span class="required">*</span></label>
        <input type="email" id="email" name="email" required>
    </div>

    <div class="form-group">
        <label for="password">Password <span class="required">*</span></label>
        <input type="password" id="password" name="password" required>
    </div>

    <?php foreach ($errors as $err): ?>
        <div class="flash flash-error"><?= h($err) ?></div>
    <?php endforeach; ?>

    <button type="submit" class="btn btn-primary">Log in</button>
    <p class="auth-switch">Forgot your password? <a href="forgot_password.php">Reset it</a></p>
    <p class="auth-switch">Don't have an account? <a href="register.php">Register</a></p>
</form>

<?php include 'includes/footer.php'; ?>