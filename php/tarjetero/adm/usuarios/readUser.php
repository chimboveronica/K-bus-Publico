<?php

require_once('../../../../dll/conect_tarjetero.php');

extract($_GET);

$salida = "{failure:true}";

$consultaSql = 
    "SELECT U.ID_USUARIO, P.PER_CEDULA, P.PER_NOMBRES, P.PER_APELLIDOS, RO.ID_ROL,RO.ROL_TIPO, U.USU_USUARIO, U.USU_CLAVE
     FROM usuarios U
     JOIN personas P ON U.USU_ID_PERSONA = P.ID_PERSONA
     JOIN roles RO ON U.USU_ID_ROL = RO.ID_ROL
     ORDER BY P.PER_APELLIDOS";

consulta($consultaSql);
$resulset = variasFilas();

$salida = "{'usuarios': [";

for ($i = 0; $i < count($resulset); $i++) {
    $fila = $resulset[$i];
    $salida .= "{
            'id_usuario':".$fila["ID_USUARIO"].",
            'cedula':'" . $fila["PER_CEDULA"] . "',
            'persona':'" . utf8_encode($fila["PER_APELLIDOS"]." ".$fila["PER_NOMBRES"]) . "',
            'rol':'" . utf8_encode($fila["ROL_TIPO"]) . "',
            'usuario':'" . utf8_encode($fila["USU_USUARIO"]) . "',
            'clave':'" . utf8_encode($fila["USU_CLAVE"]) . "'            
        }";
    if ($i != count($resulset) - 1) {
        $salida .= ",";
    }
}

$salida .="]}";

echo $salida;
?>
