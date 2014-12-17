<?php

include ('../../../../dll/config.php');

$proceso = 1;
extract($_POST);

if (!$mysqli = getConectionDb()) {
    echo "{failure:true, msg: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {
    if (!strpos($horaIni, ":")) {
        $horaIni = substr($horaIni, 0, 2) . ":" . substr($horaIni, 2, 4);
    }
    
    $consultaHoraFinRuta = "select addtime(addtime(time(sec_to_time(sum(time_to_sec(tiempos)))), '$horaIni'), '00:05:00') as hora_fin "
            . "from punto_rutas "
            . "where id_ruta = $idRoute";

    $resultsetHoraFin = $mysqli->query($consultaHoraFinRuta)->fetch_assoc();
    echo "{success: true, horaFin: '" . $resultsetHoraFin["hora_fin"] . "'}";
    $mysqli->close();
}