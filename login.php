<?php
session_start();
include 'config.php';

$error_message = ""; // Mesaj pentru erori

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Verificare utilizator în baza de date
    $query = "SELECT id_doctor, CNP, nume, prenume, email FROM doctori WHERE email = ? AND parola = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $doctor = $result->fetch_assoc();

        // Setăm datele utilizatorului în sesiune
        $_SESSION['doctor_id'] = $doctor['id_doctor'];
        $_SESSION['doctor_cnp'] = $doctor['CNP'];
        $_SESSION['doctor_name'] = $doctor['nume'] . " " . $doctor['prenume'];

        // Verificăm rolul utilizatorului
        if ($doctor['email'] === 'director@clinica.com') {
            $_SESSION['role'] = 'director';
        } else {
            $_SESSION['role'] = 'doctor';
        }

        // Redirecționare către dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        $error_message = "Email sau parolă incorectă!";
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autentificare</title>
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
        .message {
            color: #ff4c4c;
        }
        .register-link {
            margin-top: 10px;
            display: inline-block;
            text-decoration: none;
            color: #007BFF;
        }
        .register-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h2>Autentificare</h2>
    <form method="POST" action="">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Parola" required>
        <button type="submit" name="login">Autentificare</button>
    </form>

    <a href="register.php" class="register-link">Nu ai cont? Creează unul aici</a>
</body>
</html>
