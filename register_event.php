<?php
include_once "config/database.php";

if (isset($_GET['event_id'])) {
    $eventId = $_GET['event_id'];
    $memberId = 1; // Exemplu de utilizator curent, înlocuiește cu sesiunea activă.

    $database = new Database();
    $db = $database->getConnection();

    // Inserarea înregistrării
    $query = "INSERT INTO event_registrations (member_id, event_id) VALUES (?, ?)";
    $stmt = $db->prepare($query);

    try {
        $stmt->execute([$memberId, $eventId]);
        header("Location: events.php?status=registered");
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

