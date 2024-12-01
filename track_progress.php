<?php
include_once "config/database.php";
include_once "includes/header.php";

$database = new Database();
$db = $database->getConnection();

$query = "SELECT 
            ms.session_date, 
            ms.duration_minutes, 
            ms.topic, 
            ms.status, 
            mentor.first_name AS mentor_name, 
            mentee.first_name AS mentee_name
          FROM mentorship_sessions ms
          JOIN mentorship_matches mm ON ms.match_id = mm.id
          JOIN members mentor ON mm.mentor_id = mentor.id
          JOIN members mentee ON mm.mentee_id = mentee.id";
$stmt = $db->prepare($query);
$stmt->execute();
$sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($sessions as $session): ?>
    <div>
        <h3><?php echo "{$session['mentor_name']} - {$session['mentee_name']}"; ?></h3>
        <p>Date: <?php echo $session['session_date']; ?></p>
        <p>Duration: <?php echo $session['duration_minutes']; ?> minutes</p>
        <p>Topic: <?php echo $session['topic']; ?></p>
        <p>Status: <?php echo $session['status']; ?></p>
    </div>
    <hr>
<?php endforeach; ?>

