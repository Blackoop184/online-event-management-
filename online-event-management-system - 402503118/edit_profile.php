<?php
// edit_profile.php - Update profile image and name (Requirement 3b)
require_once 'includes/auth.php';
requireLogin();
$user = getCurrentUser($pdo);

$errors = [];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = sanitizeInput($_POST['full_name'] ?? '');

    if ($fullName === '') {
        $errors[] = 'Full name is required.';
    }

    // Secure profile picture upload (Requirement 5e)
    $newPic = $user['profile_picture'];
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $upload = uploadProfileImage($_FILES['profile_picture']);
        if (isset($upload['error'])) {
            $errors[] = $upload['error'];
        } else {
            $newPic = $upload['filename'];
        }
    }

    if (empty($errors)) {
        $update = $pdo->prepare(
            "UPDATE users SET full_name = ?, profile_picture = ? WHERE id = ?"
        );
        $update->execute([$fullName, $newPic, $user['id']]);

        $_SESSION['flash'] = ['msg' => 'Profile updated successfully.', 'type' => 'success'];
        header('Location: profile.php?id=' . (int)$user['id']);
        exit;
    }
}

$pageTitle = 'Edit Profile';
$pageHeading = 'Edit Profile';
include 'includes/header.php';
?>

<form action="edit_profile.php" method="POST" enctype="multipart/form-data" class="auth-form" novalidate>
    <div class="form-group">
        <label for="full_name">Full Name <span class="required">*</span></label>
        <input type="text" id="full_name" name="full_name" value="<?= h($user['full_name']) ?>" required>
    </div>

    <div class="form-group">
        <label for="profile_picture">Profile Picture</label>
        <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
        <small class="hint">Optional. JPG, PNG or GIF (max 2 MB).</small>
        <div class="current-avatar" style="margin-top:10px;">
            <strong>Current:</strong><br>
            <img src="uploads/profile_pics/<?= h($user['profile_picture']) ?>" 
                 alt="Current profile picture" width="80" height="80"
                 style="border-radius:50%; object-fit:cover;" 
                 onerror="this.style.display='none'">
        </div>
    </div>

    <?php foreach ($errors as $err): ?>
        <div class="flash flash-error"><?= h($err) ?></div>
    <?php endforeach; ?>

    <button type="submit" class="btn btn-primary">Save Changes</button>
    <a href="profile.php?id=<?= (int)$user['id'] ?>" class="btn btn-outline">Cancel</a>
</form>

<?php include 'includes/footer.php'; ?>