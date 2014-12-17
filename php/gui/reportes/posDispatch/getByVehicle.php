<?php

include ('../../../../dll/config.php');
extract($_POST);

if (!$mysqli = getConectionDb()) {
    echo "{failure: true, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {
    $queryTypeDevieSql = "select e.id_tipo_equipo "
            . "from vehiculos v, equipos e "
            . "where v.id_equipo = e.id_equipo "
            . "and v.id_vehiculo = $idVehicle";

    $resultTypeDevice = $mysqli->query($queryTypeDevieSql);
    $myrowtypedevice = $resultTypeDevice->fetch_assoc();

    if (isset($horaIniC)) {
        $horaIni = $horaIniC;
    }

    if ($myrowtypedevice["id_tipo_equipo"] == 1) {
        $consultaSql = "select ds.fecha, ds.hora, ds.id_sky_evento, ds.id_punto, v.reg_municipal, v.id_vehiculo, v.id_empresa "
                . "from kbushistoricodb.dato_skps ds, kbusdb.equipos e, kbusdb.vehiculos v "
                . "where ds.id_equipo = e.id_equipo "
                . "and e.id_equipo = v.id_equipo "
                . "and v.id_vehiculo = ? "
                . "and ds.fecha between ? and ? "
                . "and ds.id_punto > 1 "
                . "order by ds.fecha, ds.hora";

        $stmt = $mysqli->prepare($consultaSql);
        if ($stmt) {
            /* ligar parámetros para marcadores */
            $stmt->bind_param("iss", $idVehicle, $fechaIni, $fechaFin);
            /* ejecutar la consulta */
            $stmt->execute();

            $result = $stmt->get_result();

            $stmt->close();

            if ($result) {
                $resulset = allRows($result);

                $H = substr($resulset[0]["hora"], 0, 2);
                $M = substr($resulset[0]["hora"], 3, 2);
                $minAnt = $H * 60 + $M - 60;

                $json = "data: [";
                for ($i = 0; $i < count($resulset); $i++) {
                    $myrow = $resulset [$i];
                    $HN = substr($myrow["hora"], 0, 2);
                    $MN = substr($myrow["hora"], 3, 2);
                    $minNew = $HN * 60 + $MN;

                    $queryPointSql = "select pr.id_ruta, pr.tiempos, r.ruta from punto_rutas pr, rutas r where pr.id_ruta = r.id_ruta and pr.orden = 1 and pr.id_punto=" . $myrow["id_punto"];
                    $resultsetPoint = $mysqli->query($queryPointSql);
                    if ($resultsetPoint) {
                        while ($myrowpoint = $resultsetPoint->fetch_assoc()) {
                            $h1 = substr($myrowpoint["tiempos"], 0, 2);
                            $m1 = substr($myrowpoint["tiempos"], 3, 2);

                            $h2 = substr($myrow["hora"], 0, 2);
                            $m2 = substr($myrow["hora"], 3, 2);
                            $s2 = substr($myrow["hora"], 6, 2);

                            if ($m2 < $m1) {
                                $m2 += 60;
                                $h1++;
                            }

                            $hor = $h2 - $h1;
                            if ($hor < 10) {
                                $hor = '0' . $hor;
                            } $min = $m2 - $m1;
                            if ($s2 >= 30) {
                                $min++;
                            }
                            if ($min < 10) {
                                $min = '0' + $min;
                            }

                            $horaAnt = $hor . ":" . $min;

                            $json .= "{" . "idVehicleDispatch:" . $myrow["id_vehiculo"] . ","
                                    . "idCompanyDispatch:" . $myrow["id_empresa"] . ","
                                    . "idRouteDispatch:" . $myrowpoint["id_ruta"] . ","
                                    . "routeDispatch:'" . utf8_encode($myrowpoint["ruta"]) . "',"
                                    . "dateTimeDispatch:'" . $myrow["fecha"] . " " . $myrow["hora"] . "',"
                                    . "muniRegDispatch:'" . $myrow["reg_municipal"] . "'},";

                            $HA = substr($myrow["hora"], 0, 2);
                            $MA = substr($myrow["hora"], 3, 2);

                            $minAnt = $HA * 60 + $MA;
                        }
                    }
                }
                $json .="]";
                echo "{success: true, $json}";
            } else {
                echo "{failure: true, message:'No hay datos entre estas fechas y horas.'}";
            }
        } else {
            echo "{failure: true, message: 'Problemas en la construcción de la consulta.'}";
        }
    } else {
        $consultaSql = "select df.id_equipo, df.fecha, df.hora, df.id_punto, v.reg_municipal, v.id_vehiculo, v.id_empresa "
                . "from kbushistoricodb.dato_fastracks df, kbusdb.equipos e, kbusdb.vehiculos v "
                . "where df.id_equipo = e.id_equipo "
                . "and e.id_equipo = v.id_equipo "
                . "and v.id_vehiculo = ? "
                . "and df.fecha between ? and ? "
                . "and df.id_punto > 1 "
                . "order by df.fecha, df.hora";
        $stmt = $mysqli->prepare($consultaSql);

        if ($stmt) {
            /* ligar parámetros para marcadores */
            $stmt->bind_param("iss", $idVehicle, $fechaIni, $fechaFin);
            /* ejecutar la consulta */
            $stmt->execute();

            $result = $stmt->get_result();

            $stmt->close();

            if ($result) {
                $resulset = allRows($result);

                $H = substr($resulset[0]["hora"], 0, 2);
                $M = substr($resulset[0]["hora"], 3, 2);
                $minAnt = $H * 60 + $M - 60;

                $json = "data: [";
                $haveData = false;
                for ($i = 0; $i < count($resulset); $i++) {
                    $myrow = $resulset [$i];
                    $HN = substr($myrow["hora"], 0, 2);
                    $MN = substr($myrow["hora"], 3, 2);
                    $minNew = $HN * 60 + $MN;

                    $queryPointSql = "select pr.id_ruta, pr.tiempos, r.ruta from punto_rutas pr, rutas r where pr.id_ruta = r.id_ruta and pr.orden = 1 and pr.id_punto=" . $myrow["id_punto"];
                    $resultsetPoint = $mysqli->query($queryPointSql);
                    if ($resultsetPoint) {
                        $haveData = true;
                        while ($myrowpoint = $resultsetPoint->fetch_assoc()) {
                            $h1 = substr($myrowpoint["tiempos"], 0, 2);
                            $m1 = substr($myrowpoint["tiempos"], 3, 2);

                            $h2 = substr($myrow["hora"], 0, 2);
                            $m2 = substr($myrow["hora"], 3, 2);
                            $s2 = substr($myrow["hora"], 6, 2);

                            if ($m2 < $m1) {
                                $m2 += 60;
                                $h1++;
                            }

                            $hor = $h2 - $h1;
                            if ($hor < 10) {
                                $hor = '0' . $hor;
                            } $min = $m2 - $m1;
                            if ($s2 >= 30) {
                                $min++;
                            }
                            if ($min < 10) {
                                $min = '0' + $min;
                            }

                            $horaAnt = $hor . ":" . $min;

                            $json .= "{" . "idVehicleDispatch:" . $myrow["id_vehiculo"] . ","
                                    . "idCompanyDispatch:" . $myrow["id_empresa"] . ","
                                    . "idRouteDispatch:" . $myrowpoint["id_ruta"] . ","
                                    . "routeDispatch:'" . utf8_encode($myrowpoint["ruta"]) . "',"
                                    . "dateTimeDispatch:'" . $myrow["fecha"] . " " . $myrow["hora"] . "',"
                                    . "muniRegDispatch:'" . $myrow["reg_municipal"] . "'},";

                            $HA = substr($myrow["hora"], 0, 2);
                            $MA = substr($myrow["hora"], 3, 2);

                            $minAnt = $HA * 60 + $MA;
                        }
                    }
                }
                $json .="]";
                if ($haveData) {
                    echo "{success: true, $json}";
                } else {
                    echo "{failure: true, message:'No hay datos entre estas fechas y horas.'}";
                }
            } else {
                echo "{failure: true, message:'No hay datos entre estas fechas y horas.'}";
            }
        } else {
            echo "{failure: true, message: 'Problemas en la construcción de la consulta.'}";
        }
    }
    $mysqli->close();
}