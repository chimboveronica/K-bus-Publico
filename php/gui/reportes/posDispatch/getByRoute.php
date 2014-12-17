<?php

include ('../../../../dll/config.php');
extract($_POST);

if (!$mysqli = getConectionDb()) {
    echo "{failure: true, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {
    $consultaSql = "select df.id_equipo, v.id_vehiculo, v.id_empresa, df.fecha, df.hora, df.id_ruta, v.reg_municipal, r.ruta "
            . "from kbushistoricodb.dato_fastracks df, kbusdb.equipos e, kbusdb.vehiculos v, rutas r "
            . "where df.id_equipo = e.id_equipo "
            . "and e.id_equipo = v.id_equipo "
            . "and df.id_ruta = r.id_ruta "
            . "and df.id_ruta = ? "
            . "and df.fecha between ? and ? "
            . "order by df.id_equipo, df.fecha, df.hora";
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
            $resulset = allRows($result);
            $aux = -1;
            $auxHora = 1;
            $H = substr($resulset[0]["hora"], 0, 2);
            $M = substr($resulset[0]["hora"], 3, 2);
            $minAnt = $H * 60 + $M - 60;

            $json = "data: [";
            $haveData = false;
            for ($i = 0; $i < count($resulset); $i++) {
                $myrow = $resulset[$i];
                $HN = substr($myrow["hora"], 0, 2);
                $MN = substr($myrow["hora"], 3, 2);
                $minNew = $HN * 60 + $MN;

                if ($myrow["id_equipo"] != $aux || $minNew - $minAnt >= 15) {
                    $haveData = true;
                    $json .= "{"
                            . "idVehicleDispatch:" . $myrow["id_vehiculo"] . ","
                            . "idCompanyDispatch:" . $myrow["id_empresa"] . ","
                            . "idRouteDispatch:" . $myrow["id_ruta"] . ","
                            . "routeDispatch:'" . $myrow["ruta"] . "',"
                            . "dateTimeDispatch:'" . $myrow["fecha"] . " " . $myrow["hora"] . "',"
                            . "muniRegDispatch:'" . $myrow["reg_municipal"] . "'}";

                    if ($i != count($resulset) - 1) {
                        $json .= ",";
                    }
                }

                $HA = substr($myrow["hora"], 0, 2);
                $MA = substr($myrow["hora"], 3, 2);

                $minAnt = $HA * 60 + $MA;


                $aux = $myrow["id_equipo"];
                $auxHora = substr($myrow["hora"], 0, 2);
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