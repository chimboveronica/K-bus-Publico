<?php

include ('../../../dll/config.php');

if (!$mysqli = getConectionDb()) {
    echo "{success:false, msg: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {
    $consultaSql = "select id_equipo, equipo from equipos order by id_equipo";
    $result = $mysqli->query($consultaSql);
    if ($result->num_rows > 0) {
        $objJson = "{data: [";
        while ($myrow = $result->fetch_assoc()) {
            $equipo = $myrow["id_equipo"];
            $consultaSql2 = "select id_vehiculo from vehiculos where id_equipo='$equipo'";
            $result1 = $mysqli->query($consultaSql2);
            if ($result1->num_rows > 0) {
                
            } else {
                $objJson .= "{"
                        . "id:" . $myrow["id_equipo"] . ","
                        . "text:'" . utf8_encode($myrow["equipo"]) . "'},";
            }
        }
        $objJson .="]}";
        echo $objJson;
    } else {
        echo "{success:false, msg: 'No hay datos que obtener'}";
    }
    $mysqli->close();
}
    