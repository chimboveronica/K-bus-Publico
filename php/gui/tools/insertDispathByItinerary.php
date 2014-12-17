<?php

include ('../../login/isLogin.php');
include ('../../../dll/config.php');

extract($_POST);

if (!$mysqli = getConectionDb()) {
    echo "{failure:true, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {
    $success = false;
    $consultaSql = "select id_ruta, tiempo from itinerarios_p where  id_turno = $id_turno";

    $result = $mysqli->query($consultaSql);

    if ($result->num_rows > 0) {
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
        
        while ($myrow = $result->fetch_assoc()) {
            $insertSql = "insert into kbushistoricodb.despachos (id_usuario, id_ruta, id_vehiculo, fecha, hora_ini, ip, host) "
                    . "values(?, ?, ?, date(now()), ?, ?, ?)";

            $stmt = $mysqli->prepare($insertSql);

            if ($stmt) {
                $stmt->bind_param("iiisss", $_SESSION["IDUSUARIOKBUS" . $site_city], $myrow["id_ruta"], $idVehicle, $myrow["tiempo"], $ip, $host);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    $success = true;
                } else {
                    $success = false;
                }
                $stmt->close();
            } else {
                $success = false;
            }
        }

        if ($success) {
            echo "{success:true, message:'Datos Insertados Correctamente.'}";
        } else {
            echo "{failure:true, message: 'El despacho ya se encuentra registrado.'}";
        }
    } else {
        echo "{failure:true, message:'El Itinerario no se encuentra registrado en el Sistema.'}";
    }
    $mysqli->close();
}