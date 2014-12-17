<?php

include('../../login/isLogin.php');
require_once('../../../dll/conect_tarjetero.php');

extract($_POST);

$consultaSql = " SELECT count(*) AS TotalConsulta".
        " FROM transline  "
        . "WHERE TransDate>='$fechaInis' "
        . "AND  TransDate<='$fechaFins' "
        . "AND TransDateTime>= '$horaInis' "
        . "AND TransDateTime<='$horaFins'"
        ."AND TransType= 'Debito'";
consulta($consultaSql);
$resulset = unicaFila();

if($resulset["TotalConsulta"] >= 1){


$consultaSql = "SELECT TransAmt".
        " FROM transline "
        . "WHERE TransDate>='$fechaInis' "
        . "AND  TransDate<='$fechaFins' "
        . "AND TransDateTime>= '$horaInis' "
        . "AND TransDateTime<='$horaFins'"
        ."AND TransType= 'Debito'";
        
consulta($consultaSql);
$resulset = variasFilas();  

$consultaSql1 = "SELECT TransAmt".
        " FROM transline "
        . "WHERE TransDate>='$fechaInis' "
        . "AND  TransDate<='$fechaFins' "
        . "AND TransDateTime>= '$horaInis' "
        . "AND TransDateTime<='$horaFins'"
        ."AND TransType= 'Recarga'";
consulta($consultaSql1);
$resulset1 = variasFilas();   
        $debito=0;
        for ($i = 0; $i < count($resulset); $i++) {
            $fila = $resulset[$i];
            $debito+=$fila["TransAmt"];
        }            
        $recarga=0;
        for ($i = 0; $i < count($resulset1); $i++) {
            $fila1 = $resulset1[$i];
            $recarga+=$fila1["TransAmt"];
        }            
     
        $json = "{transline: [";
        $json .= "{
                debito:'". $debito  . "',
                recarga:'". $recarga  . "'
            }";

        $json .="]}";
            
        $json = preg_replace("[\n|\r|\n\r]", "", utf8_encode($json));        

        $salida = "{success:true, string: ".json_encode($json)."}";
}else{
     
       $salida = "{failure:true, $consultaSql }";
}

echo $salida;