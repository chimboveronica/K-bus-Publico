<?php

require_once('../../../../dll/conect_tarjetero.php');

extract($_POST);

$salida = "{success:false, message:'No se pudo insertar los Datos'}";

$json = json_decode($equipos, true);
date_default_timezone_set('America/Guayaquil');
$Hoy = date('Y-m-d'); 

$insertSql = 
    "INSERT INTO equipos (TIPO,MARCA,MODELO,VALOR,FECHA_INGRESO)
  	VALUES('".$json["nombre"]."','".$json["marca"]."','".$json["modelo"]."','".$json["valor"]."','".$Hoy."')"
;

$insertSql = utf8_decode($insertSql);

if (consulta($insertSql) == 1) {
    $salida = "{success:true, message:'Datos Insertados Correctamente'}";
} else {
    $salida = "{success:false, message:'$insertSql'}";
}

echo $salida;
?>