<?php

include ('../../../dll/config.php');

if (!$mysqli = getConectionDb()) {
    echo "{success:false, message: 'No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {

    $requestBody = file_get_contents('php://input');
    $json = json_decode($requestBody, true);

    $existeSql = "SELECT usuario FROM usuarios WHERE usuario='" . $json["userUser"] . "'";

    $result = $mysqli->query($existeSql);

    if ($result) {
        if ($result->num_rows > 0) {
            echo "{success:false, message:'El Usuario ya se encuentra registrado.'}";
        } else {

            $insertSql = "INSERT INTO usuarios (id_empresa, id_rol_usuario, id_persona, usuario, clave)"
                    . "VALUES(?, ?, ?, ?, ?)";

            $stmt = $mysqli->prepare($insertSql);
            if ($stmt) {
                $dataPass = explode(",", utf8_decode($json["passwordUser"]));
                $stmt->bind_param("iiiss", $json["idCompanyUser"], $json["idRolUser"], $json["idPersonUser"], utf8_decode($json["userUser"]), getEncryption($dataPass[0]));
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    echo "{success:true, message:'Usuario registrado correctamente.'}";
                } else {
                    echo "{success:false, message: 'No se pudo registrar el Usuario.'}";
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