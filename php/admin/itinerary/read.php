<?php

include ('../../../dll/config.php');

if (!$mysqli = getConectionDb()) {
    echo "{success:false, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.',state: false}";
} else {

    $consultaSql = "select i.id_ruta,r.ruta,i.id_turno,t.nombre,i.tiempo 
from kbusdb.itinerarios_p i, rutas r, turnos t
where i.id_ruta=r.id_ruta and i.id_turno=t.id_turno order by id_turno";

    $result = $mysqli->query($consultaSql);
    $mysqli->close();

    $objJson = "{data: [";



    while ($myrow = $result->fetch_assoc()) {
        $objJson .= "{"
                . "idRutaTurnoTiempo:'" . $myrow["id_turno"] . "_" . $myrow["id_ruta"] . "_" . $myrow["tiempo"] ."',"
                . "idRutaItine:" . $myrow["id_ruta"] . ","
                . "rutaItine:'" . $myrow["ruta"] . "',"
                . "turnoItine:" . $myrow["id_turno"] . ","
                . "turnoNomItine:'" . $myrow["nombre"] . "',"
                . "horaItine:'" . $myrow["tiempo"] . "'},";
    }
    $objJson .= "]}";
    echo $objJson;
}
