<?php

require_once('../../../dll/conect_tarjetero.php');

extract($_GET);

$salida = "{failure:true}";

$consultaSql = 
	"SELECT idpuntos_venta, nombre FROM puntos_venta
";

consulta($consultaSql);
$resulset = variasFilas();

$salida = "{'puntos': [";

for ($i = 0; $i < count($resulset); $i++) {
    $fila = $resulset[$i];
    $salida .= "{
            'id':'" . $fila["idpuntos_venta"] . "',
            'nombreP':'". $fila["nombre"] . "'
        }";
    if ($i != count($resulset) - 1) {
        $salida .= ",";
    }
}

$salida .="]}";

echo utf8_encode($salida);
?>
