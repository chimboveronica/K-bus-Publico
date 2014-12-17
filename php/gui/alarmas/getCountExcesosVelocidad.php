<?php

include('../../login/isLogin.php');
include ('../../../dll/config.php');
extract($_GET);

if (!$mysqli = getConectionDb()) {
    echo "{success: false, message: 'No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {

    $consultaSql = "SELECT e.empresa, COUNT(v.id_empresa) as total "
            . "FROM kbushistoricodb.alarmas a, vehiculos v, empresas e "
            . "WHERE a.id_vehiculo = v.id_vehiculo "
            . "AND v.id_empresa = e.id_empresa "
            . "AND DATE(a.fecha_hora_inicio) = DATE(NOW()) "
            . "GROUP BY v.id_empresa";

    $result = $mysqli->query($consultaSql);
    $mysqli->close();
    if ($result->num_rows > 0) {
        $objJson = "{data: [";
        while ($myrow = $result->fetch_assoc()) {
            $objJson .= "{"
                    . "companyPastel: '" . utf8_encode($myrow["empresa"]) . "',"
                    . "totalPastel: " . $myrow["total"] . "},";
        }
        $objJson.= "]}";

        echo $objJson;
    } else {
        echo "{success: false, message: 'No hay datos que obtener'}";
    }
}