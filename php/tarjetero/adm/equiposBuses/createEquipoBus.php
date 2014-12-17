<?php

require_once('../../../../dll/conect_tarjetero.php');

extract($_POST);

$salida = "{success:false, message:'No se pudo insertar los Datos'}";

$json = json_decode($equiposbus, true);
date_default_timezone_set('America/Guayaquil');
$Hoy = date('Y-m-d'); 
$lugar=$json["id_bus"].$json["id_equipo"].$json["id_punto"];
$insertSql = 
    "INSERT INTO equipos_buses (ID_EQUIPO,ID_BUS,ESTADO,FECHA_REGISTRO,SERIE,ID_ESP_EQUIPO)
  	VALUES('".$json["id_equipo"]."','".$lugar."','".$json["estado"]."','".$Hoy."','".$json["serie"]."','".$json["id_esp_equipo"]."')"
;
$insertSql = utf8_decode($insertSql);

if (consulta($insertSql) == 1) {
    $salida = "{success:true, message:'Datos Insertados Correctamente.'}";
} else {
    $salida = "{success:false, message:'$insertSql'}";
}


echo $salida;
?>