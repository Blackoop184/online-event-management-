<?php
// edit_event.php - Edit an existing event (Requirement 2b)
// Restricted page protection + ownership access control (Requirement 5e)
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

// Access control: only creator can edit
if (!isCurrentUser($event['created_by'])) {
    $_SESSION['flash'] = ['msg' => 'You are not authorized to edit this event.', 'type' => 'error'];
    header('Location: dashboard.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title     = sanitizeInput($_POST['title'] ?? '');
    $desc      = sanitizeInput($_POST['description'] ?? '');
    $eventDate = $_POST['event_date'] ?? '';
    $location  = sanitizeInput($_POST['location'] ?? '');

    if ($title === '') {
        $errors[] = 'Event title is required.';
    }
    if ($eventDate === '') {
        $errors[] = 'Event date is required.';
    }

    if (empty($errors)) {
        $update = $pdo->prepare(
            "UPDATE events SET title = ?, description = ?, event_date = ?, location = ? WHERE id = ?"
        );
        $update->execute([$title, $desc, $eventDate, $location, $id]);

        $_SESSION['flash'] = ['msg' => 'Event updated successfully.', 'type' => 'success'];
        header('Location: dashboard.php');
        exit;
    }
}

$pageTitle = 'Edit Event';
$pageHeading = 'Edit Event';
include 'includes/header.php';
?>

<form action="edit_event.php?id=<?= (int)$id ?>" method="POST" class="event-form" novalidate>
    <div class="form-group">
        <label for="title">Event Title <span class="required">*</span></label>
        <input type="text" id="title" name="title" value="<?= h($event['title']) ?>" required>
    </div>

    <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="4"><?= h($event['description'] ?? '') ?></textarea>
    </div>

    <div class="form-group">
        <label for="event_date">Date & Time <span class="required">*</span></label>
        <!-- Convert DATETIME to datetime-local format -->
        <input type="datetime-local" id="event_date" name="event_date"
               value="<?= h(date('Y-m-d\TH:i', strtotime($event['event_date']))) ?>" required>
    </div>

    <div class="form-group">
        <label for="location">Location</label>
        <input type="text" id="location" name="location" value="<?= h($event['location'] ?? '') ?>">
    </div>

    <?php foreach ($errors as $err): ?>
        <div class="flash flash-error"><?= h($err) ?></div>
    <?php endforeach; ?>

    <button type="submit" class="btn btn-primary">Update Event</button>
    <a href="dashboard.php" class="btn btn-outline">Cancel</a>
</form>

<?php include 'includes/footer.php'; ?>