<?php

include ('../../dll/config.php');

if (!$mysqli = getConectionDb()) {
    echo "{success:false, msg: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {

    $consultaSql = "SELECT descripcion_estado_mecanico FROM kbusdb.estado_mecanicos";

    $result = $mysqli->query($consultaSql);
    $mysqli->close();

    if ($result->num_rows > 0) {
        $objJson = "[";
        while ($myrow = $result->fetch_assoc()) {
            $objJson .= "["
                    . "'" . utf8_encode($myrow["descripcion_estado_mecanico"]) . "',"
                    . "'" . utf8_encode($myrow["descripcion_estado_mecanico"]) . "'],";
        }

        $objJson .="]";
        echo $objJson;
    } else {
        echo "{success:false, msg: 'No hay datos que obtener'}";
    }
}