<?php
session_start();

// Verificăm dacă utilizatorul este autentificat și este director
if (!isset($_SESSION['doctor_id']) || $_SESSION['role'] != 'director') {
    die("Nu aveți permisiunea de a accesa această secțiune.");
}

include 'config.php';

// Verificăm conexiunea
if ($conn->connect_error) {
    die("Eroare de conexiune: " . $conn->connect_error);
}

// Obține statistici pentru specialități
$query_specialitati = "
    SELECT s.nume_specialitate, COUNT(DISTINCT c.CNP_pacient) AS nr_pacienti
    FROM consultatii c
    JOIN doctori d ON c.CNP_doctor = d.CNP
    JOIN specialitati s ON d.id_specialitate = s.id_specialitate
    GROUP BY s.nume_specialitate";
$result_specialitati = $conn->query($query_specialitati);
if (!$result_specialitati) {
    die("Eroare la interogarea pentru specialități: " . $conn->error);
}
$specialitati = $result_specialitati->fetch_all(MYSQLI_ASSOC);

// Obține statistici pentru boli cronice
$query_boli = "
    SELECT diagnostic AS boala, COUNT(DISTINCT CNP_pacient) AS nr_pacienti
    FROM consultatii
    WHERE diagnostic IN ('diabet', 'hipertensiune', 'astm bronsic', 'cancer')
    GROUP BY diagnostic";
$result_boli = $conn->query($query_boli);
if (!$result_boli) {
    die("Eroare la interogarea pentru boli cronice: " . $conn->error);
}
$boli = $result_boli->fetch_all(MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistici</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #000;
            color: #fff;
            padding: 20px;
        }
        h2, h3 {
            text-align: center;
        }
        canvas {
            display: block;
            margin: 20px auto;
            max-width: 600px;
        }
        form {
            text-align: center;
            margin-top: 20px;
        }
        button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h2>Statistici Grafice</h2>

    <h3>Numărul de pacienți pe specialități</h3>
    <canvas id="specialityPieChart"></canvas>
    <canvas id="specialityBarChart"></canvas>

    <h3>Numărul de pacienți cu boli cronice</h3>
    <canvas id="diseasePieChart"></canvas>
    <canvas id="diseaseBarChart"></canvas>

    <!-- Buton pentru descărcarea PDF -->
    <form action="statistici_pdf.php" method="POST">
        <button type="submit">Descarcă PDF</button>
    </form>

    <script>
        const specialityData = <?php echo json_encode($specialitati); ?>;
        const diseaseData = <?php echo json_encode($boli); ?>;

        const specialityLabels = specialityData.map(item => item.nume_specialitate);
        const specialityValues = specialityData.map(item => item.nr_pacienti);

        const diseaseLabels = diseaseData.map(item => item.boala);
        const diseaseValues = diseaseData.map(item => item.nr_pacienti);

        new Chart(document.getElementById('specialityPieChart'), {
            type: 'pie',
            data: {
                labels: specialityLabels,
                datasets: [{
                    data: specialityValues,
                    backgroundColor: ['#007bff', '#28a745', '#dc3545', '#ffc107']
                }]
            }
        });

        new Chart(document.getElementById('specialityBarChart'), {
            type: 'bar',
            data: {
                labels: specialityLabels,
                datasets: [{
                    data: specialityValues,
                    backgroundColor: '#007bff'
                }]
            }
        });

        new Chart(document.getElementById('diseasePieChart'), {
            type: 'pie',
            data: {
                labels: diseaseLabels,
                datasets: [{
                    data: diseaseValues,
                    backgroundColor: ['#28a745', '#ffc107', '#007bff', '#dc3545']
                }]
            }
        });

        new Chart(document.getElementById('diseaseBarChart'), {
            type: 'bar',
            data: {
                labels: diseaseLabels,
                datasets: [{
                    data: diseaseValues,
                    backgroundColor: '#28a745'
                }]
            }
        });
    </script>
</body>
</html>
