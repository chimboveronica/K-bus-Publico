<?php

require_once('../../dll/conect.php');

$consultaVehCoop = 
	"SELECT CODIGO,ESTACION,LATITUD,LONGITUD 
    FROM estaciones"
;    

consulta($consultaVehCoop);
$resulset = variasFilas();
$objJson = "[";
for ($i = 0; $i < count($resulset); $i++) {
    $filas = $resulset[$i];        
    $idEstacion = $filas["CODIGO"];
    $nombreEstacion = $filas["ESTACION"];

    $objJson.="{'text':'".$idEstacion. ":: " . utf8_encode($nombreEstacion) . "',
        'iconCls':'estacionIco',
        'id':'" . $idEstacion . "',
        'leaf':true}";

    if ($i < (count($resulset) - 1)) {
        $objJson .= ",";
    }
}

$objJson .= "]";
echo $objJson;
?>