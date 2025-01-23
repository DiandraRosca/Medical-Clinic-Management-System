<?php
require('fpdf/fpdf.php'); // Include librăria FPDF
include 'config.php'; // Include conexiunea la baza de date

if (isset($_GET['id'])) {
    $pacient_id = $_GET['id'];

    // Conectare la baza de date și preluarea datelor pacientului
    $query = "SELECT * FROM pacienti WHERE id_pacient = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $pacient_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $pacient = $result->fetch_assoc();

        // Crearea PDF-ului
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);

        // Titlul
        $pdf->Cell(0, 10, 'Fisa Pacientului', 0, 1, 'C');
        $pdf->Ln(10);

        // Informațiile pacientului
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(50, 10, 'ID:', 0, 0);
        $pdf->Cell(0, 10, $pacient['id_pacient'], 0, 1);
        $pdf->Cell(50, 10, 'CNP:', 0, 0);
        $pdf->Cell(0, 10, $pacient['CNP'], 0, 1);
        $pdf->Cell(50, 10, 'Nume:', 0, 0);
        $pdf->Cell(0, 10, $pacient['nume'], 0, 1);
        $pdf->Cell(50, 10, 'Prenume:', 0, 0);
        $pdf->Cell(0, 10, $pacient['prenume'], 0, 1);
        $pdf->Cell(50, 10, 'Adresa:', 0, 0);
        $pdf->Cell(0, 10, $pacient['adresa'], 0, 1);
        $pdf->Cell(50, 10, 'Data Nasterii:', 0, 0);
        $pdf->Cell(0, 10, $pacient['data_nasterii'], 0, 1);
        $pdf->Cell(50, 10, 'Varsta:', 0, 0);
        $pdf->Cell(0, 10, $pacient['varsta'], 0, 1);
        $pdf->Cell(50, 10, 'Telefon:', 0, 0);
        $pdf->Cell(0, 10, $pacient['telefon'], 0, 1);
        $pdf->Cell(50, 10, 'Email:', 0, 0);
        $pdf->Cell(0, 10, $pacient['email'], 0, 1);

        // Salvare și afișare PDF
        $pdf->Output('D', 'Fisa_Pacient_' . $pacient['id_pacient'] . '.pdf');
    } else {
        echo "Pacientul nu a fost găsit.";
    }
} else {
    echo "ID-ul pacientului nu a fost furnizat.";
}
?>
