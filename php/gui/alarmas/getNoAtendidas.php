<?php

include('../../login/isLogin.php');
include ('../../../dll/config.php');

extract($_GET);

if (!$mysqli = getConectionDb()) {
    echo "{success: false, message: 'No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {
    $idCompany = $_SESSION["IDEMPRESAKBUS" . $site_city];
    $idRol = $_SESSION["IDROLKBUS" . $site_city];

    $consultaSql = "SELECT a.id_alarma, v.id_empresa, a.id_vehiculo, v.reg_municipal, a.alarma, a.fecha_hora_inicio, a.longitud, a.latitud "
            . "FROM kbushistoricodb.alarmas a, kbusdb.vehiculos v "
            . "WHERE a.id_vehiculo = v.id_vehiculo "
            . "AND a.estado = 0 "
            . "ORDER BY a.fecha_hora_fin DESC";

    $result = $mysqli->query($consultaSql);
    $mysqli->close();

    if ($result->num_rows > 0) {
        $objJson = "{data: [";
        while ($myrow = $result->fetch_assoc()) {
            $objJson .= "{"
                    . "idAlarm: " . $myrow["id_alarma"] . ","
                    . "idCompanyAlarm: " . $myrow["id_empresa"] . ","
                    . "idVehicleAlarm: " . $myrow["id_vehiculo"] . ","
                    . "muniRegAlarm: '" . $myrow["reg_municipal"] . "',"
                    . "alarmAlarm: '" . utf8_encode($myrow["alarma"]) . "',"
                    . "latitudAlarm: " . $myrow["latitud"] . ","
                    . "longitudAlarm: " . $myrow["longitud"] . ","
                    . "dateTimeStartAlarm: '" . $myrow["fecha_hora_inicio"] . "'},";
        }

        $objJson.= "]}";

        echo $objJson;
    } else {
        echo "{failure: true, message: 'No hay datos que obtener'}";
    }
}