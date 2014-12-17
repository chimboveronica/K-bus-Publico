<?php

include ('../../../dll/config.php');

if (!$mysqli = getConectionDb()) {
    echo "{success:false, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.',state: false}";
} else {

    $consultaSql = "select pr.id_ruta, pr.id_punto, pr.orden, pr.tiempos, pr.tiempos_pico, pr.imprimir, "
            . "r.ruta, p.punto "
            . "from punto_rutas pr, rutas r, puntos p "
            . "where pr.id_ruta = r.id_ruta "
            . "and pr.id_punto = p.id_punto "
            . "order by pr.id_ruta, pr.orden";

    $result = $mysqli->query($consultaSql);
    $mysqli->close();

    $objJson = "{data: [";

    while ($myrow = $result->fetch_assoc()) {
        $objJson .= "{"
                . "idPointRoute:'" . $myrow["id_ruta"] . "_" . $myrow["orden"] . "',"
                . "idRoutePointRoute:" . $myrow["id_ruta"] . ","
                . "idPointPointRoute:" . $myrow["id_punto"] . ","
                . "orderPointRoute:" . $myrow["orden"] . ","
                . "timePointRoute:'" . $myrow["tiempos"] . "',"
                . "timePikePointRoute:'" . $myrow["tiempos_pico"] . "',"
                . "printPointRoute:" . $myrow["imprimir"] . ","
                . "routePointRoute:'" . utf8_encode($myrow["ruta"]) . "',"
                . "pointPointRoute:'" . utf8_encode($myrow["punto"]) . "'},";
    }
    $objJson .= "]}";
    echo $objJson;
}