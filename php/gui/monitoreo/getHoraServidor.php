<?php

include('../../login/isLogin.php');
require_once('../../../dll/conect.php');

$consultaSql = 
    "SELECT hora FROM ultimos_gps u ,vehiculos b where
 u.fecha=Date(now()) and u.id_equipo=b.id_equipo and b.id_tipo_equipo=1 order by u.hora desc limit 1 ";
    

consulta($consultaSql);
$resulset = unicaFila();
$salida = "{horaSer : [{hora:'" . $resulset["hora"]."'}]}";
echo utf8_encode($salida);
?>