<?php

include('../../login/isLogin.php');
include ('../../../dll/config.php');

extract($_POST);

if (!$mysqli = getConectionTarjeteroDb()) {
    echo "{failure: true, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
}
else{
$ct;

$consultaSql="";
$tipo = $_POST["tipo"];

//Sienviamos el id_bus
$consultaSql = "SELECT  * FROM equipos WHERE tipo = '$tipo'";

 $result = $mysqli->query($consultaSql);
    $mysqli->close();
      
    if ($result->num_rows > 0) {
        $json = "{equipos: [";
              while ($myrow = $result->fetch_assoc()) {
                
                $json .= "{"
                      ."idequipo:'".$myrow["idequipo"]."',"   
                      ."tipo    :'".$myrow["tipo"]."'," 
                      ."marca   :'".$myrow["marca"]."',"
                      ."modelo  :'".$myrow["modelo"]."',"
                      ."valor   :'".$myrow["valor"]."',"
                      ."fechaingreso:'".$myrow["fecha_ingreso"]."'    
                   },";
                
             }
        $json .="]}";
        $json = preg_replace("[\n|\r|\n\r]", "", utf8_encode($json));        
        $salida = "{success:true, string: ".json_encode($json)."}";
}else{
     $salida = "{failure:true, $consultaSql - $consultaSql2 - $bus}";
}    
echo $salida;
}