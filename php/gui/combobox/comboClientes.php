<?php

require_once('../../../dll/conect_tarjetero.php');

extract($_GET);

$salida = "{failure:true}";

$consultaSql = 
	"SELECT CEDULA, NOMBRES, APELLIDOS
    FROM clientes ORDER BY APELLIDOS
";

consulta($consultaSql);
$resulset = variasFilas();

$salida = "{'cliente': [";

for ($i = 0; $i < count($resulset); $i++) {
    $fila = $resulset[$i];
    $salida .= "{
            'id':'" . $fila["CEDULA"] . "',
            'nombre':'" . $fila["APELLIDOS"]." ".$fila["NOMBRES"] . "'
        }";
    if ($i != count($resulset) - 1) {
        $salida .= ",";
    }
}

$salida .="]}";

echo utf8_encode($salida);
?>
