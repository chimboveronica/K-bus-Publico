<?php

include('../../login/isLogin.php');
include ('../../../dll/config.php');

$menuClick = 0;

if (!$mysqli = getConectionDb()) {
    echo "{success:false, msg: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {
        $consultaSql = "select id_cmd_predefinido, descripcion from cmd_predefinidos";

    $result = $mysqli->query($consultaSql);
    $mysqli->close();

    if ($result->num_rows > 0) {
        $objJson = "{data: [";
        while ($myrow = $result->fetch_assoc()) {
            $objJson .= "{"
                    . "id:" . $myrow["id_cmd_predefinido"] . ","
                    . "text:'" . utf8_encode($myrow["descripcion"]) . "'}";
        }

        $objJson .="]}";
        echo $objJson;
    } else {
        echo "{success:false, msg: 'No hay datos que obtener'}";
    }
}