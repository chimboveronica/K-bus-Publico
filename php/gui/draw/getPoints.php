<?php

include ('../../../dll/config.php');

extract($_POST);

if (!$mysqli = getConectionDb()) {
    echo "{failure:true, msg: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {

    $consultaPointSql = "select id_punto, geocerca_skp, geocerca_fastrack, punto, "
            . "latitud, longitud, direccion "
            . "from puntos p ";
    
    $resultPoint = $mysqli->query($consultaPointSql);
    $mysqli->close();

    if ($resultPoint->num_rows > 0) {
        $objJsonPoint = "data: [";
        while ($myrowpoint = $resultPoint->fetch_assoc()) {
            $objJsonPoint .= "{"
                    . "idPoint:" . $myrowpoint["id_punto"] . ","
                    . "pointPoint:'" . utf8_encode($myrowpoint["punto"]) . "',"
                    . "latitudPoint:" . $myrowpoint["latitud"] . ","
                    . "longitudPoint:" . $myrowpoint["longitud"] . ","
                    . "addressPoint:'" . utf8_encode($myrowpoint["direccion"]) . "',"
                    . "geoSkpPoint:'" . $myrowpoint["geocerca_skp"] . "',"
                    . "geoFastrackPoint:" . $myrowpoint["geocerca_fastrack"] . "},";
        }

        $objJsonPoint .="]";
        echo "{success:true, $objJsonPoint}";
    } else {
        echo "{failure:true, msg: 'No hay datos para esta Ruta'}";
    }
}