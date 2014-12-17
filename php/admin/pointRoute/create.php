<?php

include ('../../../dll/config.php');

if (!$mysqli = getConectionDb()) {
    echo "{success:true, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.',state: false}";
} else {

    $requestBody = file_get_contents('php://input');
    $json = json_decode($requestBody, true);

    $existeSql = "select id_punto from punto_rutas where id_ruta = " . $json["idRoutePointRoute"] . " and orden = " . $json["orderPointRoute"];

    $result = $mysqli->query($existeSql);

    if ($result) {
        if ($result->num_rows > 0) {
            echo "{success:true, message:'Ya existe un Punto de Control asignado al orden que desea colocar.',state: false}";
        } else {

            $insertSql = "insert into punto_rutas (id_ruta, id_punto, orden, tiempos, tiempos_pico, imprimir) "
                    . "values(?, ?, ?, ?, ?, ?)";

            $stmt = $mysqli->prepare($insertSql);
            if ($stmt) {
                $stmt->bind_param("iiissi", $json["idRoutePointRoute"], $json["idPointPointRoute"], $json["orderPointRoute"], $json["timePointRoute"], $json["timePikePointRoute"], $json["printPointRoute"]);
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
    } else {
        echo "{success:true, message: 'Problemas en la construcción de la consulta.',state: false}";
    }
}