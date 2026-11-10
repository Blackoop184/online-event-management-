<?php
// create_event.php - Create a new event (Requirement 2b, 2c)
require_once 'includes/auth.php';
requireLogin();
$user = getCurrentUser($pdo);

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
        $stmt = $pdo->prepare(
            "INSERT INTO events (title, description, event_date, location, created_by) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$title, $desc, $eventDate, $location, $user['id']]);

        $_SESSION['flash'] = ['msg' => 'Event created successfully.', 'type' => 'success'];
        header('Location: dashboard.php');
        exit;
    }
}

$pageTitle = 'Create Event';
$pageHeading = 'Create New Event';
include 'includes/header.php';
?>

<form action="create_event.php" method="POST" class="event-form" novalidate>
    <div class="form-group">
        <label for="title">Event Title <span class="required">*</span></label>
        <input type="text" id="title" name="title" value="<?= h($_POST['title'] ?? '') ?>" required>
    </div>

    <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="4"><?= h($_POST['description'] ?? '') ?></textarea>
    </div>

    <div class="form-group">
        <label for="event_date">Date & Time <span class="required">*</span></label>
        <input type="datetime-local" id="event_date" name="event_date" value="<?= h($_POST['event_date'] ?? '') ?>" required>
    </div>

    <div class="form-group">
        <label for="location">Location</label>
        <input type="text" id="location" name="location" value="<?= h($_POST['location'] ?? '') ?>">
    </div>

    <?php foreach ($errors as $err): ?>
        <div class="flash flash-error"><?= h($err) ?></div>
    <?php endforeach; ?>

    <button type="submit" class="btn btn-primary">Create Event</button>
    <a href="dashboard.php" class="btn btn-outline">Cancel</a>
</form>

<?php include 'includes/footer.php'; ?>