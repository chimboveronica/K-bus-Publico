<?php

include ('../../../dll/config.php');

if (!$mysqli = getConectionDb()) {
    echo "{success:false, message: 'No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {
    $requestBody = file_get_contents('php://input');
    $json = json_decode($requestBody, true);

    $setIdCompany = $setIdRolUser = $setIdPerson = $setUser = $setPass = $setActive = "";

    if (isset($json["idCompanyUser"])) {
        $setIdCompany = "id_empresa=" . $json["idCompanyUser"] . ",";
    }
    if (isset($json["idRolUser"])) {
        $setIdRolUser = "id_rol_usuario=" . $json["idRolUser"] . ",";
    }
    if (isset($json["idPersonUser"])) {
        $setIdPerson = "id_persona=" . $json["idPersonUser"] . ",";
    }
    if (isset($json["userUser"])) {
        $setUser = "usuario='" . utf8_decode($json["userUser"]) . "',";
    }
    if (isset($json["passwordUser"])) {
        $dataPass = explode(",", utf8_decode($json["passwordUser"]));
        $passEncryption = getEncryption($dataPass[0]);

        $consultaPassSql = "SELECT id_usuario FROM usuarios WHERE clave = '" . $passEncryption . "' AND id_usuario = " . $json["id"];
        $resultPass = $mysqli->query($consultaPassSql);
        if ($resultPass->num_rows > 0) {
            $setPass = "";
        } else {
            $setPass = "clave='" . $passEncryption . "',";
        }
    }
    if (isset($json["activeUser"])) {
        $setActive = "activo=" . $json["activeUser"] . ",";
    }

    $setId = "id_usuario = " . $json["id"];

    if ($setUser != "") {
        $existeSql = "SELECT usuario FROM usuarios WHERE usuario='" . $json["userUser"] . "'";

        $result = $mysqli->query($existeSql);

        if ($result->num_rows > 0) {
            echo "{success:false, message:'El Usuario ya se encuentra en uso por otra persona.'}";
        } else {
            $updateSql = "UPDATE usuarios "
                    . "SET $setIdCompany$setIdRolUser$setIdPerson$setUser$setPass$setActive$setId "
                    . "WHERE id_usuario = ?";

            $stmt = $mysqli->prepare($updateSql);
            if ($stmt) {
                $stmt->bind_param("i", $json["id"]);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    echo "{success:true, message: 'Datos del Usuario, actualizados correctamente.'}";
                } else {
                    echo "{success:false, message: 'No se pudo actualizar los Datos del Usuario.'}";
                }
                $stmt->close();
            } else {
                echo "{success:false, message: 'Problemas en la construcción de la consulta.'}";
            }
        }
    } else {
        if ($setPass != "") {
            $updateSql = "UPDATE usuarios "
                    . "SET $setIdCompany$setIdRolUser$setIdPerson$setUser$setPass$setActive$setId "
                    . "WHERE id_usuario = ?";

            $stmt = $mysqli->prepare($updateSql);
            if ($stmt) {
                $stmt->bind_param("i", $json["id"]);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    echo "{success:true, message: 'Datos del Usuario, actualizados correctamente.'}";
                } else {
                    echo "{success:false, message: 'No se pudo actualizar los Datos del Usuario.'}";
                }
                $stmt->close();
            } else {
                echo "{success:false, message: 'Problemas en la construcción de la consulta.'}";
            }
        } else {
            if ($setActive != "") {
                $updateSql = "UPDATE usuarios "
                        . "SET $setIdCompany$setIdRolUser$setIdPerson$setUser$setPass$setActive$setId "
                        . "WHERE id_usuario = ?";

                $stmt = $mysqli->prepare($updateSql);
                if ($stmt) {
                    $stmt->bind_param("i", $json["id"]);
                    $stmt->execute();

                    if ($stmt->affected_rows > 0) {
                        echo "{success:true, message: 'Datos del Usuario, actualizados correctamente.'}";
                    } else {
                        echo "{success:false, message: 'No se pudo actualizar los Datos del Usuario.'}";
                    }
                    $stmt->close();
                } else {
                    echo "{success:false, message: 'Problemas en la construcción de la consulta.'}";
                }
            } else {
                echo "{success:false, message: 'El Usuario ya tiene esa contraseña, por favor ingresar una nueva.'}";
            }
        }
    }
    $mysqli->close();
}