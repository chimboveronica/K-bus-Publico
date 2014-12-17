<?php
require_once('../../../dll/conect_tarjetero.php');

extract($_GET);

$salida = "{failure:true}";

$consultaSql = "SELECT TransDate FROM transline";
consulta($consultaSql);
$resulset = variasFilas();
//Array para almacenar los anios
$anios = array();
//Almacena la cantidad de respuesta
$ta = count($anios);

//2014-03-15
$salida = "{'anios': [";
for ($i = 0; $i < count($resulset); $i++) {
    $fila = $resulset[$i];
    $an = substr($fila["TransDate"],0,4);
    if(!booAnio($an,$anios)){
         $anios[$ta] = $an;
         $salida .= "{
                'id':" . $i . ",
                'anio':'" . $an . "'
            }";
         $ta++;
        if ($i != count($resulset) - 1) {
            $salida .= ",";
        }
    }
}

$salida .="]}";

echo utf8_encode($salida);

/*
 * Funcion encargada de determinar si el anio enviado existe en nuestro arreglo
 * para que este no se repita.
 */
function booAnio($anio, $listAnios)
{
    $val = false;
    for($j = 0; $j <count($listAnios); $j++){
        if($anio == $listAnios[$j]){
            $val = true;
            break;
        }
    }
    
    return $val;
}


?>