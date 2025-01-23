<?php
// Include fișierul de configurare pentru baza de date
include 'config.php';

$errors = [];
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cnp = $_POST['cnp'];
    $nume = $_POST['nume'];
    $prenume = $_POST['prenume'];
    $adresa = $_POST['adresa'];
    $data_nasterii = $_POST['data_nasterii'];
    $varsta = $_POST['varsta'];
    $telefon = $_POST['telefon'];
    $email = $_POST['email'];

    // Validare CNP
    if (!preg_match("/^[1-9][0-9]{12}$/", $cnp)) {
        $errors['cnp'] = "CNP-ul introdus nu este valid.";
    }

    // Validare număr de telefon
    if (!preg_match("/^0[0-9]{9}$/", $telefon)) {
        $errors['telefon'] = "Numărul de telefon trebuie să aibă 10 cifre și să înceapă cu 0.";
    }

    // Verifică dacă nu există erori
    if (empty($errors)) {
        $query = "INSERT INTO pacienti (CNP, nume, prenume, adresa, data_nasterii, varsta, telefon, email) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?);";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssssss", $cnp, $nume, $prenume, $adresa, $data_nasterii, $varsta, $telefon, $email);

        if ($stmt->execute()) {
            $success_message = "Pacient adăugat cu succes!";
        } else {
            $errors['general'] = "Eroare la adăugarea pacientului.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adaugă Pacient</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #000;
            color: #fff;
            text-align: center;
            padding: 20px;
        }

        form {
            background-color: #1a1a1a;
            border: 1px solid #444;
            padding: 20px;
            margin: 20px auto;
            max-width: 400px;
            border-radius: 8px;
        }

        input, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #444;
            border-radius: 5px;
        }

        button {
            background-color: #007BFF;
            color: #fff;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .error {
            color: #ff4c4c;
            font-size: 14px;
            text-align: left;
            margin-top: -10px;
            margin-bottom: 10px;
        }

        .success {
            color: #28a745;
            font-size: 16px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h2>Adaugă Pacient</h2>

    <?php if (!empty($success_message)): ?>
        <p class="success"><?php echo $success_message; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="text" name="cnp" placeholder="CNP" value="<?php echo htmlspecialchars($_POST['cnp'] ?? ''); ?>" required>
        <?php if (isset($errors['cnp'])): ?>
            <p class="error"><?php echo $errors['cnp']; ?></p>
        <?php endif; ?>

        <input type="text" name="nume" placeholder="Nume" value="<?php echo htmlspecialchars($_POST['nume'] ?? ''); ?>" required>
        <input type="text" name="prenume" placeholder="Prenume" value="<?php echo htmlspecialchars($_POST['prenume'] ?? ''); ?>" required>
        <input type="text" name="adresa" placeholder="Adresă" value="<?php echo htmlspecialchars($_POST['adresa'] ?? ''); ?>" required>
        <input type="date" name="data_nasterii" value="<?php echo htmlspecialchars($_POST['data_nasterii'] ?? ''); ?>" required>
        <input type="number" name="varsta" placeholder="Vârstă" value="<?php echo htmlspecialchars($_POST['varsta'] ?? ''); ?>" required>

        <input type="text" name="telefon" placeholder="Telefon" value="<?php echo htmlspecialchars($_POST['telefon'] ?? ''); ?>" required>
        <?php if (isset($errors['telefon'])): ?>
            <p class="error"><?php echo $errors['telefon']; ?></p>
        <?php endif; ?>

        <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>

        <button type="submit">Adaugă pacient</button>

        <?php if (isset($errors['general'])): ?>
            <p class="error"><?php echo $errors['general']; ?></p>
        <?php endif; ?>
    </form>
</body>
</html>
