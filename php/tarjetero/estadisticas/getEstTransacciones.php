<?php

require_once('../../../dll/conect_tarjetero.php');

extract($_GET);
extract($_POST);

$salida = "{failure:true}";

//if(isset($anio)){
    
//Array para guardar los datos de los meses
$meses = array("enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre");
$trans = array();
$dias = array();
$dia ="";
$prueba="datos:";
for($i = 0; $i < 12; $i++){$trans[$i] = 0;}

if(isset($tipo) && isset($mes) && isset($anio)){
    //Calcular dias de cada mes para todos los anios incluso bisiesto
    $first_of_month = mktime (0,0,0, $mes, 1, $anio); 
    $maxdays = date('t', $first_of_month);
    
        //Llenamos todo el array con 0
        for($k = 1; $k <= $maxdays; $k++){$dias[$k] = 0;}
        if($mes<10){
            $mes = "0".$mes;
        }
        $consultaSql = "SELECT TransDate FROM transline WHERE TransDate LIKE '".$anio."-".$mes."-%'";
        consulta($consultaSql);
        $resulset = variasFilas();

        //Para recorrer el numero de resultados de la consulta
        for ($i = 0; $i < count($resulset); $i++) {
            $fila = $resulset[$i];
            $dia = intval(substr($fila["TransDate"],8,2));
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
                    tran :" .$dias[$j] ."
                    }";
            $c++;
            if ($j != count($dias)) {
                $salida .= ",";
            }
        }
        $salida .="]}";
}else{
    
    if (isset($tipo)) {
        if($tipo=="Todas" || $tipo=="null") {
            $consultaSql = 
            "SELECT TransDate FROM transline 
             WHERE TransDate LIKE '%$anio%'
             ";
         consulta($consultaSql);
        } else{
            $consultaSql = 
            "SELECT TransDate FROM transline 
             WHERE TransDate LIKE '%$anio%' and TransType LIKE '%$tipo%'
            ";
            consulta($consultaSql);
        }

    }else{
        $consultaSql = 
            "SELECT TransDate FROM transline 
             WHERE TransDate LIKE '%$anio%'
            ";
            consulta($consultaSql);
    }

$resulset = variasFilas();


//Extraemos el numero de transacciones por cada mes en ese anio
for ($i = 0; $i < count($resulset); $i++) {
    $fila = $resulset[$i];
    $mes = 0;
    $mes = substr($fila["TransDate"],5,2);
    switch ($mes){
        case "01":
            $val = $trans[0];
            $trans[0] = $val + 1;
            break;
        case "02":
            $val = $trans[1];
            $trans[1] = $val + 1;
            break;
        case "03":
            $val = $trans[2];
            $trans[2] = $val + 1;
            break;
        case "04":
            $val = $trans[3];
            $trans[3] = $val + 1;
            break;
        case "05":
            $val = $trans[4];
            $trans[4] = $val + 1;
            break;
        case "06":
            $val = $trans[5];
            $trans[5] = $val + 1;
            break;
        case "07":
            $val = $trans[6];
            $trans[6] = $val + 1;
            break;
        case "08":
            $val = $trans[7];
            $trans[7] = $val + 1;
            break;
        case "09":
            $val = $trans[8];
            $trans[8] = $val + 1;
            break;
        case "10":
            $val = $trans[9];
            $trans[9] = $val + 1;
            break;
        case "11":
            $val = $trans[10];
            $trans[10] = $val + 1;
            break;
        case "12":
            $val = $trans[11];
            $trans[11] = $val + 1;
            break;
        default:
            break;
    }
    $val = 0;
}

//Asignamos los datos al json
$salida = "{meses: [";
$c = 1;
for($j = 0; $j<count($meses); $j++){
    $salida .= "{
            id  :".($c*10).",
            mes :'" .$meses[$j] ."',
            tran:" .$trans[$j] ."
            }";
    $c++;
    if ($j != count($meses) - 1) {
        $salida .= ",";
    }
}
$salida .="]}";
}

echo utf8_encode($salida);
//
//$salida = "{meses:   [ {id:0,mes:'ene',tran:10},"
//                    . "{id:10,mes:'feb',tran:40},"
//                    . "{id:20,mes:'mar',tran:50},"
//                    . "{id:30,mes:'abr',tran:70},"
//                    . "{id:40,mes:'may',tran:100},"
//                    . "{id:50,mes:'jun',tran:20},"
//                    . "{id:60,mes:'jul',tran:30},"
//                    . "{id:70,mes:'ago',tran:50},"
//                    . "{id:80,mes:'sep',tran:60},"
//                    . "{id:90,mes:'oct',tran:20},"
//                    . "{id:100,mes:'nov',tran:10},"
//                    . "{id:40,mes:'dic',tran:100}]}";
//
//$salida = "{failure:true}";
       
//}else{
//    echo utf8_encode($salida);
//}


function diaSemana($ano,$mes,$dia)
{
	// 0->domingo	 | 6->sabado
	$dia= date("w",mktime(0, 0, 0, $mes, $dia, $ano));
        
        switch($dia){
            case "0":
                return "Domingo";
            case "1":
                return "Lunes";
            case "2":
                return "Martes";
            case "3":
                return "Miercoles";
            case "4":
                return "Jueves";
            case "5":
                return "Viernes";
            case "6":
                return "Sabado";
        }
        
}

?>
