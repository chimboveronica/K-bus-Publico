<?php

include('../../login/isLogin.php');
include ('../../../dll/config.php');

extract($_POST);

if (!$mysqli = getConectionDb()) {
    echo "{success:true, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {

    $idUsuario = $_SESSION["IDUSUARIOKBUS" . $site_city];
    $existe = substr_count($listVeh, ',');
    if ($existe == 0) {
        $listVeh1 = $listVeh;
    } else {
        $listVeh1 = split(',', $listVeh);
    }
    if ($existe > 0) {
        $normal = false;
        for ($i = 0; $i < count($listVeh1); $i++) {
            $insertSql = "insert into kbushistoricodb.reten_vehiculos(id_usuario, id_vehiculo, fecha_hora_salida)"
                    . "values(?, ?, date_add(now(), INTERVAL $cbxTiempoSal DAY))";

            $stmt = $mysqli->prepare($insertSql);
            if ($stmt) {
                $stmt->bind_param("ii", $idUsuario, $listVeh1[$i]);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    $normal = true;
                } else {
                    $normal = false;
                }
            }
        }
        if ($normal) {
            echo "{success:true, message:'Datos Insertados Correctamente.'}";
        } else {
            echo "{failure:true message: 'Problemas al Insertar en la Tabla.'}";
        }
    } else {
        $insertSql = "insert into kbushistoricodb.reten_vehiculos(id_usuario, id_vehiculo, fecha_hora_salida)"
                . "values(?, ?, date_add(now(), INTERVAL $cbxTiempoSal DAY))";

        $stmt = $mysqli->prepare($insertSql);
        if ($stmt) {
            $stmt->bind_param("ii", $idUsuario, $listVeh1);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo "{success:true, message:'Datos Insertados Correctamente.'}";
            } else {
                echo "{failure:true message: 'Problemas al Insertar en la Tabla.'}";
            }
        }
    }
    $stmt->close();
    $mysqli->close();
}    