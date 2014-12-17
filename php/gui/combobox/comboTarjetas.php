<?php

require_once('../../../dll/conect_tarjetero.php');

extract($_GET);

$salida = "{failure:true}";
$aux = $idTarjeta;

if ($idTarjeta=='Normal') {
    $idTarjeta=0;
}else{
    if ($idTarjeta=='Estudiantes') {
        $idTarjeta=1;
    }else{
        if ($idTarjeta=='Tercera Edad') {
            $idTarjeta=2;
        }else{
            if ($idTarjeta=='Discapacitados') {
                $idTarjeta=3;
            }else{
                if ($idTarjeta=='Chofer') {
                    $idTarjeta="'0D'";
                }
            }
        }
    }
}

$cn=str_pad ( $idTarjeta ,16 ,"0", STR_PAD_LEFT );

$consultaSql = 
	"SELECT tra.CardNo, tra.CardType
         FROM transline tra, tarjetas t 
         WHERE tra.transtype ='Creacion' 
            AND tra.CardType=$idTarjeta
            AND t.CardNo = tra.CardNo
            AND t.idCliente = null
            OR t.idCliente = ' '
";

consulta($consultaSql);
$resulset = variasFilas();

if(count($resulset)>0){
$salida = "{'tarjetas': [";

for ($i = 0; $i < count($resulset); $i++) {
    $fila = $resulset[$i];
    $salida .= "{
            'tarj':" . $fila["CardNo"] . ",
            'tipo':'" . $fila['CardType']. "'
        }";
    if ($i != count($resulset) - 1) {
        $salida .= ",";
    }
}

$salida .="]}";
}else{
    $salida = "{failure:true, $consultaSql, $aux}";
}
echo utf8_encode($salida);
?>
