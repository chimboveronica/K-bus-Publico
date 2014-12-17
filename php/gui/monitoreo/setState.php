<?php

include('../../login/isLogin.php');
include ('../../../dll/config.php');

extract($_POST);

if (!$mysqli = getConectionDb()) {
    echo "{success:false, message: 'No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {
    $idUsuario = $_SESSION["IDUSUARIOKBUS" . $site_city];
    if ($rb == 1) {
        $updateSql = "INSERT INTO kbushistoricodb.comentario_equipos (id_usuario, id_equipo, comentario) "
                . "VALUES (?, ?, ?)";
    } else {
        $updateSql = "INSERT INTO kbushistoricodb.comentario_vehiculos (id_usuario, id_vehiculo, comentario) "
                . "VALUES (?, ?, ?)";
    }

    $stmt = $mysqli->prepare($updateSql);

    if ($stmt) {
        if ($rb == 1) {
            $stmt->bind_param("iis", $idUsuario, $idDevice, utf8_decode($stadoEqp));
        } else {
            $stmt->bind_param("iis", $idUsuario, $idVehicle, utf8_decode($stadoEqp));
        }
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "{success:true, message: 'Datos actualizados correctamente.'}";
        } else {
            echo "{failure:true, message: 'Problemas al actualizar en la tabla.'}";
        }
        $stmt->close();
    } else {
        echo "{failure:true, message: 'Problemas en la construcción de la consulta.'}";
    }
    $mysqli->close();
}