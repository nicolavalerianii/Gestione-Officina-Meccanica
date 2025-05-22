<?php
session_start();
if (!isset($_SESSION["username"]) || !isset($_SESSION["password"])) {
    header("location: ../login.php");
    exit();
}

// Connessione al database
$conn = new mysqli("localhost", "root", "", "progetto");
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

$username = $conn->real_escape_string($_SESSION["username"]);
$messaggio = "";

// Se l'operatore ha cliccato su "Prendi in carico"
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["cod_pre"], $_POST["tariffa"])) {
    $cod_pre = $conn->real_escape_string($_POST["cod_pre"]);
    $tariffa_input = $_POST["tariffa"];

    if (is_numeric($tariffa_input) && floatval($tariffa_input) >= 0) {
        $tariffa = $conn->real_escape_string($tariffa_input);

        // Verifica che la prenotazione non sia già stata presa in carico
        $check_sql = "SELECT * FROM intervento WHERE cod_pre = '$cod_pre'";
        $check_result = $conn->query($check_sql);

        if ($check_result && $check_result->num_rows == 0) {

             // Ottieni il nuovo cod_int
            $query_max = "SELECT MAX(cod_int) AS max_cod FROM intervento";
            $result_max = $conn->query($query_max);
            $nuovo_cod_int = 1; // default se non esistono interventi

            if ($result_max && $row_max = $result_max->fetch_assoc()) {
                $nuovo_cod_int = $row_max["max_cod"] + 1;
            }


            $data_oggi = date("Y-m-d");
            $insert_sql = "INSERT INTO intervento (cod_int, cod_pre, cod_op, data, data_f, costo, stato, tariffa, ore)
                       VALUES ($nuovo_cod_int, '$cod_pre', '$username', '$data_oggi', NULL, NULL, 'In Corso', '$tariffa', 0)";

            if ($conn->query($insert_sql)) {
                $messaggio = "<div class='alert alert-success text-center'>Prenotazione <strong>$cod_pre</strong> presa in carico con successo!</div>";
            } else {
                $messaggio = "<div class='alert alert-danger text-center'>Errore durante l'inserimento: " . $conn->error . "</div>";
            }
        } else {
            $messaggio = "<div class='alert alert-warning text-center'>Prenotazione già presa in carico.</div>";
        }
    } else {
        $messaggio = "<div class='alert alert-warning text-center'>Tariffa non valida. Inserire un valore numerico maggiore o uguale a 0.</div>";
    }
}

// Recupera le prenotazioni non ancora prese in carico
$prenotazioni = [];
$sql = "SELECT p.cod_pre, p.descrizione, a.targa
        FROM prenotazione p
        JOIN auto a ON p.targa = a.targa
        LEFT JOIN intervento i ON p.cod_pre = i.cod_pre
        WHERE i.cod_pre IS NULL";

$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $prenotazioni[] = $row;
    }
}
$conn->close();
?>



<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Prendi in Carico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        html, body { height: 90%; margin: 0; }
        .page-container { min-height: 90%; display: flex; flex-direction: column; }
        .content { flex: 1; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#"><i class="bi bi-tools"></i> Potsdam Autohaus</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        Menu
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="profilooperatore.php">Profilo</a></li>
                        <li><a class="dropdown-item" href="concludi.php">Concludi Intervento</a></li>
                        <li><a class="dropdown-item" href="ricambi.php">Richiedi Ricambio</a></li>
                        <li><a class="dropdown-item" href="interventiconclusi.php">Interventi Conclusi</a></li>
                    </ul>
                </li>
            </ul>
            <a href="../logout.php" class="btn btn-warning fw-bold px-4"><i class="bi bi-box-arrow-right"></i></a>
        </div>
    </div>
</nav>

<div class="container page-container" style="margin-top: 100px;">
    <h2 class="mb-4 text-center">Prenotazioni Disponibili</h2>

    <?php echo $messaggio; ?>

    <?php if (!empty($prenotazioni)): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>Cod. Prenotazione</th>
                        <th>Auto (Targa)</th>
                        <th>Descrizione</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($prenotazioni as $pre): ?>
                        <tr>
                            <td class="text-center"><?php echo $pre["cod_pre"]; ?></td>
                            <td class="text-center"><?php echo $pre["targa"]; ?></td>
                            <td><?php echo htmlspecialchars($pre["descrizione"]); ?></td>
                            <td class="text-center">
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="cod_pre" value="<?php echo $pre["cod_pre"]; ?>">
                                    <div class="input-group mb-2">
                                        <input type="number" step="0.01" min="0" name="tariffa" class="form-control" placeholder="Tariffa oraria (€)" required>
                                        <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Prendi in carico</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">Non ci sono prenotazioni disponibili al momento.</div>
    <?php endif; ?>
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
