<?php

require_once('../../../../dll/conect_tarjetero.php');

extract($_POST);

$salida = "{success:false, message:'No se pudo insertar los Datos'}";

$json = json_decode($usuarios, true);

$clave = $json["clave"];
$clave = $clave[0];

$salt = "KR@D@C";
$encriptClave = md5(md5(md5($clave) . md5($salt)));

$queryMaxId = "SELECT MAX(ID_USUARIO) AS ID_USER FROM usuarios";
consulta($queryMaxId);
$data = unicaFila();
$id_user = $data["ID_USER"]+1;

$insertSql = 
    "INSERT INTO usuarios (ID_USUARIO,USU_USUARIO,USU_CLAVE,USU_ID_ROL,USU_ID_PERSONA,USU_ID_EMPRESA)
    VALUES('$id_user','".$json["usuario"]."','".$encriptClave."','".$json["rol"]."','".$json["persona"]."','1')"
;
$insertSql = utf8_decode($insertSql);

if (consulta($insertSql) == 1) {
    $salida = "{success:true, message:'Datos Insertados Correctamente.'}";
} else {
    $salida = "{success:false, message:'$insertSql'}";
}


echo $salida;
?>