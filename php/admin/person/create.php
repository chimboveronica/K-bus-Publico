<?php

include('../../login/isLogin.php');
include ('../../../dll/config.php');

if (!$mysqli = getConectionDb()) {
    echo "{success:false, message: 'No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {
    $idCompany = $_SESSION["IDEMPRESAKBUS" . $site_city];

    $requestBody = file_get_contents('php://input');
    $json = json_decode($requestBody, true);

    $existeSql = "SELECT cedula FROM personas WHERE cedula='" . $json["documentPerson"] . "'";

    $result = $mysqli->query($existeSql);

    if ($result) {
        if ($result->num_rows > 0) {
            echo "{success:false, message: 'Ya existe una persona con ese Nº de Cedula'}";
        } else {

            $insertSql = "INSERT INTO personas (id_empresa, cedula, nombres, apellidos, genero, fecha_nacimiento, id_tipo_licencia, conyugue, direccion, correo, celular, imagen) "
                    . "VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $mysqli->prepare($insertSql);
            if ($stmt) {
                $stmt->bind_param("isssisisssss", $idCompany, $json["documentPerson"], utf8_decode($json["namePerson"]), utf8_decode($json["surnamePerson"]), $json["genderPerson"], $json["dateOfBirthPerson"], $json["idTypeLicensePerson"], utf8_decode($json["spousePerson"]), utf8_decode($json["addressPerson"]), $json["emailPerson"], $json["cellPerson"], utf8_decode($json["imagePerson"]));
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    echo "{success:true, message: 'Persona registrada correctamente.'}";
                } else {
                    echo "{success:false, message: 'No se pudo registrar la Persona.'}";
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