<?php

require_once('../../../../dll/conect_tarjetero.php');

extract($_POST);

$salida = "{success:false, message:'No se pudo insertar los Datos'}";

$json = json_decode($puntos, true);

$insertSql = 
    "INSERT INTO puntos_venta (NOMBRE,ID_CLIENTE, DIRECCION,FECHA,HORA)
    VALUES('".$json["nombre"]."','".$json["id_cliente"]."','".$json["direccion"]."',Date(now()),Time(now()))"
;
$insertSql = utf8_decode($insertSql);

if (consulta($insertSql) == 1) {
    $salida = "{success:true, message:'Datos Insertados Correctamente.'}";
} else {
    $salida = "{success:false, message:'$insertSql'}";
}
echo $salida;
?>