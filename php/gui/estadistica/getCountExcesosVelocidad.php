<?php

include('../../login/isLogin.php');
include('../../../dll/config.php');
if (!$mysqli = getConectionDb()) {
    echo "{success:false, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {

    $consultaSql = "SELECT v.reg_municipal,e.empresa, concat(p.nombres,' ', p.apellidos)as persona, ev.cantidad as total FROM kbushistoricodb.exceso_velocidades ev, kbusdb.vehiculos v, kbusdb.personas p,kbusdb.empresas e
where ev.id_vehiculo=v.id_vehiculo and v.id_persona=p.id_persona and v.id_empresa=e.id_empresa and fecha=date(now())";

    $result = $mysqli->query($consultaSql);
    $mysqli->close();
    $objJson = "{data: [";

    while ($myrow = $result->fetch_assoc()) {
        $objJson .= "{"
                . "persona:'" . utf8_encode($myrow["persona"]) . "',"
                . "reg_municipal:'" . $myrow["reg_municipal"] . "',"
                . "empresa:'" . $myrow["empresa"] . "',"
                . "total:" . $myrow["total"] . "},";
    }
    $objJson .= "]}";
    echo $objJson;
}
