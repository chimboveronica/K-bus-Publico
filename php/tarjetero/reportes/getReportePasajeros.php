<?php

include('../../../login/isLogin.php');
include ('../../../dll/config.php');
extract($_POST);
if (!$mysqli = getConectionTarjeteroDb()) {
    echo "{failure: true, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
}
else{
$consultaSql = " SELECT count(*) AS TotalConsulta ,t.CardNo,t.TransDate,t.TransDateTime,t.CardBal,t.TransAmt,t.CardType,p.idCliente".
        " FROM transline t, tarjetas p "
        . "WHERE t.TransDate>='$fechaInisP' "
        . "AND  t.TransDate<='$fechaFinsP' "
        . "AND t.TransDateTime>= '$horaInisP' "
        . "AND t.TransDateTime<='$horaFinsP'"
        ."AND t.TransType= 'Debito'"
        ."AND t.CardNo= p.cardno";
$result = $mysqli->query($consultaSql);
$mysqli->close();

if ($result->num_rows >0) {

        $json = "{transline: [";

        while ($myrow = $result->fetch_assoc()) {
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
                transamt:'" . $myrow["TransAmt"] . "',
                cardno:'". $myrow["CardNo"]  . "',
                cardtype:'". $tipTarjeta  . "',
                transdatetime:'". $myrow["TransDateTime"]  . "',
                transdate:'". $myrow["TransDate"]  . "',
                busno:'". $myrow["BusNo"]  . "',
                lineno:'". $myrow["LineNo"]  . "',
                cardbal:'". $myrow["CardBal"]  . "',
                cedula:'". $myrow["idCliente"]  . "'
            },";
        }

        $json .="]}";
            
        $salida = "{success:true, string: ".json_encode($json)."}";
}else{
     
       $salida = "{failure:true, $consultaSql }";
}

echo $salida;
}