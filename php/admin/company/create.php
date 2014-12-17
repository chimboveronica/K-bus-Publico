<?php

include ('../../../dll/config.php');

if (!$mysqli = getConectionDb()) {
    echo "{success:true, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.',state: false}";
} else {

    $requestBody = file_get_contents('php://input');
    $json = json_decode($requestBody, true);

    $existeSql = "select empresa from empresas where acronimo='" . $json["acronymCompany"] . "' or empresa='" . $json["companyCompany"] . "'";

    $result = $mysqli->query($existeSql);
    if ($result) {
        if ($result->num_rows > 0) {
            echo "{success:true, message:'Los datos para esta empresa ya esta en uso.',state: false}";
        } else {

            $insertSql = "insert into empresas (acronimo, empresa, direccion, telefono, correo)"
                    . "values(?, ?, ?, ?, ?)";

            $stmt = $mysqli->prepare($insertSql);
            if ($stmt) {
                $stmt->bind_param("sssss", utf8_decode($json["acronymCompany"]), utf8_decode($json["companyCompany"]), utf8_decode($json["addressCompany"]), $json["cellCompany"], $json["emailCompany"]);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    echo "{success:true, message:'Empresa Creada Correctamente.',state: true}";
                } else {
                    echo "{success:true, message: 'Problemas al Ingresar los Datos.',state: false}";
                }
                $stmt->close();
            } else {
                echo "{success:true, message: 'Problemas en la construcción de la consulta.',state: false}";
            }
        }
        $mysqli->close();
    } else {
        echo "{success:true, message: 'Problemas en la construcción de la consulta.',state: false}";
    }
}