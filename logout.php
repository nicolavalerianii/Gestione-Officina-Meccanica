<?php
    session_start();
    session_unset(); // Rimuove tutte le variabili di sessione
    session_destroy(); // Termina la sessione
    header("Location: index.php"); // Reindirizza a index.php
    exit();
?>