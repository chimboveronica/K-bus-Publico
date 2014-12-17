<?php

include ('../../../dll/config.php');

if (!$mysqli = getConectionDb()) {
    echo "{success:true, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.',state: false}";
} else {
    $requestBody = file_get_contents('php://input');
    $json = json_decode($requestBody, true);

    $setIdRoute = $setIdPoint = $setOrder = $setTime = $setTimePike = $setPrint = "";

    if (isset($json["idRoutePointRoute"])) {
        $setIdRoute = "id_ruta=" . $json["idRoutePointRoute"] . ",";
    }
    if (isset($json["idPointPointRoute"])) {
        $setIdPoint = "id_punto=" . $json["idPointPointRoute"] . ",";
    }
    if (isset($json["orderPointRoute"])) {
        $setOrder = "orden=" . $json["orderPointRoute"] . ",";
    }
    if (isset($json["timePointRoute"])) {
        $setTime = "tiempos='" . $json["timePointRoute"] . "',";
    }
    if (isset($json["timePikePointRoute"])) {
        //Aqui se quita la coma al final, para que se arme bien la consulta
        //Este valor siempre va a llegar
        $setTimePike = "tiempos_pico='" . $json["timePikePointRoute"] . "'";
    }
    if (isset($json["printPointRoute"])) {
        $setPrint = "imprimir=" . $json["printPointRoute"] . ",";
    }
    
    $id = explode("_", $json["id"]);

    if ($setOrder != "") {
        if ($setIdRoute != "") {
            $existeSql = "select id_ruta from punto_rutas where id_ruta=" . $json["idRoutePointRoute"] . " and orden = " . $json["orderPointRoute"];
        } else {
            $existeSql = "select id_ruta from punto_rutas where id_ruta=" . $id[0] . " and orden = " . $json["orderPointRoute"];
        }

        $result = $mysqli->query($existeSql);

        if ($result->num_rows > 0) {
            echo "{success:true, message:'Ya existe un Punto de Control asignado al orden que desea colocar.',state: false}";
        } else {
            $updateSql = "update punto_rutas "
                    . "set $setIdRoute$setIdPoint$setOrder$setTime$setPrint$setTimePike "
                    . "where id_ruta = ? and orden = ?";
            
            $stmt = $mysqli->prepare($updateSql);
            if ($stmt) {
                $stmt->bind_param("ii", $id[0], $id[1]);

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
        $exist = false;
        if ($setIdRoute != "") {
            $existeSql = "select id_ruta from punto_rutas where id_ruta=" . $json["idRoutePointRoute"] . " and orden = " . $id[1];
            $result = $mysqli->query($existeSql);
            if ($result->num_rows > 0) {
                $exist = true;
            }
        }
        
        if ($exist) {
            echo "{success:true, message:'Ya existe un Punto de Control asignado al orden que desea colocar.',state: false}";
        } else {
            $updateSql = "update punto_rutas "
                    . "set $setIdRoute$setIdPoint$setOrder$setTime$setPrint$setTimePike "
                    . "where id_ruta = ? and orden = ?";

            $stmt = $mysqli->prepare($updateSql);
            if ($stmt) {
                if ($setIdRoute != "") {
                    $stmt->bind_param("ii", $json["idRoutePointRoute"], $id[1]);
                } else {
                    $stmt->bind_param("ii", $id[0], $id[1]);
                }
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