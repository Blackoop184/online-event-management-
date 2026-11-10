<?php
// events.php - List all events with ordering & timestamps (Requirement 2a, 2c, 2d)
require_once 'includes/auth.php';
requireLogin();
$user = getCurrentUser($pdo);

$pageTitle = 'All Events';
$pageHeading = 'All Events';
include 'includes/header.php';

$stmt = $pdo->prepare(
    "SELECT e.*, u.full_name AS creator_name 
     FROM events e 
     JOIN users u ON e.created_by = u.id 
     ORDER BY e.event_date ASC"
);
$stmt->execute();
$events = $stmt->fetchAll();
?>

<section class="card events-card">
    <div class="card-header">
        <h2>All Events</h2>
        <?php if (isCurrentUser($user['id'])): ?>
            <a href="create_event.php" class="btn btn-primary">Create Event</a>
        <?php endif; ?>
    </div>

    <?php if ($events): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Event Date</th>
                    <th>Location</th>
                    <th>Created At</th>
                    <th>Created By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $ev): ?>
                    <tr>
                        <td><?= h($ev['title']) ?></td>
                        <td><?= h($ev['description'] ?? '-') ?></td>
                        <td><?= h(date('M d, Y \a\\t h:i A', strtotime($ev['event_date']))) ?></td>
                        <td><?= h($ev['location'] ?? '-') ?></td>
                        <td><?= h(date('M d, Y \a\\t h:i A', strtotime($ev['created_at']))) ?></td>
                        <td><?= h($ev['creator_name']) ?></td>
                        <td class="actions">
                            <?php if (isCurrentUser($ev['created_by'])): ?>
                                <a href="edit_event.php?id=<?= (int)$ev['id'] ?>" class="btn btn-sm btn-outline">Edit</a>
                                <a href="delete_event.php?id=<?= (int)$ev['id'] ?>" class="btn btn-sm btn-danger"
                                   onclick="return confirm('Delete this event?')">Delete</a>
                            <?php else: ?>
                                <span class="badge badge-view">View only</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="empty">No events found.</p>
    <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>