<?php
session_start(); // Pornim sesiunea
session_unset(); // Eliminăm toate variabilele sesiunii
session_destroy(); // Distrugem sesiunea

// Redirecționăm utilizatorul către pagina de login
header("Location: login.php");
exit();
?>
