<?php

include('isLogin.php');
require_once('../../dll/config.php');

if (!$mysqli = getConectionDb()) {
    echo "{success:true, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {
    $requestBody = file_get_contents('php://input');
    $json = json_decode($requestBody, true);
    
    $idUser = $_SESSION["IDUSUARIOKBUS" . $site_city];

    $consultaSql = "SELECT COUNT(id_usuario) AS c "
            . "FROM usuarios "
            . "WHERE id_usuario = $idUser " 
            . "AND conectado = 1";
    $result = $mysqli->query($consultaSql);
    $myrow = $result->fetch_assoc();

    if ($myrow["c"] == 1) {
        $updateSql = "UPDATE usuarios "
                . "SET conectado = 0 "
                . "WHERE id_usuario = ?";

        $stmt = $mysqli->prepare($updateSql);
        if ($stmt) {
            $stmt->bind_param("i", $idUser);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                session_destroy();
                header('Location: ../../index.php');
            } else {
                header('Location: ../../index.php');
            }
            $stmt->close();
        } else {
            echo "{success:true, message: 'Problemas en la construcción de la consulta.'}";
        }
    } else {
        session_destroy();
        header('Location: ../../index.php');
    }

    $mysqli->close();
}