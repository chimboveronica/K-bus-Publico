<?php

include('../../login/isLogin.php');
require_once('../../../dll/conect.php');

extract($_POST);

$updateSql = 
    "UPDATE estado_eqp_udp
    SET ESTADO = '$estado',
        FECHA_ESTADO = '$date'
    WHERE ID_EQUIPO = '$idEqp'
    "
;

if (consulta(utf8_decode($updateSql)) == 1) {
    $salida = "{success: true}";
} else {
    $salida = "{failure: true}";
}

echo $salida;
?>