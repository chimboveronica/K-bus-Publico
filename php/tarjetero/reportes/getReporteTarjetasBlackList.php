<?php

include('../../login/isLogin.php');
include ('../../../dll/config.php');

extract($_POST);

if (!$mysqli = getConectionTarjeteroDb()) {
    echo "{failure: true, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {

$consultaSql = "";

//Preguntamos si  $all esta enviado y creamos la consulta para todos los datos
      if(isset($all)){
          if($all == "todos"){
               $consultaSql = "SELECT * FROM blacklist";
          }else{
              if($all == "hoy"){
                  $fechaHoy = "2014-03-19";
                  $consultaSql = "SELECT * FROM blacklist WHERE Fecha = '".$fechaHoy."'";
              }
          }

      }else{
         $consultaSql = "SELECT * FROM blacklist WHERE Fecha >= '".$fechaIni."' AND Fecha <= '".$fechaFin."'".
                        " AND Hora >= '".$horaIni."' AND Hora <= '".$horaFin."'";
      }
    
    $result = $mysqli->query($consultaSql);
    $mysqli->close();
    if ($result->num_rows >0) {
    $json = "{blacklist: [";

            while ($myrow = $result->fetch_assoc()) {            
            if($myrow["BlackType"] == 0){
                $bt = "Perdida";
            }
            if($myrow["BlackType"] == 1){
                $bt = "Robo";
            }
            if($myrow["BlackType"] == 2){
                $bt = "Deterioro";
            }
            $cn=str_pad ( $myrow["CardNo"] ,16 ,"0", STR_PAD_LEFT );
            $json .= "{
                cardno   :'" . $cn . "',
                fecha  :'" . $myrow["Fecha"] . "',
                hora  :'" . $myrow["Hora"] . "',
                blacktype:'" . $bt . "'
            },";
        }
        $json .="]}";
        //$json = preg_replace("[\n|\r|\n\r]", "", utf8_encode($json));        
        $salida = "{success:true, string: ".json_encode($json)."}";
    } else {
        $salida = "{failure:true}";
    }

echo $salida;
}