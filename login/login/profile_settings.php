<?php
session_start();
require_once 'db.php';

// Überprüfen, ob der Benutzer angemeldet ist
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];
$success = '';

// Profil-Einstellungen aktualisieren
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phone_number = $_POST['phone_number'];

    $stmt = $conn->prepare("UPDATE users SET phone_number = ? WHERE id = ?");
    if ($stmt->execute([$phone_number, $user_id])) {
        $success = "Handynummer erfolgreich aktualisiert.";
    }
}

// Benutzerinformationen abrufen
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profileinstellungen</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link zu deiner CSS-Datei -->
</head>
<body>
    <header>
        <h1>Profileinstellungen</h1>
        <?php include 'navbar.php'; ?>
    </header>

    <main>
        <div class="container">
            <h2>Handynummer ändern</h2>
            <form method="post" action="">
                <div class="form-group">
                    <label for="phone_number">Handynummer:</label>
                    <input type="text" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>
                </div>
                <input type="hidden" name="update_phone" value="1">
                <input type="submit" value="Handynummer aktualisieren">
                <?php if ($error): ?>
                    <p class="error"><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>
                <?php if ($success): ?>
                    <p class="success"><?php echo htmlspecialchars($success); ?></p>
                <?php endif; ?>
            </form>

            <h2>Passwort ändern</h2>
            <form method="post" action="">
                <div class="form-group">
                    <label for="current_password">Aktuelles Passwort:</label>
                    <input type="password" name="current_password" required>
                </div>
                <div class="form-group">
                    <label for="new_password">Neues Passwort:</label>
                    <input type="password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Neues Passwort bestätigen:</label>
                    <input type="password" name="confirm_password" required>
                </div>
                <input type="hidden" name="change_password" value="1">
                <input type="submit" value="Passwort ändern">
                <?php if ($error): ?>
                    <p class="error"><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>
                <?php if ($success): ?>
                    <p class="success"><?php echo htmlspecialchars($success); ?></p>
                <?php endif; ?>
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Meine Webseite. Alle Rechte vorbehalten.</p>
    </footer>
</body>
</html>