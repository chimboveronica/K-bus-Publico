<?php

include ('../../../dll/config.php');

if (!$mysqli = getConectionDb()) {
    echo "{success:false, msg: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {

    $consultaSql = "select id_turno, nombre from turnos";

    $result = $mysqli->query($consultaSql);
    $mysqli->close();

    if ($result->num_rows > 0) {
        $objJson = "{data: [";
        while ($myrow = $result->fetch_assoc()) {
            $objJson .= "{"
                    . "id:" . $myrow["id_turno"] . ","
                    . "text:'" . utf8_encode($myrow["nombre"]) . "'},";
        }

        $objJson .="]}";
        echo $objJson;
    } else {
        echo "{success:false, msg: 'No hay datos que obtener'}";
    }
}