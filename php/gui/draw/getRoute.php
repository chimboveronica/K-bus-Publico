<?php

include ('../../../dll/config.php');

extract($_POST);

if (!$mysqli = getConectionDb()) {
    echo "{failure:true, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {

    $consultaLineSql = "select pr.orden, pr.latitud, pr.longitud, r.color "
            . "from linea_rutas pr, rutas r "
            . "where pr.id_ruta = r.id_ruta "
            . "and pr.id_ruta = $idRoute "
            . "order by pr.orden";

    $consultaPointSql = "select p.id_punto, p.geocerca_skp, p.geocerca_fastrack, pr.orden, p.punto, "
            . "p.latitud, p.longitud, p.direccion, r.color "
            . "from punto_rutas pr, puntos p, rutas r "
            . "where p.id_punto = pr.id_punto "
            . "and pr.id_ruta = r.id_ruta "
            . "and pr.id_ruta = $idRoute "
            . "order by pr.orden";

    $resultLine = $mysqli->query($consultaLineSql);
    $resultPoint = $mysqli->query($consultaPointSql);
    $mysqli->close();

    if ($resultLine->num_rows > 0 && $resultPoint->num_rows > 0) {
        $objJsonLine = "dataLine: [";
        while ($myrowline = $resultLine->fetch_assoc()) {
            $objJsonLine .= "{"
                    . "latitudLine:" . $myrowline["latitud"] . ","
                    . "longitudLine:" . $myrowline["longitud"] . ","
                    . "colorLine:'" . $myrowline["color"] . "'},";
        }

        $objJsonLine .="]";

        $objJsonPoint = "dataPoint: [";
        while ($myrowpoint = $resultPoint->fetch_assoc()) {
            $objJsonPoint .= "{"
                    . "idPoint:" . $myrowpoint["id_punto"] . ","
                    . "orderPoint:" . $myrowpoint["orden"] . ","
                    . "pointPoint:'" . utf8_encode($myrowpoint["punto"]) . "',"
                    . "latitudPoint:" . $myrowpoint["latitud"] . ","
                    . "longitudPoint:" . $myrowpoint["longitud"] . ","
                    . "addressPoint:'" . utf8_encode($myrowpoint["direccion"]) . "',"
                    . "geoSkpPoint:'" . $myrowpoint["geocerca_skp"] . "',"
                    . "geoFastrackPoint:" . $myrowpoint["geocerca_fastrack"] . ","
                    . "colorPoint:'" . $myrowpoint["color"] . "'},";
        }

        $objJsonPoint .="]";
        echo "{success:true, $objJsonLine, $objJsonPoint}";
    } else {
        echo "{failure:true, message: 'No hay datos para esta Ruta'}";
    }
}