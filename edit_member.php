<?php
include_once "config/database.php";
include_once "includes/header.php";

$database = new Database();
$db = $database->getConnection();

$member_id = isset($_GET['id']) ? $_GET['id'] : die("Member ID not provided");

$query = "SELECT * FROM members WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$_GET['id']]);
$member = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $photo_path = $member['profile_picture']; // Imaginea existentă

    // Gestionarea unui nou fișier încărcat
    if (isset($_FILES['profile_photo']) && $_FILES['profile_picture']['error'] == 0) {
        $target_dir = "images/";
        $file_name = time() . "_" . basename($_FILES['profile_picture']['name']);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
            $photo_path = $target_file; // Actualizează calea imaginii
        }
    }

    $query = "UPDATE members
              SET first_name = ?, 
                  last_name = ?, 
                  email = ?, 
                  profession = ?, 
                  company = ?,
                  linkedin_profile = ?,
                  profile_picture = ?
              WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([
        $_POST['first_name'],
        $_POST['last_name'],
        $_POST['email'],
        $_POST['profession'],
        $_POST['company'],
        $_POST['linkedin_profile'],
        $photo_path,
        $member_id
    ]);

    header("Location: members.php");
    exit();
}

?>


<div class="form-container">
    <h2>Edit Member</h2>
    <form method="POST">
        <div class="form-group">
            <label>First Name</label>
            <input type="text" name="first_name" class="form-control"
                   value="<?php echo htmlspecialchars($member['first_name']); ?>" required>
        </div>

        <div class="form-group">
            <label>Last Name</label>
            <input type="text" name="last_name" class="form-control"
                   value="<?php echo htmlspecialchars($member['last_name']); ?>" required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control"
                   value="<?php echo htmlspecialchars($member['email']); ?>" required>
        </div>

        <div class="form-group">
            <label>Profession</label>
            <input type="text" name="profession" class="form-control"
                   value="<?php echo htmlspecialchars($member['profession']); ?>">
        </div>

        <div class="form-group">
            <label>Company</label>
            <input type="text" name="company" class="form-control"
                   value="<?php echo htmlspecialchars($member['company']); ?>">
        </div>

        <div class="form-group">
            <label>LinkedIn Profile</label>
            <input type="url" name="linkedin_profile" class="form-control"
                   value="<?php echo htmlspecialchars($member['linkedin_profile']); ?>">
        </div>

        <div class="form-group">
            <label>Profile Photo</label>
            <input type="file" name="profile_photo" class="form-control">
            <?php if (!empty($member['profile_photo'])): ?>
                <p>Current Photo: <img src="<?php echo htmlspecialchars($member['profile_photo']); ?>" width="100"></p>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary">Update Member</button>
    </form>
</div>

<?php include_once "includes/footer.php"; ?>

