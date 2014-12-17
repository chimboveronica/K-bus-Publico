<?php

include('../../../login/isLogin.php');
include ('../../../dll/config.php');

extract($_POST);

if (!$mysqli = getConectionTarjeteroDb()) {
    echo "{failure: true, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
}
else{
$consultaSql = "SELECT COUNT(CardNo) AS TotalTarjetas FROM blacklist";
$result = $mysqli->query($consultaSql);
$mysqli->close();

if ($result->num_rows >0) {
    $consultaSql = "SELECT * FROM blacklist WHERE Fecha >= '".$fechaIni."' AND Fecha <= '".$fechaFin."'".
                   " AND Hora >= '".$horaIni."' AND Hora <= '".$horaFin."'";
    $result = $mysqli->query($consultaSql);
    $mysqli->close();

    $json = "{blacklist: [";

        while ($myrow = $result->fetch_assoc()) {
             $json .= "{"
                ."cardno   :'" . $myrow["CardNo"] . "',"
                ."fecha  :'" . $myrow["Fecha"] . "',"
                ."hora  :'" . $myrow["Hora"] . "',"
                ."blacktype:'" . $myrow["BlackType"] ."'},";
            }
         $json.="]}";
        echo $json;
    } else {
        echo "{success:false, msg: 'No hay datos que obtener'}";
    }
}