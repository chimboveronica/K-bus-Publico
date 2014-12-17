<?php

require_once('../../dll/conect.php');

extract($_GET);

$salida = "{failure:true}";

$consultaSql = "SELECT id_geo FROM puntos";

consulta($consultaSql);
$resulset = variasFilas();

$salida = "[";

for ($i = 0; $i < count($resulset); $i++) {
    $fila = $resulset[$i];
    $salida .= "[
            '" . utf8_encode($fila["id_geo"]) . "',
            '" . utf8_encode($fila["id_geo"]) . "'
        ]";

    if ($i != count($resulset) - 1) {
        $salida .= ",";
    }
}

$salida .="]";

echo $salida;
?>
