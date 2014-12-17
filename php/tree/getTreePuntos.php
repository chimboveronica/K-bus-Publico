<?php

include ('../../dll/config.php');

if (!$mysqli = getConectionDb()) {
    echo "Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.";
} else {

    $consultaSql = "select id_punto, punto, direccion "
            . "from puntos where id_punto > 1";
    $result = $mysqli->query($consultaSql);
    $mysqli->close();

    if ($result->num_rows > 0) {
        $objJson = "[";
        while ($myrow = $result->fetch_assoc()) {
            $objJson.="{"
                    . "text:'" . $myrow["id_punto"] . ":: " . utf8_encode($myrow["punto"]) . "',"
                    . "iconCls:'icon-point',"
                    . "id:'" . $myrow["id_punto"] . "',"
                    . "leaf:true},";
        }
        $objJson .= "]";

        echo $objJson;
    } else {
        echo "No hay datos que obtener";
    }
}