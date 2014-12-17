<?php

require_once('../../../../dll/conect_tarjetero.php');
extract($_GET);

$consultaSql = "select p.nombre,p.idpuntos_venta, p.direccion, c.nombres, c.apellidos "
        . "from puntos_venta p, clientes c where p.id_cliente = c.cedula";

consulta($consultaSql);
$resulset = variasFilas();

$salida = "{puntos: [";

for ($i = 0; $i < count($resulset); $i++) {
    $fila = $resulset[$i];
    $salida .= "{"
            . "idpuntos_venta:'" . $fila["idpuntos_venta"] . "',"
            . "nombre:'" . $fila["nombre"] . "',"
            . "direccion:'" . $fila["direccion"] . "',"
            . "cliente:'" . utf8_encode($fila["apellidos"] . " " . $fila["nombres"]) . "'}";
    if ($i != count($resulset) - 1) {
        $salida .= ",";
    }
}

$salida .="]}";

echo $salida;
