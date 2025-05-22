<?php
session_start();
if(!isset($_SESSION["username"]) || !isset($_SESSION["password"])){
    header("location: login.php");
    exit();
}

$errore = "";
$successo = "";

// Connessione al database
$conn = new mysqli("localhost", "root", "", "progetto");
if ($conn->connect_error) {
    $errore = "Connessione fallita: " . $conn->connect_error;
} else {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nome = isset($_POST["nome"]) ? $conn->real_escape_string(trim($_POST["nome"])) : "";
        $descrizione = isset($_POST["descrizione"]) ? $conn->real_escape_string(trim($_POST["descrizione"])) : "";
        $costo = isset($_POST["costo"]) ? str_replace(",", ".", trim($_POST["costo"])) : "";

        if (empty($nome) || empty($descrizione) || empty($costo)) {
            $errore = "Tutti i campi sono obbligatori.";
        } elseif (!is_numeric($costo) || floatval($costo) < 0) {
            $errore = "Il costo deve essere un numero positivo.";
        } else {
            // Calcola il nuovo cod_ricambio come MAX + 1
            $sql_max = "SELECT MAX(cod_ricambio) AS max_cod FROM ricambi";
            $res_max = $conn->query($sql_max);
            $new_cod = 1;
            if ($res_max && $row_max = $res_max->fetch_assoc()) {
                $new_cod = intval($row_max["max_cod"]) + 1;
            }
            $costo = floatval($costo);
            $sql = "INSERT INTO ricambi (cod_ricambio, nome, descrizione, costo) VALUES ('$new_cod', '$nome', '$descrizione', '$costo')";
            if ($conn->query($sql)) {
                $successo = "Ricambio registrato con successo.";
            } else {
                $errore = "Errore durante la registrazione: " . $conn->error;
            }
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registra Ricambio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        html, body { height: 90%; margin: 0; }
        .page-container { min-height: 90%; display: flex; flex-direction: column; }
        .content { flex: 1; }
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
                            <li><a class="dropdown-item" href="profiloadmin.php">Home</a></li>    
                            <li><a class="dropdown-item" href="autocompatibile.php">Registra una nuova Auto compatibile</a></li>
                            <li><a class="dropdown-item" href="registracompatibile.php">Registra Compatibilità</a></li>
                        </ul>
                    </li>
                </ul>
                <a href="../logout.php" class="btn btn-warning fw-bold px-4"><i class="bi bi-box-arrow-right"></i></a>
            </div>
        </div>
    </nav>

    <div class="page-container container" style="margin-top: 120px; max-width: 500px;">
        <div class="card shadow">
            <div class="card-body">
                <h3 class="card-title text-center mb-4">Registra Ricambio</h3>
                <?php if (!empty($errore)): ?>
                    <div class="alert alert-danger text-center"><?php echo $errore; ?></div>
                <?php elseif (!empty($successo)): ?>
                    <div class="alert alert-success text-center"><?php echo $successo; ?></div>
                <?php endif; ?>
                <form method="POST" autocomplete="off">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="descrizione" class="form-label">Descrizione</label>
                        <textarea class="form-control" id="descrizione" name="descrizione" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="costo" class="form-label">Costo (€)</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="costo" name="costo" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Registra</button>
                </form>
            </div>
        </div>
    </div>

    <footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top container">
        <div class="col-md-4 d-flex align-items-center">
            <span class="mb-3 mb-md-0 text-body-secondary">© 2025 Potsdam Autohaus SRL, tutti i diritti riservati.</span>
        </div>
        <ul class="nav col-md-4 justify-content-end list-unstyled d-flex">
            <li class="ms-3"><a class="text-body-secondary" href="http://instagram.com"><i class="bi bi-instagram"></i></a></li>
            <li class="ms-3"><a class="text-body-secondary" href="http://facebook.com"><i class="bi bi-facebook"></i></a></li>
        </ul>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>