<?php

include ('../../../dll/config.php');

extract($_POST);

if (!$mysqli = getConectionDb()) {
    echo "{failure: true, message: 'No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {

    $existeSql = "SELECT cedula FROM personas WHERE cedula = '$documentPerson'";

    $result = $mysqli->query($existeSql);

    if ($result) {
        if ($result->num_rows > 0) {
            echo "{failure: true, message: 'Ya existe una persona con ese Nº de Cedula'}";
        } else {
            if (!isset($idTypeLicensePerson)) {
                $idTypeLicensePerson = 1;
            }

            $insertSql = "INSERT INTO personas (id_empresa, cedula, nombres, apellidos, genero, fecha_nacimiento, id_tipo_licencia) "
                    . "VALUES(1, ?, ?, ?, ?, ?, ?)";

            $stmt = $mysqli->prepare($insertSql);
            if ($stmt) {
                $stmt->bind_param("sssiss", $documentPerson, utf8_decode($namePerson), utf8_decode($surnamePerson), $genderPerson, $dateOfBirthPerson, $idTypeLicensePerson);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    echo "{success: true, message: 'Registro realizado corectamente.'}";
                } else {
                    echo "{failure: true, message: 'Problemas al Insertar en la Tabla.'}";
                }
                $stmt->close();
            } else {
                echo "{failure: true, message: 'Problemas en la construcción de la consulta.'}";
            }
        }
        $mysqli->close();
    } else {
        echo "{failure: true, message: 'Problemas en la construcción de la consulta.'}";
    }
}