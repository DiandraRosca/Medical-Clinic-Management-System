<?php
session_start();
require('fpdf/fpdf.php');
include 'config.php';

// Verifică dacă doctorul este conectat
if (!isset($_SESSION['doctor_cnp'])) {
    die("Eroare: Nu sunteți conectat. Vă rugăm să vă autentificați.");
}
$doctor_cnp = $_SESSION['doctor_cnp'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $diagnostic = trim($_POST['diagnostic']);

    // Query pentru diagnostic pe baza CNP_pacient
    $query = "SELECT p.CNP AS pacient_CNP, p.nume AS pacient_nume, p.prenume AS pacient_prenume,
                     p.varsta, p.telefon, p.email
              FROM consultatii c
              JOIN pacienti p ON c.CNP_pacient = p.CNP
              WHERE LOWER(TRIM(c.diagnostic)) = LOWER(TRIM(?)) AND c.CNP_doctor = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $diagnostic, $doctor_cnp);

    if (!$stmt->execute()) {
        die("Eroare SQL: " . $stmt->error);
    }

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $pacienti = [];

        while ($row = $result->fetch_assoc()) {
            $pacienti[] = $row;
        }

        // Creare PDF
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);

        $pdf->Cell(0, 10, 'Lista Pacientilor cu Diagnostic: ' . $diagnostic, 0, 1, 'C');
        $pdf->Ln(10);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(40, 10, 'CNP', 1);
        $pdf->Cell(50, 10, 'Nume', 1);
        $pdf->Cell(50, 10, 'Prenume', 1);
        $pdf->Cell(30, 10, 'Varsta', 1);
        $pdf->Cell(40, 10, 'Telefon', 1);
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 12);
        foreach ($pacienti as $pacient) {
            $pdf->Cell(40, 10, $pacient['pacient_CNP'], 1);
            $pdf->Cell(50, 10, $pacient['pacient_nume'], 1);
            $pdf->Cell(50, 10, $pacient['pacient_prenume'], 1);
            $pdf->Cell(30, 10, $pacient['varsta'], 1);
            $pdf->Cell(40, 10, $pacient['telefon'], 1);
            $pdf->Ln();
        }

        $pdf->Output('D', 'Lista_Pacienti_' . $diagnostic . '.pdf');
    } else {
        echo "Nu există pacienți cu diagnosticul specificat.";
    }
} else {
    echo "Metoda de accesare este incorectă.";
}
?>
