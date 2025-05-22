<?php
session_start();
if (!isset($_SESSION["username"]) || !isset($_SESSION["password"])) {
    header("location: login.php");
    exit();
}

$prenotazioni = [];
$errore = "";
$successo = "";
$stato_prenotazione = "";

// Connessione al database
$conn = new mysqli("localhost", "root", "", "progetto");

if ($conn->connect_error) {
    $errore = "Connessione fallita: " . $conn->connect_error;
} else {
    $cod_cli = $conn->real_escape_string($_SESSION["username"]);

    // Gestione cancellazione
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["cod_pre"]) && isset($_POST["azione"])) {
        $cod_pre = $conn->real_escape_string($_POST["cod_pre"]);

        if ($_POST["azione"] === "cancella") {
            // Verifica che la prenotazione appartenga all'utente
            $check_sql = "SELECT p.cod_pre FROM prenotazione p
                          JOIN auto a ON p.targa = a.targa
                          WHERE p.cod_pre = '$cod_pre' AND a.cod_cli = '$cod_cli'";

            $check_result = $conn->query($check_sql);

            if ($check_result && $check_result->num_rows > 0) {
                $delete_sql = "DELETE FROM prenotazione WHERE cod_pre = '$cod_pre'";
                if ($conn->query($delete_sql)) {
                    $successo = "Prenotazione cancellata con successo.";
                } else {
                    $errore = "Errore durante la cancellazione: " . $conn->error;
                }
            } else {
                $errore = "Prenotazione non trovata o non autorizzata.";
            }
        } 
    }

    // Recupera le prenotazioni aggiornate
    $sql = "SELECT p.cod_pre, a.targa, p.descrizione, i.stato, i.costo
        FROM auto a
        JOIN prenotazione p ON a.targa = p.targa
        LEFT JOIN intervento i ON i.cod_pre = p.cod_pre
        WHERE a.cod_cli = '$cod_cli'";

    
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $prenotazioni[] = $row;
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Le tue prenotazioni</title>

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
                            <li><a class="dropdown-item" href="registraauto.php">Registra una nuova Auto</a></li>
                        </ul>
                    </li>
                </ul>
                <a href="../logout.php" class="btn btn-warning fw-bold px-4"><i class="bi bi-box-arrow-right"></i></a>
            </div>
        </div>
    </nav>

    <div class="page-container container" style="margin-top: 120px; max-width: 800px;">
        <div class="card shadow">
            <div class="card-body">
                <h3 class="card-title text-center mb-4">Le tue Prenotazioni</h3>

                <?php if (!empty($errore)): ?>
                    <div class="alert alert-danger text-center"><?php echo $errore; ?></div>
                <?php elseif (!empty($successo)): ?>
                    <div class="alert alert-success text-center"><?php echo $successo; ?></div>
                <?php elseif (!empty($stato_prenotazione)): ?>
                    <div class="alert alert-info text-center"><?php echo $stato_prenotazione; ?></div>
                <?php endif; ?>

                <?php if (empty($prenotazioni)): ?>
                    <div class="alert alert-info text-center">Nessuna prenotazione trovata.</div>
                <?php else: ?>
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Codice</th>
                                <th>Targa</th>
                                <th>Descrizione</th>
                                <th>Stato</th>
                                <th>Costo</th>
                                <th>Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($prenotazioni as $p): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($p["cod_pre"]); ?></td>
                                    <td><?php echo htmlspecialchars($p["targa"]); ?></td>
                                    <td><?php echo htmlspecialchars($p["descrizione"]); ?></td>
                                    <td>
                                        <?php 
                                            if($p["stato"]!=null){
                                                echo htmlspecialchars($p["stato"]); 
                                            }else{
                                                echo htmlspecialchars("Non ancora presa in carico.");
                                            }
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                            if($p["stato"]!=null && isset($p["costo"])){
                                                echo htmlspecialchars(number_format($p["costo"], 2)) . " €";
                                            }else{
                                                echo "0 €";
                                            }
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <form method="POST" action = "<?php $_SERVER['PHP_SELF']?>" style="display:inline-block;">
                                            <input type="hidden" name="cod_pre" value="<?php echo htmlspecialchars($p["cod_pre"]); ?>">
                                            <input type="hidden" name="azione" value="cancella">
                                            <?php if($p["stato"]==null){ ?>
                                              <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i> Cancella</button>
                                            <?php } ?>
                                            
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
