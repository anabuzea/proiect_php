<?php
include_once "config/database.php";
include_once "includes/header.php";

$database = new Database();
$db = $database->getConnection();

// Interogarea pentru a obține membrii activi
$query = "SELECT id, first_name, last_name FROM members WHERE status = 'active'";
$stmt = $db->prepare($query);
$stmt->execute();
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sort = isset($_GET['sort']) ? $_GET['sort'] : 'created_at';
$professionFilter = $_GET['profession'] ?? ''; // Profesie selectată pentru filtrare
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1; // Pagină curentă (implicit 1)
$recordsPerPage = 5; // Număr de înregistrări pe pagină

$allowedSortFields = ['created_at', 'first_name']; // Listează câmpurile permise pentru sortare
if (!in_array($sort, $allowedSortFields)) {
    $sort = 'created_at'; // Asigurare că doar câmpurile valide sunt utilizate
}

// Calcularea offset-ului pentru query
$offset = ($page - 1) * $recordsPerPage;

// Construirea query-ului SQL pentru numărarea totalului de membri
$countQuery = "SELECT COUNT(*) AS total FROM members";
$countParams = [];

// Adăugarea filtrului de profesii în query-ul de numărare, dacă este cazul
if (!empty($professionFilter)) {
    $countQuery .= " WHERE profession = :profession";
    $countParams[':profession'] = $professionFilter;
}

$countStmt = $db->prepare($countQuery);
$countStmt->execute($countParams);
$totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalRecords / $recordsPerPage);

// Construirea query-ului SQL
$query = "SELECT * FROM members";
$params = [];

// Adăugarea filtrului de profesii, dacă este cazul
if (!empty($professionFilter)) {
    $query .= " WHERE profession = :profession";
    $params[':profession'] = $professionFilter;
}

$query .= " ORDER BY $sort ASC LIMIT :offset, :recordsPerPage";
//$query = "SELECT * FROM members ORDER BY $sort ASC"; // Sortare crescătoare
$stmt = $db->prepare($query);
if (!empty($professionFilter)) {
    $stmt->bindParam(':profession', $professionFilter);
}
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':recordsPerPage', $recordsPerPage, PDO::PARAM_INT);

$stmt->execute();

// Obținerea profesiilor distincte pentru dropdown-ul de filtrare
$professionQuery = "SELECT DISTINCT profession FROM members WHERE profession IS NOT NULL AND profession != ''";
$professionStmt = $db->query($professionQuery);
$professions = $professionStmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="form-container mb-4">
    <h3>Sort Members</h3>
    <form method="GET" class="form-inline mb-3">
        <label for="sort" class="mr-2">Sort by:</label>
        <select name="sort" id="sort" class="form-control mr-2">
            <option value="created_at" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'created_at') ? 'selected' : ''; ?>>Date</option>
            <option value="first_name" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'first_name') ? 'selected' : ''; ?>>Name</option>
        </select>

        <label for="profession" class="mr-2">Filter by Profession:</label>
        <select name="profession" id="profession" class="form-control mr-2">
            <option value="">All Professions</option>
            <?php foreach ($professions as $profession): ?>
                <option value="<?php echo htmlspecialchars($profession['profession']); ?>" <?php echo ($professionFilter == $profession['profession']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($profession['profession']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary">Apply</button>
    </form>
</div>


<h2>Members Directory</h2>

<div class="row">
    <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
        <div class="col-md-4">
            <div class="card member-card">
                <?php if (!empty($row['profile_photo'])): ?>
                    <img src="<?php echo htmlspecialchars($row['profile_photo']); ?>" class="card-img-top" alt="Profile Photo">
                <?php else: ?>
                    <img src="images/default.jpg" class="card-img-top" alt="Default Photo">
                <?php endif; ?>
                <div class="card-body">
                    <h5 class="card-title">
                        <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>
                    </h5>
                    <p class="card-text">
                        <strong>Profession:</strong> <?php echo htmlspecialchars($row['profession']); ?><br>
                        <strong>Company:</strong> <?php echo htmlspecialchars($row['company']); ?>
                    </p>
                    <a href="edit_member.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">Edit</a>
                    <a href="delete_member.php?id=<?php echo $row['id']; ?>" class="btn btn-danger"
                       onclick="return confirm('Are you sure?')">Delete</a>
                </div>
                <a href="member_profile.php?id=<?php echo $row['id']; ?>" class="btn btn-info">View Profile</a>

            </div>
        </div>
    <?php endwhile; ?>
</div>

<!-- Paginare -->
<nav aria-label="Page navigation">
    <ul class="pagination justify-content-center mt-3">
        <!-- Link către pagina anterioară -->
        <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
            <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">Previous</a>
        </li>

        <!-- Link-uri pentru fiecare pagină -->
        <?php
        $maxPagesToShow = 5;
        $startPage = max(1, $page - floor($maxPagesToShow / 2)); // Start cu 2 pagini înainte
        $endPage = min($totalPages, $startPage + $maxPagesToShow - 1); // Termină cu 2 pagini după

        for ($i = $startPage; $i <= $endPage; $i++): ?>
            <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>

        <!-- Link către pagina următoare -->
        <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
            <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">Next</a>
        </li>
    </ul>
</nav>


<?php include_once "includes/footer.php"; ?>

