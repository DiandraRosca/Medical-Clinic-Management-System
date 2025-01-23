<?php
include 'config.php';

$message = ""; // Variabilă pentru mesaj

// Verificăm dacă s-a trimis formularul
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $id_pacient = $_POST['id_pacient'];
    $nume = $_POST['nume'];
    $prenume = $_POST['prenume'];
    $adresa = $_POST['adresa'];
    $data_nasterii = $_POST['data_nasterii'];
    $varsta = $_POST['varsta'];
    $telefon = $_POST['telefon'];
    $email = $_POST['email'];

    // Actualizare în baza de date
    $query = "UPDATE pacienti SET 
                nume = ?, 
                prenume = ?, 
                adresa = ?, 
                data_nasterii = ?, 
                varsta = ?, 
                telefon = ?, 
                email = ? 
              WHERE id_pacient = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssissi", $nume, $prenume, $adresa, $data_nasterii, $varsta, $telefon, $email, $id_pacient);

    if ($stmt->execute()) {
        $message = "Datele pacientului au fost actualizate cu succes!";
    } else {
        $message = "Eroare la actualizarea datelor: " . $conn->error;
    }
}

// Obține detalii despre pacient
if (isset($_GET['id']) || !empty($id_pacient)) {
    $id_pacient = $id_pacient ?? $_GET['id'];
    $query = "SELECT * FROM pacienti WHERE id_pacient = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_pacient);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
} else {
    echo "ID-ul pacientului nu a fost specificat!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifică pacient</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #000;
            color: #fff;
            margin: 0;
            padding: 0;
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

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 5px;
            color: #ccc;
        }

        input {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #444;
            border-radius: 4px;
            background-color: #333;
            color: #fff;
        }

        input:focus {
            border-color: #007BFF;
            outline: none;
        }

        button {
            padding: 10px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        .centered {
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Modifică datele pacientului</h2>
    <?php if (!empty($message)): ?>
        <p style="color: green; text-align: center;"><?php echo $message; ?></p>
    <?php endif; ?>
    <form action="" method="POST">
        <input type="hidden" name="id_pacient" value="<?php echo $row['id_pacient']; ?>">
        <label for="nume">Nume:</label>
        <input type="text" id="nume" name="nume" value="<?php echo $row['nume']; ?>" required>

        <label for="prenume">Prenume:</label>
        <input type="text" id="prenume" name="prenume" value="<?php echo $row['prenume']; ?>" required>

        <label for="adresa">Adresă:</label>
        <input type="text" id="adresa" name="adresa" value="<?php echo $row['adresa']; ?>" required>

        <label for="data_nasterii">Data nașterii:</label>
        <input type="date" id="data_nasterii" name="data_nasterii" value="<?php echo $row['data_nasterii']; ?>" required>

        <label for="varsta">Vârstă:</label>
        <input type="number" id="varsta" name="varsta" value="<?php echo $row['varsta']; ?>" required>

        <label for="telefon">Telefon:</label>
        <input type="text" id="telefon" name="telefon" value="<?php echo $row['telefon']; ?>" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo $row['email']; ?>" required>

        <div class="centered">
            <button type="submit" name="update">Salvează modificările</button>
        </div>
        <div class="centered">
    <a href="vizualizeaza_pacienti.php" class="back-button">Înapoi la lista pacienților</a>
</div>

    </form>
</div>

</body>
</html>
