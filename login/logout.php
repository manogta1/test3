<?php
session_start();
session_destroy(); // Sitzung beenden
header("Location: index.php"); // Weiterleitung zur index.php nach dem Abmelden
exit();
?>