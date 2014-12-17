<?php

require_once('../../../../dll/conect_tarjetero.php');

extract($_POST);

$salida = "{success:false, message:'No se pudo actualizar los Datos'}";
$setCedula = $setNombres = $setApellidos =  $setDireccion = $setCorreo = $setImagen = "";

$json = json_decode($personas, true);

if (isset($json["cedula"])) {
    $setCedula = "PER_CEDULA='".$json["cedula"]."',";
}

if (isset($json["nombres"])) {
    $setNombres = "PER_NOMBRES='".$json["nombres"]."',";
}

if (isset($json["apellidos"])) {
    $setApellidos = "PER_APELLIDOS='".$json["apellidos"]."',";
}

if (isset($json["correo"])) {
    $setCorreo = "PER_CORREO='".$json["correo"]."',";
}

if (isset($json["direccion"])) {
    $setDireccion = "PER_DIRECCION='".$json["direccion"]."',";
}

if (isset($json["img"])) {
    $imagen = substr($json['img'], 12);
    $setImagen = "PER_IMAGEN='".$imagen."',";
}


$setId = "PER_CEDULA = ".$json["cedula"];

$updateSql = 
    "UPDATE personas 
     SET $setNombres$setApellidos$setDireccion$setCorreo$setImagen
     WHERE PER_CEDULA = '".$json["cedula"]."'";

$updateSql = utf8_decode($updateSql);

if (consulta($updateSql) == 1) {
    $salida = "{success:true, message:'Datos Actualizados Correctamente.'}";
} else {
    $salida = "{success:false, message:'$updateSql'}";
}

echo $salida;
?>