<?php

//include('../../login/isLogin.php');
include ('../../../dll/config.php');

extract($_POST);

if (!$mysqli = getConectionTarjeteroDb()) {
    echo "{failure: true, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
}
else{

$consultaSql = "SELECT t.CardNo,t.TransDate,t.TransDateTime,t.CardBal,t.TransAmt,t.CardType,p.idCliente".
        " FROM transline t, tarjetas p "
        . "WHERE t.CardNo= p.cardno "
        ."AND t.TransType= 'Recarga' "
        ."and date(t.TransDate) between ? and ? "
        //."AND  t.TransDate>= (?) "
        //. "AND  t.TransDate<= (?)"
        . "AND t.TransDateTime>= (?) "
        . "AND t.TransDateTime<= (?) ";
$stmt = $mysqli->prepare($consultaSql);
if ($stmt) {
        $stmt->bind_param("ssss",$fechaInisR, $fechaFinsR, $horaInisR, $horaFinsR);
          /* ejecutar la consulta */
        $stmt->execute();

        $result = $stmt->get_result();

        $stmt->close();
        $mysqli->close();

        if ($result->num_rows > 0) {
        $json = "data: [";

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

            $json .= "{"
                ."transamt:'" . $myrow["TransAmt"]. "',"
                ."cardno:'". $myrow["CardNo"]  . "',"
                ."cardtype:'". utf8_encode($tipTarjeta)  . "',"
                ."transdatetime:'". $myrow["TransDateTime"]."',"
                ."transdate:'". $myrow["TransDate"]."',"
                ."cardbal:'". $myrow["CardBal"]."',"
                ."cedula:'". utf8_encode($myrow["idCliente"])."'},";
        }

        $json.="]";
        echo "{success: true, $json}";
            
    }else{
        //Para fail de salida
        echo "{success:false, msg: 'No hay datos que obtener'}";
    }
}
 else {
        echo "{failure: true, message: 'Problemas en la Construcción de la Consulta.'}";
    }
}