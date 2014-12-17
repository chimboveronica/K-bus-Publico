<?php

include ('../../../dll/config.php');

if (!$mysqli = getConectionDb()) {
    echo "{success:true, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.',state: false}";
} else {

    $requestBody = file_get_contents('php://input');
    $json = json_decode($requestBody, true);

    $existeSql = "select id_punto from puntos where geocerca_skp = '" . $json["geoSkpPoint"] . "' or geocerca_fastrack = " . $json["geoFastrackPoint"];

    $result = $mysqli->query($existeSql);

    if ($result) {
        if ($result->num_rows > 0) {
            echo "{success:true, message:'Geocercas de SkyPatrol o Fastrack ya existe en otro Punto de Control.',state: false}";
        } else {

            $insertSql = "insert into puntos (punto, geocerca_skp, geocerca_fastrack, latitud, longitud, direccion) "
                    . "values(?, ?, ?, ?, ?, ?)";

            $stmt = $mysqli->prepare($insertSql);
            if ($stmt) {
                $stmt->bind_param("ssidds", utf8_decode($json["pointPoint"]), utf8_decode($json["geoSkpPoint"]), $json["geoFastrackPoint"], $json["latitudPoint"], $json["longitudPoint"], utf8_decode($json["addressPoint"]));
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