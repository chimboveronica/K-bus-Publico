<?php

include('../../login/isLogin.php');
include ('../../../dll/config.php');

extract($_POST);

if (!$mysqli = getConectionTarjeteroDb()) {
    echo "{failure: true, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
}
else{

$consultaSql = "SELECT * FROM transline "
        . "WHERE TransDate>='$fechaInisTra' "
        . "AND  TransDate<='$fechaFinsTra' "
        . "AND TransDateTime>= '$horaInisTra' "
        . "AND TransDateTime<='$horaFinsTra'"
        . "ORDER BY TransDate DESC, TransDateTime DESC";

$result = $mysqli->query($consultaSql);
$mysqli->close();

if ($result->num_rows >0) {
    
        $json = "{transline: [";

        while ($myrow = $result->fetch_assoc()) {
            //Para el tipo de tarjeta
            $tipTarjeta = "";
            
            if($myrow["CardType"] == "00"){
                $tipTarjeta = $myrow["CardType"].":NORMAL";
            }else{
                if($myrow["CardType"] == "01"){
                    $tipTarjeta = $myrow["CardType"].":ESTUDIANTE";
                }else{
                    if($myrow["CardType"] == "02"){
                        $tipTarjeta = $myrow["CardType"].":TERCERA EDAD";
                    }else{
                        if($myrow["CardType"] == "0A"){
                            $tipTarjeta = $myrow["CardType"].":VIP";
                        }else{
                            if($myrow["CardType"] == "0B"){
                                $tipTarjeta = $myrow["CardType"].":COLECTORDATOS";
                            }else{
                                if($myrow["CardType"] == "0C"){
                                    $tipTarjeta = $myrow["CardType"].":CONFIGURACION";
                                }else{
                                    if($myrow["CardType"] == "0D"){
                                        $tipTarjeta = $myrow["CardType"].":CONDUCTOR";
                                    }else{
                                         $tipTarjeta = $myrow["CardType"].":NO RECONOCIDA";
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $json .= "{
                merchno:'" . $myrow["MerchNo"] . "',
                lineno:'" . $myrow["LineNo"] . "',
                busno:'" . $myrow["BusNo"] . "',
                termno:'" . $myrow["TermNo"] . "',
                operno:'" . $myrow["OperNo"] . "',
                transtype:'" . $myrow["TransType"] . "',
                transamt:'" . $myrow["TransAmt"] . "',
                cardno:'". $myrow["CardNo"]  . "',
                csn:'". $myrow["CSN"]  . "',
                cardtype:'". $tipTarjeta  . "',
                cardcount:'". $myrow["CardCount"]  . "',
                psamno:'". $myrow["PsamNo"] . "',
                psamcount:'". $myrow["PsamCount"] . "',
                transdatetime:'". $myrow["TransDateTime"]  . "',
                transdate:'". $myrow["TransDate"]  . "',
                cardbal:'". $myrow["CardBal"]  . "',
                recvdatetime:'". $myrow["RecvDateTime"]  . "',
                tac:'". $myrow["TAC"]  . "',
                status:'". $myrow["Status"] . "',
                idtransline:'". $myrow["idTransLine"] . "',
                ipprivada:'". $myrow["IPPrivada"] . "',
                ippublica:'". $myrow["IPPublica"] . "',
            }";

            if ($i != count($resulset) - 1) {
                $json .= ",";
            }
        }

        $json .="]}";
            
        $json = preg_replace("[\n|\r|\n\r]", "", utf8_encode($json));        

        $salida = "{success:true, string: ".json_encode($json)."}";
}else{
     $salida = "{failure:true}";
}

echo $salida;
}