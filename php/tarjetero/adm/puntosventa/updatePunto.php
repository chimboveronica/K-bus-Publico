<?php

require_once('../../../../dll/conect_tarjetero.php');

extract($_POST);

$salida = "{success:false, message:'No se pudo actualizar los Datos'}";
$setIdcliente = $setNombre = $setDireccion= "";
$json = json_decode($puntos, true);


if (isset($json["id_cliente"])) {
    $setIdcliente = "ID_CLIENTE='".$json["id_cliente"]."',";
}

if (isset($json["nombre"])) {
    $setNombre = "Nombre='".$json["nombre"]."',";
}
if (isset($json["Direccion"])) {
    $setDireccion = "direccion='".$json["direccion"]."',";
}
$setIdPuntosVenta = "idpuntos_venta= ".$json["id"];
$updateSql = 
    "UPDATE puntos_venta SET $setIdcliente$setNombre$setDireccion$setIdPuntosVenta
   WHERE  idpuntos_venta= ".$json["id"]
;
$updateSql = utf8_decode($updateSql);

if (consulta($updateSql) == 1) {
    $salida = "{success:true, message:'Datos Insertados Correctamente.'}";
} else {
    $salida = "{success:false, message:'$updateSql'}";
}


echo $salida;
?>