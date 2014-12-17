<?php

require_once('../../../../dll/conect_tarjetero.php');
extract($_GET);

$consultaSql = "select t.cardno, t.idcliente, t.tipotarifa, t.fecha, t.hora, c.nombres, c.apellidos "
        . "from tarjetas t, clientes c where t.idcliente = c.cedula";

consulta($consultaSql);
$resulset = variasFilas();

$salida = "{tarjetas: [";

for ($i = 0; $i < count($resulset); $i++) {
    $fila = $resulset[$i];
    $salida .= "{"
            . "cardno:'" . $fila["cardno"] . "',"
            . "idCliente:'" . $fila["idcliente"] . "',"
            . "tipoTarifa:'" . $fila["tipotarifa"] . "',"
            . "fecha:'" . $fila["fecha"] . "',"
            . "hora:'" . $fila["hora"] . "',"
            . "cliente:'" . utf8_encode($fila["apellidos"] . " " . $fila["nombres"]) . "'}";
    if ($i != count($resulset) - 1) {
        $salida .= ",";
    }
}

$salida .="]}";

echo $salida;
