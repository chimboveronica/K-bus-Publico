<?php

require_once('../../../dll/conect_tarjetero.php');

extract($_POST);

$json = json_decode($personas, true);

$destroySql = 
    "DELETE FROM personas WHERE per_cedula = ".$json["per_cedula"]
;

if (consulta($destroySql) == 1) {
    $salida = "{success:true, message:'Datos Eliminados Correctamente.'}";
} else {
    $salida = "{success:false, message:'$destroySql'}";
}


echo $salida;
?>