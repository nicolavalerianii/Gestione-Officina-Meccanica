<?php
session_start();
if (!isset($_SESSION["username"]) || !isset($_SESSION["password"])) {
    header("location: ../login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "progetto");
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

$username = $conn->real_escape_string($_SESSION["username"]);
$messaggio = "";

// Gestione invio richiesta ricambio
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["cod_int"], $_POST["cod_ricambio"])) {
    $cod_int = $conn->real_escape_string($_POST["cod_int"]);
    $cod_ricambio = $conn->real_escape_string($_POST["cod_ricambio"]);

    // Calcola nuovo cod_ri
    $sql_max = "SELECT MAX(cod_ri) AS max_ri FROM richieste";
    $res_max = $conn->query($sql_max);
    $cod_ri = 1;
    if ($res_max && $row_max = $res_max->fetch_assoc()) {
        $cod_ri = $row_max["max_ri"] + 1;
    }

    // Prendi il costo del ricambio
    $sql_costo = "SELECT costo FROM ricambi WHERE cod_ricambio = '$cod_ricambio'";
    $res_costo = $conn->query($sql_costo);
    $costo_ricambio = 0;
    if ($res_costo && $row_costo = $res_costo->fetch_assoc()) {
        $costo_ricambio = $row_costo["costo"];
    }

    // Inserisci la richiesta
    $sql_insert = "INSERT INTO richieste (cod_ri, cod_int, cod_ricambio) VALUES ($cod_ri, '$cod_int', '$cod_ricambio')";
    if ($conn->query($sql_insert)) {
        // Aggiorna il costo dell'intervento
        $sql_update = "UPDATE intervento SET costo = IFNULL(costo,0) + $costo_ricambio WHERE cod_int = '$cod_int'";
        $conn->query($sql_update);
        $messaggio = "<div class='alert alert-success text-center'>Richiesta inserita e costo aggiornato!</div>";
    } else {
        $messaggio = "<div class='alert alert-danger text-center'>Errore nell'inserimento della richiesta: " . $conn->error . "</div>";
    }
}

// Recupera interventi disponibili per l'operatore (solo "In Corso")
$interventi = [];
$sql_int = "SELECT cod_int, cod_pre FROM intervento WHERE cod_op = '$username' AND stato = 'In Corso'";
$res_int = $conn->query($sql_int);
if ($res_int && $res_int->num_rows > 0) {
    while ($row = $res_int->fetch_assoc()) {
        $interventi[] = $row;
    }
}

// Recupera ricambi disponibili
$ricambi = [];
$sql_ric = "SELECT cod_ricambio, nome, costo FROM ricambi";
$res_ric = $conn->query($sql_ric);
if ($res_ric && $res_ric->num_rows > 0) {
    while ($row = $res_ric->fetch_assoc()) {
        $ricambi[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Richiedi Ricambio</title>
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
                        <li><a class="dropdown-item" href="incarico.php">Prendi in Carico</a></li>
                        <li><a class="dropdown-item" href="concludi.php">Concludi Intervento</a></li>
                        <li><a class="dropdown-item" href="interventiconclusi.php">Interventi Conclusi</a></li>
                    </ul>
                </li>
            </ul>
            <a href="../logout.php" class="btn btn-warning fw-bold px-4"><i class="bi bi-box-arrow-right"></i></a>
        </div>
    </div>
</nav>

<div class="container page-container" style="margin-top: 100px;">
    <h2 class="mb-4 text-center">Richiedi Ricambio</h2>
    <?php echo $messaggio; ?>

    <form method="POST" class="mx-auto" style="max-width: 500px;">
        <div class="mb-3">
            <label for="cod_int" class="form-label">Intervento (In Corso)</label>
            <select class="form-select" id="cod_int" name="cod_int" required>
                <option value="" disabled selected>-- Seleziona Intervento --</option>
                <?php foreach ($interventi as $int): ?>
                    <option value="<?php echo $int['cod_int']; ?>">
                        Cod: <?php echo $int['cod_int']; ?> | Prenotazione: <?php echo $int['cod_pre']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="cod_ricambio" class="form-label">Ricambio</label>
            <select class="form-select" id="cod_ricambio" name="cod_ricambio" required>
                <option value="" disabled selected>-- Seleziona prima un intervento --</option>
            </select>
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Conferma Richiesta</button>
        </div>
    </form>
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
<script>
document.getElementById('cod_int').addEventListener('change', function() {
    var cod_int = this.value;
    var ricambioSelect = document.getElementById('cod_ricambio');
    ricambioSelect.innerHTML = '<option value="" disabled selected>Caricamento...</option>';
    fetch('get_ricambi_compatibili.php?cod_int=' + encodeURIComponent(cod_int))
        .then(response => response.json())
        .then(data => {
            ricambioSelect.innerHTML = '';
            if (data.length === 0) {
                ricambioSelect.innerHTML = '<option value="" disabled selected>Nessun ricambio compatibile</option>';
            } else {
                ricambioSelect.innerHTML = '<option value="" disabled selected>-- Seleziona Ricambio --</option>';
                data.forEach(function(ric) {
                    ricambioSelect.innerHTML += `<option value="${ric.cod_ricambio}">${ric.nome} (${ric.costo} €)</option>`;
                });
            }
        });
});
</script>
</body>
</html>