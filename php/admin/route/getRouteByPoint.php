<?php

include ('../../../dll/config.php');

extract($_POST);

if (!$mysqli = getConectionDb()) {
    echo "{failure:true, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {

    $consultaLineSql = "select p.latitud, p.longitud, r.color  "
            . "from punto_rutas pr, rutas r, puntos p "
            . "where pr.id_ruta = r.id_ruta "
            . "and pr.id_punto = p.id_punto "
            . "and pr.id_ruta = $idRoute "
            . "order by pr.orden";

    $resultLine = $mysqli->query($consultaLineSql);
    $mysqli->close();

    if ($resultLine->num_rows > 0) {
        $objJsonLine = "dataLine: [";
        while ($myrowline = $resultLine->fetch_assoc()) {
            $objJsonLine .= "{"
                    . "latitudLine:" . $myrowline["latitud"] . ","
                    . "longitudLine:" . $myrowline["longitud"] . ","
                    . "colorLine:'" . $myrowline["color"] . "'},";
        }

        $objJsonLine .="]";
        echo "{success:true, $objJsonLine}";
    } else {
        echo "{failure:true, message: 'No hay Puntos asociados para esta Ruta.'}";
    }
}