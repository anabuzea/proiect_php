<?php
include_once "config/database.php";
include_once "includes/header.php";

$database = new Database();
$db = $database->getConnection();

// Total Members
$totalMembersQuery = "SELECT COUNT(*) AS total FROM members";
$totalMembersStmt = $db->prepare($totalMembersQuery);
$totalMembersStmt->execute();
$totalMembers = $totalMembersStmt->fetch(PDO::FETCH_ASSOC)['total'];

// Total Events
$totalEventsQuery = "SELECT COUNT(*) AS total FROM events";
$totalEventsStmt = $db->prepare($totalEventsQuery);
$totalEventsStmt->execute();
$totalEvents = $totalEventsStmt->fetch(PDO::FETCH_ASSOC)['total'];

// Recent Members
$recentMembersQuery = "SELECT * FROM members ORDER BY created_at DESC LIMIT 5";
$recentMembersStmt = $db->prepare($recentMembersQuery);
$recentMembersStmt->execute();

// Recomandări de conexiuni
$currentMemberId = 1; // Exemplu de ID utilizator curent (înlocuit cu sesiunea curentă)

// Detalii despre membrul curent
$currentMemberQuery = "SELECT * FROM members WHERE id = ?";
$currentMemberStmt = $db->prepare($currentMemberQuery);
$currentMemberStmt->execute([$currentMemberId]);
$currentMember = $currentMemberStmt->fetch(PDO::FETCH_ASSOC);

// Găsirea recomandărilor de conexiuni
$recommendationsQuery = "SELECT * FROM members 
                         WHERE (profession = ? OR company = ?) 
                         AND id != ? 
                         ORDER BY created_at DESC LIMIT 5";
$recommendationsStmt = $db->prepare($recommendationsQuery);
$recommendationsStmt->execute([$currentMember['profession'], $currentMember['company'], $currentMemberId]);
?>

<div class="container mt-4">
    <h1>Dashboard</h1>
    <div class="row">
        <!-- Total Members -->
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h3>Total Members</h3>
                    <p class="display-4"><?php echo $totalMembers; ?></p>
                </div>
            </div>
        </div>
        <!-- Total Events -->
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h3>Total Events</h3>
                    <p class="display-4"><?php echo $totalEvents; ?></p>
                </div>
            </div>
        </div>
    </div>

    <h2 class="mt-4">Recent Members</h2>
    <ul class="list-group mb-4">
        <?php while ($row = $recentMembersStmt->fetch(PDO::FETCH_ASSOC)): ?>
            <li class="list-group-item">
                <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>
                - <?php echo htmlspecialchars($row['profession']); ?>
            </li>
        <?php endwhile; ?>
    </ul>

    <h2>Recommended Connections</h2>
    <ul class="list-group">
        <?php while ($row = $recommendationsStmt->fetch(PDO::FETCH_ASSOC)): ?>
            <li class="list-group-item">
                <strong><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></strong><br>
                Profession: <?php echo htmlspecialchars($row['profession']); ?><br>
                Company: <?php echo htmlspecialchars($row['company']); ?><br>
                <a href="add_connection.php?member_id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">Connect</a>
            </li>
        <?php endwhile; ?>
    </ul>
</div>

<?php include_once "includes/footer.php"; ?>
