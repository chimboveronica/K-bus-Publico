<?php

require_once('../../../../dll/conect_tarjetero.php');

extract($_POST);

$salida = "{success:false, message:'No se pudo insertar los Datos'}";

$json = json_decode($tarjetas, true);

$insertSql = 
    "INSERT INTO tarjetas (CARDNO,IDCLIENTE,TIPOTARIFA,FECHA,HORA)
    VALUES('".$json["cardno"]."','".$json["idCliente"]."','".$json["tipoTarifa"]."',Date(now()),Time(now()))"
;
$insertSql = utf8_decode($insertSql);

if (consulta($insertSql) == 1) {
    $salida = "{success:true, message:'Datos Insertados Correctamente.'}";
} else {
    $salida = "{success:false, message:'$insertSql'}";
}


echo $salida;
?>