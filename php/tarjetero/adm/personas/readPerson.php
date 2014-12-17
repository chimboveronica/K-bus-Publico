<?php

require_once('../../../../dll/conect_tarjetero.php');

extract($_GET);

$salida = "{failure:true}";

$consultaSql = 
	"SELECT *
     FROM personas P
     ORDER BY P.PER_APELLIDOS
";

consulta($consultaSql);
$resulset = variasFilas();

$salida = "{personas: [";

for ($i = 0; $i < count($resulset); $i++) {
    $fila = $resulset[$i];
    $salida .= "{
            id_persona:".$fila["id_persona"].",
            cedula:'" . $fila["per_cedula"] . "',
            nombres:'" . $fila["per_nombres"] . "',
            apellidos:'" . $fila["per_apellidos"] . "',            
            correo:'" . $fila["per_correo"] . "',
            direccion:'" . $fila["per_direccion"] . "',
            img:'" .$fila["per_imagen"] . "'
        }";
    if ($i != count($resulset) - 1) {
        $salida .= ",";
    }
}

$salida .="]}";

echo utf8_encode($salida);
?>