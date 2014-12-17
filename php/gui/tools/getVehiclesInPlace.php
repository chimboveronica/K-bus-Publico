<?php

include ('../../../dll/config.php');

extract($_POST);

if (!$mysqli = getConectionDb()) {
    echo "{failure:true, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {
    $coord = explode(";", $verticesPlace);
    $numVer = count($coord);

    for ($i = 0; $i < count($coord); $i++) {
        $data = explode(",", $coord[$i]);
        $vertx[$i] = $data[1];
        $verty[$i] = $data[0];
    }

    $consultaSql = "SELECT v.reg_municipal, ds.fecha, ds.hora, ds.velocidad, ds.latitud, ds.longitud, ds.id_sky_evento, ds.bateria, ds.gsm, ds.gps, ds.ign "
            . "FROM kbushistoricodb.dato_skps ds, vehiculos v "
            . "WHERE ds.id_equipo = v.id_equipo "
            . "AND ds.fecha = '$fecha' "
            . "AND ds.hora BETWEEN '$horaIni' AND '$horaFin'";

    $result = $mysqli->query($consultaSql);
    $mysqli->close();

    if ($result->num_rows > 0) {
        $haveVehicles = false;

        $json = "data: [";

        while ($myrow = $result->fetch_assoc()) {
            if (pointOnVertice($numVer, $verty, $vertx, $myrow["latitud"], $myrow["longitud"])) {
                $json .= "{"
                        . "muniRegPlace: '" . $myrow["reg_municipal"] . "',"
                        . "dateTimePlace: '" . $myrow["fecha"] . ' ' . $myrow["hora"] . "',"
                        . "speedPlace: " . $myrow["velocidad"] . ","
                        . "latitudPlace: " . $myrow["latitud"] . ","
                        . "longitudPlace: " . $myrow["longitud"] . ","
                        . "idSkyEventPlace: " . $myrow["id_sky_evento"] . ","
                        . "batteryPlace: " . $myrow["bateria"] . ","
                        . "gsmPlace: " . $myrow["gsm"] . ","
                        . "gpsPlace: " . $myrow["gps"] . ","
                        . "ignPlace: " . $myrow["ign"] . "},";
                $haveVehicles = true;
            }
        }

        $json .="]";

        if ($haveVehicles) {
            echo "{success: true, $json }";
        } else {
            echo "{failure: true, message: 'No se encuentran vehiculos en el lugar'}";
        }
    } else {
        echo "{failure: true, message: 'No se encuentran vehiculos en el lugar'}";
    }
}

function pointOnVertice($numVer, $verty, $vertx, $testy, $testx) {
    $c = false;
    for ($i = 0, $j = $numVer - 1; $i < $numVer; $j = $i++) {
        if ((($vertx[$i] > $testx) != ($vertx[$j] > $testx)) && ($testy < ($verty[$j] - $verty[$i]) * ($testx - $vertx[$i]) / ($vertx[$j] - $vertx[$i]) + $verty[$i])) {
            $c = !$c;
        }
    }
    return $c;
}
