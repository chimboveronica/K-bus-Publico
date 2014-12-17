<?php

include('../../../login/isLogin.php');
include ('../../../../dll/config.php');

extract($_POST);

if (!$mysqli = getConectionDb()) {
    echo "{failure:false, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {
    $insertSql = "insert into kbushistoricodb.mensajes (id_usuario, id_equipo, mensaje) "
            . "values(?, ?, ?)";

    $stmt = $mysqli->prepare($insertSql);
    if ($stmt) {
        $stmt->bind_param("iis", $_SESSION["IDUSUARIOKBUS".$site_city], $idDevice, preg_replace("[\n|\r|\n\r]", "", utf8_decode($cmdManual)));
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "{success:true, message:'Datos Insertados Correctamente.'}";
        } else {
            echo "{failure:false, message: 'Problemas al Insertar en la Tabla.'}";
        }
        $stmt->close();
    } else {
        echo "{failure:false, message: 'Problemas en la Construcción de la Consulta.'}";
    }
    $mysqli->close();
}