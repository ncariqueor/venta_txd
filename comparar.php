<?php

function prueba($tienda, $fecha, $fechaAnt, $ventas)
{
    $venta = new mysqli('localhost', 'root', '', 'ventas');

    ini_set("max_execution_time", 0);

    $query = "select max(hortrantsl) as hortrantsl from ingreso where fectrantsl = $fecha";

    $res = $ventas->query($query);

    $hora = 0;

    while($row = mysqli_fetch_assoc($res)){
        $hora = $row['hortrantsl'];
    }

    $query = "select sum(totarttsl - descuento)/1.19 as ingresoneto, sum(venta_costo) as venta_costo, division, dep from ingreso where fectrantsl = $fecha and nomtienda = '$tienda' group by dep";

    if(date("Ymd") == $fecha)
        $query = "select sum(totarttsl - descuento)/1.19 as ingresoneto, sum(venta_costo) as venta_costo, division, dep from ingreso where fectrantsl = $fecha and hortrantsl <= $hora and nomtienda = '$tienda' group by dep";

    $tienda_act = array();
    $ingreso_neto_act = array();
    $venta_costo_act = array();
    $zona_act = array();

    $res = $ventas->query($query);

    $i = 0;

    while($row = mysqli_fetch_assoc($res)){
        $tienda_act[$i] = $row['dep'];
        $ingreso_neto_act[$i] = round($row['ingresoneto']);
        $venta_costo_act[$i] = round($row['venta_costo']);
        $zona_act[$i] = $row['division'];
        $i++;
    }

    $query = "select sum(totarttsl - descuento)/1.19 as ingresoneto, sum(venta_costo) as venta_costo, division, dep from ingreso where fectrantsl = $fechaAnt and nomtienda = '$tienda' group by dep";

    if(date("Ymd") == $fecha)
        $query = "select sum(totarttsl - descuento)/1.19 as ingresoneto, sum(venta_costo) as venta_costo, division, dep from ingreso where fectrantsl = $fechaAnt and hortrantsl <= $hora and nomtienda = '$tienda' group by dep";

    $tienda_ant = array();
    $ingreso_neto_ant = array();
    $venta_costo_ant = array();
    $zona_ant = array();

    $res = $ventas->query($query);

    $i = 0;

    while($row = mysqli_fetch_assoc($res)){
        $tienda_ant[$i] = $row['dep'];
        $ingreso_neto_ant[$i] = round($row['ingresoneto']);
        $venta_costo_ant[$i] = round($row['venta_costo']);
        $zona_ant[$i] = $row['division'];

        $i++;
    }

    $query = "select depto1, division, concat(depto1, ' - ', nomdepto) as depto from depto";

    $nomtienda = array();
    $zona = array();
    $tiendas = array();

    $res = $venta->query($query);

    $i = 0;

    while($row = mysqli_fetch_assoc($res)){
        $zona[$i] = $row['division'];
        $nomtienda[$i] = $row['depto1'];
        $tiendas[$i] = $row['depto'];
        $i++;
    }

    $count_act = count($tienda_act);

    $count_ant = count($tienda_ant);

    foreach($nomtienda as $shop){
        if(!in_array($shop, $tienda_act)){
            $tienda_act[$count_act] = $shop;
            $ingreso_neto_act[$count_act] = 0;
            $venta_costo_act[$count_act] = 0;
            $zona_act[$count_act] = $zona[array_search($shop, $nomtienda)];
            $count_act++;
        }
        if(!in_array($shop, $tienda_ant)){
            $tienda_ant[$count_ant] = $shop;
            $ingreso_neto_ant[$count_ant] = 0;
            $venta_costo_ant[$count_ant] = 0;
            $zona_ant[$count_ant] = $zona[array_search($shop, $nomtienda)];
            $count_ant++;
        }
    }

    $count = count($tienda_act);

    for($i = 1; $i < $count; $i++){
        for($j = 0; $j < ($count - 1); $j++){
            if($tienda_act[$j] > $tienda_act[$j+1]){
                $tmp_tienda_act = $tienda_act[$j];
                $tienda_act[$j] = $tienda_act[$j+1];
                $tienda_act[$j+1] = $tmp_tienda_act;

                $tmp_ingreso_neto_act = $ingreso_neto_act[$j];
                $ingreso_neto_act[$j] = $ingreso_neto_act[$j+1];
                $ingreso_neto_act[$j+1] = $tmp_ingreso_neto_act;

                $tmp_venta_costo_act = $venta_costo_act[$j];
                $venta_costo_act[$j] = $venta_costo_act[$j+1];
                $venta_costo_act[$j+1] = $tmp_venta_costo_act;

                $tmp_zona_act = $zona_act[$j];
                $zona_act[$j] = $zona_act[$j+1];
                $zona_act[$j+1] = $tmp_zona_act;
            }
        }
    }



    echo "<table class='table'>";
    echo "<thead><tr>";
    echo "<th style=\"color: white; background-color: #00ABFF;\">Zona / Tienda</th>";
    echo "<th style=\"color: white; background-color: #00ABFF;\">Venta Neta Día Actual</th>";
    echo "<th style=\"color: white; background-color: #00ABFF;\">Venta Neta Día Anterior</th>";
    echo "<th style=\"color: white; background-color: #00ABFF;\">Crecimiento</th>";
    echo "<th style=\"color: white; background-color: #00ABFF;\">Margen Día Actual</th>";
    echo "<th style=\"color: white; background-color: #00ABFF;\">Margen Día Anterior</th>";
    echo "<th style=\"color: white; background-color: #00ABFF;\">Var. Margen</th>";
    echo "</tr></thead>";

    //=========================================== TOTAL ACTUAL ==================================

    $total_paris_act = 0;

    $venta_costo_paris_act = 0;

    $count = count($tienda_act);

    for($i = 0; $i < $count; $i++){
        $total_paris_act += $ingreso_neto_act[$i];
        $venta_costo_paris_act += $venta_costo_act[$i];
    }

    $margen_total_paris_act = 0;
    if($total_paris_act > 0)
        $margen_total_paris_act = round((($total_paris_act - $venta_costo_paris_act) / $total_paris_act) * 100, 1);

    //============================ TOTAL ANTERIOR =============================================

    $total_paris_ant = 0;

    $venta_costo_paris_ant = 0;

    $count = count($tienda_ant);

    for($i = 0; $i < $count; $i++){
        $total_paris_ant += $ingreso_neto_ant[$i];
        $venta_costo_paris_ant += $venta_costo_ant[$i];
    }

    $margen_total_paris_ant = 0;
    if($total_paris_ant > 0)
        $margen_total_paris_ant = round((($total_paris_ant - $venta_costo_paris_ant) / $total_paris_ant) * 100, 1);

    //======================================= CALCULO MARGEN Y RPAST ================================================

    $var_margen_paris = $margen_total_paris_act - $margen_total_paris_ant;

    $rpast_paris = 0;
    if($total_paris_ant > 0)
        $rpast_paris = round((($total_paris_act / $total_paris_ant) - 1) * 100, 1);

    $label_paris = "";

    if($rpast_paris > 0)
        $label_paris = "label label-success";

    if($rpast_paris == 0)
        $label_paris = "label label-warning";

    if($rpast_paris < 0)
        $label_paris = "label label-danger";

    echo "<tr style='height: 45px;'><td><b>Total</b></td>";
    echo "<td><h5 class='text-center'><b>" . number_format($total_paris_act, 0, ',', '.') . "</b></h5></td>";
    echo "<td><h5 class='text-center'><b>" . number_format($total_paris_ant, 0, ',', '.') . "</b></h5></td>";
    echo "<td class='text-center'><h5 class='$label_paris' style='font-size: 12px;'>" . number_format($rpast_paris, 1, ',', '.') . " %</h5></td>";
    echo "<td><h5 class='text-center'><b>" . number_format($margen_total_paris_act, 1, ',', '.') . " %</b></h5></td>";
    echo "<td><h5 class='text-center'><b>" . number_format($margen_total_paris_ant, 1, ',', '.') . " %</b></h5></td>";
    echo "<td><h5 class='text-center'><b>" . number_format($var_margen_paris, 1, ',', '.') . "</b></h5></td></tr>";

    //===================================== FIN TOTAL ====================================================
    $zona = array('HOMBRES', 'DEPORTES', 'MUJER', 'ACCESORIOS', 'INFANTIL', 'ELECTRO-HOGAR', 'TECNOLOGIA', 'DECO-HOGAR', 'OTROS');

    $count_act = count($tienda_act);

    $count_ant = count($tienda_ant);

    foreach($zona as $item){
        $total_act = 0;
        $venta_costo_total_act = 0;
        for($i = 0; $i < $count_act; $i++){
            if($zona_act[$i] == $item) {
                $total_act += $ingreso_neto_act[$i];
                $venta_costo_total_act += $venta_costo_act[$i];
            }
        }

        $margen_act = 0;
        if($total_act != 0)
            $margen_act = round((($total_act - $venta_costo_total_act) / $total_act) * 100, 1);

        $total_ant = 0;
        $venta_costo_total_ant = 0;
        for($i = 0; $i < $count_ant; $i++){
            if($zona_ant[$i] == $item) {
                $total_ant += $ingreso_neto_ant[$i];
                $venta_costo_total_ant += $venta_costo_ant[$i];
            }
        }

        $margen_ant = 0;
        if($total_ant != 0)
            $margen_ant = round((($total_ant - $venta_costo_total_ant) / $total_ant) * 100, 1);

        $var_margen = $margen_act - $margen_ant;

        $rpast = 0;
        if($total_ant != 0)
            $rpast = round((($total_act / $total_ant) - 1) * 100, 1);

        $label = "";

        if($rpast > 0)
            $label = "label label-success";

        if($rpast == 0)
            $label = "label label-warning";

        if($rpast < 0)
            $label = "label label-danger";

        $clase = $item;

        echo '<tr style=\'height: 45px;\'><td><h5><a href="#" style="text-decoration: none;" onclick="mostrar'; echo "('.$clase'); return false;"; echo '"><b>' . $item . '</b> <span class="glyphicon glyphicon-collapse-down" aria-hidden="true"></span></h5></a></td>';
        echo "<td><h5 class='text-center'><b>" . number_format($total_act, 0, ',', '.') . "</b></h5></td>";
        echo "<td><h5 class='text-center'><b>" . number_format($total_ant, 0, ',', '.') . "</b></h5></td>";
        echo "<td class='text-center'><h5 class='$label' style='font-size: 12px;'>" . number_format($rpast, 1, ',', '.') . " %</h5></td>";
        echo "<td><h5 class='text-center'><b>" . number_format($margen_act, 1, ',', '.') . " %</b></h5></td>";
        echo "<td><h5 class='text-center'><b>" . number_format($margen_ant, 1, ',', '.') . " %</b></h5></td>";
        echo "<td><h5 class='text-center'><b>" . number_format($var_margen, 1, ',', '.') . "</b></h5></td></tr>";

        for($i = 0; $i < $count_act; $i++){
            if($zona_act[$i] == $item) {
                $tienda = $tienda_act[$i];
                $tienda2 = $tiendas[array_search($tienda, $nomtienda)];

                $total_act = $ingreso_neto_act[$i];
                $total_ant = $ingreso_neto_ant[array_search($tienda, $tienda_ant)];

                $venta_costo_actual = $venta_costo_act[$i];
                $venta_costo_anterior = $venta_costo_ant[array_search($tienda, $tienda_ant)];

                $margen_act = 0;
                if($total_act != 0)
                    $margen_act = round((($total_act - $venta_costo_actual) / $total_act) * 100, 1);

                $margen_ant = 0;
                if($total_ant != 0)
                    $margen_ant = round((($total_ant - $venta_costo_anterior) / $total_ant) * 100, 1);

                $var_margen = $margen_act - $margen_ant;

                $rpast = 0;
                if($total_ant != 0)
                    $rpast = round((($total_act / $total_ant) - 1) * 100, 1);

                $label = "";

                if($rpast > 0)
                    $label = "label label-success";

                if($rpast == 0)
                    $label = "label label-warning";

                if($rpast < 0)
                    $label = "label label-danger";

                echo "<tr><td class='$clase' style='display: table-cell'>$tienda2</td>";
                echo "<td class='$clase' style='display: table-cell'><h5 class='text-center'>" . number_format($total_act, 0, ',', '.') . "</h5></td>";
                echo "<td class='$clase' style='display: table-cell'><h5 class='text-center'>" . number_format($total_ant, 0, ',', '.') . "</h5></td>";
                echo "<td class='text-center $clase' style='display: table-cell'><h5 class='$label' style='font-size: 12px;'>" . number_format($rpast, 1, ',', '.') . " %</h5></td>";
                echo "<td class='$clase' style='display: table-cell'><h5 class='text-center'>" . number_format($margen_act, 1, ',', '.') . " %</h5></td>";
                echo "<td class='$clase' style='display: table-cell'><h5 class='text-center'>" . number_format($margen_ant, 1, ',', '.') . " %</h5></td>";
                echo "<td class='$clase' style='display: table-cell'><h5 class='text-center'>" . number_format($var_margen, 1, ',', '.') . "</h5></td></tr>";
            }
        }
    }

    echo "</table>";
}