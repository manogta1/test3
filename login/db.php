<?php
$host = 'localhost'; // oder die IP-Adresse deines Datenbankservers
$db = 'my_website';
$user = 'root'; // z.B. root
$pass = 'maNu1997!';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Verbindung fehlgeschlagen: " . $e->getMessage());
}
?>