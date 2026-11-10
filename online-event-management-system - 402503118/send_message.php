<?php
// send_message.php - Send a private message (Requirement 4a, 4b)
// Stores message in a separate messages table with access control (Requirement 4d)
require_once 'includes/auth.php';
requireLogin();
$user = getCurrentUser($pdo);

$receiverId = filter_input(INPUT_POST, 'receiver_id', FILTER_VALIDATE_INT);
$message    = trim($_POST['message'] ?? '');

if (!$receiverId || $receiverId == $user['id'] || $message === '') {
    header('Location: messages.php');
    exit;
}

// Verify receiver exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
$stmt->execute([$receiverId]);
if (!$stmt->fetch()) {
    $_SESSION['flash'] = ['msg' => 'User not found.', 'type' => 'error'];
    header('Location: messages.php');
    exit;
}

// Insert into separate messages table (Requirement 4b)
$insert = $pdo->prepare(
    "INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)"
);
$insert->execute([$user['id'], $receiverId, $message]);

header('Location: conversation.php?id=' . (int)$receiverId);
exit;
?>