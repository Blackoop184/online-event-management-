<?php
// register.php - User registration (Requirement 1a, 1b, 1c, 1f)
require_once 'includes/auth.php';
requireGuest();
require_once 'includes/functions.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = sanitizeInput($_POST['full_name'] ?? '');
    $email    = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    // ---- Input validation (Requirement 5c) ----
    if ($fullName === '') {
        $errors[] = 'Full name is required.';
    }
    if (!validateEmail($email)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    // Unique email validation (Requirement 1b)
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'This email is already registered.';
        }
    }

    // ---- Profile picture secure upload (Requirement 5e) ----
    $profilePic = 'default.png';
    if (!empty($errors) === false && isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $upload = uploadProfileImage($_FILES['profile_picture']);
        if (isset($upload['error'])) {
            $errors[] = $upload['error'];
        } else {
            $profilePic = $upload['filename'];
        }
    }

    // ---- Insert user with hashed password (Requirement 1c) ----
    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare(
            "INSERT INTO users (full_name, email, password, profile_picture) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$fullName, $email, $hash, $profilePic]);

        $_SESSION['flash'] = ['msg' => 'Registration successful! Please log in.', 'type' => 'success'];
        header('Location: login.php');
        exit;
    }
}

$pageTitle = 'Register';
$pageHeading = 'Create an Account';
include 'includes/header.php';
?>

<form action="register.php" method="POST" enctype="multipart/form-data" class="auth-form" novalidate>
    <div class="form-group">
        <label for="full_name">Full Name <span class="required">*</span></label>
        <input type="text" id="full_name" name="full_name" value="<?= h($_POST['full_name'] ?? '') ?>" required>
    </div>

    <div class="form-group">
        <label for="email">Email Address <span class="required">*</span></label>
        <input type="email" id="email" name="email" value="<?= h($_POST['email'] ?? '') ?>" required>
    </div>

    <div class="form-group">
        <label for="password">Password <span class="required">*</span></label>
        <input type="password" id="password" name="password" required>
    </div>

    <div class="form-group">
        <label for="confirm_password">Confirm Password <span class="required">*</span></label>
        <input type="password" id="confirm_password" name="confirm_password" required>
    </div>

    <div class="form-group">
        <label for="profile_picture">Profile Picture</label>
        <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
        <small class="hint">Optional. JPG, PNG or GIF (max 2 MB).</small>
    </div>

    <?php foreach ($errors as $err): ?>
        <div class="flash flash-error"><?= h($err) ?></div>
    <?php endforeach; ?>

    <button type="submit" class="btn btn-primary">Register</button>
    <p class="auth-switch">Already have an account? <a href="login.php">Log in</a></p>
</form>

<?php include 'includes/footer.php'; ?>