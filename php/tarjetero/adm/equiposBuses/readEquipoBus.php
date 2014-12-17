<?php

require_once('../../../../dll/conect_tarjetero.php');
extract($_GET);

$consultaSql = "select e.id_equipo,e.id_esp_equipo,e.serie, e.id_bus, e.estado,e.id_buses_equipo,e.fecha_registro, eq.idequipo, eq.tipo, eq.modelo,eq.marca".
" from equipos_buses e ,equipos eq where e.id_equipo=eq.idequipo ";

consulta($consultaSql);
$resulset = variasFilas();

$salida = "{equiposbus: [";

for ($i = 0; $i < count($resulset); $i++) {
    $fila = $resulset[$i];
    $salida .= "{"
            . "id_equipo:'" . $fila["marca"]." " . $fila["tipo"] . "',"
            . "id_bus:'" . $fila["id_bus"] . "',"
            . "estado:'" . $fila["estado"] . "',"
            . "serie:'" . $fila["serie"] . "',"
            . "id_esp_equipo:'" . $fila["id_esp_equipo"] . "',"
            . "id_buses_equipo:'" . $fila["id_buses_equipo"] . "',"
            . "fecha_registro:'" . $fila["fecha_registro"] . "',
            
        }";
    if ($i != count($resulset) - 1) {
        $salida .= ",";
    }
}

$salida .="]}";

echo $salida;
