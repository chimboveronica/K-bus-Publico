<?php

require_once('../../../../dll/conect_tarjetero.php');

extract($_GET);

$salida = "{failure:true}";

$consultaSql = 
	"SELECT *
     FROM blacklist
";

consulta($consultaSql);
$resulset = variasFilas();

$salida = "{blacklist: [";

for ($i = 0; $i < count($resulset); $i++) {
    $fila = $resulset[$i];
    if($fila["BlackType"] == 0){
        $bt = "Perdida";
    }
    if($fila["BlackType"] == 1){
        $bt = "Robo";
    }
    if($fila["BlackType"] == 2){
        $bt = "Deterioro";
    }
    
    $salida .= "{
            carno:'" . $fila["CardNo"] . "',
            fecha:'" . $fila["Fecha"] . "',
            hora:'" . $fila["Hora"] . "',            
            blacktype:'" . $bt. "',
            
        }";
    if ($i != count($resulset) - 1) {
        $salida .= ",";
    }
}

$salida .="]}";

echo utf8_encode($salida);
?>