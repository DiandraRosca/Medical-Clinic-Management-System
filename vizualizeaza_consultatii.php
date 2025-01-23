<?php
session_start();

include 'config.php'; // Conectare la baza de date

if ($conn->connect_error) {
    die("Conexiune eșuată: " . $conn->connect_error);
}

// Verificăm dacă medicul este autentificat
if (!isset($_SESSION['doctor_cnp'])) {
    die("Eroare: Nu sunteți autentificat. Vă rugăm să vă conectați.");
} 

// Obține CNP-ul doctorului logat
$cnp_doctor_logat = $_SESSION['doctor_cnp'];

// Inițializarea variabilelor
$rezultate_cautare = [];
$cnp_cautat = "";

// Funcționalitate de căutare după CNP Pacient
if (isset($_POST['cauta_consultatii'])) {
    $cnp_cautat = $_POST['cnp_pacient_cautare'];

    $query_cautare = "SELECT * FROM consultatii WHERE CNP_pacient = '$cnp_cautat' AND CNP_doctor = '$cnp_doctor_logat'";
    $result_cautare = $conn->query($query_cautare);

    if ($result_cautare->num_rows > 0) {
        $rezultate_cautare = $result_cautare->fetch_all(MYSQLI_ASSOC);
    } else {
        $mesaj = "Nu au fost găsite consultații pentru CNP-ul căutat.";
    }
}

// Funcționalitate de adăugare a unei consultații
if (isset($_POST['adauga_consultatie'])) {
    $cnp_pacient = $_POST['cnp_pacient'];
    $data_consultatie = $_POST['data_consultatie'];
    $diagnostic = $_POST['diagnostic'];
    $medicatie = $_POST['medicatie'];

    $query_insert = "INSERT INTO consultatii (CNP_pacient, CNP_doctor, data_consultatie, diagnostic, medicatie)
                     VALUES ('$cnp_pacient', '$cnp_doctor_logat', '$data_consultatie', '$diagnostic', '$medicatie')";

    if ($conn->query($query_insert)) {
        echo "<p style='color: green;'>Consultația a fost adăugată cu succes!</p>";
    } else {
        echo "<p style='color: red;'>Eroare la adăugare: " . $conn->error . "</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vizualizează Consultații</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #000;
            color: #fff;
            margin: 0;
            padding: 20px;
        }

        h2, h3 {
            text-align: center;
            color: #fff;
        }

        form {
            margin-bottom: 20px;
            padding: 20px;
            border: 1px solid #444;
            border-radius: 8px;
            background-color: #1a1a1a;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        label {
            font-weight: bold;
            color: #ccc;
        }

        input, textarea, button {
            display: block;
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            margin-bottom: 15px;
            border: 1px solid #444;
            border-radius: 5px;
            background-color: #333;
            color: #fff;
        }

        button {
            background-color: #007BFF;
            border: none;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
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

        .add-btn {
            display: inline-block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #28a745;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            transition: background-color 0.3s;
        }

        .add-btn:hover {
            background-color: #218838;
        }

        .centered {
            text-align: center;
            margin-top: 20px;
        }

        .action-delete {
            color: red;
        }

        .action-delete:hover {
            color: #ff4c4c;
        }
    </style>
</head>
<body>
    <h2>Vizualizează Consultații</h2>

    <!-- Formular de căutare -->
    <form action="" method="POST">
        <label for="cnp_pacient_cautare">Caută consultații după CNP:</label>
        <input type="text" name="cnp_pacient_cautare" id="cnp_pacient_cautare" placeholder="Introdu CNP-ul pacientului" value="<?php echo htmlspecialchars($cnp_cautat); ?>" required>
        <button type="submit" name="cauta_consultatii">Caută</button>
    </form>

    <!-- Formular pentru generare PDF -->
    <form action="pdf_afectiune.php" method="POST">
        <label for="diagnostic">Introduceți numele afecțiunii:</label>
        <input type="text" id="diagnostic" name="diagnostic" placeholder="Ex: Astm bronșic" required>
        <button type="submit">Generare PDF</button>
    </form>

    <!-- Formular de adăugare -->
    <h3>Adaugă o Nouă Consultație</h3>
    <form action="" method="POST">
        <label for="cnp_pacient">CNP Pacient</label>
        <input type="text" name="cnp_pacient" id="cnp_pacient" required>

        <label for="data_consultatie">Data Consultației</label>
        <input type="date" name="data_consultatie" id="data_consultatie" required>

        <label for="diagnostic">Diagnostic</label>
        <textarea name="diagnostic" id="diagnostic" rows="3" required></textarea>

        <label for="medicatie">Medicație</label>
        <textarea name="medicatie" id="medicatie" rows="3" required></textarea>

        <button type="submit" name="adauga_consultatie">Adaugă Consultație</button>
    </form>

    <!-- Rezultatele căutării -->
    <?php if (!empty($rezultate_cautare)): ?>
    <h3>Rezultatele Căutării</h3>
    <table>
        <tr>
            <th>ID Consultație</th>
            <th>Pacient</th>
            <th>Data Consultației</th>
            <th>Diagnostic</th>
            <th>Medicație</th>
            <th>Acțiuni</th>
        </tr>
        <?php foreach ($rezultate_cautare as $row): ?>
        <tr>
            <td><?php echo $row['id_consultatie']; ?></td>
            <td><?php echo $row['CNP_pacient']; ?></td>
            <td><?php echo $row['data_consultatie']; ?></td>
            <td><?php echo $row['diagnostic']; ?></td>
            <td><?php echo $row['medicatie']; ?></td>
            <td>
                <a href="modifica_consultatie.php?edit_id=<?php echo $row['id_consultatie']; ?>">Modifică</a>
                |
                <a href="sterge_consultatie.php?delete_id=<?php echo $row['id_consultatie']; ?>" class="action-delete" onclick="return confirm('Ești sigur că vrei să ștergi această consultație?');">Șterge</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php elseif (isset($mesaj)): ?>
    <p style="color: red; text-align: center;"><?php echo $mesaj; ?></p>
    <?php endif; ?>

    <!-- Tabel complet dacă nu este căutare -->
    <?php if (empty($rezultate_cautare) && empty($cnp_cautat)): ?>
    <h3>Lista Consultațiilor</h3>
    <?php
    $query = "SELECT * FROM consultatii WHERE CNP_doctor = '$cnp_doctor_logat'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        echo "<table>
                <tr>
                    <th>ID Consultație</th>
                    <th>Pacient</th>
                    <th>Data Consultației</th>
                    <th>Diagnostic</th>
                    <th>Medicație</th>
                    <th>Acțiuni</th>
                </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['id_consultatie']}</td>
                    <td>{$row['CNP_pacient']}</td>
                    <td>{$row['data_consultatie']}</td>
                    <td>{$row['diagnostic']}</td>
                    <td>{$row['medicatie']}</td>
                    <td>
                        <a href='modifica_consultatie.php?edit_id={$row['id_consultatie']}'>Modifică</a> |
                        <a href='sterge_consultatie.php?delete_id={$row['id_consultatie']}' class='action-delete'>Șterge</a>
                    </td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='text-align: center;'>Nu există consultații pentru doctorul logat.</p>";
    }
    ?>
    <?php endif; ?>
</body>
</html>
