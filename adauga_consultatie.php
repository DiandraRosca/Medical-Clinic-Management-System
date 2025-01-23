<?php
session_start();
include 'config.php';

// Verifică dacă medicul este conectat
if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

$doctor_cnp = $_SESSION['doctor_cnp'];

// Când formularul este trimis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cnp_pacient = $_POST['cnp_pacient'];
    $data_consultatie = $_POST['data_consultatie'];
    $diagnostic = $_POST['diagnostic'];
    $medicatie = $_POST['medicatie'];

    $query = "INSERT INTO consultatii (CNP_pacient, CNP_doctor, data_consultatie, diagnostic, medicatie)
              VALUES ('$cnp_pacient', '$doctor_cnp', '$data_consultatie', '$diagnostic', '$medicatie')";

    if ($conn->query($query) === TRUE) {
        echo "Consultația a fost adăugată cu succes.";
    } else {
        echo "Eroare: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adaugă Consultație</title>
</head>
<body>
    <h2>Adaugă o Consultație</h2>
    <form method="POST">
        <label for="cnp_pacient">CNP Pacient:</label>
        <input type="text" id="cnp_pacient" name="cnp_pacient" required><br><br>

        <label for="data_consultatie">Data Consultației:</label>
        <input type="date" id="data_consultatie" name="data_consultatie" required><br><br>

        <label for="diagnostic">Diagnostic:</label>
        <textarea id="diagnostic" name="diagnostic" required></textarea><br><br>

        <label for="medicatie">Medicație:</label>
        <textarea id="medicatie" name="medicatie" required></textarea><br><br>

        <button type="submit">Adaugă Consultație</button>
    </form>
</body>
</html>
