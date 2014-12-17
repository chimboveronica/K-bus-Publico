<?php

include ('../../../dll/config.php');

if (!$mysqli = getConectionDb()) {
    echo "{success: false, message: 'No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {

    $consultaSql = "SELECT id_sancion, sancion FROM sanciones WHERE id_sancion >= 1";

    $result = $mysqli->query($consultaSql);
    $mysqli->close();

    if ($result->num_rows > 0) {
        $objJson = "{data: [";
        while ($myrow = $result->fetch_assoc()) {
            $objJson .= "{"
                    . "id:" . $myrow["id_sancion"] . ","
                    . "text:'" . utf8_encode($myrow["sancion"]) . "'},";
        }

        $objJson .="]}";
        echo $objJson;
    } else {
        echo "{success: false, message: 'No hay datos que obtener'}";
    }
}