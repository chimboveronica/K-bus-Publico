<?php

include('../../login/isLogin.php');
include ('../../../dll/config.php');

extract($_GET);

if (!$mysqli = getConectionDb()) {
    echo "{success:false, msg: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {
    $idPersona = $_SESSION["IDPERSONAKBUS" . $site_city];
    $idRol = $_SESSION["IDROLKBUS" . $site_city];

    if ($idRol == 4) {
        $consultaSql = "SELECT v.id_empresa, v.id_vehiculo, v.reg_municipal, p.nombres, p.apellidos "
                . "FROM vehiculos v, personas p "
                . "WHERE p.id_persona = v.id_persona "
                . "AND v.id_persona = $idPersona "
                . "ORDER BY v.reg_municipal";
    } else {
        $consultaSql = "SELECT v.id_empresa, v.id_vehiculo, v.reg_municipal, p.nombres, p.apellidos "
                . "FROM vehiculos v, personas p "
                . "WHERE p.id_persona = v.id_persona "
                . "AND v.id_empresa = $idCompany "
                . "ORDER BY v.reg_municipal";

        if ($idCompany == 1) {
            $consultaSql = "SELECT v.id_empresa, v.id_vehiculo, v.reg_municipal, p.nombres, p.apellidos "
                    . "FROM vehiculos v, personas p "
                    . "WHERE p.id_persona = v.id_persona "
                    . "ORDER BY v.reg_municipal";
        }
    }

    $result = $mysqli->query($consultaSql);
    $mysqli->close();

    if ($result->num_rows > 0) {
        $objJson = "{data: [";
        while ($myrow = $result->fetch_assoc()) {
            $objJson .= "{"
                    . "id:" . $myrow["id_vehiculo"] . ","
                    . "text:'" . substr($myrow["reg_municipal"], 3) . " " . utf8_encode($myrow["nombres"] . " " . $myrow["apellidos"]) . "',"
                    . "idCompany:" . $myrow["id_empresa"] . ","
                    . "muniReg: '" . $myrow["reg_municipal"] . "'},";
        }

        $objJson .="]}";
        echo $objJson;
    } else {
        echo "{success:false, msg: 'No hay datos que obtener'}";
    }
}
