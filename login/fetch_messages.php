<?php
session_start();
require_once 'db.php'; // Stelle sicher, dass die Datenbankverbindung korrekt ist

// Alle Nachrichten abrufen
$stmt = $conn->prepare("SELECT messages.message, messages.created_at, messages.image_path, users.username FROM messages JOIN users ON messages.user_id = users.id ORDER BY messages.created_at DESC");
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($messages as $msg) {
    echo '<li>';
    echo '<strong>' . htmlspecialchars($msg['username']) . ':</strong> ';
    echo htmlspecialchars($msg['message']);
    if ($msg['image_path']) {
        echo '<br><img src="' . htmlspecialchars($msg['image_path']) . '" alt="Bild" class="message-image">';
    }
    echo '<br><small>' . htmlspecialchars($msg['created_at']) . '</small>';
    echo '</li>';
}
?>