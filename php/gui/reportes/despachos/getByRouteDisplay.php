<?php

include ('../../../../dll/config.php');
extract($_POST);

if (!$mysqli = getConectionDb()) {
    echo "{failure: true, message: 'No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {
    $consultaSql = "SELECT v.id_vehiculo, v.id_empresa, v.reg_municipal, r.id_ruta, r.ruta, cr.fecha_hora_equipo "
            . "FROM kbushistoricodb.control_rutas cr, vehiculos v, rutas r "
            . "WHERE cr.id_vehiculo = v.id_vehiculo "
            . "AND cr.id_ruta = r.id_ruta "
            . "AND cr.id_ruta = ? "
            . "AND DATE(cr.fecha_hora_equipo) BETWEEN ? AND ?";

    $stmt = $mysqli->prepare($consultaSql);

    if ($stmt) {
        /* ligar parámetros para marcadores */
        $stmt->bind_param("iss", $idRoute, $fechaIni, $fechaFin);
        /* ejecutar la consulta */
        $stmt->execute();

        $result = $stmt->get_result();

        $stmt->close();
        $mysqli->close();

        if ($result->num_rows > 0) {
            $json = "data: [";
            while ($myrow = $result->fetch_assoc()) {
                $json .= "{" . "idVehicleDispatch:" . $myrow["id_vehiculo"] . ","
                        . "idCompanyDispatch:" . $myrow["id_empresa"] . ","
                        . "idRouteDispatch:" . $myrow["id_ruta"] . ","
                        . "routeDispatch:'" . utf8_encode($myrow["ruta"]) . "',"
                        . "dateTimeDispatch:'" . $myrow["fecha_hora_equipo"] . "',"
                        . "muniRegDispatch:'" . $myrow["reg_municipal"] . "'},";
            }
            $json .="]";

            echo "{success: true, $json}";
        } else {
            echo "{failure: true, message:'No hay reportes entre estas Fechas.'}";
        }
    } else {
        echo "{failure: true, message: 'Problemas en la Construcción de la Consulta.'}";
    }
}