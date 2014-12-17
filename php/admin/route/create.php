<?php

include ('../../../dll/config.php');

if (!$mysqli = getConectionDb()) {
    echo "{success:false, message: 'No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {

    $requestBody = file_get_contents('php://input');
    $json = json_decode($requestBody, true);

    $existeSql = "select cod_ruta from rutas where cod_ruta='" . $json["codeRoute"] . "'";

    $result = $mysqli->query($existeSql);

    if ($result) {
        if ($result->num_rows > 0) {
            echo "{success:false, message: 'Ya existe el codigo asignado a otra ruta.'}";
        } else {

            $insertSql = "insert into rutas (cod_ruta, ruta, linea, tiempo_sancion, color, icono, distancia) "
                    . "values(?, ?, ?, ?, ?, ?, ?)";

            $stmt = $mysqli->prepare($insertSql);
            if ($stmt) {
                $stmt->bind_param("ssisssd", utf8_decode($json["codeRoute"]), utf8_decode($json["routeRoute"]), $json["lineRoute"], $json["timePenaltyRoute"], $json["colorRoute"], utf8_decode($json["iconRoute"]), $json["distanciaRoute"]);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    $selectRutaSql = "select id_ruta from rutas where cod_ruta='" . $json["codeRoute"] . "'";
                    $resultRuta = $mysqli->query($selectRutaSql);
                    $myrow = $resultRuta->fetch_assoc();

                    $dataVertice = explode(";", $json["verticesRoute"]);
                    for ($i = 0; $i < count($dataVertice); $i++) {
                        $dataPoint = explode(",", $dataVertice[$i]);
                        $insertPoinsSql = "insert into linea_rutas (id_ruta, orden, latitud, longitud) "
                                . "values(?, ?, ?, ?)";
                        $stmtPoint = $mysqli->prepare($insertPoinsSql);
                        if ($stmtPoint) {
                            $orden = $i + 1;
                            $stmtPoint->bind_param("iidd", $myrow["id_ruta"], $orden, $dataPoint[0], $dataPoint[1]);
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
                        echo "{success:true, message: 'Datos Insertados Correctamente.'}";
                    } else {
                        echo "{success:false, message: 'No se pudo ingresar el trazado de la Ruta.'}";
                    }
                } else {
                    echo "{success:false, message: 'No se pudo ingresar la Ruta.'}";
                }
                $stmt->close();
            } else {
                echo "{success:false, message: 'Problemas en la construcción de la consulta.'}";
            }
        }
        $mysqli->close();
    } else {
        echo "{success:false, message: 'Problemas en la construcción de la consulta.'}";
    }
}