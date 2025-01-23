<?php
$servername = "localhost";
$username = "root"; // Schimbă dacă ai setat alt utilizator
$password = ""; // Parola ta MySQL
$dbname = "med_clinica";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifică conexiunea
if ($conn->connect_error) {
    die("Conexiunea a eșuat: " . $conn->connect_error);
}
?>
