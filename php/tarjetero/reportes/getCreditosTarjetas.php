<?php

include ('../../../dll/config.php');

extract($_POST);

if (!$mysqli = getConectionTarjeteroDb()) {
    echo "{failure: true, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
}
else{
    $consultaSql = "SELECT * FROM Utranstarjeta ut, clientes cl, tarjetas t 
                                WHERE ut.CardNo = t.CardNo
                                AND cl.cedula = t.idCliente";
    $result = $mysqli->query($consultaSql);
    $mysqli->close();
      
    if ($result->num_rows > 0) {

    $json = "{creditos: [";

        while ($myrow = $result->fetch_assoc()) {
            //Para sacar el total de creditos de la tarejta
            $creditos = 0;
            if($myrow["TipoTarifa"]=="Normal"){
                $creditos = $myrow["Saldo"]."N"; 
            }
            
            if($myrow["TipoTarifa"]=="Estudiantes" || $myrow["TipoTarifa"]=="Tercera_edad" || $myrow["TipoTarifa"]=="Discapacitados"){
                $creditos = $myrow["Saldo"]."E"; 
            }
            $cn=str_pad ( $myrow["CardNo"] ,16 ,"0", STR_PAD_LEFT );
            $json .= "{"
                ."persona:'" .utf8_encode($myrow["apellidos"]." ".$myrow["nombres"])."',"
                ."cardno     :'".$cn . "',"
                ."fecha      :'".$myrow["Fecha"] . "',"
                ."hora       :'".$myrow["Hora"] . "',"
                ."tipotarifa :'".$myrow["TipoTarifa"] . "',"
                ."creditos   :'".$creditos."',"
                ."cedula   :'".$myrow["cedula"]."',"
                ."correo     :'".$myrow["correo"]."'},";
        }
        $json.="]}";
        echo $json;
            
    }else{
        //Para fail de salida
        echo "{success:false, msg: 'No hay datos que obtener'}";
    }
}
