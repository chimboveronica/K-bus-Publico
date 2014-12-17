<?php

include ('../../../dll/config.php');

if (!$mysqli = getConectionDb()) {
    echo "{success:false, message: 'No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {

    $consultaSql = "SELECT p.id_persona, p.id_empresa, p.cedula, p.nombres, "
            . "p.apellidos, p.genero, p.fecha_nacimiento, p.conyugue, "
            . "p.correo, p.direccion, p.celular, p.imagen, tl.id_tipo_licencia "
            . "FROM personas p, tipo_licencias tl "
            . "WHERE  p.id_tipo_licencia = tl.id_tipo_licencia "
            . "ORDER BY p.apellidos";

    $result = $mysqli->query($consultaSql);
    $mysqli->close();

    $objJson = "{data: [";

    while ($myrow = $result->fetch_assoc()) {
        $objJson .= "{"
                . "idPerson:" . $myrow["id_persona"] . ","
                . "documentPerson:'" . $myrow["cedula"] . "',"
                . "namePerson:'" . utf8_encode($myrow["nombres"]) . "',"
                . "surnamePerson:'" . utf8_encode($myrow["apellidos"]) . "',"
                . "genderPerson:" . $myrow["genero"] . ","
                . "dateOfBirthPerson:'" . utf8_encode($myrow["fecha_nacimiento"]) . "',"
                . "idTypeLicensePerson:" . $myrow["id_tipo_licencia"] . ","
                . "spousePerson:'" . utf8_encode($myrow["conyugue"]) . "',"
                . "addressPerson:'" . utf8_encode($myrow["direccion"]) . "',"
                . "emailPerson:'" . utf8_encode($myrow["correo"]) . "',"
                . "cellPerson:'" . $myrow["celular"] . "',"
                . "imagePerson:'" . utf8_encode($myrow["imagen"]) . "',"
                . "idCompanyPerson:" . $myrow["id_empresa"] . "},";
    }
    $objJson .= "]}";
    echo $objJson;
}