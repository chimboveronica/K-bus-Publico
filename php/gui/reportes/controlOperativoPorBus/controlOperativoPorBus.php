<?php

include ('../../../../dll/config.php');

extract($_POST);

if (!$mysqli = getConectionDb()) {
    echo "{failure: true, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {

    $consultaSql = "select e.empresa,concat(p.apellidos, ' ', p.nombres) as persona, r.ruta, r.distancia, 
            v.reg_municipal, d.fecha, d.hora_ini, time(d.fecha_hora_reg) as hora_reg, d.par_ruta, 
            d.par_ruta_atrav, d.hora_fin_prog, d.hora_fin_real, 
            d.atraso,d.adelanto, d.estado_sancion, d.id_ruta, d.id_vehiculo, 
            v.id_empresa, d.observacion, d.ip,d.host,d.valido,v.id_conductor,v.id_ayudante 
            from kbushistoricodb.despachos d, kbusdb.usuarios u, kbusdb.personas p, kbusdb.vehiculos v, kbusdb.rutas r ,kbusdb.empresas e
            where d.id_usuario = u.id_usuario 
            and p.id_persona = u.id_persona 
            and v.id_vehiculo = d.id_vehiculo 
            and v.id_empresa = e.id_empresa
            and d.id_ruta = r.id_ruta 
            and d.id_vehiculo = ?
            and d.fecha between ? and ?
            order by d.fecha, d.hora_ini";

    $stmt = $mysqli->prepare($consultaSql);
    if ($stmt) {
        /* ligar parámetros para marcadores */
        $stmt->bind_param("iss", $idVehicleG, $fechaIni, $fechaFin);
        /* ejecutar la consulta */
        $stmt->execute();

        $result = $stmt->get_result();

        $stmt->close();
        $mysqli->close();

        if ($result->num_rows > 0) {
            $isFirst = false;
            $cont = 1;
            $cadena = 'Vuelta';
            $json = "data: [";
            $distanciaTotal = 0;
            while ($myrow = $result->fetch_assoc()) {
                $vuelta = $cadena . ' ' . $cont;

                if (!$isFirst) {
                    $H = substr($myrow["hora_ini"], 0, 2);
                    $M = substr($myrow["hora_ini"], 3, 2);
                    $minAnt = $H * 60 + $M;
                    $fechaAnt = $myrow["fecha"];
                    $isFirst = true;
                }

                $HN = substr($myrow["hora_ini"], 0, 2);
                $MN = substr($myrow["hora_ini"], 3, 2);
                $minNew = $HN * 60 + $MN;

                $fechaNew = $myrow["fecha"];
                $intervalo = $minNew - $minAnt;
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
                $distanciaTotal = $distanciaTotal + $myrow["distancia"];
                $json .= "{"
                        . "vueltaDispatch:'" . $vuelta . "',"
                        . "distanciaDispatch:" . $myrow["distancia"] . ","
                        . "empresaDispatch:'" . $myrow["empresa"] . "',"
                        . "idVehicleDispatch:" . $myrow["id_vehiculo"] . ","
                        . "idCompanyDispatch:" . $myrow["id_empresa"] . ","
                        . "idRouteDispatch:" . $myrow["id_ruta"] . ","
                        . "dateTimeDispatch:'" . $myrow["fecha"] . " " . $myrow["hora_ini"] . "',"
                        . "dateTimeRegDispatch:'" . $registro . "',"
                        . "intervalDispatch:" . $intervalo . ","
                        . "pointByRouteCrossDispatch:'" . $myrow["par_ruta_atrav"] . " / " . $myrow["par_ruta"] . "',"
                        . "timeFinishProgDispatch:'" . $myrow["hora_fin_prog"] . "',"
                        . "timeFinishCorrectDispatch:'" . $myrow["hora_fin_real"] . "',"
                        . "delayDispatch:'" . $myrow["atraso"] . "',"
                        . "statePenaltyDispatch:" . $myrow["estado_sancion"] . ","
                        . "personDispatch:'" . utf8_encode($myrow["persona"]) . "',"
                        . "routeDispatch: '" . utf8_encode($myrow["ruta"]) . "',"
                        . "muniRegDispatch:'" . $myrow["reg_municipal"] . "',"
                        . "conductorDispatch:" . $myrow["id_conductor"] . ","
                        . "ayudanteDispatch:" . $myrow["id_ayudante"] . ","
                        . "commentDispatch:'" . utf8_encode($myrow["observacion"]) . "',"
                        . "validoDispatch:" . $myrow["valido"] . ","
                        . "adelantoDispatch:'" . $myrow["adelanto"] . "',"
                        . "ipDispatch:'" . $myrow["ip"] . "',"
                        . "hostDispatch:'" . $myrow["host"] . "'"
                        . "},";
                $HA = substr($myrow["hora_ini"], 0, 2);
                $MA = substr($myrow["hora_ini"], 3, 2);
                $minAnt = $HA * 60 + $MA;
                $fechaAnt = $myrow["fecha"];
                $cont++;
            }

            $json .= "{"
                    . "vueltaDispatch:'" . 'TOTAL' . "',"
                    . "distanciaDispatch:" . $distanciaTotal . ","
                    . "empresaDispatch:'" . '' . "',"
                    . "idVehicleDispatch:" . 0 . ","
                    . "idCompanyDispatch:" . 0 . ","
                    . "idRouteDispatch:" . 0 . ","
                    . "dateTimeDispatch:'" . '' . "',"
                    . "dateTimeRegDispatch:'" . '' . "',"
                    . "intervalDispatch:" . 0 . ","
                    . "pointByRouteCrossDispatch:'" . '' . " " . '' . "',"
                    . "timeFinishProgDispatch:'" . '' . "',"
                    . "timeFinishCorrectDispatch:'" . '' . "',"
                    . "delayDispatch:'" . '' . "',"
                    . "statePenaltyDispatch:" . 0 . ","
                    . "personDispatch:'" . '' . "',"
                    . "routeDispatch: '" . '' . "',"
                    . "muniRegDispatch:'" . '' . "',"
                    . "conductorDispatch:" . 0 . ","
                    . "ayudanteDispatch:" . 0 . ","
                    . "commentDispatch:'" . '' . "',"
                    . "validoDispatch:" . 0 . ","
                    . "adelantoDispatch:'" . '' . "',"
                    . "ipDispatch:'" . '' . "',"
                    . "hostDispatch:'" . '' . "'"
                    . "},";

            $json .="]";

            echo "{success: true, $json}";
        } else {
            echo "{failure: true, message:'No hay despachos entre estas Fechas.'}";
        }
    } else {
        echo "{failure: true, message: 'Problemas en la Construcción de la Consulta.'}";
    }
}