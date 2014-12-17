<?php

//include('../../login/isLogin.php');
require_once('../../../dll/conect_tarjetero.php');

extract($_POST);

$ct;
if(isset($_POST["bus"])){
    if($_POST["bus"] == "1"){
        $ct = "cedula";
    }else{
        $ct = "tarjeta";
    }
}
$consultaSql="";
$consultaSql2="";
$bus = $_POST["bus"];

//Si enviamos la cedula
if($bus == 1){
    $consultaSql = "SELECT  count(idCliente) AS TotalConsulta FROM tarjetas WHERE idCliente='".$_POST['txt']."'";
    $consultaSql2 = "SELECT * FROM tarjetas WHERE idCliente='".$_POST['txt']."'";
}else{
    //Si enviamos el acardno
    $consultaSql = "SELECT count(CardNo) AS TotalConsulta FROM tarjetas WHERE CardNo='".$_POST['txt']."'";
    $consultaSql2 = "SELECT * FROM tarjetas WHERE CardNo='".$_POST['txt']."'";
}


consulta($consultaSql);
$resulset = unicaFila();

consulta($consultaSql2);
$resulset2 = unicaFila();

$cedula = $resulset2["idCliente"];
if($resulset["TotalConsulta"]>=1){
    
$consultaSql = "SELECT CardNo FROM tarjetas WHERE idCliente='".$cedula."'";
consulta($consultaSql);
$resulset = unicaFila();
$cardnumber=$resulset["CardNo"];


$consultaSqlPerson = "SELECT * FROM clientes WHERE cedula = '".$cedula."'";
consulta($consultaSqlPerson);
$resulsetN = unicaFila();
$nombres=$resulsetN["nombres"];
$apellidos=$resulsetN["apellidos"];

$consultaSql2 = "SELECT * FROM utranstarjeta WHERE CardNo='".$cardnumber."'";
consulta($consultaSql2);
$resulset3 = unicaFila();

$cn=str_pad ( $cardnumber ,16 ,"0", STR_PAD_LEFT );
$consultaSql3 = "SELECT * FROM transline WHERE CardNo = '".$cn."' AND TransDate BETWEEN '$fechaInisT' AND '$fechaFinsT' ORDER BY CardBal DESC";
consulta($consultaSql3);
$resulset4 = variasFilas();

if(count($resulset4) > 0){
        $json = "{ultrans: [";
            $fila = $resulset3;
            $a=str_pad ( $fila["CardNo"] ,16 ,"0", STR_PAD_LEFT );
            for ($i = 0; $i < count($resulset4); $i++) {
                $fila2 = $resulset4[$i];
                $json .= "{
                      nombres  :'".$nombres."',
                      apellidos:'".$apellidos."',
                      carno    :'".$a."',
                      saldo    :'".$fila2["CardBal"]."',
                      transtype:'".$fila2["TransType"]."',   
                      transamt :'".$fila2["TransAmt"]."', 
                      fecha    :'".$fila2["TransDate"]."',
                      hora     :'".$fila2["TransDateTime"]."'
                   }";
                if ($i != count($resulset4) - 1) {
                    $json .= ",";
                }
             }
        $json .="]}";
        $json = preg_replace("[\n|\r|\n\r]", "", utf8_encode($json));        
       // $salida = "{success:true, ".$consultaSql."-".$consultaSql2."-".$consultaSql3."}";
        $salida = "{success:true, string: ".json_encode($json)."}";
}else{
     $salida = "{failure:true, $consultaSql - $consultaSql2 - $bus}";
}
}else{
     $salida = "{failure:true, $consultaSql - $consultaSql2 - $bus}";
}    
echo $salida;