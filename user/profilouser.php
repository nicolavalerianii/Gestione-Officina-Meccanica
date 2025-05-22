<?php
session_start();
if(!isset($_SESSION["username"]) || !isset($_SESSION["password"])){
    header("location: login.php");
    exit();
}

// Connessione al database
$conn = new mysqli("localhost", "root", "", "progetto");

if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

$cod_cli = $_SESSION["username"];
$targhe = [];

// Recupera tutte le targhe associate all'utente loggato
$sql = "SELECT targa FROM auto WHERE cod_cli = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $cod_cli);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $targhe[] = $row["targa"];
}

$stmt->close();

// Gestione dell'invio del form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $targa = $_POST["targa"];
    $descrizione = $_POST["descrizione"];

    //Metodo per generare un codice prenotazione unico in base all'ultimo coidice prenotazione
    $sql = "SELECT MAX(cod_pre) AS max_cod_pre FROM prenotazione";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $max_cod_pre = $row["max_cod_pre"];
    $cod_pre = $max_cod_pre + 1;

    // Inserimento prenotazione nel database
    $sql_insert = "INSERT INTO prenotazione (cod_pre, descrizione, targa) VALUES ('$cod_pre','$descrizione', '$targa')";
    if ($conn->query($sql_insert) === TRUE) {
        $inserimento_successo = true;
    } else {
        $errore = "Errore durante l'inserimento: " . $conn->error;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleziona Auto</title>

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
                            <li><a class="dropdown-item" href="registraauto.php">Registra una nuova Auto</a></li>
                            <li><a class="dropdown-item" href="visualizzainterventi.php">Visualizza Interventi</a></li>
                        </ul>
                    </li>
                </ul>
                <a href="../logout.php" class="btn btn-warning fw-bold px-4"><i class="bi bi-box-arrow-right"></i></a>
            </div>
        </div>
    </nav>

    <!-- Contenuto -->
    <div class="page-container container" style="margin-top: 120px; max-width: 700px;">
        <div class="card shadow">
            <div class="card-body">
                <h3 class="card-title text-center mb-4">Prenota un Intervento</h3>

                <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] ?>">
                    <div class="mb-3">
                        <label for="targa" class="form-label">Seleziona la tua auto (targa)</label>
                        <select class="form-select" id="targa" name="targa" required>
                            <option value="">-- Seleziona una targa --</option>
                            <?php foreach ($targhe as $targa): ?>
                                <option value="<?php echo htmlspecialchars($targa); ?>"><?php echo htmlspecialchars($targa); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="descrizione" class="form-label">Descrizione</label>
                        <textarea class="form-control" id="descrizione" name="descrizione" rows="4" required></textarea>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-warning px-4">Invia richiesta</button>
                    </div>
                </form>
            </div>
        </div>
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
