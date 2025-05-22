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

// Gestione form submit per concludere intervento
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["cod_int"], $_POST["ore"])) {
    $cod_int = $conn->real_escape_string($_POST["cod_int"]);
    $ore = floatval($_POST["ore"]);
    $data_oggi = date("Y-m-d");

    // Recupera la tariffa dell'intervento
    $sql_tariffa = "SELECT tariffa FROM intervento WHERE cod_int = '$cod_int' AND cod_op = '$username' AND stato = 'In Corso'";
    $res_tariffa = $conn->query($sql_tariffa);
    $tariffa = 0;
    if ($res_tariffa && $row_tariffa = $res_tariffa->fetch_assoc()) {
        $tariffa = floatval($row_tariffa['tariffa']);
    }

    // Calcola il costo aggiuntivo
    $costo_ore = $ore * $tariffa;

    // Aggiorna data_f, stato, ore e aggiungi il costo delle ore al costo totale
    $update_sql = "UPDATE intervento 
                   SET data_f = '$data_oggi', stato = 'Finito', ore = $ore, costo = IFNULL(costo,0) + $costo_ore
                   WHERE cod_int = '$cod_int' AND cod_op = '$username' AND stato = 'In Corso'";

    if ($conn->query($update_sql)) {
        if ($conn->affected_rows > 0) {
            $messaggio = "<div class='alert alert-success text-center'>Intervento $cod_int concluso con successo!</div>";
        } else {
            $messaggio = "<div class='alert alert-warning text-center'>Intervento non trovato o già concluso.</div>";
        }
    } else {
        $messaggio = "<div class='alert alert-danger text-center'>Errore durante l'aggiornamento: " . $conn->error . "</div>";
    }
}

// Recupera interventi "In Corso" dell'operatore
$interventi = [];
$sql = "SELECT cod_int, cod_pre, data, costo 
        FROM intervento 
        WHERE cod_op = '$username' AND stato = 'In Corso'
        ORDER BY data DESC";

$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $interventi[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Concludi Intervento</title>
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
                        <li><a class="dropdown-item" href="visualizzainterventi.php">Visualizza Interventi</a></li>
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
    <h2 class="mb-4 text-center">Concludi Intervento</h2>

    <?php echo $messaggio; ?>

    <?php if (!empty($interventi)): ?>
        <form method="POST" class="mx-auto" style="max-width: 500px;">
            <div class="mb-3">
                <label for="cod_int" class="form-label">Seleziona Intervento in Corso</label>
                <select class="form-select" id="cod_int" name="cod_int" required>
                    <option value="" disabled selected>-- Scegli intervento --</option>
                    <?php foreach ($interventi as $int): ?>
                        <option value="<?php echo $int['cod_int']; ?>">
                            Cod: <?php echo $int['cod_int']; ?> | Prenotazione: <?php echo $int['cod_pre']; ?> | Data inizio: <?php echo $int['data']; ?> | Costo: <?php echo isset($int['costo']) ? $int['costo'] : 'N/D'; ?> €
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="ore" class="form-label">Numero di ore lavorate</label>
                <input type="number" step="0.1" min="0" name="ore" id="ore" class="form-control" placeholder="Ore lavorate" required>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-danger"><i class="bi bi-check2-square"></i> Termina Intervento</button>
            </div>
        </form>
    <?php else: ?>
        <div class="alert alert-info text-center">Non ci sono interventi in corso da concludere.</div>
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
