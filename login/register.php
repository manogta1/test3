<?php
session_start();
require_once 'db.php';

$error = '';
$success = '';
$username = ''; // Benutzername
$last_name = '';
$email = '';
$phone_number = '';

// Fehlerverfolgung für spezifische Felder
$email_error = false;
$phone_error = false;

// Registrierung verarbeiten
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $username = $_POST['username']; // Benutzername (wird als Vorname angezeigt)
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password']; // Passwortbestätigung
    $phone_number = $_POST['phone_number']; // Handynummer (optional)

    // Überprüfen, ob die E-Mail bereits existiert
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $email_error = true; // E-Mail-Fehler
        $error = "Die E-Mail ist bereits vergeben.";
    } 

    // Überprüfen, ob die Telefonnummer bereits existiert, falls sie angegeben wurde
    if (!empty($phone_number)) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE phone_number = ?");
        $stmt->execute([$phone_number]);
        $phone_user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($phone_user) {
            $phone_error = true; // Telefonnummer-Fehler
            if (!$error) {
                $error = "Die Telefonnummer ist bereits vergeben.";
            }
        }
    }

    if ($password !== $confirm_password) {
        // Überprüfen, ob die Passwörter übereinstimmen
        $error = "Die Passwörter stimmen nicht überein.";
    } elseif (empty($error)) {
        // Passwort hashen
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);
        
        // Benutzer in der Datenbank registrieren
        $stmt = $conn->prepare("INSERT INTO users (username, last_name, email, password, phone_number) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$username, $last_name, $email, $password_hashed, $phone_number])) {
            // Erfolgreiche Registrierung
            $_SESSION['username'] = $username; // Optional: Speichere den Benutzernamen in der Session
            header("Location: profile.php"); // Weiterleitung zur Profilseite
            exit(); // Beende das Skript nach der Weiterleitung
        } else {
            $error = "Fehler bei der Registrierung. Bitte versuche es später erneut.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrierung</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link zu deiner CSS-Datei -->
    <style>
        .error {
            border: 2px solid red; /* Rote Umrandung bei Fehlern */
        }
    </style>
</head>
<body>
    <header>
        <h1>Registrierung</h1>
    </header>

    <main>
        <div class="container">
            <form method="post" action="">
                <div class="form-group">
                    <label for="username">Vorname:</label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Nachname:</label>
                    <input type="text" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">E-Mail:</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" class="<?php echo $email_error ? 'error' : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="password">Passwort:</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Passwort bestätigen:</label>
                    <input type="password" name="confirm_password" required>
                </div>
                <div class="form-group">
                    <label for="phone_number">Handynummer (optional):</label>
                    <input type="text" name="phone_number" value="<?php echo htmlspecialchars($phone_number); ?>" class="<?php echo $phone_error ? 'error' : ''; ?>">
                    <small>Dieses Feld ist optional und kann leer bleiben.</small>
                </div>
                <input type="submit" name="register" value="Registrieren">
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