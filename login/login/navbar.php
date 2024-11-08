<!-- navbar.php -->
<nav>
    <ul>
        <li><a href="index.php">Startseite</a></li>
        <li><a href="profile.php">Profil</a></li>
        <li><a href="user_posts.php">Meine Beiträge</a></li> <!-- Link zur neuen Seite -->
        <li><a href="profile_settings.php">Profil-Einstellungen</a></li>
        <li><a href="logout.php">Abmelden</a></li>
        <?php
        // Überprüfen, ob der Benutzer angemeldet ist
        if (isset($_SESSION['id'])) {
            require_once 'db.php'; // Datenbankverbindung einbinden
            $user_id = $_SESSION['id'];
            $stmt = $conn->prepare("SELECT admin FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Wenn der Benutzer ein Administrator ist, zeige den Link an
            if ($user && $user['admin'] == 1) {
                echo '<li><a href="admin.php">Admin-Bereich</a></li>';
            }
        }
        ?>
    </ul>
</nav>