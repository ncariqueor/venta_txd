<?php
function obtenerDia($dia){
    if($dia == 'Mon')
        return 'Lun';

    if($dia == 'Tue')
        return 'Mar';

    if($dia == 'Wed')
        return 'Mié';

    if($dia == 'Thu')
        return 'Jue';

    if($dia == 'Fri')
        return 'Vie';

    if($dia == 'Sat')
        return 'Sáb';

    if($dia == 'Sun')
        return 'Dom';
}
