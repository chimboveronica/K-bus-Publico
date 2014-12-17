<?php

include ('../../../dll/config.php');

if (!$mysqli = getConectionDb()) {
    echo "{success:false, message: 'No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {
    $requestBody = file_get_contents('php://input');
    $json = json_decode($requestBody, true);

    $destroySql = "delete from rutas where id_ruta = ?";

    $stmt = $mysqli->prepare($destroySql);
    if ($stmt) {
        $stmt->bind_param("i", $json["id"]);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "{success:true, message: 'Datos Eliminados Correctamente.'}";
        } else {
            echo "{success:false, message: 'Problemas al Eliminar en la Tabla.'}";
        }
        $stmt->close();
    } else {
        echo "{success:false, message: 'Problemas en la construcción de la consulta.'}";
    }
    $mysqli->close();
}