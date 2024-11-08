<?php
session_start();
require_once 'db.php'; // Stelle sicher, dass die Datenbankverbindung korrekt ist

// Überprüfen, ob der Benutzer angemeldet ist
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

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

    // Benutzer-ID abrufen
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        $user_id = $user['id']; // Hier wird die Benutzer-ID abgerufen
        $stmt = $conn->prepare("INSERT INTO messages (user_id, message, image_path) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $message, $image_path]); // Hier wird die Benutzer-ID verwendet
    }
}

// Alle Nachrichten abrufen
$stmt = $conn->prepare("SELECT messages.message, messages.created_at, messages.image_path, users.username, users.last_name FROM messages JOIN users ON messages.user_id = users.id ORDER BY messages.created_at DESC");
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil von <?php echo htmlspecialchars($username); ?></title>
    <link rel="stylesheet" href="styles.css"> <!-- Link zu deiner CSS-Datei -->
    <style>
        .message-image {
            max-width: 200px; /* Setze die maximale Breite der Bilder */
            height: auto; /* Automatische Höhe */
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- jQuery einbinden -->
    <script>
        function fetchMessages() {
            $.ajax({
                url: 'fetch_messages.php', // Die Datei, die die Nachrichten abruft
                method: 'GET',
                success: function(data) {
                    $('.messages').html(data); // Aktualisiere die Nachrichtenanzeige
                }
            });
        }

        $(document).ready(function() {
            fetchMessages(); // Nachrichten beim Laden der Seite abrufen
            setInterval(fetchMessages, 5000); // Alle 5 Sekunden Nachrichten abrufen
        });
    </script>
</head>
<body>
    <header>
        <h1>Willkommen, <?php echo htmlspecialchars($username); ?>!</h1>
        <?php include 'navbar.php'; ?>
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
            </ input type="submit" value="Nachricht senden">
            </form>

            <h2>Nachrichten</h2>
            <div class="messages">
                <!-- Hier werden die Nachrichten dynamisch geladen -->
                <?php if (count($messages) > 0): ?>
                    <ul>
                        <?php foreach ($messages as $msg): ?>
                            <li>
                                <strong><?php echo htmlspecialchars($msg['username']); ?>:</strong>
                                <?php echo htmlspecialchars($msg['message']); ?>
                                <?php if ($msg['image_path']): ?>
                                    <br><img src="<?php echo htmlspecialchars($msg['image_path']); ?>" alt="Bild" class="message-image">
                                <?php endif; ?>
                                <br><small><?php echo htmlspecialchars($msg['created_at']); ?></small>
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
