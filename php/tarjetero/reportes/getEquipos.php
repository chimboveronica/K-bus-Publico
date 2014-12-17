<?php

include ('../../../dll/config.php');

extract($_POST);

if (!$mysqli = getConectionTarjeteroDb()) {
    echo "{failure: true, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
}
 else {
$ct;
if(isset($_POST["bus"])){
    if($_POST["bus"] == "1"){
        $ct = "id_equipo";
    }else{
        $ct = "id_bus";
    }
}
$consultaSql="";
$consultaSql2="";
$bus = $_POST["bus"];

if($bus == 1){
    $consultaSql = "SELECT  count(id_equipo) AS TotalConsulta FROM equipos_buses WHERE id_equipo='".$_POST['txt']."'";
    $consultaSql2 = "SELECT * FROM equipos_buses WHERE id_equipo='".$_POST['txt']."'";
}else{
    //Sienviamos el id_bus
    $consultaSql = "SELECT  count(id_equipo) AS TotalConsulta FROM equipos_buses WHERE id_bus='".$_POST['txt']."'";
    $consultaSql2 = "SELECT * FROM equipos_buses WHERE id_bus='".$_POST['txt']."'";
}

$result = $mysqli->query($consultaSql);
$result2 = $mysqli->query($consultaSql2);
$mysqli->close();
if ($result->num_rows > 0) {
    
        $json = "{cequipos: [";
         while ($myrow = $result2->fetch_assoc()) { 
                $json .= "{"
                      ."id_bus    :'".$myrow["id_bus"]."',"
                      ."id_equipo:'".$myrow["id_equipo"]."',"   
                      ."serie :'".$myrow["serie"]."',"
                      ."fecha    :'".$myrow["fecha_registro"]."',"
                      ."estado     :'".$myrow["estado"]."',"
                      ."id_esp_equipo:'".$myrow["id_esp_equipo"]."'},";
              }                   
       $json.="]}";
       echo "{success:true, string: ".json_encode($json)."}";
}else{
    echo "{success:false, msg: 'No hay datos que obtener'}";
}    

 }