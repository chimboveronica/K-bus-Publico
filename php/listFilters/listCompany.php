<?php

include ('../../dll/config.php');

if (!$mysqli = getConectionDb()) {
    echo "{success:false, msg: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {

    $consultaSql = "select empresa from empresas";

    $result = $mysqli->query($consultaSql);
    $mysqli->close();

    if ($result->num_rows > 0) {
        $objJson = "[";
        while ($myrow = $result->fetch_assoc()) {
            $objJson .= "["
                    . "'" . utf8_encode($myrow["empresa"]) . "',"
                    . "'" . utf8_encode($myrow["empresa"]) . "'],";
        }

        $objJson .="]";
        echo $objJson;
    } else {
        echo "{success:false, msg: 'No hay datos que obtener'}";
    }
}