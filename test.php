<!DOCTYPE html>
<html>
<head>
    <title>Venta Tiendas</title>
    <link rel="stylesheet" type="text/css" href="bootstrap-3.3.6-dist/css/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="bootstrap-datepicker-master/dist/css/bootstrap-datepicker3.css" />
</head>

<body>
<header class="container">
    <nav class="navbar navbar-default">
        <div class="row">
            <div class="col-lg-12"><h1 class="text-center"><a href="http://10.95.17.114/paneles"><img src="paris.png" width="140" height="100"></a>Tiendas por Departamento</h1></div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <h5 class="text-center">
                    <?php
                    $venta_txd = new mysqli('localhost', 'root', '', 'venta_txd');

                    $query = "select fecha, hora from actualizado";

                    $res = $venta_txd->query($query);

                    while($row = mysqli_fetch_assoc($res)){
                        $fecha = new DateTime($row['fecha']);
                        $hora  = $row['hora'];
                        if(strlen($hora) < 4)
                            $hora = "0" . $hora;

                        $hora = new DateTime($hora);
                        $hora = $hora->format("H:i");
                        $diasem = $fecha->format("D");
                        if($diasem == 'Mon')
                            $diasem = "Lunes";
                        if($diasem == 'Tue')
                            $diasem = "Martes";
                        if($diasem == 'Wed')
                            $diasem = "Miércoles";
                        if($diasem == 'Thu')
                            $diasem = "Jueves";
                        if($diasem == 'Fri')
                            $diasem = "Viernes";
                        if($diasem == 'Sat')
                            $diasem = "Sábado";
                        if($diasem == 'Sun')
                            $diasem = "Domingo";

                        $fecha = $fecha->format("d / m / Y");

                        echo "<p class='label label-info' style='font-size: 15px;'>Última actualización el día $diasem, $fecha a las $hora horas.</p>";

                    }
                    ?>
                </h5>
            </div>
        </div><br>

        <form class="row" method="get" action="test.php">
            <div class="col-lg-2">
                <div class="text-center"><span class="label label-primary" style="font-size: 13px;">Seleccione Tienda</span></div>
                <select style="position: relative;" name="tienda" title="Seleccione Tienda" class="form-control" id="tienda">
                    <?php
                    $ventas = new mysqli('localhost', 'root', '', 'venta_txd');
                    if(isset($_GET['tienda'])){
                        $tienda = $_GET['tienda'];

                        $query = "select tienda, nomtienda, nomtienda2 from tiendas order by tienda asc";

                        $res = $ventas->query($query);

                        while($row = mysqli_fetch_assoc($res)){
                            $shop = $row['tienda'];
                            $nomtienda = $row['nomtienda'];
                            $shop2 = $row['tienda'] . " - " . utf8_encode($row['nomtienda2']);
                            if($tienda == $nomtienda)
                                echo "<option value='$nomtienda' selected='selected'>$shop2</option>";
                            else
                                echo "<option value='$nomtienda'>$shop2</option>";
                        }
                    }else{
                        $query = "select tienda, nomtienda, nomtienda2 from tiendas order by tienda asc";

                        $res = $ventas->query($query);

                        while($row = mysqli_fetch_assoc($res)){
                            $tienda = $row['tienda'];
                            $nomtienda = $row['nomtienda'];
                            $shop = $tienda . " - " . utf8_encode($row['nomtienda2']);
                            echo "<option value='$nomtienda'>$shop</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="col-lg-2">
                <div class="text-center"><span class="label label-primary" style="font-size: 13px;">Seleccione día actual</span></div>
                <div class="input-group date" data-provide="datepicker">
                    <input name='fecha' class="form-control" type="text" value="<?php
                    date_default_timezone_set("America/Santiago");

                    require_once 'fecha_es.php';

                    if(isset($_GET['fecha'])){
                        echo $_GET['fecha'];
                    }else {
                        echo obtenerDia(date("D")) . ", " . date("d/m/Y");
                    }
                    ?>">
                    <div class="input-group-addon">
                        <span class="glyphicon glyphicon-th"></span>
                    </div>
                </div>
            </div>

            <div class="col-lg-2">
                <div class="text-center"><span class="label label-primary" style="font-size: 13px;">Seleccione día Anterior</span></div>
                <div class="input-group date" data-provide="datepicker">
                    <input name='anterior' class="form-control" type="text" value="<?php

                    require_once 'fechas.php';

                    if(isset($_GET['anterior']))
                        echo $_GET['anterior'];
                    else {
                        $fecAnt = fecha(date("Ymd"));
                        echo obtenerDia(date("D", strtotime("{$fecAnt}"))) . ", " . date("d/m/Y", strtotime("{$fecAnt}"));
                    }
                    ?>">
                    <div class="input-group-addon">
                        <span class="glyphicon glyphicon-th"></span>
                    </div>
                </div>
            </div>

            <div class="col-lg-2"><br>
                <div class="dropdown">
                    <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        Seleccione Tipo de Panel
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                        <li><a href="index.php">Todas las Tiendas</a></li>
                        <li><a href="txd.php">Tiendas por Departamento</a></li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-2"><br>
                <button class="btn btn-primary">Actualizar</button>
            </div>
        </form>
    </nav>
</header>

<div class="container">
    <?php
    require_once 'comparar.php';
    if(isset($_GET['fecha']) && isset($_GET['anterior']) && isset($_GET['tienda'])){
        $fecha = utf8_decode($_GET['fecha']);
        $fechaAnt = utf8_decode($_GET['anterior']);
        $tienda = $_GET['tienda'];

        $fecha = str_split($fecha);
        $fecha = $fecha[11] . $fecha[12] . $fecha[13] . $fecha[14] . $fecha[8] . $fecha[9] . $fecha[5] . $fecha[6];

        $fechaAnt = str_split($fechaAnt);
        $fechaAnt = $fechaAnt[11] . $fechaAnt[12] . $fechaAnt[13] . $fechaAnt[14] . $fechaAnt[8] . $fechaAnt[9] . $fechaAnt[5] . $fechaAnt[6];

        prueba($tienda, $fecha, $fechaAnt, $ventas);

    }else{
        require_once 'fechas.php';

        $fecha = date("Ymd");

        $fechaAnt = fecha($fecha);

        prueba('Paris Alameda', $fecha, $fechaAnt, $ventas);

    }
    ?>
</div>

<script src="jquery-1.12.0.min.js"></script>
<script src="bootstrap-3.3.6-dist/js/bootstrap.min.js"></script>
<script src="bootstrap-datepicker-master/dist/js/bootstrap-datepicker.min.js"></script>
<script src="bootstrap-datepicker-master/dist/locales/bootstrap-datepicker.es.min.js"></script>
<script>
    $('.date').datepicker({
        format: 'D, dd/mm/yyyy',
        language: 'es-ES'
    });
</script>
<script>
    function mostrar(id){
        var estado = document.querySelectorAll(id);
        var cant   = estado.length;

        for(var i = 0; i < cant; i++){
            var vista = estado[i].style.display;
            if(vista == 'none')
                vista = 'table-cell';
            else
                vista = 'none';
            estado[i].style.display = vista;
        }
    }
</script>
</body>
</html>