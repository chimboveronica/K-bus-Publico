<?php

include ('../../../dll/config.php');

if (!$mysqli = getConectionDb()) {
    echo "{success:false, msg: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {
    $consultaSql = "select p.id_persona, p.nombres, p.apellidos,p.cedula, e.empresa "
            . "from personas p, empresas e "
            . "where p.id_empresa = e.id_empresa "
            . "order by p.apellidos";

    $result = $mysqli->query($consultaSql);
    $mysqli->close();

    if ($result->num_rows > 0) {
        $objJson = "{data: [";
        while ($myrow = $result->fetch_assoc()) {
            $objJson .= "{"
                    . "id:" . $myrow["id_persona"] . ","
                    . "text:'" . utf8_encode($myrow["apellidos"] . " " . $myrow["nombres"]) . "',"
                    . "textDocument:'" . utf8_encode($myrow["apellidos"] . " " . $myrow["nombres"]) ." - ".$myrow["cedula"] . "',"
                    . "empresa: '" . utf8_encode($myrow["empresa"]) . "'},";
        }

        $objJson .="]}";
        echo $objJson;
    } else {
        echo "{success:false, msg: 'No hay datos que obtener'}";
    }
}
