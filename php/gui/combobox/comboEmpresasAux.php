<?php

include('../../login/isLogin.php');
include ('../../../dll/config.php');

//Se coloca antes para cuando el usuario a traves de click sobre el boton derecho
//cargue automaticamente la cooperativa
$idCompany = $_SESSION["IDEMPRESAKBUS" . $site_city];

$menuClick = 0;

extract($_GET);

if (!$mysqli = getConectionDb()) {
    echo "{success:false, msg: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {
    $idRol = $_SESSION["IDROLKBUS" . $site_city];

    if ($menuClick == 1) {
        $consultaSql = "SELECT id_empresa, empresa "
                . "FROM empresas WHERE id_empresa = $idCompany";
    } else {
        if ($idRol == 1 || $idRol == 3 || $idRol == 6 || $idRol == 5) {
            $consultaSql = "SELECT id_empresa, empresa "
                    . "FROM empresas WHERE id_empresa > 1";
        } else {
            $consultaSql = "SELECT id_empresa, empresa "
                    . "FROM empresas WHERE id_empresa = $idCompany";
        }
    }

    $result = $mysqli->query($consultaSql);
    $mysqli->close();

    if ($result->num_rows > 0) {
        $objJson = "{data: [";
        while ($myrow = $result->fetch_assoc()) {
            $objJson .= "{"
                    . "id:" . $myrow["id_empresa"] . ","
                    . "text:'" . utf8_encode($myrow["empresa"]) . "'},";
        }

        $objJson .="]}";
        echo $objJson;
    } else {
        echo "{success:false, msg: 'No hay datos que obtener'}";
    }
}