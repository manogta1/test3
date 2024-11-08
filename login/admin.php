<?php
session_start();
require_once 'db.php'; // Stelle sicher, dass die Datenbankverbindung korrekt ist

// Überprüfen, ob der Benutzer angemeldet ist
if (!isset($_SESSION['id'])) {
    header("Location: login.php"); // Weiterleitung zur Login-Seite, wenn nicht angemeldet
    exit();
}

// Überprüfen, ob der Benutzer ein Administrator ist
$user_id = $_SESSION['id'];
$stmt = $conn->prepare("SELECT admin FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || $user['admin'] != 1) {
    // Wenn der Benutzer kein Administrator ist, Zugriff verweigern
    header("Location: profile.php"); // Weiterleitung zur Profilseite
    exit();
}

// Nachrichten abrufen
$stmt = $conn->prepare("SELECT messages.id, messages.message, messages.created_at, users.username FROM messages JOIN users ON messages.user_id = users.id ORDER BY messages.created_at DESC");
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Nachrichten löschen
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM messages WHERE id = ?");
    $stmt->execute([$delete_id]);
    header("Location: admin.php"); // Nach dem Löschen zurück zur Admin-Seite
    exit();
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin-Bereich</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link zu deiner CSS-Datei -->
</head>
<body>
    <header>
        <h1>Admin-Bereich</h1>
        <?php include 'navbar.php'; ?>
    </header>

    <main>
        <div class="container">
            <h2>Nachrichten verwalten</h2>
            <div class="messages">
                <?php if (count($messages) > 0): ?>
                    <ul>
                        <?php foreach ($messages as $msg): ?>
                            <li>
                                <strong><?php echo htmlspecialchars($msg['username']); ?>:</strong>
                                <?php echo htmlspecialchars($msg['message']); ?>
                                <br><small><?php echo htmlspecialchars($msg['created_at']); ?></small>
                                <br>
                                <a href="?delete_id=<?php echo $msg['id']; ?>" onclick="return confirm('Möchten Sie diese Nachricht wirklich löschen?');">Löschen</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>Keine Nachrichten vorhanden.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Meine Webseite </p>
    </footer>
</body>
</html>