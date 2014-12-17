<?php

include('../../login/isLogin.php');
include ('../../../dll/config.php');

extract($_GET);

if (!$mysqli = getConectionDb()) {
    echo "{success:false, message: 'Error: No se ha podido conectar a la Base de Datos.'}";
} else {
    $idUsuario = $_SESSION["IDUSUARIOKBUS" . $site_city];

    $consultaSql = "SELECT v.reg_municipal, d.fecha, d.hora_ini, d.fecha_hora_reg, "
            . "d.par_ruta, d.par_ruta_atrav, d.hora_fin_prog, "
            . "d.hora_fin_real, d.atraso, d.id_sancion, "
            . "v.id_empresa, v.id_vehiculo, d.id_ruta, d.observacion, d.valido "
            . "FROM kbushistoricodb.despachos d, usuarios u, vehiculos v "
            . "WHERE d.id_usuario = u.id_usuario "
            . "AND v.id_vehiculo = d.id_vehiculo "
            . "AND u.id_usuario = $idUsuario "
            . "AND d.fecha = DATE(NOW()) "
            . "ORDER BY d.hora_ini DESC";

    $result = $mysqli->query($consultaSql);
    $mysqli->close();

    if ($result->num_rows > 0) {
        $objJson = "{data: [";
        while ($myrow = $result->fetch_assoc()) {
            $objJson .= "{"
                    . "idVehicleDispatch:" . $myrow["id_vehiculo"] . ","
                    . "idCompanyDispatch:" . $myrow["id_empresa"] . ","
                    . "idRouteDispatch:" . $myrow["id_ruta"] . ","
                    . "muniRegDispatch:'" . $myrow["reg_municipal"] . "',"
                    . "dateTimeDispatch:'" . $myrow["fecha"] . " " . $myrow["hora_ini"] . "',"
                    . "dateTimeRegDispatch:'" . $myrow["fecha_hora_reg"] . "',"
                    . "pointByRouteCrossDispatch:'" . $myrow["par_ruta_atrav"] . " / " . $myrow["par_ruta"] . "',"
                    . "timeFinishProgDispatch:'" . $myrow["hora_fin_prog"] . "',"
                    . "timeFinishRealDispatch:'" . $myrow["hora_fin_real"] . "',"
                    . "delayDispatch:'" . $myrow["atraso"] . "',"
                    . "idPenaltyDispatch:" . $myrow["id_sancion"] . ","
                    . "commentDispatch:'" . $myrow["observacion"] . "',"
                    . "validDispatch: " . $myrow["valido"] . "},";
        }

        $objJson.= "]}";

        echo $objJson;
    } else {
        echo "{failure:true, message:'No hay datos que obtener'}";
    }
}