<?php

require_once('../../../../dll/conect_tarjetero.php');

extract($_POST);

$salida = "{success:false, message:'No se pudo actualizar los Datos'}";
$setTipo = $setMarca = $setDireccion= $setValor= "";
$json = json_decode($equipos, true);


if (isset($json["tipo"])) {
    $setTipo = "TIPO='".$json["tipo"]."',";
}

if (isset($json["marca"])) {
    $setMarca = "MARCA='".$json["marca"]."',";
}
if (isset($json["modelo"])) {
    $setModelo= "MODELO='".$json["modelo"]."',";
}
if (isset($json["valor"])) {
    $setValor = "VALOR='".$json["valor"]."',";
}
$setId = "IDEQUIPO= ".$json["id"];
$updateSql = 
    "UPDATE equipos SET $setTipo$setMarca$setDireccion$setValor$setId
   WHERE  idequipo= ".$json["id"]
;
$updateSql = utf8_decode($updateSql);

if (consulta($updateSql) == 1) {
    $salida = "{success:true, message:'Datos Insertados Correctamente.'}";
} else {
    $salida = "{success:false, message:'$updateSql'}";
}


echo $salida;
?>