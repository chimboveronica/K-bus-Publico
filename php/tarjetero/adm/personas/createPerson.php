<?php

require_once('../../../../dll/conect_tarjetero.php');

extract($_POST);

$salida = "{success:false, message:'No se pudo insertar los Datos'}";

$json = json_decode($personas, true);

$existeSql = 
    "SELECT COUNT(PER_CEDULA) AS C FROM personas WHERE PER_CEDULA='".$json["cedula"]."'";

consulta($existeSql);

$resultados = unicaFila();

if ($resultados["C"] >= 1) {
    $salida = "{success:false, message:'Cedula Repetida'}";
} else {
    // C:/fakepath/bus.png 
    $imagen = substr($json["img"], 12);

    $insertSql = 
    "INSERT INTO personas (PER_NOMBRES,PER_APELLIDOS,PER_CEDULA,PER_CORREO,PER_DIRECCION,PER_IMAGEN)
    VALUES('".$json["nombres"]."','".$json["apellidos"]."','".$json["cedula"]."','".$json["correo"]."','".$json["direccion"]."','".$imagen."')";
    $insertSql = utf8_decode($insertSql);

    if (consulta($insertSql) == 1) {
        $salida = "{success:true, message:'Datos Insertados Correctamente.'}";
    } else {
        $salida = "{success:false, message:'$insertSql'}";
    }
}

echo $salida;
?>