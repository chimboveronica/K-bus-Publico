<?php

require_once('../../../../dll/conect_tarjetero.php');
extract($_GET);

$consultaSql = "select * from equipos";

consulta($consultaSql);
$resulset = variasFilas();

$salida = "{equipos: [";

for ($i = 0; $i < count($resulset); $i++) {
    $fila = $resulset[$i];
    $salida .= "{"
            . "nombre:'" . $fila["tipo"] . "',"
            . "marca:'" . $fila["marca"] . "',"
            . "modelo:'" . $fila["modelo"] . "',"
            . "valor:'" . $fila["valor"] . "',"
            . "idequipo:'" . $fila["idequipo"] . "',"
            . "fecha_ingreso:'" . $fila["fecha_ingreso"] . "',
            
        }";
    if ($i != count($resulset) - 1) {
        $salida .= ",";
    }
}

$salida .="]}";

echo $salida;
