<?php
include_once "includes/header.php";
include_once "config/database.php";

// Conectare la baza de date
$database = new Database();
$db = $database->getConnection();

// Calendar Evenimente
$query_events = "SELECT * FROM events ORDER BY event_date ASC";
$stmt_events = $db->prepare($query_events);
$stmt_events->execute();

// Înregistrare Evenimente
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register_event'])) {
    session_start();
    $member_id = $_SESSION['member_id']; // Preia member_id din sesiune
    $event_id = $_POST['event_id'];

    // Verifică dacă membrul există în tabelul members
    $query_member_check = "SELECT id FROM members WHERE id = ?";
    $stmt_member_check = $db->prepare($query_member_check);
    $stmt_member_check->execute([$member_id]);

    if ($stmt_member_check->rowCount() == 0) {
        echo "Eroare: Membru inexistent!";
        exit();
    }

    $query_register = "INSERT INTO event_registrations (member_id, event_id) VALUES (?, ?)";
    $stmt_register = $db->prepare($query_register);
    $stmt_register->execute([$member_id, $event_id]);

    header("Location: events.php");
    exit();
}

// Feedback Evenimente
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_feedback'])) {
    session_start();
    $member_id = $_SESSION['member_id']; // Preia member_id din sesiune

    // Verifică dacă membrul există în tabelul members
    $query_member_check = "SELECT id FROM members WHERE id = ?";
    $stmt_member_check = $db->prepare($query_member_check);
    $stmt_member_check->execute([$member_id]);

    if ($stmt_member_check->rowCount() == 0) {
        echo "Eroare: Membru inexistent!";
        exit();
    }

    $event_id = $_POST['event_id_feedback'];
    $rating = $_POST['rating'];
    $feedback = $_POST['feedback'];

    $query_feedback = "INSERT INTO event_feedback (event_id, member_id, rating, feedback) VALUES (?, ?, ?, ?)";
    $stmt_feedback = $db->prepare($query_feedback);
    $stmt_feedback->execute([$event_id, $member_id, $rating, $feedback]);

    header("Location: events.php");
    exit();
}

// Verifică dacă formularul a fost trimis pentru a adăuga un eveniment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_event'])) {
    $title = $_POST['title'];
    $event_date = $_POST['event_date'];
    $location = $_POST['location'];
    $description = $_POST['description'];

    // Inserare eveniment în baza de date
    $query_add_event = "INSERT INTO events (title, event_date, location, description) VALUES (?, ?, ?, ?)";
    $stmt_add_event = $db->prepare($query_add_event);
    $stmt_add_event->execute([$title, $event_date, $location, $description]);

    // Redirecționează după adăugarea evenimentului
    header("Location: events.php");
    exit();
}
?>


<!-- Formular pentru adăugarea unui eveniment -->
<div class="container mt-4">
    <h2>Adaugă un Eveniment</h2>
    <form method="POST">
        <div class="form-group">
            <label for="title">Titlu Eveniment</label>
            <input type="text" class="form-control" name="title" id="title" required>
        </div>
        <div class="form-group">
            <label for="event_date">Data Evenimentului</label>
            <input type="date" class="form-control" name="event_date" id="event_date" required>
        </div>
        <div class="form-group">
            <label for="location">Locație</label>
            <input type="text" class="form-control" name="location" id="location" required>
        </div>
        <div class="form-group">
            <label for="description">Descriere</label>
            <textarea class="form-control" name="description" id="description" rows="4" required></textarea>
        </div>
        <button type="submit" name="add_event" class="btn btn-success">Adaugă Eveniment</button>
    </form>
</div>

<div class="container mt-4">
    <h2>Evenimente</h2>

    <!-- Calendar Evenimente -->
    <section id="calendar">
        <h3>Calendar Evenimente</h3>
        <table class="table">
            <thead>
            <tr>
                <th>Eveniment</th>
                <th>Data</th>
                <th>Locație</th>
                <th>Acțiune</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($event = $stmt_events->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($event['title']); ?></td>
                    <td><?php echo date('d M Y', strtotime($event['event_date'])); ?></td>
                    <td><?php echo htmlspecialchars($event['location']); ?></td>
                    <td>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#registerModal<?php echo $event['id']; ?>">Înregistrează-te</button>
                        <button class="btn btn-info" data-toggle="modal" data-target="#feedbackModal<?php echo $event['id']; ?>">Feedback</button>
                    </td>
                </tr>

                <!-- Modal pentru înregistrare -->
                <div class="modal fade" id="registerModal<?php echo $event['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="registerModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="registerModalLabel">Înregistrează-te la Eveniment</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form method="POST">
                                <div class="modal-body">
                                    <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                                    <p>Vrei să te înregistrezi pentru evenimentul: <strong><?php echo htmlspecialchars($event['title']); ?></strong>?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Anulează</button>
                                    <button type="submit" name="register_event" class="btn btn-primary">Înregistrează-te</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modal pentru feedback -->
                <div class="modal fade" id="feedbackModal<?php echo $event['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="feedbackModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="feedbackModalLabel">Lăsați Feedback pentru Eveniment</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form method="POST">
                                <div class="modal-body">
                                    <input type="hidden" name="event_id_feedback" value="<?php echo $event['id']; ?>">
                                    <div class="form-group">
                                        <label for="rating">Rating:</label>
                                        <select name="rating" class="form-control">
                                            <option value="1">1 - Foarte Rău</option>
                                            <option value="2">2 - Rău</option>
                                            <option value="3">3 - Mediocru</option>
                                            <option value="4">4 - Bun</option>
                                            <option value="5">5 - Excelent</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="feedback">Feedback:</label>
                                        <textarea name="feedback" class="form-control" rows="3" required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Anulează</button>
                                    <button type="submit" name="submit_feedback" class="btn btn-primary">Trimite Feedback</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            <?php endwhile; ?>
            </tbody>
        </table>
    </section>
</div>

<?php include_once "includes/footer.php"; ?>

