<?php

include('../../login/isLogin.php');
include('../../../dll/config.php');
if (!$mysqli = getConectionDb()) {
    echo "{success:false, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {

    $consultaSql = "SELECT usuario, id_rol_usuario, id_empresa, fecha_hora_con, conectado, ip, longitud, latitud "
            . "FROM usuarios "
            . "ORDER BY fecha_hora_con DESC";

    $result = $mysqli->query($consultaSql);
    $mysqli->close();

    $objJson = "{data: [";

    while ($myrow = $result->fetch_assoc()) {
        $objJson .= "{"
                . "usuarioConect:'" . utf8_encode($myrow["usuario"]) . "',"
                . "idRolConect:" . $myrow["id_rol_usuario"] . ","
                . "idCompanyConect:" . $myrow["id_empresa"] . ","
                . "fechaHoraConect:'" . $myrow["fecha_hora_con"] . "',"
                . "conectadoConect:" . $myrow["conectado"] . ","
                . "ipConect:'" . $myrow["ip"] . "',"
                . "longitudConect:" . $myrow["longitud"] . ","
                . "latitudConect:" . $myrow["latitud"] . "},";
    }
    $objJson .= "]}";
    echo $objJson;
}

