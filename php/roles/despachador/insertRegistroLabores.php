<?php

include('../../login/isLogin.php');
include ('../../../dll/config.php');

extract($_POST);

if (!$mysqli = getConectionDb()) {
    echo "{success:true, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {

    $requestBody = file_get_contents('php://input');
    $json = json_decode($requestBody, true);

    $insertSql = "insert into kbushistoricodb.registro_labores (id_usuario, estado) "
            . "values(?, ?)";

    $stmt = $mysqli->prepare($insertSql);
    if ($stmt) {
        $stmt->bind_param("ii", $_SESSION["IDUSUARIOKBUS" . $site_city], $state);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            if ($state) {
                echo "{success:true, message:'Inicio de Jornada registrado Correctamente.'}";
            } else {
                echo "{success:true, message:'Fin de Jornada registrado Correctamente.'}";
            }
        } else {
            echo "{success:true, message: 'Problemas al Insertar en la Tabla.'}";
        }
        $stmt->close();
    } else {
        echo "{success:true, message: 'Problemas en la construcción de la consulta.'}";
    }
    $mysqli->close();
}