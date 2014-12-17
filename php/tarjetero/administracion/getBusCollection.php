<?php

require_once('../../../dll/conect_tarjetero.php');

extract($_GET);
extract($_POST);

$like = "";
$salida = "{failure:true}";


//COnvertir el id del bus 
// 0009 Carlos Ulises Galvez Romero
$id_bus = substr($nameBus, 0, 4);

$consultaSql = 
	"SELECT count(*) AS TotalConsulta
         FROM transline T
         WHERE BusNo = $id_bus
         AND TransDate >= '$dateStart'
         AND TransDate <= '$dateFinish'    
        ";
consulta($consultaSql);
$resulset = unicaFila();

if($resulset["TotalConsulta"]>0){


$consultaSql = 
	"SELECT BusNo, TransAmt, TransDate, TransDateTime
         FROM transline
         WHERE BusNo = $id_bus
         AND TransDate >= '$dateStart'
         AND TransDate <= '$dateFinish'   
        ";

consulta($consultaSql);
$resulset = variasFilas();

$salida = "{recaudo: [";
$total = 0;
for ($i = 0; $i < count($resulset); $i++) {
    $fila = $resulset[$i];
    $salida .= "{
            id_bus         :'".$fila["BusNo"]."',
            total_pasajeros:'0',
            total_recaudo  :" . $fila["TransAmt"].",
            fecha  :'" . $fila["TransDate"]."',
            hora  :'" . $fila["TransDateTime"]."'
            }";
    if ($i != count($resulset) - 1) {
        $salida .= ",";
    }
    
    $total += $fila["TransAmt"];
}
$salida .= ",{total: $total}";

$salida .="]}";
$json = preg_replace("[\n|\r|\n\r]", "", utf8_encode($salida));   
$salida = "{success:true, string: ".json_encode($json)."}";
 
}else{
    $con = "SELECT count(*) AS TotalConsulta
         FROM transline T
         WHERE BusNo = $id_bus
         AND TransDate >= $dateStart
         AND TransDate <= $dateFinish    
        ";
   
    $salida = "{failure:true, $con}";
    
}

echo $salida;