<?php
// messages.php - Conversation list (Requirement 4c)
require_once 'includes/auth.php';
requireLogin();
$user = getCurrentUser($pdo);

// Find distinct users the current user has messaged or received messages from
$stmt = $pdo->prepare(
    "SELECT m.*, u.full_name, u.profile_picture 
     FROM messages m
     JOIN users u ON u.id = CASE 
         WHEN m.sender_id = ? THEN m.receiver_id 
         ELSE m.sender_id 
     END
     WHERE m.sender_id = ? OR m.receiver_id = ?
     ORDER BY m.created_at DESC"
);
$stmt->execute([$user['id'], $user['id'], $user['id']]);
$rows = $stmt->fetchAll();

// Deduplicate conversations keeping latest per contact
$contacts = [];
foreach ($rows as $r) {
    $contactId = ($r['sender_id'] == $user['id']) ? $r['receiver_id'] : $r['sender_id'];
    if (!isset($contacts[$contactId]) || $r['created_at'] > $contacts[$contactId]['created_at']) {
        $contacts[$contactId] = $r;
    }
}
$contacts = array_values($contacts);

$pageTitle = 'Messages';
$pageHeading = 'Private Messages';
include 'includes/header.php';
?>

<section class="card messages-card">
    <h2>Conversations</h2>
    <?php if ($contacts): ?>
        <ul class="conversation-list">
            <?php foreach ($contacts as $c): ?>
                <li>
                    <a href="conversation.php?id=<?= (int)$c['sender_id'] == $user['id'] ? (int)$c['receiver_id'] : (int)$c['sender_id'] ?>">
                        <img src="uploads/profile_pics/<?= h($c['profile_picture']) ?>" 
                             class="avatar-sm" alt="" 
                             onerror="this.style.display='none'">
                        <div class="conv-info">
                            <span class="conv-name"><?= h($c['full_name']) ?></span>
                            <span class="conv-preview"><?= h(mb_strimwidth($c['message'], 0, 60, '...')) ?></span>
                        </div>
                        <span class="conv-time"><?= h(date('M d, h:i A', strtotime($c['created_at']))) ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="empty">No conversations yet. Search for a user and start messaging.</p>
        <p><a href="search.php" class="btn btn-outline">Find a user</a></p>
    <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>