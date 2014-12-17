<?php

require_once('../../../dll/conect.php');

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
    $setEmail = "PER_CORREO='".$json["correo"]."',";
}

if (isset($json["direccion"])) {
    $setDireccion = "PER_DIRECCION='".$json["direccion"]."',";
}

if (isset($json["img"])) {
    $setDireccion = "PER_IMAGEN='".$json["imagen"]."',";
}


$setId = "PER_CEDULA = ".$json["PER_CEDULA"];

$updateSql = 
    "UPDATE personas 
    SET $setCedula$setNombres$setApellidos$setConyugue$setEmpleo$setFechaNac$setDireccion$setEmail$setCelular$setId
    WHERE PER_CEDULA = ".$json["PER_CEDULA"]
;

$updateSql = utf8_decode($updateSql);

if (consulta($updateSql) == 1) {
    $salida = "{success:true, message:'Datos Actualizados Correctamente.'}";
} else {
    $salida = "{success:false, message:'$updateSql'}";
}

echo $salida;
?>