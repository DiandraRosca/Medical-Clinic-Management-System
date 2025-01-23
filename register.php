<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Înregistrare</title>
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
        input, button, select {
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
        .error-message {
            color: #ff4c4c;
            font-size: 14px;
            text-align: left;
            margin-top: -8px;
            margin-bottom: 10px;
        }
        .success-message {
            color: #28a745;
            font-size: 16px;
            text-align: center;
            margin-top: 20px;
        }
    </style>
    <script>
        function validateForm() {
            const cnp = document.getElementById('cnp').value;
            const cnpRegex = /^[1-8]\d{12}$/; // Formatul CNP
            const cnpError = document.getElementById('cnpError');
            if (!cnpRegex.test(cnp)) {
                cnpError.textContent = "CNP-ul este invalid! Asigurați-vă că are 13 cifre și începe cu o cifră validă.";
                return false;
            }
            cnpError.textContent = ""; // Șterge mesajul de eroare dacă este valid
            return true;
        }
    </script>
</head>
<body>
    <h2>Înregistrare</h2>

    <?php if (isset($_GET['message'])): ?>
        <p class="success-message">
            <?php echo htmlspecialchars($_GET['message']); ?>
            <a href="login.php">Autentifică-te aici</a>
        </p>
    <?php else: ?>
        <form method="POST" action="register_action.php" onsubmit="return validateForm()">
            <input type="text" name="nume" placeholder="Nume" required>
            <input type="text" name="prenume" placeholder="Prenume" required>
            <input type="text" id="cnp" name="cnp" placeholder="CNP" required>
            <div id="cnpError" class="error-message"></div>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Parola" required>
            <select name="specialitate" required>
                <option value="">Selectați specialitatea</option>
                <?php
                include 'config.php';
                $query = "SELECT nume_specialitate FROM specialitati";
                $result = $conn->query($query);
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['nume_specialitate']}'>{$row['nume_specialitate']}</option>";
                }
                ?>
            </select>
            <button type="submit" name="register">Creează Cont</button>
        </form>
    <?php endif; ?>
</body>
</html>
