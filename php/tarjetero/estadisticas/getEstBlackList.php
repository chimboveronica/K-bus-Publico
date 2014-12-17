<?php

require_once('../../../dll/conect_tarjetero.php');

extract($_GET);
extract($_POST);

//Array para guardar los datos de los meses
$meses = array("enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre");
$black = array();
$consultaSql = "";
$dias = array();
$dia ="";
for($i = 0; $i < 12; $i++){$black[$i] = 0;}

$salida = "{failure:true}";
    if(isset($anio) && isset($mes)){
       if($mes <10){
           $mes = "0".$mes;
       }
       $consultaSql = "SELECT Fecha FROM blacklist WHERE Fecha LIKE '$anio-$mes-%'";
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
            $dia = intval(substr($fila["Fecha"],8,2));
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
                    black :" .$dias[$j] ."
                    }";
            $c++;
            if ($j != count($dias)) {
                $salida .= ",";
            }
        }
        $salida .="]}";
       
       
    }else{
        if(isset($anio)){
            $consultaSql = "SELECT Fecha FROM blacklist WHERE Fecha LIKE '%$anio%'";
            consulta($consultaSql);
            $resulset = variasFilas();
            for ($i = 0; $i < count($resulset); $i++) {
                $fila = $resulset[$i];
                $mes = 0;
                $mes = substr($fila["Fecha"],5,2);
                switch ($mes){
                    case "01":
                        $val = $black[0];
                        $black[0] = $val + 1;
                        break;
                    case "02":
                        $val = $black[1];
                        $black[1] = $val + 1;
                        break;
                    case "03":
                        $val = $black[2];
                        $black[2] = $val + 1;
                        break;
                    case "04":
                        $val = $black[3];
                        $black[3] = $val + 1;
                        break;
                    case "05":
                        $val = $black[4];
                        $black[4] = $val + 1;
                        break;
                    case "06":
                        $val = $black[5];
                        $black[5] = $val + 1;
                        break;
                    case "07":
                        $val = $black[6];
                        $black[6] = $val + 1;
                        break;
                    case "08":
                        $val = $black[7];
                        $black[7] = $val + 1;
                        break;
                    case "09":
                        $val = $black[8];
                        $black[8] = $val + 1;
                        break;
                    case "10":
                        $val = $black[9];
                        $black[9] = $val + 1;
                        break;
                    case "11":
                        $val = $black[10];
                        $black[10] = $val + 1;
                        break;
                    case "12":
                        $val = $black[11];
                        $black[11] = $val + 1;
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
                        black:" .$black[$j] ."
                        }";
                if ($j != count($meses) - 1) {
                    $salida .= ",";
                }
            }
            $salida .="]}";
            
        }
    }


//Extraemos el numero de blackacciones por cada mes en ese anio





echo utf8_encode($salida);




?>