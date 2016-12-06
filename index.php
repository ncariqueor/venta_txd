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
                    <div class="col-lg-12"><h1 class="text-center"><a href="http://10.95.17.114/paneles"><img src="paris.png" width="140px" height="100px"></a>Venta Todas Las Tiendas</h1></div>
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

                <form class="row" method="post" action="index.php">
                    <div class="col-lg-2">
                        <div class="text-center"><span class="label label-primary" style="font-size: 13px;">Seleccione día actual</span></div>
                        <div class="input-group date" data-provide="datepicker">
                            <input name='fecha' class="form-control" type="text" value="<?php
                            date_default_timezone_set("America/Santiago");

                            require_once 'fecha_es.php';

                            if(isset($_POST['fecha'])){
                                echo $_POST['fecha'];
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

                            if(isset($_POST['anterior']))
                                echo $_POST['anterior'];
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

                    <div class="col-lg-3"><br>
                        <select name="depto" id="depto" class="form-control">
                            <?php
                            $ventas = new mysqli('localhost', 'root', '', 'ventas');

                            if(isset($_POST['depto'])){
                                $depto = $_POST['depto'];

                                if($depto == 'todos')
                                    echo "<option value='todos' selected='selected'>Todos los Departamentos</option>";
                                else
                                    echo "<option value='todos'>Todos los Departamentos</option>";

                                $query = "select depto1, nomdepto from depto where division <> ''";

                                $res = $ventas->query($query);

                                while($row = mysqli_fetch_assoc($res)){
                                    $depto1 = $row['depto1'];
                                    $nomdepto = $row['nomdepto'];
                                    if($depto == $depto1)
                                        echo "<option value='$depto1' selected='selected'>$depto1 - $nomdepto</option>";
                                    else
                                        echo "<option value='$depto1'>$depto1 - $nomdepto</option>";
                                }
                            }else{
                                $query = "select depto1, nomdepto from depto where division <> ''";

                                $res = $ventas->query($query);

                                echo "<option value='todos' selected='selected'>Todos los Departamentos</option>";

                                while($row = mysqli_fetch_assoc($res)){
                                    $depto1 = $row['depto1'];
                                    $nomdepto = $row['nomdepto'];
                                    echo "<option value='$depto1'>$depto1 - $nomdepto</option>";
                                }
                            }
                            ?>
                        </select>
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

        <?php
            require_once 'paneles.php';



            if(isset($_POST['fecha']) && isset($_POST['anterior']) && isset($_POST['depto'])){
                $fecha = utf8_decode($_POST['fecha']);
                $fechaAnt = utf8_decode($_POST['anterior']);
                $depto = $_POST['depto'];

                $fecha = str_split($fecha);
                $fecha = $fecha[11] . $fecha[12] . $fecha[13] . $fecha[14] . $fecha[8] . $fecha[9] . $fecha[5] . $fecha[6];

                $fechaAnt = str_split($fechaAnt);
                $fechaAnt = $fechaAnt[11] . $fechaAnt[12] . $fechaAnt[13] . $fechaAnt[14] . $fechaAnt[8] . $fechaAnt[9] . $fechaAnt[5] . $fechaAnt[6];

                echo "<div class='container'>";
                todas($fecha, $fechaAnt, $venta_txd, $depto);
                echo "</div>";

            }else{
                require_once 'fechas.php';

                $fecha = date("Ymd");

                $fechaAnt = fecha($fecha);

                echo "<div class='container'>";
                todas($fecha, $fechaAnt, $venta_txd, 'todos');
                echo "</div>";

            }
        ?>

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