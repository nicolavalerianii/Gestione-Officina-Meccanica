<?php
session_start();
if (!isset($_SESSION["username"]) || !isset($_SESSION["password"])) {
    header("location: ../login.php");
    exit();
}

// Connessione al database
$conn = new mysqli("localhost", "root", "", "progetto");

if ($conn->connect_error) {
    $errore = "Connessione fallita: " . $conn->connect_error;
} else {
    $username = $conn->real_escape_string($_SESSION["username"]);
    $interventi = [];

    // Query per ottenere solo gli interventi "In Corso" dell'operatore loggato
    $sql = "SELECT i.cod_int, i.cod_pre, i.data, i.data_f, i.costo, p.descrizione, a.targa
            FROM intervento i
            JOIN prenotazione p ON i.cod_pre = p.cod_pre
            JOIN auto a ON p.targa = a.targa
            WHERE i.cod_op = '$username' AND i.stato = 'In Corso'
            ORDER BY i.data DESC";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $interventi[] = $row;
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Profilo Operatore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

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
                            <li><a class="dropdown-item" href="incarico.php">Prendi in Carico</a></li>
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
        <h2 class="mb-4 text-center">Interventi Presi in Carico</h2>

        <?php if (!empty($interventi)): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>Cod. Intervento</th>
                            <th>Cod. Prenotazione</th>
                            <th>Auto (Targa)</th>
                            <th>Descrizione</th>
                            <th>Data Inizio</th>
                            <th>Data Fine</th>
                            <th>Costo (€)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($interventi as $int): ?>
                            <tr>
                                <td class="text-center"><?php echo $int["cod_int"]; ?></td>
                                <td class="text-center"><?php echo $int["cod_pre"]; ?></td>
                                <td class="text-center"><?php echo $int["targa"]; ?></td>
                                <td><?php echo htmlspecialchars($int["descrizione"]); ?></td>
                                <td class="text-center"><?php echo $int["data"]; ?></td>
                                <td class="text-center"><?php echo isset($int["data_f"]) ? $int["data_f"] : "In corso"; ?></td>
                                <td class="text-center"><?php echo number_format($int["costo"], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">Nessun intervento ancora preso in carico.</div>
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
