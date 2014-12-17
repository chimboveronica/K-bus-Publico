<?php

include ('../../../dll/config.php');

if (!$mysqli = getConectionDb()) {
    echo "{success:false, message: 'No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.',state: false}";
} else {

    $consultaSql = "select id_ruta, cod_ruta, ruta, linea, tiempo_sancion, icono, color, distancia "
            . "from rutas";

    $result = $mysqli->query($consultaSql);

    $objJson = "{data: [";

    while ($myrow = $result->fetch_assoc()) {
        $idRoute = $myrow["id_ruta"];
        $consultaLineSql = "select pr.orden, pr.latitud, pr.longitud, r.color "
                . "from linea_rutas pr, rutas r "
                . "where pr.id_ruta = r.id_ruta "
                . "and pr.id_ruta = $idRoute "
                . "order by pr.orden";

        $resultLine = $mysqli->query($consultaLineSql);

        $verticesRoute = "";
        if ($resultLine->num_rows > 0) {
            while ($myrowline = $resultLine->fetch_assoc()) {
                $verticesRoute .= round($myrowline["latitud"], 8) . "," . round($myrowline["longitud"], 8) . ";";
            }
        }

        $objJson .= "{"
                . "idRoute:" . $idRoute . ","
                . "codeRoute:'" . $myrow["cod_ruta"] . "',"
                . "routeRoute:'" . utf8_encode($myrow["ruta"]) . "',"
                . "lineRoute:" . $myrow["linea"] . ","
                . "timePenaltyRoute:'" . $myrow["tiempo_sancion"] . "',"
                . "iconRoute:'" . utf8_encode($myrow["icono"]) . "',"
                . "distanciaRoute:'" . $myrow["distancia"] . "',"
                . "colorRoute:'" . $myrow["color"] . "',"
                . "verticesRoute:'" . substr($verticesRoute, 0, -1) . "'},";
    }
    $objJson .= "]}";
    echo $objJson;
    $mysqli->close();
}