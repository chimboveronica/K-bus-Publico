<?php

require_once('../../../dll/conect.php');

extract($_GET);

$salida = "{failure:true}";

$consultaSql = "SELECT ID_TIPO_BUS, TIPO_BUS
    FROM tipo_bus"
;

consulta($consultaSql);
$resulset = variasFilas();

$salida = "{'tipo_bus': [";

for ($i = 0; $i < count($resulset); $i++) {
    $fila = $resulset[$i];
    $salida .= "{
            'id':" . $fila["ID_TIPO_BUS"] . ",
            'nombre':'" . $fila["TIPO_BUS"] . "'
        }";
    if ($i != count($resulset) - 1) {
        $salida .= ",";
    }
}

$salida .="]}";

echo utf8_encode($salida);
?>
