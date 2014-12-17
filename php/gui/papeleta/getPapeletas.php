<?php

include('../../login/isLogin.php');
include ('../../../dll/config.php');

extract($_GET);

if (!$mysqli = getConectionDb()) {
    echo "{success:false, message: 'Error: No se ha podido conectar a la Base de Datos.'}";
} else {
    $idCompany = $_SESSION["IDEMPRESAKBUS" . $site_city];
    $idRol = $_SESSION["IDROLKBUS" . $site_city];

    if ($idRol == 2) {
        
        $consultaSql = "select d.id_diario, v.id_vehiculo, v.reg_municipal,r.distancia, r.cod_ruta, d.hora_ini, d.par_ruta_atrav, "
                . "d.par_ruta, d.atraso, d.adelanto, r.ruta,d.ip,d.host,d.valido,d.observacion"
                . "from kbushistoricodb.despachos d, rutas r, vehiculos v, empresas e "
                . "where d.id_ruta = r.id_ruta "
                . "and d.id_vehiculo = v.id_vehiculo "
                . "and d.fecha = date(now()) "
                . "and v.id_empresa = e.id_empresa "
                . "and v.id_empresa = $idCompany "
                . "order by d.hora_ini desc";
    } else {
        $consultaSql = "select v.id_vehiculo, v.reg_municipal,r.distancia, r.cod_ruta, d.hora_ini, d.par_ruta_atrav, "
                . "d.par_ruta, d.atraso, d.adelanto, r.ruta,d.ip,d.host,d.valido,d.observacion "
                . "from kbushistoricodb.despachos d, kbusdb.rutas r, kbusdb.vehiculos v "
                . "where d.id_ruta = r.id_ruta "
                . "and d.id_vehiculo = v.id_vehiculo "
                . "and d.fecha = date(now()) "
                . "order by d.hora_ini desc";
    }

    $result = $mysqli->query($consultaSql);
    $mysqli->close();

    if ($result->num_rows > 0) {
        $objJson = "{data: [";
        while ($myrow = $result->fetch_assoc()) {
            $objJson .= "{"

                    . "idVehiclePa: " . $myrow["id_vehiculo"] . ","
                    . "muniRegPa: '" . $myrow["reg_municipal"] . "',"
                    . "codeRoutePa: '" . $myrow["cod_ruta"] . "',"
                    . "routePa: '" . utf8_encode($myrow["ruta"]) . "',"
                    . "timeStartPa: '" . $myrow["hora_ini"] . "',"
                    . "crossedStopsPa: '" . $myrow["par_ruta_atrav"] . '/' . $myrow["par_ruta"] . "',"
                    . "delayPa: '" . $myrow["atraso"] . "',"
                    . "ipPa: '" . $myrow["ip"] . "',"
                    . "distanciaPa: " . $myrow["distancia"] . ","
                    . "hostPa: '" . $myrow["host"] . "',"
                    . "validoPa: " . $myrow["valido"] . ","
                    . "observacionPa: '" . $myrow["observacion"] . "',"
                    . "advancePa: '" . $myrow["adelanto"] . "'},";
        }

        $objJson.= "]}";

        echo $objJson;
    } else {
        echo "{failure:true, message:'No hay datos que obtener'}";
    }
}
