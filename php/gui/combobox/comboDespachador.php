<?php

include ('../../../dll/config.php');

if (!$mysqli = getConectionDb()) {
    echo "{success:false, msg: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {
    $consultaSql = "select p.id_persona, concat(p.apellidos, ' ', p.nombres) as persona "
            . "from usuarios u,  personas p "
            . "where u.id_persona = p.id_persona "
            . "and u.id_rol_usuario in (3, 5) "
            . "group by p.cedula " //Corregir porque hay dos usuarios con el usuario diego en el rol 3 y el 5
            . "order by p.apellidos";

    $result = $mysqli->query($consultaSql);
    $mysqli->close();

    if ($result->num_rows > 0) {
        $objJson = "{data: [";
        while ($myrow = $result->fetch_assoc()) {
            $objJson .= "{"
                    . "id:" . $myrow["id_persona"] . ","
                    . "text:'" . utf8_encode($myrow["persona"]) . "'},";
        }

        $objJson .="]}";
        echo $objJson;
    } else {
        echo "{success:false, msg: 'No hay datos que obtener'}";
    }
}