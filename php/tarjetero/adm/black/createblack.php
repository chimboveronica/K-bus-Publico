<?php

require_once('../../../../dll/conect_tarjetero.php');

extract($_POST);

$salida = "{success:false, message:'No se pudo insertar los Datos'}";

$json = json_decode($blacklist, true);
date_default_timezone_set('America/Guayaquil');
$Hoy = date('Y-m-d'); 
$Hora = date('H:i:s'); 
   $insertSql = 
    "INSERT INTO blacklist (CardNo,Fecha,Hora, BlackType)
    VALUES('".$json["carno"]."','".$Hoy."','".$Hora."',".$json["blacktype"].")";
    $insertSql = utf8_decode($insertSql);
    
    if (consulta($insertSql) == 1) {
        $salida = "{success:true, message:'Datos Insertados Correctamente.'}";
    } else {
        $salida = "{success:false, message:'$insertSql'}";
    }
echo $salida;
?>