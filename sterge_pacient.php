<?php
include 'config.php'; // Conectare la baza de date

if (isset($_GET['id'])) {
    $id_pacient = $_GET['id'];

    // Pregătește și execută ștergerea
    $stmt = $conn->prepare("DELETE FROM pacienti WHERE id_pacient = ?");
    $stmt->bind_param("i", $id_pacient);

    if ($stmt->execute()) {
        echo "Pacientul a fost șters cu succes.";
        echo "<br><a href='vizualizeaza_pacienti.php'>Înapoi la lista de pacienți</a>";
    } else {
        echo "Eroare la ștergerea pacientului: " . $conn->error;
    }

    $stmt->close();
} else {
    echo "ID-ul pacientului nu este specificat.";
}

$conn->close();
?>
