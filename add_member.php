<?php
include_once "config/database.php";
include_once "includes/header.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database = new Database();
    $db = $database->getConnection();

    $photo_path = null;
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
        $target_dir = "images/"; // Folderul pentru imagini
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true); // Creează folderul dacă nu există
        }
        $file_name = time() . "_" . basename($_FILES['profile_photo']['name']);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $target_file)) {
            $photo_path = $target_file; // Stochează calea completă
        }
    }

    $query = "INSERT INTO members 
              (first_name, last_name, email, profession, company, linkedin_profile, profile_picture) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $db->prepare($query);

    $stmt->execute([
        $_POST['first_name'],
        $_POST['last_name'],
        $_POST['email'],
        $_POST['profession'],
        $_POST['company'],
        $_POST['linkedin_profile'],
        $photo_path
    ]);

    header("Location: members.php");
    exit();
}
?>

<div class="form-container">
    <h2>Add New Member</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>First Name</label>
            <input type="text" name="first_name" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Last Name</label>
            <input type="text" name="last_name" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Profession</label>
            <input type="text" name="profession" class="form-control">
        </div>

        <div class="form-group">
            <label>Company</label>
            <input type="text" name="company" class="form-control">
        </div>

        <div class="form-group">
            <label>LinkedIn Profile</label>
            <input type="url" name="linkedin_profile" class="form-control">
        </div>

        <div class="form-group">
            <label>Profile Photo</label>
            <input type="file" name="profile_photo" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Add Member</button>
    </form>
</div>

<?php include_once "includes/footer.php"; ?>

