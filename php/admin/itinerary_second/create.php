<?php

include ('../../../dll/config.php');

if (!$mysqli = getConectionDb()) {
    echo "{success:true, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.',state: false}";
} else {

    $requestBody = file_get_contents('php://input');
    $json = json_decode($requestBody, true);
    $consultaSql1 = "select id_turno from itinerarios_s  where id_turno=" . $json["turnoItine_s"] . " and  id_ruta=" . $json["idRutaItine_s"] ." and  num_vehiculos=" . $json["numItine_s"] . " and tiempo = '" . $json["horaItine_s"] . "'";
    $result1 = $mysqli->query($consultaSql1);

    if ($result1->num_rows > 0) {
        echo "{success:true, message: 'Ya existe.',state: false}";
    } else {
        $insertSql = "insert into itinerarios_s (id_ruta,id_turno,tiempo,num_vehiculos) "
                . "values(?, ?, ?,?)";

        $stmt = $mysqli->prepare($insertSql);
        if ($stmt) {
            $stmt->bind_param("iisi", $json["idRutaItine_s"], $json["turnoItine_s"], $json["horaItine_s"], $json["numItine_s"]);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo "{success:true, message:'Datos Insertados Correctamente.',state: true}";
            } else {
                echo "{success:true, message: 'Problemas al Insertar en la Tabla.',state: false}";
            }
            $stmt->close();
        } else {
            echo "{success:true, message: 'Problemas en la construcción de la consulta.',state: false}";
        }
    }
    $mysqli->close();
}