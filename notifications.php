<?php
include_once "config/database.php";

// Funcție pentru a adăuga o notificare
function addNotification($member_id, $message) {
    global $db;

    $query = "INSERT INTO notifications (member_id, message) VALUES (:member_id, :message)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':member_id', $member_id, PDO::PARAM_INT);
    $stmt->bindParam(':message', $message, PDO::PARAM_STR);

    return $stmt->execute();
}

// Funcție pentru a obține notificările pentru un membru
function getNotifications($member_id) {
    global $db;

    $query = "SELECT * FROM notifications WHERE member_id = :member_id ORDER BY created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':member_id', $member_id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Funcție pentru a marca notificările ca citite
function markNotificationsAsRead($member_id) {
    global $db;

    $query = "UPDATE notifications SET read_status = TRUE WHERE member_id = :member_id AND read_status = FALSE";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':member_id', $member_id, PDO::PARAM_INT);

    return $stmt->execute();
}
?>
