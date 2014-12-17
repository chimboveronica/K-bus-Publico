<?php

include('../../login/isLogin.php');
include('../../../dll/config.php');
if (!$mysqli = getConectionDb()) {
    echo "{success:false, message: 'No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {

    $idRol = $_SESSION["IDROLKBUS" . $site_city];
    $idCompany = $_SESSION["IDEMPRESAKBUS" . $site_city];

    $consultaSql = "SELECT ((LENGTH(trama))/1048576) AS megas, (((LENGTH(trama)/1048576)*11.19)/300) AS precio, "
            . "di.descripcion, i.fecha_hora_registro, i.equipo, i.trama, i.excepcion "
            . "FROM kbushistoricodb.dato_invalidos i, kbusdb.tipo_dato_invalidos di "
            . "WHERE i.id_tipo_dato_invalido=di.id_tipo_dato_invalido "
            . "AND DATE(i.fecha_hora_registro) = DATE(NOW()) "
            . "ORDER BY i.fecha_hora_registro DESC";

    $result = $mysqli->query($consultaSql);
    $mysqli->close();

    $objJson = "{data: [";

    while ($myrow = $result->fetch_assoc()) {
        $objJson .= "{"
                . "descripcionDI:'" . utf8_encode($myrow["descripcion"]) . "',"
                . "fechaHoraRegDI:'" . $myrow["fecha_hora_registro"] . "',"
                . "equipoDI:'" . $myrow["equipo"] . "',"
                . "megasDI:" . $myrow["megas"] . ","
                . "precioDI:" . $myrow["precio"] . ","
                . "tramaDI:'" . utf8_encode((preg_replace("[\n|\r|\n\r]", "", $myrow["trama"]))) . "',"
                . "excepcionDI:'" . utf8_encode($myrow["excepcion"]) . "'},";
    }
    $objJson .= "]}";
    echo $objJson;
}
