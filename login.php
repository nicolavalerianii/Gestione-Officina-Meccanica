<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Officina Meccanica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" 
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-tools"></i> Officina Meccanica</a>
            <div class="d-flex">
                <a href="index.php" class="btn btn-warning fw-bold">Home</a>
            </div>
        </div>
    </nav>
    
    <div class="container d-flex justify-content-center align-items-center" style="height: 80vh;">
        <div class="card shadow-lg p-4" style="width: 350px;">
            <h3 class="text-center text-dark mb-3">Accedi</h3>

            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <div class="mb-3">
                    <label for="user" class="form-label">Username</label>
                    <input type="text" class="form-control" id="user" name="user" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-warning w-100 fw-bold">Login</button>
            </form>

            <div class="text-center mt-3">
                <a href="registrati.php" class="text-decoration-none">Registrati</a>
            </div>
        </div>
    </div>
    
    <div class="container mt-auto">
        <footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top">
            <div class="col-md-4 d-flex align-items-center">
                <span class="mb-3 mb-md-0 text-body-secondary">© 2025 Potsdam Autohaus SRL, tutti i diritti riservati.</span>
            </div>
            <ul class="nav col-md-4 justify-content-end list-unstyled d-flex">
                <li class="ms-3"><a class="text-body-secondary" href="http://instagram.com"><i class="bi bi-instagram"></i></a></li>
                <li class="ms-3"><a class="text-body-secondary" href="http://facebook.com"><i class="bi bi-facebook"></i></a></li>
            </ul>
        </footer>
    </div>

    <?php
        if(!empty($_POST["user"])){$username=$_POST["user"];}else{$username=null;};
        if(!empty($_POST["password"])){$password = $_POST["password"];}else{$password=null;};
        
        $host = "localhost";
        $user = "root";
        $pwd = "";
        $db = "progetto";

        $connessione = new mysqli($host, $user, $pwd, $db);

        if($connessione->connect_error){
            echo "Connessione fallita: ".$connessione->connect_error.".";
            exit();
        }

        $password_crittata = md5($password);

        $query="SELECT username,password,cognome,nome,tipo FROM utenti WHERE username='$username' AND password='$password_crittata'";

        if(!$tabella_risultato = $connessione->query($query)){
            echo "Errore nella query: ".$connessione->error.".";
            exit();
        }else{
            if($tabella_risultato->num_rows > 0) {
                $row = $tabella_risultato->fetch_array(MYSQLI_ASSOC);

                session_start();
                $_SESSION['username'] = $username;
                $_SESSION['password'] = $password;
                $_SESSION['cognome'] = $row["cognome"];
                $_SESSION["nome"] = $row["nome"];

                if($row["tipo"]=="admin"){
                    header("Location: admin/profiloadmin.php");
                } elseif($row["tipo"]=="user"){
                    header("Location: user/profilouser.php");
                }elseif($row["tipo"]=="oper"){
                    header("Location: operatore/profilooperatore.php");
                }
            }else{
                if(!empty($_POST["user"]))
                    echo "<div class='text-center text-danger fw-bold'>Credenziali non valide</div>";
            }

            $tabella_risultato->close();
            $connessione->close();
        }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" 
    crossorigin="anonymous"></script>   
</body>
</html>
