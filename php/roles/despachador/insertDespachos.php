<?php

include('../../login/isLogin.php');
include ('../../../dll/config.php');

extract($_POST);

if (!$mysqli = getConectionDb()) {
    echo "{failure:true, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {
    $haveDisparchSql = "select id_vehiculo "
            . "from kbushistoricodb.despachos "
            . "where id_vehiculo = $idVehicle "
            . "and fecha = '$fecha' "
            . "and hora_ini = '$timeStart' "
            . "and valido = 1";
    $result = $mysqli->query($haveDisparchSql);

    if ($result->num_rows > 0) {
        echo "{failure:true, "
        . "message: 'El vehiculo seleccionado, ya se encuentra despachado.<br>"
                . "- Verifique si consta en la lista de <b>Despachos Realizados</b>.<br>"
                . "- Si se encuentra despachado en otra Ruta, informar al despachador <br>"
                . "respectivo que lo establesca como <b>Invalido</b>, y vuelva a realizar el Despacho.'}";
    } else {
        // Deteccion de la ip y del proxy
        if (isset($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"])) {
            $ip = $HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"];
            $array = split(",", $ip);
            $host = @gethostbyaddr($ip_proxy);
            $ip_proxy = $HTTP_SERVER_VARS["REMOTE_ADDR"];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
            $host = @gethostbyaddr($ip);
        }

        $insertSql = "insert into kbushistoricodb.despachos (id_usuario, id_conductor, id_ayudante, id_ruta, id_vehiculo, fecha, hora_ini, ip, host) "
                . "values (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $mysqli->prepare($insertSql);
        if ($stmt) {
            $stmt->bind_param("iiiiissss", $_SESSION["IDUSUARIOKBUS" . $site_city], $idConductor, $idAyudante, $idRoute, $idVehicle, $fecha, $timeStart, $ip, $host);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo "{success:true, message:'Datos Insertados Correctamente.'}";
            } else {
                echo "{failure:true, message: 'Problemas al Insertar en la Tabla.'}";
            }
            $stmt->close();
        } else {
            echo "{failure:true, message: 'Problemas en la Construcción de la Consulta.'}";
        }
    }
    $mysqli->close();
}
