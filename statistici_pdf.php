<?php
require('fpdf/fpdf.php');
include 'config.php';

// Verificăm dacă utilizatorul este autentificat și este director
session_start();
if (!isset($_SESSION['doctor_id']) || $_SESSION['role'] != 'director') {
    die("Nu aveți permisiunea de a accesa această secțiune.");
}

// Creare obiect PDF
class PDF extends FPDF {
    // Titlu personalizat
    function Header() {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Statistici Clinica Medicala', 0, 1, 'C');
        $this->Ln(10);
    }

    // Footer personalizat
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo(), 0, 0, 'C');
    }
}

// Obține datele pentru specialități
$query_specialitati = "
    SELECT s.nume_specialitate, COUNT(DISTINCT c.CNP_pacient) AS nr_pacienti
    FROM consultatii c
    JOIN doctori d ON c.CNP_doctor = d.CNP
    JOIN specialitati s ON d.id_specialitate = s.id_specialitate
    GROUP BY s.nume_specialitate";
$result_specialitati = $conn->query($query_specialitati);
$specialitati = $result_specialitati->fetch_all(MYSQLI_ASSOC);

// Obține datele pentru boli cronice
$query_boli = "
    SELECT diagnostic AS boala, COUNT(DISTINCT CNP_pacient) AS nr_pacienti
    FROM consultatii
    WHERE diagnostic IN ('diabet', 'hipertensiune', 'astm bronsic', 'cancer')
    GROUP BY diagnostic";
$result_boli = $conn->query($query_boli);
$boli = $result_boli->fetch_all(MYSQLI_ASSOC);

// Creăm PDF-ul
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// Secțiunea pentru specialități
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Numarul de pacienti pe specialitati:', 0, 1);
$pdf->SetFont('Arial', '', 12);
foreach ($specialitati as $row) {
    $pdf->Cell(0, 10, $row['nume_specialitate'] . ': ' . $row['nr_pacienti'] . ' pacienti', 0, 1);
}

// Linie de separare
$pdf->Ln(10);
// Secțiunea pentru boli cronice
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Numarul de pacienti cu boli cronice:', 0, 1);
$pdf->SetFont('Arial', '', 12);
foreach ($boli as $row) {
    $pdf->Cell(0, 10, ucfirst($row['boala']) . ': ' . $row['nr_pacienti'] . ' pacienti', 0, 1);
}
// Trimite PDF-ul pentru descărcare
$pdf->Output('D', 'statistici_clinica_medicala.pdf');
