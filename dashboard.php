<?php
session_start();

// Verificăm dacă utilizatorul este autentificat
if (!isset($_SESSION['doctor_id'])) {
    die("Nu sunteți autentificat. Vă rugăm să vă conectați.");
}

// Obținem datele utilizatorului din sesiune
$doctor_name = $_SESSION['doctor_name'];
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #000;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .dashboard-container {
            text-align: center;
            background-color: #1a1a1a;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.7);
        }
        .dashboard-container h1 {
            color: #fff;
        }
        .dashboard-container a {
            display: block;
            margin: 10px 0;
            padding: 10px 20px;
            background-color: #007BFF;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }
        .dashboard-container a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1>Bun venit, <?php echo htmlspecialchars($doctor_name); ?>!</h1>
        <a href="adauga_pacient.php">Adaugă Pacient</a>
        <a href="vizualizeaza_pacienti.php">Vizualizează Pacienți</a>
        <a href="vizualizeaza_consultatii.php">Vizualizează Consultații</a>
        <?php if ($role == 'director'): ?>
            <a href="statistici.php">Statistici</a>
        <?php endif; ?>
        <a href="logout.php">Deconectare</a>
    </div>
</body>
</html>
