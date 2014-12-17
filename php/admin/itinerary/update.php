<?php

include ('../../../dll/config.php');

if (!$mysqli = getConectionDb()) {
    echo "{success:true, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.',state: false}";
} else {
    $requestBody = file_get_contents('php://input');
    $json = json_decode($requestBody, true);

    $setidRuta = $setidTurno = $setTiempo = "";

    if (isset($json["turnoItine"])) {
        $setidTurno = "id_turno=" . $json["turnoItine"] . ",";
    }
    if (isset($json["idRutaItine"])) {
        $setidRuta = "id_ruta=" . $json["idRutaItine"] . ",";
    }
    if (isset($json["horaItine"])) {
        $setTiempo = "tiempo='" . $json["horaItine"] . "'";
    }

    $id = explode("_", $json["id"]);
    if ($setTiempo != "") {
        if ($setidRuta != "") {
            $existeSql = "select id_turno from itinerarios_p where id_turno=" . $id[0] . " and  id_ruta=" . $json["idRutaItine"] . " and tiempo = '" . $json["horaItine"] . "'";
        } else {
            if ($setidTurno != "") {
                $existeSql = "select id_turno from itinerarios_p  where id_turno=" .$json["turnoItine"]. " and  id_ruta=" . $id[1] . " and tiempo = '" . $json["horaItine"] . "'";
            } else {
                $existeSql = "select id_turno from itinerarios_p  where id_turno=" . $id[0] . " and  id_ruta=" . $id[1] . " and tiempo = '" . $json["horaItine"] . "'";
            }
        }
        $result = $mysqli->query($existeSql);
        if ($result->num_rows > 0) {
            echo "{success:true, message:'El Itinerario a ingresar ya existe',state: false}";
        } else {
            $updateSql = "update itinerarios_p "
                    . "set $setidTurno$setidRuta$setTiempo "
                    . "where id_turno = ? and id_ruta = ? and tiempo = ? ";

            $stmt = $mysqli->prepare($updateSql);
            if ($stmt) {
                $stmt->bind_param("iis", $id[0], $id[1], $id[2]);

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
    }
    $mysqli->close();
}