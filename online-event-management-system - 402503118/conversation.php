<?php
// conversation.php - Conversation view with access control (Requirement 4c, 4d)
require_once 'includes/auth.php';
requireLogin();
$user = getCurrentUser($pdo);

$otherId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$otherId || $otherId == $user['id']) {
    $_SESSION['flash'] = ['msg' => 'Invalid user.', 'type' => 'error'];
    header('Location: messages.php');
    exit;
}

// Verify the other user exists
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$otherId]);
$otherUser = $stmt->fetch();
if (!$otherUser) {
    $_SESSION['flash'] = ['msg' => 'User not found.', 'type' => 'error'];
    header('Location: messages.php');
    exit;
}

// Access control: mark messages from this user as read
$markRead = $pdo->prepare(
    "UPDATE messages SET is_read = 1 WHERE receiver_id = ? AND sender_id = ? AND is_read = 0"
);
$markRead->execute([$user['id'], $otherId]);

// Fetch conversation ordered by time
$msgStmt = $pdo->prepare(
    "SELECT m.*, u.full_name AS sender_name 
     FROM messages m 
     JOIN users u ON m.sender_id = u.id 
     WHERE (m.sender_id = ? AND m.receiver_id = ?) 
        OR (m.sender_id = ? AND m.receiver_id = ?)
     ORDER BY m.created_at ASC"
);
$msgStmt->execute([$user['id'], $otherId, $otherId, $user['id']]);
$messages = $msgStmt->fetchAll();

$pageTitle = 'Conversation';
$pageHeading = 'Conversation with ' . h($otherUser['full_name']);
include 'includes/header.php';
?>

<section class="card conv-card">
    <div class="conv-header">
        <a href="messages.php" class="btn btn-sm btn-outline">← Back</a>
        <h2><?= h($otherUser['full_name']) ?></h2>
        <a href="profile.php?id=<?= (int)$otherUser['id'] ?>" class="btn btn-sm btn-outline">View Profile</a>
    </div>

    <div class="conv-messages">
        <?php if ($messages): ?>
            <?php foreach ($messages as $m): ?>
                <div class="message <?= isCurrentUser($m['sender_id']) ? 'message-sent' : 'message-received' ?>">
                    <div class="message-content"><?= h($m['message']) ?></div>
                    <div class="message-meta">
                        <?= h(isCurrentUser($m['sender_id']) ? 'You' : $m['sender_name']) ?> •
                        <?= h(date('M d, h:i A', strtotime($m['created_at']))) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="empty">No messages yet. Start the conversation!</p>
        <?php endif; ?>

        <!-- Message send form -->
        <form id="sendMessageForm" action="send_message.php" method="POST">
            <input type="hidden" name="receiver_id" value="<?= (int)$otherId ?>">
            <div class="message-input">
                <input type="text" name="message" id="newMessage" placeholder="Type a message..." required autocomplete="off">
                <button type="submit" class="btn btn-primary">Send</button>
            </div>
        </form>
    </div>
</section>

<?php include 'includes/footer.php'; ?>