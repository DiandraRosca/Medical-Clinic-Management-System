<?php
// Începem sesiunea pentru a obține datele doctorului conectat
session_start();

// Verificăm dacă doctorul este conectat
if (!isset($_SESSION['doctor_cnp'])) {
    header("Location: login.html");
    exit();
}

// Obținem CNP-ul doctorului conectat din sesiune
$doctor_cnp = $_SESSION['doctor_cnp'];

// Conectare la baza de date
include 'config.php';


// Variabile pentru căutare
$searchCNP = "";
$patientInfo = null;
$consultations = [];

// Verificăm dacă s-a trimis un formular pentru căutare
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['searchCNP'])) {
    $searchCNP = $_POST['searchCNP'];

    // Căutareeea informațiile pacientului
    $patientQuery = "SELECT * FROM pacienti WHERE CNP = ? AND CNP IN (SELECT CNP_pacient FROM consultatii WHERE CNP_doctor = ?)";
    $stmt = $conn->prepare($patientQuery);
    $stmt->bind_param("ss", $searchCNP, $doctor_cnp);
    $stmt->execute();
    $patientResult = $stmt->get_result();
    if ($patientResult->num_rows > 0) {
        $patientInfo = $patientResult->fetch_assoc();

        // Căutăm consultările aferente pacientului
        $consultationQuery = "SELECT * FROM consultatii WHERE CNP_pacient = ? AND CNP_doctor = ?";
        $stmt = $conn->prepare($consultationQuery);
        $stmt->bind_param("ss", $searchCNP, $doctor_cnp);
        $stmt->execute();
        $consultations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}

// listarea  pacienților doar a doctorului conectat
$query = "SELECT DISTINCT pacienti.* 
          FROM pacienti 
          JOIN consultatii ON pacienti.CNP = consultatii.CNP_pacient 
          WHERE consultatii.CNP_doctor = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $doctor_cnp);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vizualizează Pacienți</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #000;
            color: #fff;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            background-color: #1a1a1a;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.7);
        }

        h1, h2 {
            text-align: center;
            color: #fff;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
            color: #fff;
        }

        th, td {
            border: 1px solid #444;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #333;
        }

        tr:nth-child(even) {
            background-color: #2a2a2a;
        }

        tr:hover {
            background-color: #444;
        }

        .search-box {
            margin-bottom: 20px;
            text-align: center;
        }

        .search-box input {
            padding: 10px;
            width: 300px;
            border: 1px solid #444;
            border-radius: 4px;
            background-color: #333;
            color: #fff;
        }

        .search-box button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .search-box button:hover {
            background-color: #0056b3;
        }

        .action-buttons a {
            display: inline-block;
            margin-right: 10px;
            padding: 10px 15px;
            background-color: #28a745;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .action-buttons a:hover {
            background-color: #218838;
        }

        .action-buttons a.delete {
            background-color: #dc3545;
        }

        .action-buttons a.delete:hover {
            background-color: #c82333;
        }

        .action-buttons a.pdf {
            background-color: #17a2b8;
        }

        .action-buttons a.pdf:hover {
            background-color: #138496;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Vizualizează Pacienți</h1>

        <div class="search-box">
            <form method="POST">
                <label for="searchCNP">Caută pacient după CNP:</label>
                <input type="text" id="searchCNP" name="searchCNP" value="<?php echo htmlspecialchars($searchCNP); ?>">
                <button type="submit">Caută</button>
            </form>
        </div>

        <?php if ($patientInfo): ?>
            <h2>Informații despre pacient:</h2>
            <p><strong>ID:</strong> <?php echo $patientInfo['id_pacient']; ?></p>
            <p><strong>CNP:</strong> <?php echo $patientInfo['CNP']; ?></p>
            <p><strong>Nume:</strong> <?php echo $patientInfo['nume']; ?></p>
            <p><strong>Prenume:</strong> <?php echo $patientInfo['prenume']; ?></p>
            <p><strong>Adresă:</strong> <?php echo $patientInfo['adresa']; ?></p>
            <p><strong>Data Nașterii:</strong> <?php echo $patientInfo['data_nasterii']; ?></p>
            <p><strong>Vârstă:</strong> <?php echo $patientInfo['varsta']; ?></p>
            <p><strong>Telefon:</strong> <?php echo $patientInfo['telefon']; ?></p>
            <p><strong>Email:</strong> <?php echo $patientInfo['email']; ?></p>

            <h3>Consultări aferente:</h3>
            <table>
                <tr>
                    <th>ID Consultație</th>
                    <th>Data Consultației</th>
                    <th>Diagnostic</th>
                    <th>Medicație</th>
                </tr>
                <?php foreach ($consultations as $consult): ?>
                    <tr>
                        <td><?php echo $consult['id_consultatie']; ?></td>
                        <td><?php echo $consult['data_consultatie']; ?></td>
                        <td><?php echo $consult['diagnostic']; ?></td>
                        <td><?php echo $consult['medicatie']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
            <p style="color: red; text-align: center;">Pacientul nu a fost găsit sau nu aparține acestui doctor.</p>
        <?php endif; ?>

        <h2>Lista tuturor pacienților doctorului:</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>CNP</th>
                <th>Nume</th>
                <th>Prenume</th>
                <th>Adresă</th>
                <th>Data Nașterii</th>
                <th>Vârstă</th>
                <th>Telefon</th>
                <th>Email</th>
                <th>Acțiuni</th>
            </tr>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id_pacient']; ?></td>
                        <td><?php echo $row['CNP']; ?></td>
                        <td><?php echo $row['nume']; ?></td>
                        <td><?php echo $row['prenume']; ?></td>
                        <td><?php echo $row['adresa']; ?></td>
                        <td><?php echo $row['data_nasterii']; ?></td>
                        <td><?php echo $row['varsta']; ?></td>
                        <td><?php echo $row['telefon']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td class="action-buttons">
                            <a href="modifica_pacient.php?id=<?php echo $row['id_pacient']; ?>">Modifică</a>
                            <a href="sterge_pacient.php?id=<?php echo $row['id_pacient']; ?>" class="delete" onclick="return confirm('Ești sigur că vrei să ștergi acest pacient?');">Șterge</a>
                            <a href="pdf_pacient.php?id=<?php echo $row['id_pacient']; ?>" class="pdf">Generare PDF</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="10">Nu există pacienți în baza de date.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>


<?php
$conn->close();
?>
