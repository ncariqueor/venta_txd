<?php

require_once 'fechas.php';

$con = new mysqli('localhost', 'root', '', 'venta_txd');

$query = "select nomtienda from tiendas";

$res = $con->query($query);

$tiendas = array();

$i = 0;

while($row = mysqli_fetch_assoc($res)){
    $tiendas[$i] = $row['nomtienda'];
    $i++;
}

$tiendas[$i] = fecha(date("Ymd"));

$i++;

echo json_encode($tiendas);