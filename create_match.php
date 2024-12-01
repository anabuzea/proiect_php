<?php
include_once "config/database.php";
include_once "includes/header.php";

$database = new Database();
$db = $database->getConnection();

// Fetch mentors
$mentors_query = "SELECT id, first_name, last_name FROM members WHERE role = 'mentor' AND status = 'active'";
$stmt_mentors = $db->prepare($mentors_query);
$stmt_mentors->execute();
$mentors = $stmt_mentors->fetchAll(PDO::FETCH_ASSOC);

// Fetch mentees
$mentees_query = "SELECT id, first_name, last_name FROM members WHERE role = 'mentee' AND status = 'active'";
$stmt_mentees = $db->prepare($mentees_query);
$stmt_mentees->execute();
$mentees = $stmt_mentees->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mentor_id = $_POST['mentor_id'];
    $mentee_id = $_POST['mentee_id'];
    $query = "INSERT INTO mentorship_matches (mentor_id, mentee_id, status) VALUES (?, ?, 'pending')";
    $stmt = $db->prepare($query);
    $stmt->execute([$mentor_id, $mentee_id]);
    echo "Match created successfully!";
}
?>

<h2>Create Mentorship Match</h2>
<form method="POST">
    <select name="mentor_id" required>
        <option value="">-- Select Mentor --</option>
        <?php foreach ($mentors as $mentor): ?>
            <option value="<?php echo $mentor['id']; ?>">
                <?php echo "{$mentor['first_name']} {$mentor['last_name']}"; ?>
            </option>
        <?php endforeach; ?>
    </select>

    <select name="mentee_id" required>
        <option value="">-- Select Mentee --</option>
        <?php foreach ($mentees as $mentee): ?>
            <option value="<?php echo $mentee['id']; ?>">
                <?php echo "{$mentee['first_name']} {$mentee['last_name']}"; ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Create Match</button>
</form>
