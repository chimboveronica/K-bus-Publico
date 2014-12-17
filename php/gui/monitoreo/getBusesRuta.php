<?php

require_once('../../../dll/conect.php');

extract($_GET);

$consultaSql = 
	"SELECT U.ID_EQUIPO, U.LATITUD, U.LONGITUD, U.FECHA, U.HORA, U.VELOCIDAD, E.ID_EMPRESA,
    U.DIRECCION, U.EVENTO, U.ID_EVENTO, E.EMPRESA, B.REG_MUNICIPAL, R.ICON
    FROM ultimos_gps U, empresas E, vehiculos B, rutas R
    WHERE U.ID_EMPRESA = E.ID_EMPRESA
    AND U.ID_EQUIPO = B.ID_EQUIPO
    AND U.COD_RUTA = R.COD_RUTA
    AND R.ID_RUTA = $id_ruta"
;

consulta($consultaSql);
$resulset = variasFilas();

$salida = "{ d: [";

for ($i = 0; $i < count($resulset); $i++) {
    $fila = $resulset[$i];

    if ($i > 0) {
        $salida .= ",";
    }

    $salida .= "{
        idEqp: '" . $fila["REG_MUNICIPAL"] . "',
        idEmp: '" . $fila["ID_EMPRESA"] . "',
        emp: '" . $fila["EMPRESA"] . "',
        lat: '" . $fila["LATITUD"] . "',
        lon: '" . $fila["LONGITUD"] . "',
        fec: '" . $fila["FECHA"] . "',
        hor: '" . $fila["HORA"] . "',
        vel: " . $fila["VELOCIDAD"] . ",
        dir: '" . $fila["DIRECCION"] . "',
        evt: '" . $fila["EVENTO"] . "',
        idEvt: '" . $fila["ID_EVENTO"] . "',
        icon: '" . $fila["ICON"] . "'
    }";
}

$salida.= "]}";
echo utf8_encode($salida);
?>
