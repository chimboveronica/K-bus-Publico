<?php

require_once('../../../dll/conect.php');

extract($_GET);

$salida = "{failure:true}";

$consultaSql = "SELECT ID_MODELO_VEHICULO, MODELO
    FROM modelo"
;

consulta($consultaSql);
$resulset = variasFilas();

$salida = "{'modelo': [";

for ($i = 0; $i < count($resulset); $i++) {
    $fila = $resulset[$i];
    $salida .= "{
            'id':" . $fila["ID_MODELO_VEHICULO"] . ",
            'nombre':'" . $fila["MODELO"] . "'
        }";
    if ($i != count($resulset) - 1) {
        $salida .= ",";
    }
}

$salida .="]}";

echo utf8_encode($salida);
?>
