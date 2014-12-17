<?php

include ('../../../dll/config.php');

extract($_POST);

//Total de las diferente saldos
$total_bus=0;
$total_punto_venta=0;
$total_ruta=0;

if (!$mysqli = getConectionTarjeteroDb()) {
    echo "{failure: true, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
}
else{
    $consultaSql = "SELECT BusNo, sum(TransAmt) AS TotalDia FROM transline WHERE TransDate BETWEEN '$fechaInisR' AND '$fechaFinsR'";
    $result = $mysqli->query($consultaSql);
    $mysqli->close();
    $total = $result->num_rows;  
    if ($result->num_rows > 0) {

    $json = "{recaudo: [";
        while ($myrow = $result->fetch_assoc()) {
            $total_bus+=$myrow["TotalDia"];
            $json .= "{"
                ."titulo    :'BUS-".$myrow["BusNo"]."',"
                ."saldo     :".$myrow["TotalDia"].","
                ."tipo_saldo:'BUS'}";
            $total--;
            if ($total != 0) {
                $json .= ",";
            }
        }
        //Esto como comentario hasta q se tengan datos
         $json .= ",{"
                ."titulo    :'BUS-1111',"
                ."saldo     :250.45,"
                ."tipo_saldo:'BUS'}";
         $total_bus+=250.45;
         
         $json .= ",{"
                ."titulo    :'PUNTO-VENTA-LAS-PITAS',"
                ."saldo     :100.45,"
                ."tipo_saldo:'VENTA'}";
         $total_punto_venta+=100.45;
         
         $json .= ",{"
                ."titulo    :'PUNTO-VENTA-LA-BANDA',"
                ."saldo     :50.45,"
                ."tipo_saldo:'VENTA'}";
         $total_punto_venta+=50.45;
         
         $json .= ",{"
                ."titulo    :'TOTAL BUS',"
                ."saldo     :$total_bus,"
                ."tipo_saldo:'TOTAL'}";
         
         $json .= ",{"
                ."titulo    :'TOTAL PARADA',"
                ."saldo     :$total_ruta,"
                ."tipo_saldo:'TOTAL'}";
         
         $json .= ",{"
                ."titulo    :'TOTAL PUNTO VENTA',"
                ."saldo     :$total_punto_venta,"
                ."tipo_saldo:'TOTAL'}";
         
         $total_recaudo = $total_bus+$total_punto_venta+$total_ruta;
         
          $json .= ",{"
                ."titulo    :'TOTAL RECAUDO',"
                ."saldo     :$total_recaudo,"
                ."tipo_saldo:'TOTAL'}";
          
        
        $json.="]}";  
        $json = preg_replace("[\n|\r|\n\r]", "", utf8_encode($json));
        $salida = "{success:true, string: " . json_encode($json) . "}";
    }else{
        //Para fail de salida
        $salida = "{success:false, msg: 'No hay datos que obtener'}";
    }
}
echo $salida;
