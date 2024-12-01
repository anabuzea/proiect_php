<?php
include_once "config/database.php";
include_once "includes/header.php";

$database = new Database();
$db = $database->getConnection();

// Fetch active matches
$matches_query = "SELECT mm.id, 
                         mentor.first_name AS mentor_name, 
                         mentee.first_name AS mentee_name 
                  FROM mentorship_matches mm
                  JOIN members mentor ON mm.mentor_id = mentor.id
                  JOIN members mentee ON mm.mentee_id = mentee.id
                  WHERE mm.status = 'active'";
$stmt_matches = $db->prepare($matches_query);
$stmt_matches->execute();
$matches = $stmt_matches->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $match_id = $_POST['match_id'];
    $session_date = $_POST['session_date'];
    $duration_minutes = $_POST['duration_minutes'];
    $topic = $_POST['topic'];
    $query = "INSERT INTO mentorship_sessions (match_id, session_date, duration_minutes, topic, status) 
              VALUES (?, ?, ?, ?, 'scheduled')";
    $stmt = $db->prepare($query);
    $stmt->execute([$match_id, $session_date, $duration_minutes, $topic]);
    echo "Session scheduled successfully!";
}
?>

<h2>Schedule Mentorship Session</h2>
<form method="POST">
    <select name="match_id" required>
        <option value="">-- Select Match --</option>
        <?php foreach ($matches as $match): ?>
            <option value="<?php echo $match['id']; ?>">
                Mentor: <?php echo $match['mentor_name']; ?> - Mentee: <?php echo $match['mentee_name']; ?>
            </option>
        <?php endforeach; ?>
    </select>

    <input type="datetime-local" name="session_date" required>
    <input type="number" name="duration_minutes" placeholder="Duration (minutes)" required>
    <input type="text" name="topic" placeholder="Topic" required>

    <button type="submit">Schedule Session</button>
</form>

