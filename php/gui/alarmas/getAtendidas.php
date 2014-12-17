<?php

include('../../login/isLogin.php');
include ('../../../dll/config.php');

extract($_GET);

if (!$mysqli = getConectionDb()) {
    echo "{success: false, message: 'No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {
    $idCompany = $_SESSION["IDEMPRESAKBUS" . $site_city];
    $idRol = $_SESSION["IDROLKBUS" . $site_city];

    $consultaSql = "SELECT v.id_empresa, a.id_vehiculo, v.reg_municipal, a.alarma, a.fecha_hora_inicio, "
            . "a.fecha_hora_fin, a.id_usuario, CONCAT(p.apellidos, ' ', p.nombres) as persona, "
            . "a.observacion, a.longitud, a.latitud "
            . "FROM kbushistoricodb.alarmas a, vehiculos v, usuarios u, personas p "
            . "WHERE a.id_vehiculo = v.id_vehiculo "
            . "AND a.id_usuario = u.id_usuario "
            . "AND u.id_persona = p.id_persona "
            . "AND a.estado = 1 "
            . "ORDER BY a.fecha_hora_fin DESC";

    $result = $mysqli->query($consultaSql);
    $mysqli->close();
    if ($result->num_rows > 0) {
        $objJson = "{data: [";
        while ($myrow = $result->fetch_assoc()) {
            $objJson .= "{"
                    . "idCompanyAlarmAttend: " . $myrow["id_empresa"] . ","
                    . "idVehicleAlarmAttend: " . $myrow["id_vehiculo"] . ","
                    . "muniRegAlarmAttend: '" . $myrow["reg_municipal"] . "',"
                    . "alarmAlarmAttend: '" . utf8_encode($myrow["alarma"]) . "',"
                    . "dateTimeStartAlarmAttend: '" . $myrow["fecha_hora_inicio"] . "',"
                    . "dateTimeFinishAlarmAttend: '" . $myrow["fecha_hora_fin"] . "',"
                    . "personAlarmAttend: '" . utf8_encode($myrow["persona"]) . "',"
                    . "commentAlarmAttend: '" . preg_replace("[\n|\r|\n\r]", "\\n", utf8_encode($myrow["observacion"])) . "',"
                    . "latitudAlarmAttend: " . $myrow["latitud"] . ","
                    . "longitudAlarmAttend: " . $myrow["longitud"] . "},";
        }
        $objJson.= "]}";
        echo $objJson;
    } else {
        echo "{success: false, message: 'No hay datos que obtener'}";
    }
}