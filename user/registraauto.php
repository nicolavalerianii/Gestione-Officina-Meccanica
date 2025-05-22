<?php
    session_start();
    if(!isset($_SESSION["username"]) || !isset($_SESSION["password"])){
        header("location: login.php");
        exit();
    }

    $inserimento_successo = false;
    $errore = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $conn = new mysqli("localhost", "root", "", "progetto");

        if ($conn->connect_error) {
            $errore = "Connessione fallita: " . $conn->connect_error;
        } else {
            $targa = $conn->real_escape_string($_POST["targa"]);
            $marca = $conn->real_escape_string($_POST["marca"]);
            $modello = $conn->real_escape_string($_POST["modello"]);
            $anno = intval($_POST["anno"]);
            $cod_cli = $_SESSION["username"];

            $sql = "INSERT INTO auto (targa, marca, modello, anno, cod_cli)
                    VALUES ('$targa', '$marca', '$modello', $anno, '$cod_cli')";

            if ($conn->query($sql) === TRUE) {
                $inserimento_successo = true;
            } else {
                $errore = "Errore durante l'inserimento: " . $conn->error;
            }

            $conn->close();
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profilo Utente</title>

    <style>
        html, body {
        height: 90%;
        margin: 0;
        }
        .page-container {
            min-height: 90%;
            display: flex;
            flex-direction: column;
        }
        .content {
            flex: 1;
        }
    </style>

</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#"><i class="bi bi-tools"></i> Potsdam Autohaus</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            Menu
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profilouser.php">Home</a></li>
                            <li><a class="dropdown-item" href="visualizzainterventi.php">Visualizza Interventi</a></li>
                        </ul>
                    </li>
                </ul>
                <a href="../logout.php" class="btn btn-warning fw-bold px-4"><i class="bi bi-box-arrow-right"></i></a>
            </div>
        </div>
    </nav>

    <div class="page-container container" style="margin-top: 120px; max-width: 700px;">


        <div class="card shadow">
            <div class="card-body">
                <h3 class="card-title text-center mb-4">Registra una nuova Auto</h3>
                <form method="POST" action="">
                    <div class="mb-3 mx-5">
                        <label for="targa" class="form-label">Targa</label>
                        <input type="text" class="form-control" id="targa" name="targa" required>
                    </div>
                    <div class="mb-3 mx-5">
                        <label for="marca" class="form-label">Marca</label>
                        <input type="text" class="form-control" id="marca" name="marca" required>
                    </div>
                    <div class="mb-3 mx-5">
                        <label for="modello" class="form-label">Modello</label>
                        <input type="text" class="form-control" id="modello" name="modello" required>
                    </div>
                    <div class="mb-3 mx-5">
                        <label for="anno" class="form-label">Anno</label>
                        <input type="number" class="form-control" id="anno" name="anno" required>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-warning px-4" style="font-weight: bold;">Registra Auto</button>
                    </div>
                </form>
            </div>
        </div><br>
        <?php if ($inserimento_successo): ?>
            <div class="alert alert-success text-center">Auto registrata con successo!</div>
        <?php elseif (!empty($errore)): ?>
            <div class="alert alert-danger text-center"><?php echo $errore; ?></div>
        <?php endif; ?>
    </div>
    
    <!--Footer-->
    <footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top container">
            <div class="col-md-4 d-flex align-items-center">
                <span class="mb-3 mb-md-0 text-body-secondary">© 2025 Potsdam Autohaus SRL, tutti i diritti riservati.</span>
            </div>
            <ul class="nav col-md-4 justify-content-end list-unstyled d-flex">
                <li class="ms-3"><a class="text-body-secondary" href="http://instagram.com"><i class="bi bi-instagram"></i></a></li>
                <li class="ms-3"><a class="text-body-secondary" href="http://facebook.com"><i class="bi bi-facebook"></i></a></li>
            </ul>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
