<?php

include ('../../../dll/config.php');

if (!$mysqli = getConectionDb()) {
    echo "{success:false, message: 'No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {

    $consultaSql = "SELECT u.id_usuario, u.id_rol_usuario, u.id_empresa, u.id_persona, u.usuario, u.clave, u.activo, "
            . "p.cedula "
            . "FROM usuarios u, personas p "
            . "WHERE u.id_persona = p.id_persona "
            . "ORDER BY p.apellidos";

    $result = $mysqli->query($consultaSql);
    $mysqli->close();

    $objJson = "{data: [";

    while ($myrow = $result->fetch_assoc()) {
        $objJson .= "{"
                . "idUser:" . $myrow["id_usuario"] . ","
                . "idRolUser:" . $myrow["id_rol_usuario"] . ","
                . "idCompanyUser:" . $myrow["id_empresa"] . ","
                . "idPersonUser:" . $myrow["id_persona"] . ","
                . "userUser:'" . utf8_encode($myrow["usuario"]) . "',"
                . "passwordUser:'" . utf8_encode($myrow["clave"]) . "',"
                . "activeUser:" . $myrow["activo"] . ","
                . "documentPersonUser:'" . $myrow["cedula"] . "'},";
    }
    $objJson .= "]}";
    echo $objJson;
}