<?php

require_once('../../../dll/conect_tarjetero.php');

extract($_GET);

$salida = "{failure:true}";

$consultaSql = "SELECT *
    FROM tipo_tarjetas;
";

consulta($consultaSql);
$resulset = variasFilas();

$salida = "{'tipo_tarjetas': [";

for ($i = 0; $i < count($resulset); $i++) {
    $fila = $resulset[$i];
    $salida .= "{
            'id':'" . $fila["id_tipo_tarjeta"] . "',
            'nombre':'" . $fila["tip_nombre"] . "'
        }";
    if ($i != count($resulset) - 1) {
        $salida .= ",";
    }
}

$salida .="]}";

echo utf8_encode($salida);
?>
