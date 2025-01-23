<?php
session_start();

include 'config.php'; // Conectare la baza de date

if ($conn->connect_error) {
    die("Conexiune eșuată: " . $conn->connect_error);
}

// Obține CNP-ul doctorului logat
$cnp_doctor_logat = $_SESSION['doctor_cnp'] ?? null;

if (!$cnp_doctor_logat) {
    die("Eroare: Nu sunteți autentificat corect. Conectați-vă din nou.");
}

// Preluare detalii consultație pentru modificare
$consultatie_pentru_editare = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $query_edit = "SELECT * FROM consultatii WHERE id_consultatie = ? AND CNP_doctor = ?";
    $stmt = $conn->prepare($query_edit);
    $stmt->bind_param("is", $edit_id, $cnp_doctor_logat);
    $stmt->execute();
    $result_edit = $stmt->get_result();

    if ($result_edit->num_rows > 0) {
        $consultatie_pentru_editare = $result_edit->fetch_assoc();
    } else {
        echo "<p style='color: red;'>Consultația nu există sau nu aveți permisiunea să o modificați.</p>";
        exit;
    }
}

// Funcționalitate de actualizare consultație
if (isset($_POST['modifica_consultatie'])) {
    $id_consultatie = $_POST['id_consultatie'];
    $data_consultatie = $_POST['data_consultatie'];
    $diagnostic = $_POST['diagnostic'];
    $medicatie = $_POST['medicatie'];

    $query_update = "UPDATE consultatii SET data_consultatie = ?, diagnostic = ?, medicatie = ? WHERE id_consultatie = ? AND CNP_doctor = ?";
    $stmt = $conn->prepare($query_update);
    $stmt->bind_param("sssii", $data_consultatie, $diagnostic, $medicatie, $id_consultatie, $cnp_doctor_logat);

    if ($stmt->execute()) {
        echo "<p style='color: green;'>Consultația a fost actualizată cu succes!</p>";
        // Redirecționare la pagina principală
        header("Location: vizualizeaza_consultatii.php");
        exit;
    } else {
        echo "<p style='color: red;'>Eroare la actualizare: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifică Consultație</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #000;
            color: #fff;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            width: 100%;
            max-width: 600px;
            background-color: #1a1a1a;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.7);
        }

        h2 {
            text-align: center;
            color: #fff;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
            color: #ccc;
        }

        input, textarea, button {
            width: 100%;
            margin-bottom: 15px;
            padding: 12px;
            border: 1px solid #444;
            border-radius: 5px;
            background-color: #333;
            color: #fff;
        }

        input:focus, textarea:focus {
            border-color: #007BFF;
            outline: none;
        }

        button {
            background-color: #007BFF;
            color: #fff;
            font-size: 16px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }

        .success-message {
            color: green;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($consultatie_pentru_editare): ?>
        <h2>Modifică Consultația</h2>
        <form action="" method="POST">
            <input type="hidden" name="id_consultatie" value="<?php echo $consultatie_pentru_editare['id_consultatie']; ?>">

            <label for="data_consultatie">Data Consultației</label>
            <input type="date" name="data_consultatie" id="data_consultatie" value="<?php echo $consultatie_pentru_editare['data_consultatie']; ?>" required>

            <label for="diagnostic">Diagnostic</label>
            <textarea name="diagnostic" id="diagnostic" rows="3" required><?php echo $consultatie_pentru_editare['diagnostic']; ?></textarea>

            <label for="medicatie">Medicație</label>
            <textarea name="medicatie" id="medicatie" rows="3" required><?php echo $consultatie_pentru_editare['medicatie']; ?></textarea>

            <button type="submit" name="modifica_consultatie">Salvează Modificările</button>
        </form>
        <?php else: ?>
        <p class="error-message">Consultația nu a fost găsită sau nu aveți permisiunea să o modificați.</p>
        <?php endif; ?>
    </div>
</body>
</html>
