<?php
include_once "config/database.php";
include_once "includes/header.php";

if (isset($_GET['member_id'])) {
    $database = new Database();
    $db = $database->getConnection();

    $currentMemberId = 1; // Exemplu ID al utilizatorului curent (înlocuit cu sesiunea)
    $newConnectionId = $_GET['member_id'];

    $query = "INSERT INTO connections (member1_id, member2_id) VALUES (?, ?)";
    $stmt = $db->prepare($query);
    $stmt->execute([$currentMemberId, $newConnectionId]);

    header("Location: dashboard.php");
    exit();
}
?>

