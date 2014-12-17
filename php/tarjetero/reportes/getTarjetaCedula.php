<?php
include('../../login/isLogin.php');
include ('../../../dll/config.php');

extract($_POST);

if (!$mysqli = getConectionTarjeteroDb()) {
    echo "{failure: true, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {
        $consultaSql = "SELECT * FROM tarjetas WHERE idCliente='$cedula'";
        $result = $mysqli->query($consultaSql);
        $mysqli->close();

        if ($result->num_rows > 0) {
        $json = "{tarjet: [";
            while ($myrow = $result->fetch_assoc()) {
            $myrow = $resulset[$i];
            $a=str_pad ( $myrow["CardNo"] ,16 ,"0", STR_PAD_LEFT );
            $json .= "{
               carno:'" . $a . "',
               cedula:'" . $myrow["idCliente"] . "',
            },";
        }

        $json .="]}";
            
        //$json = preg_replace("[\n|\r|\n\r]", "", utf8_encode($json));        

        $salida = "{success:true, string: ".json_encode($json)."}";
}else{
    $salida = "{failure:true}";
}
echo $salida;
}