<?php

include('../../login/isLogin.php');
include('../../../dll/config.php');
if (!$mysqli = getConectionDb()) {
    echo "{success:false, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {

    $consultaSql = "SELECT e.empresa,concat(p.nombres,' ',p.apellidos) as persona,v.reg_municipal,r.ruta,count(*) as total FROM kbushistoricodb.despachos d,kbusdb.vehiculos v,kbusdb.personas p,kbusdb.rutas r,kbusdb.empresas e where d.id_vehiculo=v.id_vehiculo and d.id_ruta=r.id_ruta and v.id_persona=p.id_persona and v.id_empresa=e.id_empresa and fecha=date(now()) group by d.id_vehiculo";

    $result = $mysqli->query($consultaSql);
    $mysqli->close();
    $objJson = "{data: [";

    while ($myrow = $result->fetch_assoc()) {
        $objJson .= "{"
                . "persona:'" . utf8_encode($myrow["persona"]) . "',"
                . "reg_municipal:'" . $myrow["reg_municipal"] . "',"
                . "empresa:'" . $myrow["empresa"] . "',"
                . "ruta:'" . utf8_encode($myrow["ruta"]) . "',"
                . "total:" . $myrow["total"] . "},";
    }
    $objJson .= "]}";
    echo $objJson;
}
