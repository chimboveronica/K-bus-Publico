<?php

include ('../../../dll/config.php');

if (!$mysqli = getConectionDb()) {
    echo "{success:true, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.',state: false}";
} else {
    $requestBody = file_get_contents('php://input');
    $json = json_decode($requestBody, true);

    $setidRuta = $setidTurno = $setTiempo = $setnum = "";

    if (isset($json["turnoItine_s"])) {
        $setidTurno = "id_turno=" . $json["turnoItine_s"] . ",";
    }
    if (isset($json["idRutaItine_s"])) {
        $setidRuta = "id_ruta=" . $json["idRutaItine_s"] . ",";
    }
    if (isset($json["numItine_s"])) {
        $setnum = "num_vehiculos=" . $json["numItine_s"] . ",";
    }
    if (isset($json["horaItine_s"])) {
        $setTiempo = "tiempo='" . $json["horaItine_s"] . "'";
    }
    $id = explode("_", $json["id"]);
    $val = 0;
    if ($setTiempo != "") {
        if ($setidRuta != "") {
            $existeSql = "select id_turno from itinerarios_s where id_turno=" . $id[0] . " and  id_ruta=" . $json["idRutaItine_s"] . " and tiempo = '" . $json["horaItine_s"] . "'";
            $val = 1;
        } else {
            if ($setidTurno != "") {
                $existeSql = "select id_turno from itinerarios_s  where id_turno=" . $json["turnoItine_s"] . " and  id_ruta=" . $id[1] . " and tiempo = '" . $json["horaItine_s"] . "'";
                $val = 1;
            } else {
                $existeSql = "select id_turno from itinerarios_s  where id_turno=" . $id[0] . " and tiempo = '" . $json["horaItine_s"] . "'";
                $val = 1;
            }
        }
//        if ($val == 0 && $setnum!="") {
//            $existeSql = "select id_turno from itinerarios_s  where id_turno=" . $id[0] . " and  id_ruta=" . $id[1] . " and  num_vehiculos=" . $json["numItine_s"] . " and tiempo = '" . $json["horaItine_s"] . "'";
//        } else {
//            $existeSql = "select id_turno from itinerarios_s  where id_turno=" . $id[0] . " and  id_ruta=" . $id[1] . " and  num_vehiculo=" . $id[3] . " and tiempo = '" . $json["horaItine_s"] . "'";
//        }
    }
    $result = $mysqli->query($existeSql);
    if ($result->num_rows > 0) {
        echo "{success:true, message:'Ya existe',state: false}";
    } else {
        $updateSql = "update itinerarios_s "
                . "set $setidTurno$setidRuta$setTiempo$setnum "
                . "where id_turno = ? and id_ruta = ? and tiempo = ? and num_vehiculos = ? ";

        $stmt = $mysqli->prepare($updateSql);
        if ($stmt) {
            $stmt->bind_param("iisi", $id[0], $id[1], $id[2], $id[3]);

            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo "{success:true, message:'Datos actualizados correctamente.',state: true}";
            } else {
                echo "{success:true, message: 'Problemas al actualizar en la tabla.',state: false}";
            }
            $stmt->close();
        } else {
            echo "{success:true, message: 'Problemas en la construcción de la consulta.',state: false}";
        }
    }

    $mysqli->close();
}    