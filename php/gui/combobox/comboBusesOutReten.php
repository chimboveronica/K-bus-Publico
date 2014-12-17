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
        $consultaSql = "select v.id_vehiculo, v.reg_municipal,p.nombres,p.apellidos "
                . "from vehiculos v, personas p "
                . "where p.id_persona = v.id_persona "
                . "and v.id_persona = $idPersona";
    } else {
        $consultaSql = "select v.id_vehiculo, v.reg_municipal,p.nombres,p.apellidos "
                . "from vehiculos v, personas p "
                . "where p.id_persona = v.id_persona "
                . "and v.id_empresa = $idCompany "
                . "order by v.id_vehiculo";

        if ($idCompany == 1) {
            $consultaSql = "select v.id_vehiculo, v.reg_municipal,p.nombres,p.apellidos "
                    . "from vehiculos v, personas p "
                    . "where p.id_persona = v.id_persona "
//                    . "and v.id_empresa = $idCompany "
                    . "order by v.id_vehiculo";
        }
    }

    $result = $mysqli->query($consultaSql);
    $mysqli->close();

    if ($result->num_rows > 0) {
        $objJson = "{data: [";
        while ($myrow = $result->fetch_assoc()) {
            $objJson .= "{"
                    . "id:" . $myrow["id_vehiculo"] . ","
                    . "text:'" . substr($myrow["reg_municipal"], 3) . " " . utf8_encode($myrow["nombres"] . " " . $myrow["apellidos"]) . "'},";
        }

        $objJson .="]}";
        echo $objJson;
    } else {
        echo "{success:false, msg: 'No hay datos que obtener'}";
    }
}
