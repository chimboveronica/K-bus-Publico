<?php

include('../../login/isLogin.php');
include ('../../../dll/config.php');

$menuClick = 0;

extract($_GET);

if (!$mysqli = getConectionDb()) {
    echo "{success:false, msg: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {
    $consultaSql = "SELECT v.reg_municipal,v.id_empresa,e.equipo,v.fecha_hora_entrada_reten,v.fecha_hora_salida_reten FROM kbusdb.vehiculos v,kbusdb.equipos e
where v.id_equipo=e.id_equipo and v.estado_reten='1'";

    $result = $mysqli->query($consultaSql);
    $mysqli->close();

    if ($result->num_rows > 0) {
        $objJson = "{buses: [";
        while ($myrow = $result->fetch_assoc()) {
            $objJson .= "{"
                    . "empresaReten:" . $myrow["id_empresa"] . ","
                    . "regReten:'" . $myrow["reg_municipal"] . "',"
                    . "equipoReten:'" . $myrow["equipo"] . "',"
                    . "fechaEntrada:'" . $myrow["fecha_hora_entrada_reten"] . "',"
                    . "fechaSalida:'" . $myrow["fecha_hora_salida_reten"] . "'},";
        }

        $objJson .="]}";
        echo $objJson;
    } else {
        echo "{success:false, msg: 'No hay datos que obtener'}";
    }
}