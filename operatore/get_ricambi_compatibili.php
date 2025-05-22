<?php
if (!isset($_GET['cod_int'])) {
    echo json_encode([]);
    exit;
}
$conn = new mysqli("localhost", "root", "", "progetto");
$cod_int = $conn->real_escape_string($_GET['cod_int']);

// Recupera marca, modello, anno dell'auto collegata all'intervento
$sql = "SELECT a.marca, a.modello, a.anno
        FROM intervento i
        JOIN prenotazione p ON i.cod_pre = p.cod_pre
        JOIN auto a ON p.targa = a.targa
        WHERE i.cod_int = '$cod_int'
        LIMIT 1";
$res = $conn->query($sql);
if (!$res || $res->num_rows == 0) {
    echo json_encode([]);
    exit;
}
$row = $res->fetch_assoc();
$marca = $conn->real_escape_string($row['marca']);
$modello = $conn->real_escape_string($row['modello']);
$anno = $conn->real_escape_string($row['anno']);

// Trova il cod_auto_c corrispondente in auto_c
$sql2 = "SELECT cod_auto_c FROM auto_c WHERE marca = '$marca' AND modello = '$modello' AND anno = '$anno' LIMIT 1";
$res2 = $conn->query($sql2);
if (!$res2 || $res2->num_rows == 0) {
    echo json_encode([]);
    exit;
}
$row2 = $res2->fetch_assoc();
$cod_auto_c = $row2['cod_auto_c'];

// Recupera i ricambi compatibili
$sql3 = "SELECT r.cod_ricambio, r.nome, r.costo
         FROM compatibilita c
         JOIN ricambi r ON c.cod_ricambio = r.cod_ricambio
         WHERE c.cod_auto_c = '$cod_auto_c'";
$res3 = $conn->query($sql3);

$ricambi = [];
while ($r = $res3->fetch_assoc()) {
    $ricambi[] = $r;
}
echo json_encode($ricambi);
$conn->close();
?>