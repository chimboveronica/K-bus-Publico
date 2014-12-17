<?php

require_once('../../../../dll/conect_tarjetero.php');

extract($_POST);

$salida = "{success:false, message:'No se pudo actualizar los Datos'}";
$setIdcliente = $setTipoTarifa = $setDireccion= "";
$json = json_decode($tarjetas, true);


if (isset($json["idCliente"])) {
    $setIdcliente = "IDCLIENTE='".$json["idCliente"]."',";
}

if (isset($json["tipoTarifa"])) {
    $setTipoTarifa = "TipoTarifa='".$json["tipoTarifa"]."',";
}
if (isset($json["fechaHora"])) {
    $setDireccion = "FECHAHORA='".$json["fechaHora"]."',";
}
$setCardNo = "CARDNO= ".$json["id"];
$updateSql = 
    "UPDATE tarjetas SET $setIdcliente$setTipoTarifa$setDireccion$setCardNo
   WHERE  CardNo= ".$json["id"]
;
$updateSql = utf8_decode($updateSql);

if (consulta($updateSql) == 1) {
    $salida = "{success:true, message:'Datos Insertados Correctamente.'}";
} else {
    $salida = "{success:false, message:'$updateSql'}";
}


echo $salida;
?>