<?php

include ('../../../dll/config.php');

if (!$mysqli = getConectionDb()) {
    echo "{success:true, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.',state: false}";
} else {
    $requestBody = file_get_contents('php://input');
    $json = json_decode($requestBody, true);

    $setPoint = $setGeoSkp = $setGeoFastrack = $setLatitud = $setLongitud = $setAddress = "";

    if (isset($json["pointPoint"])) {
        $setPoint = "punto='" . utf8_decode($json["pointPoint"]) . "',";
    }
    if (isset($json["geoSkpPoint"])) {
        $setGeoSkp = "geocerca_skp='" . utf8_decode($json["geoSkpPoint"]) . "',";
    }
    if (isset($json["geoFastrackPoint"])) {
        $setGeoFastrack = "geocerca_fastrack=" . $json["geoFastrackPoint"] . ",";
    }
    if (isset($json["latitudPoint"])) {
        $setLatitud = "latitud='" . $json["latitudPoint"] . "',";
    }
    if (isset($json["longitudPoint"])) {
        $setLongitud = "longitud='" . $json["longitudPoint"] . "',";
    }
    if (isset($json["addressPoint"])) {
        $setAddress = "direccion='" . utf8_decode($json["addressPoint"]) . "',";
    }

    $setId = "id_punto = " . $json["id"];

    if ($setGeoSkp != "" || $setGeoFastrack != "") {
        $exist = false;
        if ($setGeoSkp != "") {
            $existeGeoSkpSql = "select id_punto from puntos where geocerca_skp = '" . $json["geoSkpPoint"] . "'";
            $resultGeoSkp = $mysqli->query($existeGeoSkpSql);
            if ($resultGeoSkp->num_rows > 0) {
                $exist = true;
            }
        }
        if ($setGeoFastrack != "") {
            $existeGeoFastrackSql = "select id_punto from puntos where geocerca_fastrack = " .$json["geoFastrackPoint"];
            $resultGeoFastrack = $mysqli->query($existeGeoFastrackSql);
            if ($resultGeoFastrack->num_rows > 0) {
                $exist = true;
            }
        }

        if ($exist) {
            echo "{success:true, message:'Geocercas de SkyPatrol o Fastrack ya existe en otro Punto de Control.',state: false}";
        } else {
            $updateSql = "update puntos "
                    . "set $setPoint$setGeoSkp$setGeoFastrack$setLatitud$setLongitud$setAddress$setId "
                    . "where id_punto = ?";

            $stmt = $mysqli->prepare($updateSql);
            if ($stmt) {
                $stmt->bind_param("i", $json["id"]);
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
    } else {
        $updateSql = "update puntos "
                . "set $setPoint$setGeoSkp$setGeoFastrack$setLatitud$setLongitud$setAddress$setId "
                . "where id_punto = ?";

        $stmt = $mysqli->prepare($updateSql);
        if ($stmt) {
            $stmt->bind_param("i", $json["id"]);
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