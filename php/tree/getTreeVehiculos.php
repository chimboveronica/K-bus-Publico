<?php

include('../login/isLogin.php');
include ('../../dll/config.php');

if (!$mysqli = getConectionDb()) {
    echo "Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.";
} else {
    $idCompany = $_SESSION["IDEMPRESAKBUS" . $site_city];
    $idPerson = $_SESSION["IDPERSONAKBUS" . $site_city];
    $idRol = $_SESSION["IDROLKBUS" . $site_city];

    if ($idRol == 1 || $idRol == 3 || $idRol == 6) {
        $expand = 'false';
        $consultaSql = "select v.id_vehiculo, v.reg_municipal, e.id_empresa, e.empresa "
                . "from vehiculos v, empresas e, personas p "
                . "where v.id_empresa = e.id_empresa "
                . "and v.id_persona = p.id_persona "
                . "and e.id_empresa > 1 "
                . "order by e.empresa, v.reg_municipal"
        ;
    } else if ($idRol == 2) {
        $expand = 'true';
        $consultaSql = "select v.id_vehiculo, v.reg_municipal, e.id_empresa, e.empresa "
                . "from vehiculos v, empresas e, personas p "
                . "where v.id_empresa = e.id_empresa "
                . "and v.id_persona = p.id_persona "
                . "and e.id_empresa = $idCompany "
                . "order by v.reg_municipal"
        ;
    } else if ($idRol == 4) {
        $expand = 'true';
        $consultaSql = "select v.id_vehiculo, v.reg_municipal, e.id_empresa, e.empresa "
                . "from vehiculos v, empresas e, personas p "
                . "where v.id_empresa = e.id_empresa "
                . "and v.id_persona = p.id_persona "
                . "and v.id_persona = $idPerson"
        ;
    }

    $result = $mysqli->query($consultaSql);
    $mysqli->close();

    $compare = -1;

    if ($result->num_rows > 0) {
        $objJson = "[";
        while ($myrow = $result->fetch_assoc()) {
            $idCompany = $myrow["id_empresa"];
            $idVehicle = $myrow["id_vehiculo"];
            $regMun = $myrow["reg_municipal"];

            if ($compare != $idCompany) {
                if ($compare != -1) {
                    $objJson .= "]},";
                }

                $compare = $idCompany;
                $nameCoop = $myrow["empresa"];

                $objJson .= "{text:'" . utf8_encode($nameCoop) . "',
                expanded:" . $expand . ",
                iconCls: 'icon-company', 
                leaf: false,
                children:[{text: '" . $regMun . "',
                iconCls: 'icon-bus',
                id: '" . $idCompany . '_' . $idVehicle . "',
                leaf: true},";
            } else {
                $objJson .= "{text: '" . $regMun . "',
                iconCls: 'icon-bus',
                id: '" . $idCompany . '_' . $idVehicle . "',
                leaf: true},";
            }
        }
        $objJson .= "]}]";

        echo $objJson;
    } else {
        echo "No hay datos que obtener";
    }
}
