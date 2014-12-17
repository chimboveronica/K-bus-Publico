<?php
require_once('../../../../dll/conect_tarjetero.php');

extract($_POST);

$json = json_decode($tarjetas, true);

$destroySql = 
    "DELETE FROM tarjetas WHERE CardNo = ".$json["id"]
;

if (consulta($destroySql) == 1) {
    $salida = "{success:true, message:'Datos Eliminados Correctamente.'}";
} else {
    $salida = "{success:false, message:'$destroySql'}";
}


echo $salida;
?>