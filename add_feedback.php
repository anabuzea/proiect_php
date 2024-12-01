<?php
include_once "config/database.php";
include_once "includes/header.php";

$database = new Database();
$db = $database->getConnection();

// Fetch completed sessions
$sessions_query = "SELECT id, topic, session_date FROM mentorship_sessions WHERE status = 'completed'";
$stmt_sessions = $db->prepare($sessions_query);
$stmt_sessions->execute();
$sessions = $stmt_sessions->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $session_id = $_POST['session_id'];
    $feedback_from = $_POST['feedback_from'];
    $rating = $_POST['rating'];
    $comments = $_POST['comments'];
    $query = "INSERT INTO session_feedback (session_id, feedback_from, rating, comments) 
              VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->execute([$session_id, $feedback_from, $rating, $comments]);
    echo "Feedback submitted successfully!";
}
?>

<h2>Submit Feedback</h2>
<form method="POST">
    <select name="session_id" required>
        <option value="">-- Select Session --</option>
        <?php foreach ($sessions as $session): ?>
            <option value="<?php echo $session['id']; ?>">
                <?php echo "{$session['topic']} - {$session['session_date']}"; ?>
            </option>
        <?php endforeach; ?>
    </select>

    <select name="feedback_from" required>
        <option value="mentor">Mentor</option>
        <option value="mentee">Mentee</option>
    </select>

    <input type="number" name="rating" min="1" max="5" required>
    <textarea name="comments" placeholder="Comments"></textarea>

    <button type="submit">Submit Feedback</button>
</form>

