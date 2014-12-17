<?php

require_once('../../../dll/conect.php');
include ('../../../dll/config.php');


extract($_GET);
extract($_POST);

//Array para guardar los datos de los meses
$meses = array("ene","feb","mar","abr","may","jun","jul","ago","sep","oct","nov","dic");
$contador = array();
$utilizados = array();
$consultaSql = "";
$dia ="";
$mes =0;
for($i = 0; $i < 12; $i++){$contador[$i] = 0;}
for($j = 0; $j < 12; $j++){$utilizados[$j] = 0;}
$salida = "{failure:true}";
if(isset($anio)){



if (!$mysqli = getConectionTarjeteroDb()) {
    echo "{failure: true, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
}
else{
    $consultaSql = 
	"SELECT TransDate, TransAmt, TransType FROM transline
         WHERE TransType='Recarga'
         AND TransDate LIKE '$anio%' ORDER BY TransDate    
";
    $result = $mysqli->query($consultaSql);
    
      
    if ($result->num_rows > 0) {
        while ($myrow = $result->fetch_assoc()) {
            $fila = $myrow;
            $mes = substr($fila["TransDate"],5,2);
            //$maximo = $fila["TransAmt"]+ $maximo;
            switch ($mes){
                case "01":
                    $contador[0] = $contador[0] + $fila["TransAmt"];
                    break;
                case "02":
                    $contador[1] = $contador[1] + $fila["TransAmt"];
                    break;
                case "03":
                    $contador[2] = $contador[2] + $fila["TransAmt"];
                    break;
                case "04":
                    $contador[3] = $contador[3] + $fila["TransAmt"];
                    break;
                case "05":
                    $contador[4] = $contador[4] + $fila["TransAmt"];
                    break;
                case "06":
                    $contador[5] = $contador[5] + $fila["TransAmt"];
                    break;
                case "07":
                    $contador[6] = $contador[6] + $fila["TransAmt"];
                    break;
                case "08":
                    $contador[7] = $contador[7] + $fila["TransAmt"];
                    break;
                case "09":
                    $contador[8] = $contador[8] + $fila["TransAmt"];
                    break;
                case "10":
                    $contador[9] = $contador[9] + $fila["TransAmt"];
                    break;
                case "11":
                    $contador[10] = $contador[10] + $fila["TransAmt"];
                    break;
                case "12":
                    $contador[11] = $contador[11] + $fila["TransAmt"];
                    break;
                default:
                    $mes = "no entro a ningun caso";
                    break;
            }
        }
    }
    
    $consultaSql2 = 
	"SELECT TransDate, TransAmt, TransType FROM transline
         WHERE TransType='Debito'
         AND TransDate LIKE '$anio%' ORDER BY TransDate    
";
    
    $result2 = $mysqli->query($consultaSql2);
    $mysqli->close();
    
    if ($result2->num_rows > 0) {
        while ($myrow = $result2->fetch_assoc()) {
            $fila = $myrow;
            $mes = substr($fila["TransDate"],5,2);
            //$maximo = $fila["TransAmt"]+ $maximo;
            switch ($mes){
                case "01":
                    $utilizados[0] = $utilizados[0] + $fila["TransAmt"];
                    break;
                case "02":
                    $utilizados[1] = $utilizados[1] + $fila["TransAmt"];
                    break;
                case "03":
                    $utilizados[2] = $utilizados[2] + $fila["TransAmt"];
                    break;
                case "04":
                    $utilizados[3] = $utilizados[3] + $fila["TransAmt"];
                    break;
                case "05":
                    $utilizados[4] = $utilizados[4] + $fila["TransAmt"];
                    break;
                case "06":
                    $utilizados[5] = $utilizados[5] + $fila["TransAmt"];
                    break;
                case "07":
                    $utilizados[6] = $utilizados[6] + $fila["TransAmt"];
                    break;
                case "08":
                    $utilizados[7] = $utilizados[7] + $fila["TransAmt"];
                    break;
                case "09":
                    $utilizados[8] = $utilizados[8] + $fila["TransAmt"];
                    break;
                case "10":
                    $utilizados[9] = $utilizados[9] + $fila["TransAmt"];
                    break;
                case "11":
                    $utilizados[10] = $utilizados[10] + $fila["TransAmt"];
                    break;
                case "12":
                    $utilizados[11] = $utilizados[11] + $fila["TransAmt"];
                    break;
                default:
                    $mes = "no entro a ningun caso";
                    break;
            }
        }
    }
    
}
$maximo=0;
//Asignamos los datos al json
$salida = "{meses: [";
for($j = 0; $j<count($meses); $j++){
    $salida .= "{
            id        :".($j*10).",
            mes       :'".$meses[$j] ."',
            vendidos  :".$contador[$j] .",
            utilizados:".$utilizados[$j].",
            maximo:".$maximo."    
            }";
    if ($j != count($meses) - 1) {
        $salida .= ",";
    }
}
$salida .="]}";
}
echo utf8_encode($salida);

?>