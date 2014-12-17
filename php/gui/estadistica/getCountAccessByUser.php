<?php

include('../../login/isLogin.php');
include('../../../dll/config.php');
if (!$mysqli = getConectionDb()) {
    echo "{success:false, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {

    $consultaSql = "SELECT count(*) as total,concat(p.nombres,' ',p.apellidos) as persona,u.usuario,r.rol_usuario ,e.empresa, u.ip,u.host FROM kbushistoricodb.accesos a,usuarios u, rol_usuarios r,empresas e, personas p  where a.id_usuario=u.id_usuario and u.id_persona=p.id_persona and p.id_empresa=e.id_empresa and date(a.fecha_hora_registro)=date(now()) group by a.id_usuario";

    $result = $mysqli->query($consultaSql);
    $mysqli->close();
    $objJson = "{data: [";

    while ($myrow = $result->fetch_assoc()) {
        $objJson .= "{"
                . "persona:'" . utf8_encode($myrow["persona"]) . "',"
                . "rol_usuario:'" . $myrow["rol_usuario"] . "',"
                . "empresa:'" . $myrow["empresa"] . "',"
                . "usuario:'" . $myrow["usuario"] . "',"
                . "total:" . $myrow["total"] . ","
                . "ip:'" . $myrow["ip"] . "',"
                . "host:'" . $myrow["host"] . "'},";
    }
    $objJson .= "]}";
    echo $objJson;
}
