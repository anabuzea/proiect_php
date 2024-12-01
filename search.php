<?php
include_once "config/database.php";
include_once "includes/header.php";

// Conectarea la baza de date
$database = new Database();
$db = $database->getConnection();

// Obținerea valorii din formularul de căutare
$searchQuery = $_GET['search'] ?? ''; // Căutare după nume, profesie, etc.
$searchResults = [];

// Dacă există un termen de căutare
if (!empty($searchQuery)) {
    // Query pentru căutarea membrilor
    $query = "SELECT * FROM members WHERE first_name LIKE :search OR last_name LIKE :search OR profession LIKE :search";
    $stmt = $db->prepare($query);
    $searchTerm = "%$searchQuery%";
    $stmt->bindParam(':search', $searchTerm);
    $stmt->execute();

    // Obținerea rezultatelor
    $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<h2>Search Members</h2>

<!-- Formular de căutare -->
<form method="GET" action="search.php" class="form-inline mb-3">
    <input type="text" name="search" class="form-control mr-2" placeholder="Search by name or profession" value="<?php echo htmlspecialchars($searchQuery); ?>">
    <button type="submit" class="btn btn-primary">Search</button>
</form>

<?php if ($searchQuery && empty($searchResults)): ?>
    <p>No results found for "<?php echo htmlspecialchars($searchQuery); ?>"</p>
<?php endif; ?>

<!-- Afișarea rezultatelor -->
<?php if (!empty($searchResults)): ?>
    <div class="row">
        <?php foreach ($searchResults as $row): ?>
            <div class="col-md-4">
                <div class="card member-card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></h5>
                        <p class="card-text">
                            <strong>Profession:</strong> <?php echo htmlspecialchars($row['profession']); ?><br>
                            <strong>Company:</strong> <?php echo htmlspecialchars($row['company']); ?>
                        </p>
                        <a href="edit_member.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">Edit</a>
                        <a href="delete_member.php?id=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include_once "includes/footer.php"; ?>
