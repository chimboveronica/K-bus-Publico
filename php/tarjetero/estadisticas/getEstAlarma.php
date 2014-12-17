<?php

include('../../login/isLogin.php');
include ('../../../dll/config.php');

extract($_POST);
extract($_POST);
if (!$mysqli = getConectionTarjeteroDb()) {
    echo "{failure: true, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {


//Array para guardar los datos de los meses
$meses = array("enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre");
$conta = array();
$consultaSql = "";
$dias = array();
$dia ="";
for($i = 0; $i < 12; $i++){$conta[$i] = 0;}

$salida = "{failure:true}";
 if(isset($anio) && isset($mes)){
       if($mes <10){
           $mes = "0".$mes;
       }
       $consultaSql = "SELECT Fecha_Hora,cantidad_alarma FROM contador_alarmas
         WHERE Fecha_Hora LIKE '$anio-$mes-%'    
        ";
       consulta($consultaSql);
       $resulset = variasFilas();
       
        //Calcular dias de cada mes para todos los anios incluso bisiesto
        $first_of_month = mktime (0,0,0, $mes, 1, $anio); 
        $maxdays = date('t', $first_of_month);
    
        //Llenamos todo el array con 0
        for($k = 1; $k <= $maxdays; $k++){$dias[$k] = 0;}

        //Para recorrer el numero de resultados de la consulta
        for ($i = 0; $i < count($resulset); $i++) {
            $fila = $resulset[$i];
            $dia = intval(substr($fila["Fecha_Hora"],8,2));
            //Para compara los datos con cada dia del mes
            for ($j = 1; $j <= count($dias); $j++) {
                if($dia == $j){
                    $dias[$j] = $dias[$j] + 1;
                }
            }
        }
    
        //Asignamos los datos al json
        $salida = "{meses: [";
        $c = 1;
        for($j = 1; $j<=count($dias); $j++){
            $salida .= "{
                    id   :".($c).",
                    fecha:'" .$anio.'-'.$mes.'-'.$j."',
                    alarmas :" .$dias[$j] ."
                    }";
            $c++;
            if ($j != count($dias)) {
                $salida .= ",";
            }
        }
        $salida .="]}";
       
       
    }else{
$consultaSql = 
	"SELECT Fecha_Hora,cantidad_alarma FROM contador_alarmas
         WHERE Fecha_Hora LIKE '%$anio%'    
";

consulta($consultaSql);
$resulset = variasFilas();


//Extraemos el numero de blackacciones por cada mes en ese anio
for ($i = 0; $i < count($resulset); $i++) {
    $fila = $resulset[$i];
    $mes = 0;
    $mes = substr($fila["Fecha_Hora"],5,2);
    switch ($mes){
        case "01":
            $val = $conta[0];
            $conta[0] = $val + $fila["cantidad_alarma"];
            break;
        case "02":
            $val = $conta[1];
            $conta[1] = $val + $fila["cantidad_alarma"];
            break;
        case "03":
            $val = $conta[2];
            $conta[2] = $val + $fila["cantidad_alarma"];
            break;
        case "04":
            $val = $conta[3];
            $conta[3] = $val + $fila["cantidad_alarma"];
            break;
        case "05":
            $val = $conta[4];
            $conta[4] = $val + $fila["cantidad_alarma"];
            break;
        case "06":
            $val = $conta[5];
            $conta[5] = $val + $fila["cantidad_alarma"];
            break;
        case "07":
            $val = $conta[6];
            $conta[6] = $val + $fila["cantidad_alarma"];
            break;
        case "08":
            $val = $conta[7];
            $conta[7] = $val + $fila["cantidad_alarma"];
            break;
        case "09":
            $val = $conta[8];
            $conta[8] = $val + $fila["cantidad_alarma"];
            break;
        case "10":
            $val = $conta[9];
            $conta[9] = $val + $fila["cantidad_alarma"];
            break;
        case "11":
            $val = $conta[10];
            $conta[10] = $val + $fila["cantidad_alarma"];
            break;
        case "12":
            $val = $conta[11];
            $conta[11] = $val + $fila["cantidad_alarma"];
            break;
        default:
            break;
    }
    $val = 0;
}

//Asignamos los datos al json
$salida = "{meses: [";

for($j = 0; $j<count($meses); $j++){
    $salida .= "{
            id  :".$j.",
            mes :'" .$meses[$j] ."',
            conta:" .$conta[$j] ."
            }";
    if ($j != count($meses) - 1) {
        $salida .= ",";
    }
}
$salida .="]}";
    }
echo utf8_encode($salida);

}?>