<?php
// profile.php - Profile display & view-only access (Requirement 3a, 3d)
require_once 'includes/auth.php';
requireLogin();
$user = getCurrentUser($pdo);

$viewId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$viewId) {
    $_SESSION['flash'] = ['msg' => 'Invalid user ID.', 'type' => 'error'];
    header('Location: dashboard.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$viewId]);
$profile = $stmt->fetch();

if (!$profile) {
    $_SESSION['flash'] = ['msg' => 'User not found.', 'type' => 'error'];
    header('Location: dashboard.php');
    exit;
}

$isOwnProfile = isCurrentUser($profile['id']);
$pageTitle = 'Profile';
$pageHeading = $isOwnProfile ? 'My Profile' : h($profile['full_name']) . '\'s Profile';
include 'includes/header.php';

// Fetch events created by this user (upcoming, ordered)
$events = [];
$evtStmt = $pdo->prepare(
    "SELECT * FROM events WHERE created_by = ? AND event_date >= NOW() ORDER BY event_date ASC"
);
$evtStmt->execute([$profile['id']]);
$events = $evtStmt->fetchAll();
?>

<section class="card profile-card">
    <div class="profile-header">
        <img src="uploads/profile_pics/<?= h($profile['profile_picture']) ?>" 
             alt="Profile picture" class="profile-avatar"
             onerror="this.src='css/images/default.png'">
        <div>
            <h2><?= h($profile['full_name']) ?></h2>
            <p class="profile-email"><?= h($profile['email']) ?></p>
            <p class="profile-joined">Member since <?= h(date('M Y', strtotime($profile['created_at']))) ?></p>
        </div>
        <?php if ($isOwnProfile): ?>
            <a href="edit_profile.php" class="btn btn-primary">Edit Profile</a>
        <?php else: ?>
            <span class="badge badge-view">View-only profile</span>
            <a href="conversation.php?id=<?= (int)$profile['id'] ?>" class="btn btn-outline">Message</a>
        <?php endif; ?>
    </div>

    <h3>Events Created</h3>
    <?php if ($events): ?>
        <table class="data-table">
            <thead>
                <tr><th>Event</th><th>Date & Time</th><th>Location</th></tr>
            </thead>
            <tbody>
                <?php foreach ($events as $ev): ?>
                    <tr>
                        <td><?= h($ev['title']) ?></td>
                        <td><?= h(date('M d, Y \a\\t h:i A', strtotime($ev['event_date']))) ?></td>
                        <td><?= h($ev['location'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="empty">No upcoming events created by this user.</p>
    <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>