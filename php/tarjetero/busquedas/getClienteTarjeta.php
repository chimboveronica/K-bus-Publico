<?php

require_once('../../../dll/conect_tarjetero.php');

extract($_GET);
extract($_POST);

$like = "";
$salida = "{failure:true}";
if(isset($query)){
    $like = "AND P.per_apellidos LIKE '%".$query."%'";
}else{
    $like = "";
}


$consultaSql = 
	"SELECT *
     FROM personas P, usuarios U, roles R
     WHERE U.usu_id_persona = P.id_persona
        AND U.usu_id_rol = R.id_rol
        $like
     ORDER BY P.PER_APELLIDOS
";

consulta($consultaSql);
$resulset = variasFilas();

$salida = "{clientes: [";

for ($i = 0; $i < count($resulset); $i++) {
    $fila = $resulset[$i];
    $salida .= "{
            id:".$fila["id_persona"].",
            nombre    :'" . $fila["per_apellidos"]." ".$fila["per_nombres"] . "',
            cedula    :'" . $fila["per_cedula"]."',
            direccion :'" . $fila["per_direccion"]."',
            img       :'" . $fila["per_imagen"]."'
            }";
    if ($i != count($resulset) - 1) {
        $salida .= ",";
    }
}

$salida .="]}";

echo utf8_encode($salida);
?>