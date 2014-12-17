<?php

include ('../../../dll/config.php');

extract($_POST);

if (!$mysqli = getConectionDb()) {
    echo "{failure:true, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {
    if ($state == 0) {
        $updateSql = "update kbushistoricodb.despachos "
                . "set observacion = '" . utf8_decode($comment) . "' "
                . "where id_vehiculo = ? "
                . "and fecha = date(now()) "
                . "and hora_ini = ? "
                . "and valido = ?";
    } else if ($state == 1) {
        $updateSql = "update kbushistoricodb.despachos "
                . "set id_sancion = $idPenalty, estado_sancion = 1 "
                . "where id_vehiculo = ? "
                . "and fecha = date(now()) "
                . "and hora_ini = ? "
                . "and valido = ?";
    } else {
        $updateSql = "update kbushistoricodb.despachos "
                . "set valido = 0 "
                . "where id_vehiculo = ? "
                . "and fecha = date(now()) "
                . "and hora_ini = ? "
                . "and valido = ?";
    }

    $stmt = $mysqli->prepare($updateSql);
    if ($stmt) {
        $stmt->bind_param("isi", $idVehicle, $timeStart, $valid);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "{success:true, message:'Datos Actualizados Correctamente.'}";
        } else {
            echo "{failure:true, message: 'Problemas al Actualizar en la Tabla.'}";
        }
        $stmt->close();
    } else {
        echo "{failure:true, message: 'Problemas en la Construcción de la Consulta.'}";
    }
    $mysqli->close();
}