<?php

include ('../../../../dll/config.php');

extract($_POST);

if (!$mysqli = getConectionDb()) {
    echo "{failure: true, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {

    if ($idCompany == 1) {
        $consultaSql = "SELECT CONCAT(des.apellidos, ' ', des.nombres) AS despachador, "
                . "v.reg_municipal, d.fecha, d.hora_ini, TIME(d.fecha_hora_reg) AS hora_reg, d.par_ruta, "
                . "d.par_ruta_atrav, d.hora_fin_prog, d.hora_fin_real, "
                . "d.atraso, d.adelanto, d.id_ruta, d.id_vehiculo, "
                . "v.id_empresa, d.observacion, d.ip, d.host, d.valido, "
                . "CONCAT(con.apellidos, ' ', con.nombres) AS conductor, CONCAT(ayu.apellidos, ' ', ayu.nombres) AS ayudante "
                . "FROM kbushistoricodb.despachos d, kbusdb.vehiculos v, kbusdb.usuarios u, kbusdb.personas des, kbusdb.personas con, kbusdb.personas ayu "
                . "WHERE d.id_usuario = u.id_usuario "
                . "AND u.id_persona = des.id_persona "
                . "AND v.id_vehiculo = d.id_vehiculo "
                . "AND d.id_conductor = con.id_persona "
                . "AND d.id_ayudante = ayu.id_persona "
                . "AND d.fecha between ? AND ? "
                . "ORDER BY d.fecha, d.hora_ini desc ";

        $stmt = $mysqli->prepare($consultaSql);

        if ($stmt) {
            /* ligar parámetros para marcadores */
            $stmt->bind_param("ss", $fechaIni, $fechaFin);
            /* ejecutar la consulta */
            $stmt->execute();

            $result = $stmt->get_result();

            $stmt->close();


            if ($result->num_rows > 0) {

                $json = "data: [";
                while ($myrow = $result->fetch_assoc()) {
                    $HN = substr($myrow["hora_ini"], 0, 2);
                    $MN = substr($myrow["hora_ini"], 3, 2);
                    $minIni = $HN * 60 + $MN;

                    $HR = substr($myrow["hora_reg"], 0, 2);
                    $MR = substr($myrow["hora_reg"], 3, 2);
                    $minReg = $HR * 60 + $MR;
                    $registro = substr($myrow["hora_reg"], 0, 8);
                    if ($minIni - $minReg > 10) {
                        $registro = '<font color="red">' . substr($myrow["hora_reg"], 0, 8) . '</font>';
                    }
                    if ($minIni - $minReg < -10) {
                        $registro = '<font color="blue">' . substr($myrow["hora_reg"], 0, 8) . '</font>';
                    }

                    $json .= "{"
                            . "idVehicleDispatch:" . $myrow["id_vehiculo"] . ","
                            . "idCompanyDispatch:" . $myrow["id_empresa"] . ","
                            . "idRouteDispatch:" . $myrow["id_ruta"] . ","
                            . "dateTimeDispatch:'" . $myrow["fecha"] . " " . $myrow["hora_ini"] . "',"
                            . "dateTimeRegDispatch:'" . $registro . "',"
                            . "pointByRouteCrossDispatch:'" . $myrow["par_ruta_atrav"] . " / " . $myrow["par_ruta"] . "',"
                            . "timeFinishProgDispatch:'" . $myrow["hora_fin_prog"] . "',"
                            . "timeFinishCorrectDispatch:'" . $myrow["hora_fin_real"] . "',"
                            . "delayDispatch:'" . $myrow["atraso"] . "',"
                            . "personDispatch:'" . utf8_encode($myrow["despachador"]) . "',"
                            . "muniRegDispatch:'" . $myrow["reg_municipal"] . "',"
                            . "commentDispatch:'" . utf8_encode($myrow["observacion"]) . "',"
                            . "validoDispatch:" . $myrow["valido"] . ","
                            . "conductorDispatch:'" . utf8_encode($myrow["conductor"]) . "',"
                            . "ayudanteDispatch:'" . utf8_encode($myrow["ayudante"]) . "',"
                            . "adelantoDispatch:'" . $myrow["adelanto"] . "',"
                            . "ipDispatch:'" . $myrow["ip"] . "',"
                            . "hostDispatch:'" . $myrow["host"] . "'"
                            . "},";
                }
                $json .="]";
                echo "{success: true, $json}";
            } else {
                echo "{failure: true, message:'No hay despachos entre estas Fechas.'}";
            }
        } else {
            echo "{failure: true, message: 'Problemas en la Construcción de la Consulta.'}";
        }
    } else {
        $consultaSql = "SELECT CONCAT(des.apellidos, ' ', des.nombres) AS despachador, "
                . "v.reg_municipal, d.fecha, d.hora_ini, TIME(d.fecha_hora_reg) AS hora_reg, d.par_ruta, "
                . "d.par_ruta_atrav, d.hora_fin_prog, d.hora_fin_real, "
                . "d.atraso, d.adelanto, d.id_ruta, d.id_vehiculo, "
                . "v.id_empresa, d.observacion, d.ip, d.host, d.valido, "
                . "CONCAT(con.apellidos, ' ', con.nombres) AS conductor, CONCAT(ayu.apellidos, ' ', ayu.nombres) AS ayudante "
                . "FROM kbushistoricodb.despachos d, kbusdb.vehiculos v, kbusdb.usuarios u, kbusdb.personas des, kbusdb.personas con, kbusdb.personas ayu "
                . "WHERE d.id_usuario = u.id_usuario "
                . "AND u.id_persona = des.id_persona "
                . "AND v.id_vehiculo = d.id_vehiculo "
                . "AND d.id_conductor = con.id_persona "
                . "AND d.id_ayudante = ayu.id_persona "
                . "AND v.id_empresa = ? "
                . "AND d.fecha between ? AND ? "
                . "ORDER BY d.fecha, d.hora_ini desc ";

        $stmt = $mysqli->prepare($consultaSql);

        if ($stmt) {
            /* ligar parámetros para marcadores */
            $stmt->bind_param("iss", $idCompany, $fechaIni, $fechaFin);
            /* ejecutar la consulta */
            $stmt->execute();

            $result = $stmt->get_result();

            $stmt->close();


            if ($result->num_rows > 0) {

                $json = "data: [";
                while ($myrow = $result->fetch_assoc()) {
                    $HN = substr($myrow["hora_ini"], 0, 2);
                    $MN = substr($myrow["hora_ini"], 3, 2);
                    $minIni = $HN * 60 + $MN;

                    $HR = substr($myrow["hora_reg"], 0, 2);
                    $MR = substr($myrow["hora_reg"], 3, 2);
                    $minReg = $HR * 60 + $MR;
                    $registro = substr($myrow["hora_reg"], 0, 8);
                    if ($minIni - $minReg > 10) {
                        $registro = '<font color="red">' . substr($myrow["hora_reg"], 0, 8) . '</font>';
                    }
                    if ($minIni - $minReg < -10) {
                        $registro = '<font color="blue">' . substr($myrow["hora_reg"], 0, 8) . '</font>';
                    }

                    $json .= "{"
                            . "idVehicleDispatch:" . $myrow["id_vehiculo"] . ","
                            . "idCompanyDispatch:" . $myrow["id_empresa"] . ","
                            . "idRouteDispatch:" . $myrow["id_ruta"] . ","
                            . "dateTimeDispatch:'" . $myrow["fecha"] . " " . $myrow["hora_ini"] . "',"
                            . "dateTimeRegDispatch:'" . $registro . "',"
                            . "pointByRouteCrossDispatch:'" . $myrow["par_ruta_atrav"] . " / " . $myrow["par_ruta"] . "',"
                            . "timeFinishProgDispatch:'" . $myrow["hora_fin_prog"] . "',"
                            . "timeFinishCorrectDispatch:'" . $myrow["hora_fin_real"] . "',"
                            . "delayDispatch:'" . $myrow["atraso"] . "',"
                            . "personDispatch:'" . utf8_encode($myrow["despachador"]) . "',"
                            . "muniRegDispatch:'" . $myrow["reg_municipal"] . "',"
                            . "commentDispatch:'" . utf8_encode($myrow["observacion"]) . "',"
                            . "conductorDispatch:'" . utf8_encode($myrow["conductor"]) . "',"
                            . "ayudanteDispatch:'" . utf8_encode($myrow["ayudante"]) . "',"
                            . "validoDispatch:" . $myrow["valido"] . ","
                            . "adelantoDispatch:'" . $myrow["adelanto"] . "',"
                            . "ipDispatch:'" . $myrow["ip"] . "',"
                            . "hostDispatch:'" . $myrow["host"] . "'"
                            . "},";
                }

                $json .="]";
                echo "{success: true, $json}";
            } else {
                echo "{failure: true, message:'No hay despachos entre estas Fechas.'}";
            }
        } else {
            echo "{failure: true, message: 'Problemas en la Construcción de la Consulta.'}";
        }
    }
    $mysqli->close();
}