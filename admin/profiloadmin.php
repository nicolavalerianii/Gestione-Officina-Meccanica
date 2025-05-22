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
    // Gestione modifica ruolo ed eliminazione utente
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["username"])) {
        $username = $conn->real_escape_string($_POST["username"]);

        // Eliminazione utente e dati associati
        if (isset($_POST["elimina"])) {
            // Elimina prenotazioni e interventi associati all'utente
            // 1. Trova tutte le targhe delle auto dell'utente
            $targhe = [];
            $sql_targhe = "SELECT targa FROM auto WHERE cod_cli = '$username'";
            $res_targhe = $conn->query($sql_targhe);
            if ($res_targhe && $res_targhe->num_rows > 0) {
                while ($row = $res_targhe->fetch_assoc()) {
                    $targhe[] = $row["targa"];
                }
            }
            // 2. Per ogni targa elimina interventi e prenotazioni
            foreach ($targhe as $targa) {
                // Trova tutte le prenotazioni per questa targa
                $sql_pre = "SELECT cod_pre FROM prenotazione WHERE targa = '$targa'";
                $res_pre = $conn->query($sql_pre);
                if ($res_pre && $res_pre->num_rows > 0) {
                    while ($row = $res_pre->fetch_assoc()) {
                        $cod_pre = $row["cod_pre"];
                        // Elimina interventi associati alla prenotazione
                        $conn->query("DELETE FROM intervento WHERE cod_pre = '$cod_pre'");
                    }
                }
                // Elimina prenotazioni per questa targa
                $conn->query("DELETE FROM prenotazione WHERE targa = '$targa'");
            }
            // 3. Elimina auto dell'utente
            $conn->query("DELETE FROM auto WHERE cod_cli = '$username'");
            // 4. Elimina l'utente
            if ($conn->query("DELETE FROM utenti WHERE username = '$username'")) {
                $successo = "Utente e dati associati eliminati con successo.";
            } else {
                $errore = "Errore durante l'eliminazione: " . $conn->error;
            }
        }
        // Modifica ruolo
        elseif (isset($_POST["tipo"])) {
            $tipo = $conn->real_escape_string($_POST["tipo"]);
            $update_sql = "UPDATE utenti SET tipo = '$tipo' WHERE username = '$username'";
            if ($conn->query($update_sql)) {
                $successo = "Ruolo aggiornato con successo.";
            } else {
                $errore = "Errore durante l'aggiornamento: " . $conn->error;
            }
        }
    }

    // Recupera tutti gli utenti
    $utenti = [];
    $sql = "SELECT username, tipo FROM utenti";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $utenti[] = $row;
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
    <title>Pannello Admin</title>
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
                            <li><a class="dropdown-item" href="autocompatibile.php">Registra una nuova Auto compatibile</a></li>
                            <li><a class="dropdown-item" href="registraricambi.php">Registra Ricambi</a></li>
                            <li><a class="dropdown-item" href="registracompatibile.php">Registra Compatibilità</a></li>
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
                <h3 class="card-title text-center mb-4">Gestione Utenti</h3>
                <?php if (!empty($errore)): ?>
                    <div class="alert alert-danger text-center"><?php echo $errore; ?></div>
                <?php elseif (!empty($successo)): ?>
                    <div class="alert alert-success text-center"><?php echo $successo; ?></div>
                <?php endif; ?>

                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Username</th>
                            <th>Ruolo</th>
                            <th>Modifica Ruolo</th>
                            <th>Elimina</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($utenti as $utente): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($utente["username"]); ?></td>
                                <td><?php echo htmlspecialchars($utente["tipo"]); ?></td>
                                <td>
                                    <form method="POST" class="d-flex align-items-center" style="gap: 8px;">
                                        <input type="hidden" name="username" value="<?php echo htmlspecialchars($utente["username"]); ?>">
                                        <select name="tipo" class="form-select form-select-sm" style="width: auto;">
                                            <option value="admin" <?php if($utente["tipo"]=="admin") echo "selected"; ?>>admin</option>
                                            <option value="user" <?php if($utente["tipo"]=="user") echo "selected"; ?>>user</option>
                                            <option value="oper" <?php if($utente["tipo"]=="oper") echo "selected"; ?>>oper</option>
                                        </select>
                                        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-pencil"></i> Salva</button>
                                    </form>
                                </td>
                                <td>
                                    <form method="POST" onsubmit="return confirm('Sei sicuro di voler eliminare questo utente? Verranno eliminati anche tutti i dati associati!');">
                                        <input type="hidden" name="username" value="<?php echo htmlspecialchars($utente["username"]); ?>">
                                        <button type="submit" name="elimina" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i> Elimina</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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