<?php
session_start();
require_once 'db.php';

// Überprüfen, ob der Benutzer bereits eingeloggt ist
if (isset($_SESSION['id'])) {
    header("Location: profile.php"); // Weiterleitung zur Profilseite
    exit();
}

$error = '';

// Login verarbeiten
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $identifier = $_POST['identifier']; // E-Mail oder Handynummer
    $password = $_POST['password'];

    // Überprüfen, ob die E-Mail oder Handynummer existiert
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR phone_number = ?");
    $stmt->execute([$identifier, $identifier]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Passwort ist korrekt, Benutzer einloggen
        $_SESSION['id'] = $user['id']; // Speichere die Benutzer-ID in der Session
        $_SESSION['username'] = $user['username']; // Optional: Speichere den Benutzernamen
        header("Location: profile.php"); // Weiterleitung zur Profilseite
        exit();
    } else {
        $error = "Ungültige Anmeldedaten. Bitte versuche es erneut.";
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link zu deiner CSS-Datei -->
</head>
<body>
    <header>
        <h1>Login</h1>
    </header>

    <main>
        <div class="container">
            <form method="post" action="">
                <div class="form-group">
                    <label for="identifier">E-Mail oder Handynummer:</label>
                    <input type="text" name="identifier" required>
                </div>
                <div class="form-group">
                    <label for="password">Passwort:</label>
                    <input type="password" name="password" required>
                </div>
                <input type="submit" value="Einloggen">
                <?php if (isset($error)): ?>
                    <p class="error"><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>
            </form>
            <p>Noch kein Konto? <a href="register.php">Registriere dich hier</a>.</p>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Meine Webseite. Alle Rechte vorbehalten.</p>
    </footer>
</body>
</html>