<?php

include ('../../dll/config.php');

if (!$mysqli = getConectionDb()) {
    echo "{success:false, msg: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {

    $consultaSql = "select id_sancion,sancion from sanciones";

    $result = $mysqli->query($consultaSql);
    $mysqli->close();

    if ($result->num_rows > 0) {
        $objJson = "[";
        while ($myrow = $result->fetch_assoc()) {
            $objJson .= "["
                    . "" . $myrow["id_sancion"] . ","
                    . "'" . utf8_encode($myrow["sancion"]) . "'],";
        }

        $objJson .="]";
        echo $objJson;
    } else {
        echo "{success:false, msg: 'No hay datos que obtener'}";
    }
}