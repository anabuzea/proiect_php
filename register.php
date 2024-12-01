<?php
session_start();
include_once "config/database.php";
include_once "includes/header.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];

    $database = new Database();
    $db = $database->getConnection();

    // Verificăm dacă utilizatorul există deja
    $query = "SELECT * FROM members WHERE email = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$email]);
    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "Email already in use!";
    } else {
        // Înregistrăm utilizatorul nou
        $query = "INSERT INTO members (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->execute([$first_name, $last_name, $email, md5($password), 'member']);
        echo "Registration successful! <a href='login.php'>Login here</a>";
    }
}
?>

<div class="form-container">
    <h2>Register</h2>
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label>Name:</label>
            <input type="text" name="first_name" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Last name:</label>
            <input type="text" name="last_name" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Profession</label>
            <input type="text" name="profession" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Company</label>
            <input type="text" name="company" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="role">Role</label>
            <div class="radio-group">
                <label>
                    <input type="radio" name="role" value="member" required> Member
                </label>
                <label>
                    <input type="radio" name="role" value="mentor"> Mentor
                </label>
                <label>
                    <input type="radio" name="role" value="admin"> Admin
                </label>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
    <p>Already have an account? <a href="login.php">Login</a></p>
</div>

<?php include_once "includes/footer.php"; ?>




