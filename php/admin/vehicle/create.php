<?php

include('../../login/isLogin.php');
include ('../../../dll/config.php');

if (!$mysqli = getConectionDb()) {
    echo "{success:true, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.',state: false}";
} else {
    $requestBody = file_get_contents('php://input');
    $json = json_decode($requestBody, true);

    $existeSql = "select id_vehiculo from vehiculos where id_equipo=" . $json["idDeviceVehicles"] . "";

    $result = $mysqli->query($existeSql);

    if ($result) {
        if ($result->num_rows > 0) {
            echo "{success:true, message:'El Equipo ya esta siendo usado por otro Vehiculo',state: false}";
        } else {

            $insertSql = "insert into vehiculos (id_empresa, id_equipo, id_servicio_ruta, id_persona, placa, reg_municipal, marca, modelo, year, num_motor, num_chasis, num_disco, fecha_matricula, soat, imagen,personas_sentadas,personas_paradas) "
                    . "values(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?)";

            $stmt = $mysqli->prepare($insertSql);
            if ($stmt) {
                $stmt->bind_param("iiiissssississsii", $json["idCompanyVehicle"], $json["idDeviceVehicles"], $json["idRouServVehicle"], $json["idPersonVehicle"], utf8_decode($json["plateVehicle"]), utf8_decode($json["muniRegVehicle"]), utf8_decode($json["makeVehicle"]), utf8_decode($json["modelVehicle"]), $json["yearVehicle"], utf8_decode($json["numMotorVehicle"]), utf8_decode($json["numChasisVehicle"]), utf8_decode($json["numDiscoVehicle"]), utf8_decode($json["enrollDateVehicle"]), utf8_decode($json["soatVehicle"]), utf8_decode($json["imageVehicle"]), $json["sentadasVehicle"], $json["paradasVehicle"]);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    echo "{success:true, message:'Datos Insertados Correctamente.',state: true}";
                } else {
                    echo "{success:true, message: 'Problemas al Insertar en la Tabla.',state: false}";
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