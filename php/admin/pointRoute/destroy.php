<?php

include ('../../../dll/config.php');

if (!$mysqli = getConectionDb()) {
    echo "{success:true, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.',state: false}";
} else {
    $requestBody = file_get_contents('php://input');
    $json = json_decode($requestBody, true);

    $destroySql = "delete from punto_rutas where id_ruta = ? and orden = ?";

    $stmt = $mysqli->prepare($destroySql);
    if ($stmt) {
        $id = explode("_", $json["id"]);
        $stmt->bind_param("ii", $id[0], $id[1]);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "{success:true, message:'Datos Eliminados Correctamente.',state: true}";
        } else {
            echo "{success:true, message: 'Problemas al Eliminar en la Tabla.',state: false}";
        }
        $stmt->close();
    } else {
        echo "{success:true, message: 'Problemas en la construcción de la consulta.',state: false}";
    }
    $mysqli->close();
}