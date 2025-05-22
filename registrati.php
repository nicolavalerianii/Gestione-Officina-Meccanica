<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrazione - Officina Meccanica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-tools"></i> Officina Meccanica</a>
            <div class="d-flex">
                <a href="index.php" class="btn btn-warning fw-bold">Home</a>
            </div>
        </div>
    </nav>

    <div class="container d-flex justify-content-center align-items-center" style="height: auto; padding-top: 50px; padding-bottom: 50px;">
        <div class="card shadow-lg p-4" style="width: 400px;">
            <h3 class="text-center text-dark mb-3">Registrati</h3>

            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <div class="mb-2">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-2">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="mb-2">
                    <label for="conferma_password" class="form-label">Conferma Password</label>
                    <input type="password" class="form-control" id="conferma_password" name="conferma_password" required>
                </div>
                <div class="mb-2">
                    <label for="cognome" class="form-label">Cognome</label>
                    <input type="text" class="form-control" id="cognome" name="cognome" required>
                </div>
                <div class="mb-2">
                    <label for="nome" class="form-label">Nome</label>
                    <input type="text" class="form-control" id="nome" name="nome" required>
                </div>
                <div class="mb-2">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-2">
                    <label for="c_f" class="form-label">Codice Fiscale</label>
                    <input type="text" class="form-control" id="c_f" name="c_f" required>
                </div>
                <div class="mb-3">
                    <label for="telefono" class="form-label">Telefono</label>
                    <input type="text" class="form-control" id="telefono" name="telefono" required>
                </div>

                <button type="submit" class="btn btn-warning w-100 fw-bold">Registrati</button>
            </form>

            <div class="text-center mt-3">
                <a href="login.php" class="text-decoration-none">Hai già un account? Accedi</a>
            </div>
        </div>
    </div>

    <div class="container mt-auto">
        <footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top">
            <div class="col-md-4 d-flex align-items-center">
                <span class="mb-3 mb-md-0 text-body-secondary">© 2025 Potsdam Autohaus SRL, tutti i diritti riservati.</span>
            </div>
            <ul class="nav col-md-4 justify-content-end list-unstyled d-flex">
                <li class="ms-3"><a class="text-body-secondary" href="http://instagram.com"><i class="bi bi-instagram"></i></a></li>
                <li class="ms-3"><a class="text-body-secondary" href="http://facebook.com"><i class="bi bi-facebook"></i></a></li>
            </ul>
        </footer>
    </div>

    <?php
        $messaggio = "";
        if (!empty($_POST["username"])) {
            $username = $_POST["username"];
            $password = $_POST["password"];
            $conferma_password = $_POST["conferma_password"];
            $cognome = $_POST["cognome"];
            $nome = $_POST["nome"];
            $email = $_POST["email"];
            $c_f = $_POST["c_f"];
            $telefono = $_POST["telefono"];
            $tipo = "user";

            if ($password !== $conferma_password) {
                $messaggio = "Le password non coincidono";
            } else {
                $host = "localhost";
                $user = "root";
                $pwd = "";
                $db = "progetto";

                $conn = new mysqli($host, $user, $pwd, $db);

                if ($conn->connect_error) {
                    $messaggio = "Connessione fallita: " . $conn->connect_error;
                } else {
                    $checkQuery = "SELECT * FROM utenti WHERE username = '$username'";
                    $result = $conn->query($checkQuery);

                    if ($result->num_rows > 0) {
                        $messaggio = "Username già esistente";
                    } else {

                        $password_crittata = md5($password);

                        $insertQuery = "INSERT INTO utenti (username, password, cognome, nome, email, c_f, telefono, tipo) 
                                        VALUES ('$username', '$password_crittata', '$cognome', '$nome', '$email', '$c_f', '$telefono', '$tipo')";

                        if ($conn->query($insertQuery) === TRUE) {
                            $messaggio = "Registrazione avvenuta con successo";
                        } else {
                            $messaggio = "Errore durante la registrazione";
                        }
                    }

                    $conn->close();
                }
            }

            echo "<script>alert('$messaggio');</script>";
        }
    ?>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
