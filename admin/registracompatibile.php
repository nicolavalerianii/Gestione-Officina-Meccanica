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
    // Recupera ricambi e auto compatibili per i dropdown
    $ricambi = [];
    $auto_c = [];
    $res_ricambi = $conn->query("SELECT cod_ricambio, nome FROM ricambi");
    if ($res_ricambi && $res_ricambi->num_rows > 0) {
        while ($row = $res_ricambi->fetch_assoc()) {
            $ricambi[] = $row;
        }
    }
    $res_auto = $conn->query("SELECT cod_auto_c, marca, modello, anno FROM auto_c");
    if ($res_auto && $res_auto->num_rows > 0) {
        while ($row = $res_auto->fetch_assoc()) {
            $auto_c[] = $row;
        }
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $cod_ricambio = isset($_POST["cod_ricambio"]) ? trim($_POST["cod_ricambio"]) : "";
        $cod_auto_c = isset($_POST["cod_auto_c"]) ? trim($_POST["cod_auto_c"]) : "";

        if ($cod_ricambio === "" || $cod_auto_c === "") {
            $errore = "Seleziona sia un ricambio che un'auto compatibile.";
        } else {
            // Calcola il nuovo cod_comp come MAX + 1
            $sql_max = "SELECT MAX(cod_comp) AS max_cod FROM compatibilita";
            $res_max = $conn->query($sql_max);
            $new_cod = 1;
            if ($res_max && $row_max = $res_max->fetch_assoc()) {
                $new_cod = intval($row_max["max_cod"]) + 1;
            }
            // Verifica che la compatibilità non sia già presente
            $cod_ricambio = intval($cod_ricambio);
            $cod_auto_c = intval($cod_auto_c);
            $check = $conn->query("SELECT * FROM compatibilita WHERE cod_ricambio='$cod_ricambio' AND cod_auto_c='$cod_auto_c'");
            if ($check && $check->num_rows > 0) {
                $errore = "Questa compatibilità è già registrata.";
            } else {
                $sql = "INSERT INTO compatibilita (cod_comp, cod_ricambio, cod_auto_c) VALUES ('$new_cod', '$cod_ricambio', '$cod_auto_c')";
                if ($conn->query($sql)) {
                    $successo = "Compatibilità registrata con successo.";
                } else {
                    $errore = "Errore durante la registrazione: " . $conn->error;
                }
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
    <title>Registra Compatibilità Ricambio</title>
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
                            <li><a class="dropdown-item" href="registraricambi.php">Registra Ricambio</a></li>
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
                <h3 class="card-title text-center mb-4">Registra Compatibilità Ricambio</h3>
                <?php if (!empty($errore)): ?>
                    <div class="alert alert-danger text-center"><?php echo $errore; ?></div>
                <?php elseif (!empty($successo)): ?>
                    <div class="alert alert-success text-center"><?php echo $successo; ?></div>
                <?php endif; ?>
                <form method="POST" autocomplete="off">
                    <div class="mb-3">
                        <label for="cod_ricambio" class="form-label">Ricambio</label>
                        <select class="form-select" id="cod_ricambio" name="cod_ricambio" required>
                            <option value="">Seleziona ricambio...</option>
                            <?php foreach($ricambi as $r): ?>
                                <option value="<?php echo $r["cod_ricambio"]; ?>">
                                    <?php echo htmlspecialchars($r["nome"]); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="cod_auto_c" class="form-label">Auto Compatibile</label>
                        <select class="form-select" id="cod_auto_c" name="cod_auto_c" required>
                            <option value="">Seleziona auto compatibile...</option>
                            <?php foreach($auto_c as $a): ?>
                                <option value="<?php echo $a["cod_auto_c"]; ?>">
                                    <?php echo htmlspecialchars($a["marca"] . " " . $a["modello"] . " (" . $a["anno"] . ")"); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Registra Compatibilità</button>
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