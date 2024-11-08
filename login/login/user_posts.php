<?php
session_start();
require_once 'db.php'; // Stelle sicher, dass die Datenbankverbindung korrekt ist

// Überprüfen, ob der Benutzer angemeldet ist
if (!isset($_SESSION['id'])) {
    header("Location: index.php"); // Weiterleitung zur index.php, wenn nicht angemeldet
    exit();
}

$user_id = $_SESSION['id'];

// Benutzerinformationen abrufen
$stmt = $conn->prepare("SELECT username, last_name FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Nachricht speichern
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['message'])) {
    $message = $_POST['message'];
    $image_path = null;

    // Bildverarbeitung
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_name = basename($_FILES['image']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Überprüfen, ob die Datei ein Bild ist
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($file_ext, $allowed_extensions)) {
            // Zielverzeichnis für Bilder
            $upload_dir = 'uploads/'; // Stelle sicher, dass dieses Verzeichnis existiert
            $image_path = $upload_dir . uniqid('', true) . '.' . $file_ext;

            // Bild hochladen
            if (!move_uploaded_file($file_tmp, $image_path)) {
                $image_path = null; // Fehler beim Hochladen
            }
        }
    }

    // Benutzer-ID verwenden
    $stmt = $conn->prepare("INSERT INTO messages (user_id, message, image_path) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $message, $image_path]);
}

// Nachrichten löschen
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM messages WHERE id = ? AND user_id = ?");
    $stmt->execute([$delete_id, $user_id]);
}

// Nur die Nachrichten des angemeldeten Benutzers abrufen
$stmt = $conn->prepare("SELECT id, message, created_at, image_path FROM messages WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meine Beiträge von <?php echo htmlspecialchars($user['username']); ?></title>
    <link rel="stylesheet" href="styles.css"> <!-- Link zu deiner CSS-Datei -->
    <style>
        .message-image {
            max-width: 200px; /* Setze die maximale Breite der Bilder */
            height: auto; /* Automatische Höhe */
        }
    </style>
</head>
<body>
    <header>
        <h1>Willkommen, <?php echo htmlspecialchars($user['username']); ?>!</h1>
        <?php include 'navbar.php'; ?> <!-- Navbar einbinden -->
    </header>

    <main>
        <div class="container">
            <h2>Nachricht senden</h2>
            <form method="post" action="" enctype="multipart/form-data">
                <textarea name="message" rows="4" placeholder="Schreibe hier deine Nachricht..." required></textarea>
                <br>
                <input type="file" name="image" accept="image/*"> <!-- Datei-Upload für Bilder -->
                <br>
                <input type="submit" value="Nachricht senden">
            </form>

            <h2>Meine Nachrichten</h2>
            <div class="messages">
                <?php if (count($messages) > 0): ?>
                    <ul>
                        <?php foreach ($messages as $msg): ?>
                            <li>
                                <?php echo htmlspecialchars($msg['message']); ?>
                                <?php if ($msg['image_path']): ?>
                                    <br><img src="<?php echo htmlspecialchars($msg['image_path']); ?>" alt=" Bild" class="message-image">
                                <?php endif; ?>
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