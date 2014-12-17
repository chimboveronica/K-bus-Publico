<?php

include('../../login/isLogin.php');
include ('../../../dll/config.php');

extract($_POST);

if (!$mysqli = getConectionDb()) {
    echo "{failure:true, message: 'Error: No se ha podido conectar a la Base de Datos.'}";
} else {
    $idPersona = $_SESSION["IDPERSONAKBUS" . $site_city];
    $idRol = $_SESSION["IDROLKBUS" . $site_city];

    if ($idRol == 4) {
        $queryLastSkpSql = "select v.id_vehiculo, v.id_empresa, v.placa, v.reg_municipal, eq.equipo, uds.latitud, uds.longitud, "
                . "uds.fecha_hora_ult_dato, uds.velocidad, uds.direccion, e.empresa, r.icono "
                . "from vehiculos v, equipos eq, ultimo_dato_skps uds, empresas e, rutas r "
                . "where v.id_equipo = eq.id_equipo "
                . "and eq.id_equipo = uds.id_equipo "
                . "and v.id_empresa = e.id_empresa "
                . "and uds.id_ruta = r.id_ruta "
                . "and v.id_persona = $idPersona";

        $queryLastFastrackSql = "select v.id_vehiculo, v.id_empresa, v.placa, v.reg_municipal, eq.equipo, uds.latitud, uds.longitud, "
                . "uds.fecha_hora_ult_dato, uds.velocidad, uds.direccion, e.empresa, r.icono "
                . "from vehiculos v, equipos eq, ultimo_dato_fastracks uds, empresas e, rutas r "
                . "where v.id_equipo = eq.id_equipo "
                . "and eq.id_equipo = uds.id_equipo "
                . "and v.id_empresa = e.id_empresa "
                . "and uds.id_ruta = r.id_ruta "
                . "and v.id_persona = $idPersona";
    } else {
        $queryLastSkpSql = "select v.id_vehiculo, v.id_empresa, v.placa, v.reg_municipal, eq.equipo, uds.latitud, uds.longitud, "
                . "uds.fecha_hora_ult_dato, uds.velocidad, uds.direccion, e.empresa, r.icono "
                . "from vehiculos v, equipos eq, ultimo_dato_skps uds, empresas e, rutas r "
                . "where v.id_equipo = eq.id_equipo "
                . "and eq.id_equipo = uds.id_equipo "
                . "and v.id_empresa = e.id_empresa "
                . "and uds.id_ruta = r.id_ruta "
                . "and v.id_empresa in ($listCoop)";

        $queryLastFastrackSql = "select v.id_vehiculo, v.id_empresa, v.placa, v.reg_municipal, eq.equipo, uds.latitud, uds.longitud, "
                . "uds.fecha_hora_ult_dato, uds.velocidad, uds.direccion, e.empresa, r.icono "
                . "from vehiculos v, equipos eq, ultimo_dato_fastracks uds, empresas e, rutas r "
                . "where v.id_equipo = eq.id_equipo "
                . "and eq.id_equipo = uds.id_equipo "
                . "and v.id_empresa = e.id_empresa "
                . "and uds.id_ruta = r.id_ruta "
                . "and v.id_empresa in ($listCoop)";
    }

    $resultLastSkp = $mysqli->query($queryLastSkpSql);
    $resultLastFastrack = $mysqli->query($queryLastFastrackSql);
    $mysqli->close();
    
    if ($resultLastSkp->num_rows > 0 || $resultLastFastrack->num_rows > 0) {
        $objJson = "data: [";
        if ($resultLastSkp->num_rows > 0) {
            while ($myrow = $resultLastSkp->fetch_assoc()) {
                $objJson .= "{"
                        . "idVehicleLast: " . $myrow["id_vehiculo"] . ","
                        . "idCompanyLast: " . $myrow["id_empresa"] . ","
                        . "companyLast: '" . utf8_encode($myrow["empresa"]) . "',"
                        . "deviceLast: '" . $myrow["equipo"] . "',"
                        . "muniRegLast: '" . $myrow["reg_municipal"] . "',"
                        . "latitudLast: " . $myrow["latitud"] . ","
                        . "longitudLast: " . $myrow["longitud"] . ","
                        . "dateTimeLast: '" . $myrow["fecha_hora_ult_dato"] . "',"
                        . "speedLast: " . $myrow["velocidad"] . ","
                        . "addressLast: '" . utf8_encode($myrow["direccion"]) . "',"
                        . "iconLast: '" . $myrow["icono"] . "'},";
            }
        }

        if ($resultLastFastrack->num_rows > 0) {
            while ($myrow = $resultLastFastrack->fetch_assoc()) {
                $objJson .= "{"
                        . "idVehicleLast: " . $myrow["id_vehiculo"] . ","
                        . "idCompanyLast: " . $myrow["id_empresa"] . ","
                        . "companyLast: '" . utf8_encode($myrow["empresa"]) . "',"
                        . "deviceLast: '" . $myrow["equipo"] . "',"
                        . "muniRegLast: '" . $myrow["reg_municipal"] . "',"
                        . "latitudLast: " . $myrow["latitud"] . ","
                        . "longitudLast: " . $myrow["longitud"] . ","
                        . "dateTimeLast: '" . $myrow["fecha_hora_ult_dato"] . "',"
                        . "speedLast: " . $myrow["velocidad"] . ","
                        . "addressLast: '" . utf8_encode($myrow["direccion"]) . "',"
                        . "iconLast: '" . $myrow["icono"] . "'},";
            }
        }


        $objJson.= "]";

        echo "{success: true, $objJson }";
    } else {
        echo "{failure:true, message:'No hay datos que obtener'}";
    }
}