<?php

include ('../../../dll/config.php');

if (!$mysqli = getConectionDb()) {
    echo "{success:false, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.',state: false}";
} else {

    $consultaSql = "select id_punto, punto, geocerca_skp, geocerca_fastrack, latitud, longitud, direccion "
            . "from puntos";

    $result = $mysqli->query($consultaSql);
    $mysqli->close();

    $objJson = "{data: [";

    while ($myrow = $result->fetch_assoc()) {
        $objJson .= "{"
                . "idPoint:" . $myrow["id_punto"] . ","
                . "pointPoint:'" . utf8_encode($myrow["punto"]) . "',"
                . "geoSkpPoint:'" . utf8_encode($myrow["geocerca_skp"]) . "',"
                . "geoFastrackPoint:" . $myrow["geocerca_fastrack"] . ","
                . "latitudPoint:" . $myrow["latitud"] . ","
                . "longitudPoint:" . $myrow["longitud"] . ","
                . "addressPoint:'" . $myrow["direccion"] . "'},";
    }
    $objJson .= "]}";
    echo $objJson;
}