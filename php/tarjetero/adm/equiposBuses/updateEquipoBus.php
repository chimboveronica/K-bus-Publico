<?php

require_once('../../../../dll/conect_tarjetero.php');

extract($_POST);

$salida = "{success:false, message:'No se pudo actualizar los Datos'}";
$setNombre = $setMarca = $setDireccion= $setValor=$setIdEquipo=$setSerie=$setParada=$setPuntov= "";
$json = json_decode($equiposbus, true);


if (isset($json["id_bus"])) {
    $setNombre = "id_bus='".$json["id_bus"]."',";
}
if (isset($json["id_punto"])) {
    $setPuntov = "id_bus='".$json["id_punto"]."',";
}
if (isset($json["id_parada"])) {
    $setParada= "id_bus='".$json["id_parada"]."',";
}
if (isset($json["id_equipo"])) {
    $setMarca = "id_equipo='".$json["id_equipo"]."',";
}

if (isset($json["estado"])) {
    $setValor = "estado='".$json["estado"]."',";
}

if (isset($json["id_esp_equipo"])) {
    $setIdEquipo = "id_esp_equipo='".$json["id_esp_equipo"]."',";
}
if (isset($json["serie"])) {
    $setSerie = "serie='".$json["serie"]."',";
}
$setId = "id_buses_equipo= ".$json["id"];
$updateSql = 
    "UPDATE equipos_buses SET $setNombre$setMarca$setValor$setSerie$setSerie$setParada$setPuntov$setIdEquipo$setId
   WHERE  id_buses_equipo= ".$json["id"]
;
$updateSql = utf8_decode($updateSql);

if (consulta($updateSql) == 1) {
    $salida = "{success:true, message:'Datos Insertados Correctamente.'}";
} else {
    $salida = "{success:false, message:'$updateSql'}";
}


echo $salida;
?>