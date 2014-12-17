<?php

//include('../../login/isLogin.php');
require_once('../../../dll/conect_tarjetero.php');

extract($_POST);
$consultaSql="";
$salida = "";
if($cbxTablas){
    $consultaSql="SELECT * FROM ".$cbxTablas." WHERE Fecha BETWEEN '$fechaInisT' AND  '$fechaFinsT' AND Hora BETWEEN '$horaInisT' AND '$horaFinsT'";
    consulta($consultaSql);
    $resulset = variasFilas();
    $cont = 0;

    //Si es la tabla tarjetas
    if($cbxTablas == "Tarjetas"){
        if(count($consultaSql)>0){
            $json = "{tabla: [";
            for ($i = 0; $i < count($resulset); $i++) {
                $fila = $resulset[$i];
                $a=str_pad ( $fila["CardNo"] ,16 ,"0", STR_PAD_LEFT );
                $json .= "{
                          cardno     :'".$a."',
                          idcliente  :'".$fila['idCliente']."',
                          tipotarifa :'".$fila['TipoTarifa']."',
                          tipotarjeta:'".$fila['TipoTarjeta']."',
                          fecha      :'".$fila['Fecha']."',   
                          hora       :'".$fila['Hora']."'
                       }";
                if ($i != count($resulset) - 1) {
                    $json .= ",";
                }
            }
           
            $json .="]}";
            $json = preg_replace("[\n|\r|\n\r]", "", utf8_encode($json));        
            $salida = "{success:true, string: ".json_encode($json)."}";
 
        }else{
             $salida = "{failure:true, $consultaSql - $consultaSql2 - $bus}";
        }
    }
    if($cbxTablas == "Blacklist"){
        if(count($consultaSql)>0){
             $json = "{tabla: [";
            for ($i = 0; $i < count($resulset); $i++) {
                $fila = $resulset[$i];
                $a=str_pad ( $fila["CardNo"] ,16 ,"0", STR_PAD_LEFT );
                $json .= "{
                          cardno     :'".$a."',
                          fecha      :'".$fila['Fecha']."',
                          hora       :'".$fila['Hora']."',
                          tipotarjeta:'".$fila['BlackType']."',
                          version    :'".$fila['Version']."'
                       }";
                if ($i != count($resulset) - 1) {
                    $json .= ",";
                }
            }
           
            $json .="]}";
            $json = preg_replace("[\n|\r|\n\r]", "", utf8_encode($json));        
            $salida = "{success:true, string: ".json_encode($json)."}";
        }else{
             $salida = "{failure:true, $consultaSql - $consultaSql2 - $bus}";
        }
    }
    if($cbxTablas == "Transline"){
        $consultaSql="SELECT * FROM ".$cbxTablas." WHERE TransDate BETWEEN '$fechaInisT' AND  '$fechaFinsT' AND TransDateTime BETWEEN '$horaInisT' AND '$horaFinsT'";
        consulta($consultaSql);
        $resulset = variasFilas();
        
        if(count($consultaSql)>0){
             $json = "{tabla: [";
            for ($i = 0; $i < count($resulset); $i++) {
                $fila = $resulset[$i];
                $a=str_pad ( $fila["CardNo"] ,16 ,"0", STR_PAD_LEFT );
                $json .= "{
                          merchno      :'".$fila['MerchNo']."',
                          lineno       :'".$fila['LineNo']."',
                          busno        :'".$fila['BusNo']."',
                          termno       :'".$fila['TermNo']."',
                          operno       :'".$fila['OperNo']."',   
                          transtype    :'".$fila['TransType']."',
                          transtamt    :'".$fila['TransAmt']."',
                          cardno       :'".$a."',
                          csn          :'".$fila['CSN']."',
                          cardtype     :'".$fila['CardType']."',
                          cardcount    :'".$fila['CardCount']."',
                          psamno       :'".$fila['PsamNo']."',
                          psamcount    :'".$fila['PsamCount']."',
                          transdatetime:'".$fila['TransDateTime']."',
                          cardbal      :'".$fila['CardBal']."',
                          recvdatetime :'".$fila['RecvDateTime']."',
                          tac          :'".$fila['TAC']."',
                          status       :'".$fila['Status']."',
                          idtransline  :'".$fila['idTransLine']."',
                          transdate    :'".$fila['TransDate']."',
                          ippublica    :'".$fila['IPPublica']."',
                          ipprivada    :'".$fila['IPPrivada']."'
                       }";
                if ($i != count($resulset) - 1) {
                    $json .= ",";
                }
            }
           
            $json .="]}";
            $json = preg_replace("[\n|\r|\n\r]", "", utf8_encode($json));        
            $salida = "{success:true, string: ".json_encode($json)."}";
        }else{
             $salida = "{failure:true, $consultaSql - $consultaSql2 - $bus}";
        }
    }
}else{
    $consultaSql="SELECT * FROM ";
}
   
echo $salida;