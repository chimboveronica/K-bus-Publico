<?php

require_once('../../../dll/conect.php');

extract($_GET);
extract($_POST);

//Array para guardar los datos de los meses
$meses = array("ene","feb","mar","abr","may","jun","jul","ago","sep","oct","nov","dic");
$contador = array();
$consultaSql = "";
$dias = array();
$dia ="";
for($i = 0; $i < 12; $i++){$contador[$i] = 0;}
$salida = "{failure:true}";
if(isset($anio) && isset($mes)){
       if($mes <10){
           $mes = "0".$mes;
       }
       $consultaSql ="SELECT Fecha_Hora,entradas FROM contador_pasajeros
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
                    personas:" .$dias[$j] ."
                    }";
            $c++;
            if ($j != count($dias)) {
                $salida .= ",";
            }
        }
        $salida .="]}";
       
       
    }else{
$consultaSql = 
	"SELECT Fecha_Hora,entradas FROM contador_pasajeros
         WHERE Fecha_Hora LIKE '".$anio."%'    
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
            $val = $contador[0];
            $contador[0] = $val + $fila["entradas"];
            break;
        case "02":
            $val = $contador[1];
            $contador[1] = $val + $fila["entradas"];
            break;
        case "03":
            $val = $contador[2];
            $contador[2] = $val + $fila["entradas"];
            break;
        case "04":
            $val = $contador[3];
            $contador[3] = $val + $fila["entradas"];
            break;
        case "05":
            $val = $contador[4];
            $contador[4] = $val + $fila["entradas"];
            break;
        case "06":
            $val = $contador[5];
            $contador[5] = $val + $fila["entradas"];
            break;
        case "07":
            $val = $contador[6];
            $contador[6] = $val + $fila["entradas"];
            break;
        case "08":
            $val = $contador[7];
            $contador[7] = $val + $fila["entradas"];
            break;
        case "09":
            $val = $contador[8];
            $contador[8] = $val + $fila["entradas"];
            break;
        case "10":
            $val = $contador[9];
            $contador[9] = $val + $fila["entradas"];
            break;
        case "11":
            $val = $contador[10];
            $contador[10] = $val + $fila["entradas"];
            break;
        case "12":
            $val = $contador[11];
            $contador[11] = $val + $fila["entradas"];
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
            conta:" .$contador[$j] ."
            }";
    if ($j != count($meses) - 1) {
        $salida .= ",";
    }
}
$salida .="]}";
}
echo utf8_encode($salida);

?>