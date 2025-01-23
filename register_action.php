<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $nume = $_POST['nume'];
    $prenume = $_POST['prenume'];
    $cnp = $_POST['cnp'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $specialitate = $_POST['specialitate'];

    // Validare CNP
    if (!preg_match('/^[1-8]\d{12}$/', $cnp)) {
        header("Location: register.php?message=CNP-ul este invalid! Asigurați-vă că are 13 cifre și începe cu o cifră validă.");
        exit;
    }

    // Obține id_specialitate pe baza numelui specialității
    $query_specialitate = "SELECT id_specialitate FROM specialitati WHERE nume_specialitate = ?";
    $stmt = $conn->prepare($query_specialitate);
    $stmt->bind_param("s", $specialitate);
    $stmt->execute();
    $result_specialitate = $stmt->get_result();

    if ($result_specialitate->num_rows == 1) {
        $specialitate_data = $result_specialitate->fetch_assoc();
        $id_specialitate = $specialitate_data['id_specialitate'];

        // Verifică dacă email-ul există deja
        $check_query = "SELECT email FROM doctori WHERE email = ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $check_result = $stmt->get_result();

        if ($check_result->num_rows > 0) {
            header("Location: register.php?message=Acest email este deja utilizat!");
        } else {
            // Inserare nou doctor
            $insert_query = "INSERT INTO doctori (nume, prenume, CNP, email, parola, id_specialitate)
                             VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("sssssi", $nume, $prenume, $cnp, $email, $password, $id_specialitate);

            if ($stmt->execute()) {
                header("Location: register.php?message=Cont creat cu succes! ");
            } else {
                header("Location: register.php?message=Eroare la crearea contului!");
            }
        }
    } else {
        header("Location: register.php?message=Specialitatea selectată nu este validă!");
    }
}
?>
