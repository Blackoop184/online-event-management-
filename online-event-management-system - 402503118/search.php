<?php
// search.php - Search users and events (Requirement 3c)
require_once 'includes/auth.php';
requireLogin();
$user = getCurrentUser($pdo);

$query = trim($_GET['q'] ?? '');
$users = [];
$events = [];

if ($query !== '') {
    $like = '%' . $query . '%';

    // Search users
    $uStmt = $pdo->prepare(
        "SELECT id, full_name, email FROM users WHERE full_name LIKE ? OR email LIKE ? ORDER BY full_name"
    );
    $uStmt->execute([$like, $like]);
    $users = $uStmt->fetchAll();

    // Search events
    $eStmt = $pdo->prepare(
        "SELECT e.*, u.full_name AS creator_name FROM events e 
         JOIN users u ON e.created_by = u.id 
         WHERE e.title LIKE ? OR e.description LIKE ? 
         ORDER BY e.event_date ASC"
    );
    $eStmt->execute([$like, $like]);
    $events = $eStmt->fetchAll();
}

$pageTitle = 'Search';
$pageHeading = 'Search';
include 'includes/header.php';
?>

<div class="search-section card">
    <form action="search.php" method="GET" class="search-form">
        <input type="text" name="q" placeholder="Search users & events..." 
               value="<?= h($query) ?>" autocomplete="off">
        <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <?php if ($query !== ''): ?>
        <div class="search-results">
            <?php if ($users): ?>
                <h3>Users (<?= count($users) ?>)</h3>
                <ul class="user-list">
                    <?php foreach ($users as $u): ?>
                        <li>
                            <a href="profile.php?id=<?= (int)$u['id'] ?>"><?= h($u['full_name']) ?></a>
                            <span class="user-email"><?= h($u['email']) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <?php if ($events): ?>
                <h3>Events (<?= count($events) ?>)</h3>
                <ul class="event-list">
                    <?php foreach ($events as $ev): ?>
                        <li>
                            <strong><?= h($ev['title']) ?></strong> -
                            <?= h(date('M d, Y', strtotime($ev['event_date']))) ?>
                            <span class="event-creator">by <?= h($ev['creator_name']) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <?php if (empty($users) && empty($events)): ?>
                <p class="empty">No results found for "<?= h($query) ?>".</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>