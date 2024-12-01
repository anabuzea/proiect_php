<?php
session_start();
include_once "config/database.php";
include_once "includes/header.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Preluăm datele introduse în formular
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Conectăm la baza de date
    $database = new Database();
    $db = $database->getConnection();

    // Verificăm dacă există un utilizator cu emailul și parola date
    $query = "SELECT * FROM members WHERE email = ? AND password = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$email, md5($password)]); // md5 pentru criptare minimă (ideal: password_hash)

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Salvăm datele utilizatorului în sesiune
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['first_name'] = $user['first_name'];

        // Redirecționăm utilizatorul către dashboard
        header('Location: dashboard.php');
        exit();
    } else {
        $error_message = "Email or password incorrect!";
    }
}
?>


<div class="form-container">
    <h2>Login</h2>
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register here</a></p>
</div>



