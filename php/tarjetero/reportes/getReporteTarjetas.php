<?php

include('../../login/isLogin.php');
include ('../../../dll/config.php');

$datos = Array();
extract($_POST);
$var ="";
if (!$mysqli = getConectionTarjeteroDb()) {
    echo "{failure: true, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {
    $consultaSql = "";

//Preguntamos si  $all esta enviado y creamos la consulta para todos los datos
        if (isset($all)) {
            if ($all == "todos") {
                $consultaSql = "select t.cardno, t.idcliente, t.tipotarifa, t.fecha, t.hora, c.nombres, c.apellidos "
        . "from tarjetas t, clientes c where t.idcliente = c.cedula";
            }
        } else {
            $consultaSql = "select t.cardno, t.idcliente, t.tipotarifa, t.fecha, t.hora, c.nombres as nombres, c.apellidos as apellidos"
                   ."from tarjetas t, clientes c FROM tarjetas WHERE fecha >= '" . $fechaIniT . "' AND fecha <= '" . $fechaFinT . "'" .
                    " AND hora >= '" . $horaIniT . "' AND hora <= '" . $horaFinT . "'";
        }
        $result = $mysqli->query($consultaSql);
        $mysqli->close();
        $c=0;
        if ($result->num_rows > 0) {
            $json = "{tarjetas: [";
                while ($myrow = $result->fetch_assoc()) {
                $datos[$c]=$myrow["nombres"];
                $c++;
                $cn=str_pad ( $myrow["cardno"] ,16 ,"0", STR_PAD_LEFT );
                $json .= "{
                cardno     :'" . $cn . "',
                fecha      :'" . $myrow["fecha"] . "',
                hora       :'" . $myrow["hora"] . "',
                tipotarifa :'" . $myrow["tipotarifa"] . "',
                asignado   :'" . $myrow["apellidos"]." ".$myrow["nombres"]."'
            },";
            }
            $json .="]}";
            $json = preg_replace("[\n|\r|\n\r]", "", utf8_encode($json));
            $salida = "{success:true, string: " . json_encode($json) . "}";
        } else {
            $salida = "{failure:true}";
        }

        echo $salida;
    }    