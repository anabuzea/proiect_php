<?php
include_once "config/database.php";
include_once "notifications.php";
include_once "includes/header.php";

// Presupunem că avem ID-ul utilizatorului autentificat
$member_id = 1; // Exemplu, acest ID trebuie să vină din sesiunea utilizatorului autentificat

// Obținem notificările pentru utilizatorul curent
$notifications = getNotifications($member_id);

// Marcam notificările ca citite
markNotificationsAsRead($member_id);
?>

<h2>Notifications</h2>

<!-- Afișăm notificările -->
<?php if (empty($notifications)): ?>
    <p>No notifications available.</p>
<?php else: ?>
    <ul class="list-group">
        <?php foreach ($notifications as $notification): ?>
            <li class="list-group-item <?php echo ($notification['read_status']) ? 'list-group-item-success' : 'list-group-item-warning'; ?>">
                <strong>Message:</strong> <?php echo htmlspecialchars($notification['message']); ?>
                <br><small><em>Created on: <?php echo $notification['created_at']; ?></em></small>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php include_once "includes/footer.php"; ?>
