<?php
session_start();

include 'config.php'; // Conectare la baza de date

if ($conn->connect_error) {
    die("Conexiune eșuată: " . $conn->connect_error);
}

// Obține CNP-ul doctorului logat
$cnp_doctor_logat = $_SESSION['cnp_doctor'] ?? '2234567890123';

// Verifică dacă există un `delete_id` transmis prin GET
if (isset($_GET['delete_id'])) {
    $id_consultatie = $_GET['delete_id'];

    // Șterge doar consultațiile asociate doctorului logat
    $query_delete = "DELETE FROM consultatii WHERE id_consultatie = $id_consultatie AND CNP_doctor = '$cnp_doctor_logat'";

    if ($conn->query($query_delete)) {
        echo "<p style='color: green;'>Consultația a fost ștearsă cu succes!</p>";
    } else {
        echo "<p style='color: red;'>Eroare la ștergere: " . $conn->error . "</p>";
    }

    // Redirecționare înapoi la pagina principală
    header("Location: vizualizeaza_consultatii.php");
    exit;
} else {
    echo "<p style='color: red;'>Nu a fost specificată nicio consultație pentru ștergere.</p>";
}
?>
