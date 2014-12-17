<?php

/*
 * To change this license header, choose License Headers IN Project Properties.
 * To change this template file, choose Tools | Templates
 * AND open the template IN the editor.
 */

include ('../../../../dll/config.php');

extract($_POST);
if (!$mysqli = getConectionDb()) {
    echo "{failure: true, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {
    $queryDispatch = "SELECT v.id_equipo, d.fecha, d.hora_ini, d.hora_fin_prog "
            . "FROM kbushistoricodb.despachos d, vehiculos v, equipos e "
            . "WHERE d.id_vehiculo = v.id_vehiculo "
            . "AND v.id_equipo = e.id_equipo "
            . "AND d.id_ruta = $idRoute "
            . "AND d.fecha = '$dateReport' "
            . "AND d.valido = 1";

    /*$queryDispatch = "SELECT e.id_equipo, DATE(fecha_hora_equipo) AS fecha, TIME(fecha_hora_equipo) AS hora_ini, "
            . "(SELECT addtime(TIME(SEC_TO_TIME(SUM(TIME_TO_SEC(tiempos)))), TIME(fecha_hora_equipo)) "
            . "FROM punto_rutas "
            . "WHERE id_ruta = $idRoute) AS hora_fin_prog "
            . "FROM kbushistoricodb.control_rutas cr, vehiculos v, equipos e "
            . "WHERE cr.id_vehiculo = v.id_vehiculo "
            . "AND v.id_equipo = e.id_equipo "
            . "AND DATE(fecha_hora_equipo) = '$dateReport' "
            . "AND id_ruta = $idRoute";*/

    $resulsetDispatch = $mysqli->query($queryDispatch);

    if ($resulsetDispatch->num_rows > 0) {
        $resulsetDispatch = allRows($resulsetDispatch);
        
        $querySpeedingSkp = "SELECT ds.id_equipo, concat(ds.fecha, ' ', ds.hora) AS fecha_hora, ds.hora "
                . "FROM kbushistoricodb.dato_skps ds "
                . "WHERE ds.id_equipo IN ( "
                . "SELECT v.id_equipo "
                . "FROM kbushistoricodb.despachos d, vehiculos v, equipos e "
                . "WHERE d.id_vehiculo = v.id_vehiculo "
                . "AND v.id_equipo = e.id_equipo "
                . "AND d.id_ruta = $idRoute "
                . "AND e.id_tipo_equipo = 1 "
                . "AND d.fecha = '$dateReport' "
                . "AND d.valido = 1 GROUP BY v.id_equipo) "
                . "AND (ds.id_sky_evento = 12 OR ds.id_sky_evento = 21) "
                . "AND ds.fecha = '$dateReport'";

        $querySpeedingFastrack = "SELECT ds.id_equipo, concat(ds.fecha, ' ', ds.hora) AS fecha_hora, ds.hora "
                . "FROM kbushistoricodb.dato_fastracks ds "
                . "WHERE ds.id_equipo IN ( "
                . "SELECT v.id_equipo "
                . "FROM kbushistoricodb.despachos d, vehiculos v, equipos e "
                . "WHERE d.id_vehiculo = v.id_vehiculo "
                . "AND v.id_equipo = e.id_equipo "
                . "AND d.id_ruta = $idRoute "
                . "AND e.id_tipo_equipo = 2 "
                . "AND d.fecha = '$dateReport' "
                . "AND d.valido = 1 GROUP BY v.id_equipo) "
                . "AND ds.id_encabezado = 5 "
                . "AND ds.fecha = '$dateReport'";
        /*$querySpeedingSkp = "SELECT ds.id_equipo, concat(ds.fecha, ' ', ds.hora) AS fecha_hora, ds.hora "
                . "FROM kbushistoricodb.dato_skps ds "
                . "WHERE ds.id_equipo IN ( "
                . "SELECT v.id_equipo "
                . "FROM kbushistoricodb.control_rutas d, vehiculos v, equipos e "
                . "WHERE d.id_vehiculo = v.id_vehiculo "
                . "AND v.id_equipo = e.id_equipo "
                . "AND d.id_ruta = $idRoute "
                . "AND e.id_tipo_equipo = 1 "
                . "AND DATE(d.fecha_hora_equipo) = '$dateReport' GROUP BY v.id_equipo) "
                . "AND (ds.id_sky_evento = 12 OR ds.id_sky_evento = 21) "
                . "AND ds.fecha = '$dateReport'";

        $querySpeedingFastrack = "SELECT ds.id_equipo, concat(ds.fecha, ' ', ds.hora) AS fecha_hora, ds.hora "
                . "FROM kbushistoricodb.dato_fastracks ds "
                . "WHERE ds.id_equipo IN ( "
                . "SELECT v.id_equipo "
                . "FROM kbushistoricodb.control_rutas d, vehiculos v, equipos e "
                . "WHERE d.id_vehiculo = v.id_vehiculo "
                . "AND v.id_equipo = e.id_equipo "
                . "AND d.id_ruta = $idRoute "
                . "AND e.id_tipo_equipo = 2 "
                . "AND DATE(d.fecha_hora_equipo) = '$dateReport' GROUP BY v.id_equipo) "
                . "AND ds.id_encabezado = 5 "
                . "AND ds.fecha = '$dateReport'";*/

        $resultsetSpeedingSkp = $mysqli->query($querySpeedingSkp);
        $resultsetSpeedingFastrack = $mysqli->query($querySpeedingFastrack);

        $c = 0;
        $haveData = false;
        if ($resultsetSpeedingSkp->num_rows > 0) {
            while ($myrowskp = $resultsetSpeedingSkp->fetch_assoc()) {
                for ($i = 0; $i < count($resulsetDispatch); $i++) {
                    $myrowdispatch = $resulsetDispatch[$i];

                    $dateTimeStart = $myrowdispatch["fecha"] . " " . $myrowdispatch["hora_ini"];
                    $dateTimeFinish = $myrowdispatch["fecha"] . " " . $myrowdispatch["hora_fin_prog"];

                    if ($myrowdispatch["id_equipo"] == $myrowskp["id_equipo"] && strcmp($myrowskp["fecha_hora"], $dateTimeStart) >= 0 && strcmp($myrowskp["fecha_hora"], $dateTimeFinish) <= 0) {
                        $speedingByDispatch[$c] = array(
                            'idEquipo' => $myrowskp["id_equipo"],
                            'hora' => $myrowskp["hora"]
                        );
                        $c++;
                        $haveData = true;
                    }
                }
            }
        }
        if ($resultsetSpeedingFastrack->num_rows > 0) {
            while ($myrowfastrack = $resultsetSpeedingFastrack->fetch_assoc()) {
                for ($i = 0; $i < count($resulsetDispatch); $i++) {
                    $myrowdispatch = $resulsetDispatch[$i];

                    $dateTimeStart = $dateReport . " " . $myrowdispatch["hora_ini"];
                    $dateTimeFinish = $dateReport . " " . $myrowdispatch["hora_fin_prog"];

                    if ($myrowdispatch["id_equipo"] == $myrowfastrack["id_equipo"] && strcmp($myrowfastrack["fecha_hora"], $dateTimeStart) >= 0 && strcmp($myrowfastrack["fecha_hora"], $dateTimeFinish) <= 0) {
                        $speedingByDispatch[$c] = array(
                            'idEquipo' => $myrowfastrack["id_equipo"],
                            'hora' => $myrowfastrack["hora"]
                        );
                        $c++;
                        $haveData = true;
                    }
                }
            }
        }

        if ($haveData) {
            $objJson = "data: [";

            for ($j = 0; $j < 24; $j++) {
                $totalByHour = 0;
                for ($i = 0; $i < count($speedingByDispatch); $i++) {
                    $myrow = $speedingByDispatch[$i];

                    $timeInt = strtotime($myrow["hora"]);
                    $hour = (int) DATE("H", $timeInt);
                    if ($j == $hour) {
                        $totalByHour++;
                    }
                }

                if ($totalByHour > 0) {
                    if ($j < 10) {
                        $timeStart = "0" . $j . ":00";
                        if ($j < 9) {
                            $timeFinish = "0" . ($j + 1) . ":00";
                        } else {
                            $timeFinish = ($j + 1) . ":00";
                        }
                    } else {
                        $timeStart = $j . ":00";
                        $timeFinish = ($j + 1) . ":00";
                        if ($j == 23) {
                            $timeFinish = "00:00";
                        }
                    }

                    $objJson .= "{"
                            . "timesSpeeding: '" . $timeStart . ' - ' . $timeFinish . "',"
                            . "totalSpeeding: " . $totalByHour . "},";
                }
            }

            $objJson .= "]";
            echo "{success: true, $objJson}";
        } else {
            echo "{failure: true, message: 'No se encontraron excesos de Velocidad en la Ruta establecida.'}";
        }
    } else {
        echo "{failure: true, message: 'No hay Despachos de la Ruta en la fechas y horas establecidas.'}";
    }
}