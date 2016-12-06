<?php

function fecha($hoy){
    $dia = 20140101;
    $cantDias = 1;
    $bisiesto = 0;

    for($day = new DateTime($dia); $day->format("Ymd") <= $hoy; $day->modify("+1 day")){
        $aux = $day->format("Ymd");
        $diaANT = date("Ymd", strtotime("{$aux} -1 year +$cantDias day"));
        if($day->format("Ymd") == $hoy)
            return $diaANT;
        $bis = $day->format("md");
        $anio = $day->format("Y");
        $ceros = str_split($anio);

        if($anio%4 == 0 && ($anio%100 != 0 or $anio == 400) or ($ceros[2] == 0 && $ceros[3] == 0)) {
            if($bis == 229)
                $cantDias++;
            $bisiesto = 1;
        }else{
            if($bis == 228 && $bisiesto == 1) {
                $cantDias--;
                $bisiesto = 0;
            }
        }
    }
}
