<?php

include ('../../../dll/config.php');

if (!$mysqli = getConectionDb()) {
    echo "{success:false, message: 'No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {
    $requestBody = file_get_contents('php://input');
    $json = json_decode($requestBody, true);

    $setCode = $setRoute = $setLine = $setTimePenalty = $setColor = $setIcon = $setDistancia = "";

    if (isset($json["codeRoute"])) {
        $setCode = "cod_ruta='" . utf8_decode($json["codeRoute"]) . "',";
    }
    if (isset($json["routeRoute"])) {
        $setRoute = "ruta='" . utf8_decode($json["routeRoute"]) . "',";
    }
    if (isset($json["lineRoute"])) {
        $setLine = "linea=" . $json["lineRoute"] . ",";
    }
    if (isset($json["timePenaltyRoute"])) {
        $setTimePenalty = "tiempo_sancion='" . $json["timePenaltyRoute"] . "',";
    }
    if (isset($json["colorRoute"])) {
        $setColor = "color='" . $json["colorRoute"] . "',";
    }
    if (isset($json["iconRoute"])) {
        $setIcon = "icono='" . utf8_decode($json["iconRoute"]) . "',";
    }
    if (isset($json["distanciaRoute"])) {
        $setDistancia = "distancia='" . $json["distanciaRoute"] . "',";
    }
    $setId = "id_ruta = " . $json["id"];

    if ($setCode != "") {
        $existeSql = "select cod_ruta from rutas where cod_ruta='" . $json["codeRoute"] . "'";

        $result = $mysqli->query($existeSql);

        if ($result->num_rows > 0) {
            echo "{success:false, message: 'Ya existe el codigo asignado a otra ruta.'}";
        } else {
            $updateSql = "update rutas "
                    . "set $setCode$setRoute$setLine$setTimePenalty$setColor$setIcon$setDistancia$setId "
                    . "where id_ruta = ?";

            $stmt = $mysqli->prepare($updateSql);
            if ($stmt) {
                $stmt->bind_param("i", $json["id"]);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    if (isset($json["verticesRoute"])) {
                        editLineRoute($json["verticesRoute"], $json["id"]);
                    } else {
                        echo "{success:true, message: 'Datos actualizados correctamente.'}";
                    }
                } else {
                    echo "{success:false, message: 'Problemas al actualizar en la tabla.'}";
                }
                $stmt->close();
            } else {
                echo "{success:false, message: 'Problemas en la construcción de la consulta.'}";
            }
        }
    } else {
        $selectTiempoSql = "select tiempo_sancion from rutas where id_ruta = " . $json["id"];
        $resultTiempo = $mysqli->query($selectTiempoSql);
        $myrow = $resultTiempo->fetch_assoc();

        $updateSql = "update rutas "
                . "set $setCode$setRoute$setLine$setTimePenalty$setColor$setIcon$setDistancia$setId "
                . "where id_ruta = ?";

        $stmt = $mysqli->prepare($updateSql);
        if ($stmt) {
            $stmt->bind_param("i", $json["id"]);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                if (isset($json["verticesRoute"])) {
                    editLineRoute($json["verticesRoute"], $json["id"]);
                } else {
                    echo "{success:true, message: 'Datos actualizados correctamente.'}";
                }
            } else {
                editLineRoute($json["verticesRoute"], $json["id"]);
            }
            $stmt->close();
        } else {
            echo "{success:false, message: 'Problemas en la construcción de la consulta.'}";
        }
    }

    $mysqli->close();
}

function editLineRoute($verticesRoute, $idRuta) {
    if (!$mysqli = getConectionDb()) {
        echo "{success:false, message: 'No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
    } else {
        $destroySql = "delete from linea_rutas where id_ruta = ?";
        $stmtDestroy = $mysqli->prepare($destroySql);
        if ($stmtDestroy) {
            $stmtDestroy->bind_param("i", $idRuta);
            $stmtDestroy->execute();


            $dataVertice = explode(";", $verticesRoute);
            for ($i = 0; $i < count($dataVertice); $i++) {

                $dataPoint = explode(",", $dataVertice[$i]);
                $insertPoinsSql = "insert into linea_rutas (id_ruta, orden, latitud, longitud) "
                        . "values(?, ?, ?, ?)";
                $stmtPoint = $mysqli->prepare($insertPoinsSql);
                if ($stmtPoint) {
                    $orden = $i + 1;
                    $stmtPoint->bind_param("iidd", $idRuta, $orden, $dataPoint[0], $dataPoint[1]);
                    $stmtPoint->execute();
                    if ($stmtPoint->affected_rows > 0) {
                        $correct = true;
                    } else {
                        $correct = false;
                    }
                }
            }
            $stmtPoint->close();
            if ($correct) {
                echo "{success:true, message: 'Datos Editados Correctamente.'}";
            } else {
                echo "{success:false, message: 'No se pudo Editar el Trazado de la Ruta.'}";
            }
            $stmtDestroy->close();
        } else {
            echo "{success:false, message: 'Problemas en la construcción de la consulta.'}";
        }
    }
}
