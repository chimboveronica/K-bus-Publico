<?php

include ('../../../../dll/config.php');
extract($_POST);

if (!$mysqli = getConectionDb()) {
    echo "{failure: true, message: 'No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {
    $queryTypeDevieSql = "SELECT e.id_tipo_equipo "
            . "FROM vehiculos v, equipos e "
            . "WHERE v.id_equipo = e.id_equipo "
            . "AND v.id_vehiculo = $idVehicle";

    $resultTypeDevice = $mysqli->query($queryTypeDevieSql);
    $myrowtypedevice = $resultTypeDevice->fetch_assoc();

    if (isset($horaIniC)) {
        $horaIni = date('H:i:s', strtotime('-10 minute', strtotime($horaIniC)));
    }

    if ($myrowtypedevice["id_tipo_equipo"] == 1) {
        $consultaSql = "SELECT ds.id_equipo, ds.fecha, ds.hora, ds.fecha_hora_reg, ds.id_sky_evento, ds.id_punto, ds.latitud, ds.longitud, "
                . "ds.velocidad, ds.rumbo, ds.g1, ds.g2, ds.sal, ds.bateria, ds.v1, ds.v2, ds.gsm, ds.gps, ds.ign, ds.direccion, "
                . "p.punto "
                . "FROM kbushistoricodb.dato_skps ds, kbusdb.equipos e, kbusdb.vehiculos v, kbusdb.puntos p "
                . "WHERE ds.id_equipo = e.id_equipo "
                . "AND e.id_equipo = v.id_equipo "
                . "AND ds.id_punto = p.id_punto "
                . "AND v.id_vehiculo = ? "
                . "AND ds.fecha BETWEEN ? AND ? "
                . "ORDER BY ds.fecha, ds.hora";

        $stmt = $mysqli->prepare($consultaSql);
        if ($stmt) {
            /* ligar parámetros para marcadores */
            $stmt->bind_param("iss", $idVehicle, $fechaIni, $fechaFin);
            /* ejecutar la consulta */
            $stmt->execute();

            $result = $stmt->get_result();

            $stmt->close();
            $mysqli->close();

            if ($result->num_rows > 0) {
                $fechaHoraIni = $fechaIni . " " . $horaIni;
                $fechaHoraFin = $fechaFin . " " . $horaFin;

                $json = "data: [";
                $c = 0;
                $haveData = false;
                while ($myrow = $result->fetch_assoc()) {
                    $c++;
                    $fechaHoraRec = $myrow["fecha"] . " " . $myrow["hora"];
                    if (strcmp($fechaHoraRec, $fechaHoraIni) >= 0 && strcmp($fechaHoraRec, $fechaHoraFin) <= 0) {
                        $haveData = true;

                        $json .= "{"
                                . "idData: " . $c . ","
                                . "idVehicleData:" . $idVehicle . ","
                                . "idDeviceData:" . $myrow["id_equipo"] . ","
                                . "dateTimeData:'" . $myrow["fecha"] . " " . $myrow["hora"] . "',"
                                . "dateTimeRegData:'" . $myrow["fecha_hora_reg"] . "',"
                                . "idSkyEventData:" . $myrow["id_sky_evento"] . ","
                                . "idPointData:" . $myrow["id_punto"] . ","
                                . "latitudData:" . $myrow["latitud"] . ","
                                . "longitudData:" . $myrow["longitud"] . ","
                                . "speedData:" . $myrow["velocidad"] . ","
                                . "courseData:" . $myrow["velocidad"] . ","
                                . "g1Data:" . $myrow["g1"] . ","
                                . "g2Data:" . $myrow["g2"] . ","
                                . "salData:" . $myrow["sal"] . ","
                                . "bateriaData:" . $myrow["bateria"] . ","
                                . "v1Data:" . $myrow["v1"] . ","
                                . "v2Data:" . $myrow["v2"] . ","
                                . "gsmData:" . $myrow["gsm"] . ","
                                . "gpsData:" . $myrow["gps"] . ","
                                . "ignData:" . $myrow["ign"] . ","
                                . "addressData:'" . utf8_encode($myrow["direccion"]) . "',"
                                . "pointData:'" . utf8_encode($myrow["punto"]) . "'},";
                    }
                }

                $json .="]";

                if ($haveData) {
                    echo "{success: true, isSkp: true, $json }";
                } else {
                    echo "{failure: true, message:'No hay datos entre estas fechas y horas.'}";
                }
            } else {
                echo "{failure: true, message:'No hay datos entre estas fechas y horas.'}";
            }
        } else {
            echo "{failure: true, message: 'Problemas en la construcción de la consulta.'}";
        }
    } else {
        $consultaSql = "SELECT df.id_equipo, df.fecha, df.hora, df.fecha_hora_reg, df.id_encabezado, df.id_ruta, df.id_punto, df.id_estado_bus, df.id_display, "
                . "df.id_estado_mecanico, df.latitud, df.longitud, df.velocidad, df.ip_publica, df.ip_equipo, df.trama_restante, df.direccion, "
                . "p.punto "
                . "FROM kbushistoricodb.dato_fastracks df, kbusdb.equipos e, kbusdb.vehiculos v, kbusdb.puntos p "
                . "WHERE df.id_equipo = e.id_equipo "
                . "AND e.id_equipo = v.id_equipo "
                . "AND df.id_punto = p.id_punto "
                . "AND v.id_vehiculo = ? "
                . "AND df.fecha BETWEEN ? AND ? "
                . "ORDER BY df.fecha, df.hora";
        $stmt = $mysqli->prepare($consultaSql);

        if ($stmt) {
            /* ligar parámetros para marcadores */
            $stmt->bind_param("iss", $idVehicle, $fechaIni, $fechaFin);
            /* ejecutar la consulta */
            $stmt->execute();

            $result = $stmt->get_result();

            $stmt->close();
            $mysqli->close();

            if ($result->num_rows > 0) {
                $fechaHoraIni = $fechaIni . " " . $horaIni;
                $fechaHoraFin = $fechaFin . " " . $horaFin;

                $json = "data: [";
                $c = 0;
                $haveData = false;
                while ($myrow = $result->fetch_assoc()) {
                    $c++;
                    $fechaHoraRec = $myrow["fecha"] . " " . $myrow["hora"];
                    if (strcmp($fechaHoraRec, $fechaHoraIni) >= 0 && strcmp($fechaHoraRec, $fechaHoraFin) <= 0) {
                        $haveData = true;

                        $json .= "{"
                                . "idData: " . $c . ","
                                . "idVehicleData:" . $idVehicle . ","
                                . "idDeviceData:" . $myrow["id_equipo"] . ","
                                . "dateTimeData:'" . $myrow["fecha"] . " " . $myrow["hora"] . "',"
                                . "dateTimeRegData:'" . $myrow["fecha_hora_reg"] . "',"
                                . "idHeaderData:" . $myrow["id_encabezado"] . ","
                                . "idRouteData:" . $myrow["id_ruta"] . ","
                                . "idPointData:" . $myrow["id_punto"] . ","
                                . "idStateBusData:" . $myrow["id_estado_bus"] . ","
                                . "idDisplayData:" . $myrow["id_display"] . ","
                                . "idStateMecanicData:" . $myrow["id_estado_mecanico"] . ","
                                . "latitudData:" . $myrow["latitud"] . ","
                                . "longitudData:" . $myrow["longitud"] . ","
                                . "speedData:" . $myrow["velocidad"] . ","
                                . "ipPublicData:'" . $myrow["ip_publica"] . "',"
                                . "ipDeviceData:'" . $myrow["ip_equipo"] . "',"
                                . "addressData:'" . utf8_encode($myrow["direccion"]) . "',"
                                . "pointData:'" . utf8_encode($myrow["punto"]) . "'},";
                    }
                }

                $json .="]";

                if ($haveData) {
                    echo "{success: true, isSkp: false, $json }";
                } else {
                    echo "{failure: true, message:'No hay recorridos entre estas fechas y horas.'}";
                }
            } else {
                echo "{failure: true, message:'No hay recorridos entre estas fechas y horas.'}";
            }
        } else {
            echo "{failure: true, message: 'Problemas en la construcción de la consulta.'}";
        }
    }
}