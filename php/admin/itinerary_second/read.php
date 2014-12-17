<?php

include ('../../../dll/config.php');

if (!$mysqli = getConectionDb()) {
    echo "{success:false, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.',state: false}";
} else {

    $consultaSql = "select i.id_ruta,r.ruta,i.id_turno,t.nombre,i.tiempo,i.num_vehiculos 
from kbusdb.itinerarios_s i, rutas r, turnos t
where i.id_ruta=r.id_ruta and i.id_turno=t.id_turno order by id_turno";

    $result = $mysqli->query($consultaSql);
    $mysqli->close();

    $objJson = "{data: [";

    while ($myrow = $result->fetch_assoc()) {

        $objJson .= "{"
                . "idRutaTurnoTiempo:'" . $myrow["id_turno"] . "_" . $myrow["id_ruta"] . "_" . $myrow["tiempo"] . "_" . $myrow["num_vehiculos"] . "',"
                . "idRutaItine_s:" . $myrow["id_ruta"] . ","
                . "rutaItine_s:'" . $myrow["ruta"] . "',"
                . "numItine_s:" . $myrow["num_vehiculos"] . ","
                . "turnoItine_s:" . $myrow["id_turno"] . ","
                . "turnoNomItine_s:'" . $myrow["nombre"] . "',"
                . "horaItine_s:'" . $myrow["tiempo"] . "'},";
    }
    $objJson .= "]}";
    echo $objJson;
}
