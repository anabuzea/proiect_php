<?php
// Include conexiunea la baza de date și antetul
include_once "config/database.php";
include_once "includes/header.php";

// Verificăm dacă ID-ul membrului este setat
if (isset($_GET['id'])) {
    $database = new Database();
    $db = $database->getConnection();

    // Interogăm baza de date pentru a obține informațiile membrului
    $query = "SELECT * FROM members WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$_GET['id']]);
    $member = $stmt->fetch(PDO::FETCH_ASSOC);

    // Dacă membrul nu există
    if (!$member) {
        echo "<p>Member not found.</p>";
        include_once "includes/footer.php";
        exit();
    }
} else {
    echo "<p>Invalid member ID.</p>";
    include_once "includes/footer.php";
    exit();
}
?>

<div class="container mt-4">
    <h2>Profile of <?php echo htmlspecialchars($member['first_name']) . ' ' . htmlspecialchars($member['last_name']); ?></h2>
    <div class="row">
        <div class="col-md-4">
            <div class="card member-card">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($member['first_name']) . ' ' . htmlspecialchars($member['last_name']); ?></h5>
                    <p class="card-text">
                        <strong>Email:</strong> <?php echo htmlspecialchars($member['email']); ?><br>
                        <strong>Profession:</strong> <?php echo htmlspecialchars($member['profession']); ?><br>
                        <strong>Company:</strong> <?php echo htmlspecialchars($member['company']); ?><br>
                    </p>
                    <a href="edit_member.php?id=<?php echo $member['id']; ?>" class="btn btn-primary">Edit Profile</a>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <h3>Professional Information</h3>
            <p><strong>Biography:</strong> <?php echo nl2br(htmlspecialchars($member['biography'])); ?></p>
            <p><strong>Professional Experience:</strong> <?php echo nl2br(htmlspecialchars($member['professional_experience'])); ?></p>
            <p><strong>Achievements:</strong> <?php echo nl2br(htmlspecialchars($member['achievements'])); ?></p>

            <h3>LinkedIn Profile</h3>
            <p><a href="<?php echo htmlspecialchars($member['linkedin_profile']); ?>" target="_blank">View LinkedIn Profile</a></p>
        </div>
    </div>
</div>

<?php include_once "includes/footer.php"; ?>

