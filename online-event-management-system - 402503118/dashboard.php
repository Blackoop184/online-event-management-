<?php
// dashboard.php - Dashboard & upcoming events (Requirement 2a)
require_once 'includes/auth.php';
requireLogin();
$user = getCurrentUser($pdo);

$pageTitle = 'Dashboard';
$pageHeading = 'Dashboard';
include 'includes/header.php';

// Fetch upcoming events (event_date >= now), ordered correctly by date (Requirement 2d)
$stmt = $pdo->prepare(
    "SELECT e.*, u.full_name AS creator_name 
     FROM events e 
     JOIN users u ON e.created_by = u.id 
     WHERE e.event_date >= NOW() 
     ORDER BY e.event_date ASC"
);
$stmt->execute();
$events = $stmt->fetchAll();
?>

<div class="dashboard-grid">
    <section class="card events-card">
        <h2>Upcoming Events</h2>
        <?php if ($events): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Date & Time</th>
                        <th>Location</th>
                        <th>Created By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($events as $ev): ?>
                        <tr>
                            <td><?= h($ev['title']) ?></td>
                            <td><?= h(date('M d, Y \a\\t h:i A', strtotime($ev['event_date']))) ?></td>
                            <td><?= h($ev['location'] ?? '-') ?></td>
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
            <p class="empty">No upcoming events scheduled.</p>
        <?php endif; ?>
    </section>

    <section class="card quick-actions">
        <h2>Quick Actions</h2>
        <ul class="action-list">
            <li><a href="create_event.php" class="btn btn-primary btn-block">Create New Event</a></li>
            <li><a href="events.php" class="btn btn-outline btn-block">View All Events</a></li>
            <li><a href="profile.php?id=<?= (int)$user['id'] ?>" class="btn btn-outline btn-block">My Profile</a></li>
            <li><a href="messages.php" class="btn btn-outline btn-block">My Messages</a></li>
        </ul>
    </section>
</div>

<?php include 'includes/footer.php'; ?>