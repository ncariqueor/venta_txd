<?php
ini_set("max_execution_time", 0);

date_default_timezone_set("America/Santiago");

$roble = odbc_connect('CECEBUGD', 'USRVNP', 'USRVNP');

$ventas = new mysqli('localhost', 'root', '', 'ventas');

$venta_txd = new mysqli('localhost', 'root', '', 'venta_txd');

$dia = date("Ymd");

$query = "delete from ingreso where fectrantsl = $dia";

$res = $venta_txd->query($query);

$query = "select tienda from tiendas";

$res = $venta_txd->query($query);

$cant = mysqli_num_rows($res);

$i = 0;

$in = "";

while($row = mysqli_fetch_assoc($res)){
    $in = $in . $row['tienda'];
    if($i < $cant-1)
        $in = $in . ", ";

    $i++;
}

$inicio = date("H", strtotime("-1 hour")) . "00";

$fin    = date("H", strtotime("-1 hour")) . "59";

$query = "select SVVSF01.FECTRANTSL,

SVVSF01.FECTRANTSL||SVVSF01.NUMCTLTSL||SVVSF01.NUMTERTSL||SVVSF01.NUMTRANTSL,

                     SVVSF01.NUMCTLTSL,
                     SVVSF00.HORTRANTSL,
                     LEFT(RIGHT(SVVSF01.CODARTTSL,11),9),
                     SVVSF01.DEPARTTSL,
                     SVVSF01.CANARTTSL,
                     SVVSF01.TOTARTTSL,
                     SVVSF01.DSCARTTSL,
                     SVVSF01.SUBDEPTO,
                     EXKPF01.DESSDP,
                     EXKPF01.CODMAR,
                     EXKPF01.COSPROM*SVVSF01.CANARTTSL as venta_costo

        FROM        RDBPARIS2.EXGCBUGD.EXKPF01 EXKPF01,
                    RDBPARIS2.SVALBUGD.SVVSF00 SVVSF00,
                    RDBPARIS2.SVALBUGD.SVVSF01 SVVSF01

        WHERE       (LEFT(RIGHT(SVVSF01.CODARTTSL,11),6)=EXKPF01.ESTILO) AND
                    (SVVSF00.FECTRANTSL=SVVSF01.FECTRANTSL) AND
                    (SVVSF00.NUMCORRDUP=SVVSF01.NUMCORRDUP) AND
                    (SVVSF00.NUMCTLTSL=SVVSF01.NUMCTLTSL) AND
                    (SVVSF00.NUMTERTSL=SVVSF01.NUMTERTSL) AND
                    (SVVSF00.NUMTRANTSL=SVVSF01.NUMTRANTSL) AND
                    (SVVSF01.FECTRANTSL =$dia)

            and SVVSF01.NUMCTLTSL in ($in)
";
$res = odbc_exec($roble, $query);

while (odbc_fetch_row($res)) {
    $fectrantsl  = odbc_result($res, 1);
    $boleta      = odbc_result($res, 2);
    $tienda      = odbc_result($res, 3);
    $hortrantsl  = odbc_result($res, 4);
    $sku         = odbc_result($res, 5);
    $dep         = odbc_result($res, 6);
    $canvend     = odbc_result($res, 7);
    $total       = odbc_result($res, 8);
    $descuento   = odbc_result($res, 9);
    $subdepto    = odbc_result($res, 10);
    $dessdp      = odbc_result($res, 11);
    $codmar      = odbc_result($res, 12);
    $venta_costo = odbc_result($res, 13);

    $query = "select nomdepto, division from depto where depto1 = $dep";

    $result = $ventas->query($query);

    while ($row = mysqli_fetch_assoc($result)) {
        $desdep = $row['nomdepto'];
        $division = $row['division'];
    }

    $query = "select nomtienda, zona from tiendas where tienda = $tienda";

    $result = $venta_txd->query($query);

    while ($row = mysqli_fetch_assoc($result)) {
        $nomtienda = $row['nomtienda'];
        $zona = $row['zona'];
    }

    $query = "insert into ingreso values ($fectrantsl,
                                               $hortrantsl,
                                               $boleta,
                                               $tienda,
                                              '$nomtienda',
                                              '$zona',
                                               $sku,
                                               $dep,
                                              '$desdep',
                                              $subdepto,
                                              '$dessdp',
                                               '$division',
                                               $canvend,
                                               $total,
                                              '$codmar',
                                               $venta_costo,
                                               $descuento)";

    if ($venta_txd->query($query))
        echo "Se inserto con exito $fectrantsl\n";
    else
        echo "Error " . $venta_txd->error . "\n";
}

$fecha = date("Ymd");

$hora = date("Hi");

$venta_txd->query("delete from actualizado");

$venta_txd->query("insert into actualizado values($fecha, $hora)");
