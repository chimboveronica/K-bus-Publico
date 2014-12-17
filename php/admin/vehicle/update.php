<?php

include ('../../../dll/config.php');

if (!$mysqli = getConectionDb()) {
    echo "{success:true, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.',state: false}";
} else {
    $requestBody = file_get_contents('php://input');
    $json = json_decode($requestBody, true);

    $setIdCompany = $setIdDevice = $setIdRouteService = $setIdPerson = $setPlate = $setMuniReg = "";
    $setMake = $setModel = $setNumberMotor = $setNumberChasis = $setNumberDisco = $setYear = "";
    $setEnrollDate = $setSoat = $setImage = $sentadas = $paradas = "";

    if (isset($json["idCompanyVehicle"])) {
        $setIdCompany = "id_empresa=" . $json["idCompanyVehicle"] . ",";
    }
    if (isset($json["idDeviceVehicles"])) {
        $setIdDevice = "id_equipo=" . $json["idDeviceVehicles"] . ",";
    }
    if (isset($json["idRouServVehicle"])) {
        $setIdRouteService = "id_servicio_ruta=" . $json["idRouServVehicle"] . ",";
    }
    if (isset($json["idPersonVehicle"])) {
        $setIdPerson = "id_persona=" . $json["idPersonVehicle"] . ",";
    }
    if (isset($json["plateVehicle"])) {
        $setPlate = "placa='" . utf8_decode($json["plateVehicle"]) . "',";
    }
    if (isset($json["muniRegVehicle"])) {
        $setMuniReg = "reg_municipal='" . utf8_decode($json["muniRegVehicle"]) . "',";
    }
    if (isset($json["makeVehicle"])) {
        $setMake = "marca='" . utf8_decode($json["makeVehicle"]) . "',";
    }
    if (isset($json["modelVehicle"])) {
        $setModel = "modelo='" . utf8_decode($json["modelVehicle"]) . "',";
    }
    if (isset($json["numMotorVehicle"])) {
        $setNumberMotor = "num_motor='" . utf8_decode($json["numMotorVehicle"]) . "',";
    }
    if (isset($json["numChasisVehicle"])) {
        $setNumberChasis = "num_chasis='" . utf8_decode($json["numChasisVehicle"]) . "',";
    }
    if (isset($json["numDiscoVehicle"])) {
        $setNumberDisco = "num_disco=" . $json["numDiscoVehicle"] . ",";
    }
    if (isset($json["yearVehicle"])) {
        $setYear = "year=" . $json["yearVehicle"] . ",";
    }
    if (isset($json["enrollDateVehicle"])) {
        $setEnrollDate = "fecha_matricula='" . $json["enrollDateVehicle"] . "',";
    }
    if (isset($json["soatVehicle"])) {
        $setSoat = "soat='" . utf8_decode($json["soatVehicle"]) . "',";
    }
    if (isset($json["sentadasVehicle"])) {
        $sentadas = "personas_sentadas=" . $json["sentadasVehicle"] . ",";
    }
    if (isset($json["paradasVehicle"])) {
        $paradas = "personas_paradas=" . $json["paradasVehicle"] . ",";
    }
    if (isset($json["imageVehicle"])) {
        $setImage = "imagen='" . utf8_decode($json["imageVehicle"]) . "',";
    }

    $setId = "id_vehiculo = " . $json["id"];

    if ($setIdDevice != "") {
        $existeSql = "select id_vehiculo from vehiculos where id_equipo=" . $json["idDeviceVehicles"];

        $result = $mysqli->query($existeSql);

        if ($result->num_rows > 0) {
            echo "{success:true, message:'El Equipo ya esta siendo usado por otro Vehiculo',state: false}";
        } else {
            $updateSql = "update vehiculos "
                    . "set $setIdCompany$setIdDevice$setIdRouteService$setIdPerson$setPlate$setMuniReg$setMake$setModel$setNumberMotor$setNumberChasis$setNumberDisco$setYear$setEnrollDate$setSoat$paradas$sentadas$setImage$setId "
                    . "where id_vehiculo = ?";

            $stmt = $mysqli->prepare($updateSql);
            if ($stmt) {
                $stmt->bind_param("i", $json["id"]);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    echo "{success:true, message:'Datos actualizados correctamente.',state: true}";
                } else {
                    echo "{success:true, message: 'Problemas al actualizar en la tabla.',state: false}";
                }
                $stmt->close();
            } else {
                echo "{success:true, message: 'Problemas en la construcción de la consulta.',state: false}";
            }
        }
    } else {
        $updateSql = "update vehiculos "
                . "set $setIdCompany$setIdDevice$setIdRouteService$setIdPerson$setPlate$setMuniReg$setMake$setModel$setNumberMotor$setNumberChasis$setNumberDisco$setYear$setEnrollDate$setSoat$paradas$sentadas$setImage$setId "
                . "where id_vehiculo = ?";

        $stmt = $mysqli->prepare($updateSql);
        if ($stmt) {
            $stmt->bind_param("i", $json["id"]);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo "{success:true, message:'Datos actualizados correctamente.',state: true}";
            } else {
                echo "{success:true, message: 'Problemas al actualizar en la tabla.',state: false}";
            }
            $stmt->close();
        } else {
            echo "{success:true, message: 'Problemas en la construcción de la consulta.',state: false}";
        }
    }

    $mysqli->close();
}