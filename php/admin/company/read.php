<?php

include ('../../../dll/config.php');

if (!$mysqli = getConectionDb()) {
    echo "{success:false, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.',state: false}";
} else {

    $consultaSql = "select id_empresa, acronimo, empresa, direccion, telefono, correo "
            . "from empresas";

    $result = $mysqli->query($consultaSql);
    $mysqli->close();

    $objJson = "{data: [";

    while ($myrow = $result->fetch_assoc()) {
        $objJson .= "{"
                . "idCompany:" . $myrow["id_empresa"] . ","
                . "acronymCompany:'" . utf8_encode($myrow["acronimo"]) . "',"
                . "companyCompany:'" . utf8_encode($myrow["empresa"]) . "',"
                . "addressCompany:'" . utf8_encode($myrow["direccion"]) . "',"
                . "cellCompany:'" . $myrow["telefono"] . "',"
                . "emailCompany:'" . utf8_encode($myrow["correo"]) . "'},";
    }
    $objJson .= "]}";
    echo $objJson;
}