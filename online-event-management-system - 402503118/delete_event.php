<?php
// delete_event.php - Delete an event (Requirement 2b)
// POST-only, ownership access control (Requirement 5e)
require_once 'includes/auth.php';
requireLogin();
$user = getCurrentUser($pdo);

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    $_SESSION['flash'] = ['msg' => 'Invalid event ID.', 'type' => 'error'];
    header('Location: dashboard.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$id]);
$event = $stmt->fetch();

if (!$event) {
    $_SESSION['flash'] = ['msg' => 'Event not found.', 'type' => 'error'];
    header('Location: dashboard.php');
    exit;
}

// Access control: only creator can delete
if (!isCurrentUser($event['created_by'])) {
    $_SESSION['flash'] = ['msg' => 'You are not authorized to delete this event.', 'type' => 'error'];
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $del = $pdo->prepare("DELETE FROM events WHERE id = ?");
    $del->execute([$id]);
    $_SESSION['flash'] = ['msg' => 'Event deleted successfully.', 'type' => 'success'];
    header('Location: dashboard.php');
    exit;
}

// If accessed via GET, redirect back (confirmation handled via JS on the link)
header('Location: dashboard.php');
exit;
?>