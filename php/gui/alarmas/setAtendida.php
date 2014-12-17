<?php

include('../../login/isLogin.php');
include ('../../../dll/config.php');

extract($_POST);

if (!$mysqli = getConectionDb()) {
    echo "{failure: true, message: 'No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {
    $updateSql = "UPDATE kbushistoricodb.alarmas "
            . "SET "
            . "id_usuario = ?, "
            . "observacion = ?, "
            . "estado = 1 "
            . "WHERE id_alarma = ? ";

    $stmt = $mysqli->prepare($updateSql);

    if ($stmt) {
        $idUsuario = $_SESSION["IDUSUARIOKBUS" . $site_city];

        $stmt->bind_param("isi", $idUsuario, utf8_decode($commentAlarm), $idAlarm);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            echo "{success: true, message: 'Información registrada correctamente.'}";
        } else {
            echo "{failure: true, message: 'Problemas al actualizar en la tabla.'}";
        }
        $stmt->close();
    } else {
        echo "{failure: true, message: 'Problemas en la construcción de la consulta.'}";
    }
    $mysqli->close();
}        