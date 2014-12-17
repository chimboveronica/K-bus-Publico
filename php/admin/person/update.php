<?php

include ('../../../dll/config.php');

if (!$mysqli = getConectionDb()) {
    echo "{success: false, message: 'No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {
    $requestBody = file_get_contents('php://input');
    $json = json_decode($requestBody, true);
    $setDocument = $setName = $setSurname = $setIdGenre = $setDateOfBirth = $setTypeLicense = $setSpouse = $setAddress = $setEmail = $setCell = $setImage = "";

    if (isset($json["documentPerson"])) {
        $setDocument = "cedula='" . $json["documentPerson"] . "',";
    }
    if (isset($json["namePerson"])) {
        $setName = "nombres='" . utf8_decode($json["namePerson"]) . "',";
    }
    if (isset($json["surnamePerson"])) {
        $setSurname = "apellidos='" . utf8_decode($json["surnamePerson"]) . "',";
    }
    if (isset($json["genderPerson"])) {
        $setIdGenre = "genero=" . $json["genderPerson"] . ",";
    }
    if (isset($json["dataOfBirthPerson"])) {
        $setDateOfBirth = "fecha_nacimiento='" . $json["dataOfBirthPerson"] . "',";
    }
    if (isset($json["idTypeLicensePerson"])) {
        $setTypeLicense = "id_tipo_licencia=" . $json["idTypeLicensePerson"] . ",";
    }
    if (isset($json["spousePerson"])) {
        $setSpouse = "conyugue='" . utf8_decode($json["spousePerson"]) . "',";
    }
    if (isset($json["addressPerson"])) {
        $setAddress = "direccion='" . utf8_decode($json["addressPerson"]) . "',";
    }
    if (isset($json["emailPerson"])) {
        $setEmail = "correo='" . $json["emailPerson"] . "',";
    }
    if (isset($json["cellPerson"])) {
        $setCell = "celular='" . $json["cellPerson"] . "',";
    }
    if (isset($json["imagePerson"])) {
        $setImage = "imagen='" . utf8_decode($json["imagePerson"]) . "',";
    }

    $setId = "id_persona = " . $json["id"];

    if ($setDocument != "") {
        $existeSql = "SELECT cedula FROM personas WHERE cedula='" . $json["documentPerson"] . "'";

        $result = $mysqli->query($existeSql);

        if ($result->num_rows > 0) {
            echo "{success: false, message: 'Ya existe una persona con ese Nº de Cedula'}";
        } else {
            $updateSql = "UPDATE personas "
                    . "SET $setDocument$setName$setSurname$setIdGenre$setDateOfBirth$setTypeLicense$setSpouse$setAddress$setEmail$setCell$setImage$setId "
                    . "WHERE id_persona = ?";

            $stmt = $mysqli->prepare($updateSql);
            if ($stmt) {
                $stmt->bind_param("i", $json["id"]);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    echo "{success: true, message: 'Datos de la Persona, actualizados correctamente.'}";
                } else {
                    echo "{success: false, message: 'No se pudo actualizar los Datos de la Persona.'}";
                }
                $stmt->close();
            } else {
                echo "{success: false, message: 'Problemas en la construcción de la consulta.'}";
            }
        }
    } else {
        $updateSql = "UPDATE personas "
                . "SET $setDocument$setName$setSurname$setIdGenre$setDateOfBirth$setTypeLicense$setSpouse$setAddress$setEmail$setCell$setImage$setId "
                . "WHERE id_persona = ?";

        $stmt = $mysqli->prepare($updateSql);
        if ($stmt) {
            $stmt->bind_param("i", $json["id"]);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo "{success: true, message: 'Datos de la Persona, actualizados correctamente.'}";
            } else {
                echo "{success: false, message: 'No se pudo actualizar los Datos de la Persona.'}";
            }
            $stmt->close();
        } else {
            echo "{success: false, message: Problemas en la construcción de la consulta.'}";
        }
    }

    $mysqli->close();
}