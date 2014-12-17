<?php

include ('../../../dll/config.php');

extract($_POST);

if (!$mysqli = getConectionDb()) {
    echo "{failure: true, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {

    $consultaSql = "SELECT numero_vuelta, numeracion, total_dia, fecha_hora, "
            . "fecha_hora_reg, gps, entradas, salidas "
            . "FROM contador_pasajeros "
            . "WHERE id_equipo = ? "
            . "AND date(fecha_hora) = ? "
            . "AND TIME(fecha_hora) "
            . "ORDER BY TIME(fecha_hora) DESC";

    $stmt = $mysqli->prepare($consultaSql);
    if ($stmt) {
        date_default_timezone_set('America/Guayaquil');
        $fechactual   = date("Y-m-j");
//        $horaactual = date("H:i:s");
//        $horaantes = date("H:i:s") - date("00:10:00");
        
        /* ligar parámetros para marcadores */
        $stmt->bind_param("ss", $cbxBuses, $fechactual);
        /* ejecutar la consulta */
        $stmt->execute();

        $result = $stmt->get_result();

        $stmt->close();
        $mysqli->close();

        if ($result->num_rows > 0) {
            $json = "data: [";
            while ($myrow = $result->fetch_assoc()) {
                $json .= "{"
                        . "numeroVuelta:" . $myrow["numero_vuelta"] . ","
                        . "numeracion: " . $myrow["numeracion"] . ","
                        . "totalDia:" . $myrow["total_dia"] . ","
                        . "fecha_hora:'" . $myrow["fecha_hora"] . "',"
                        . "fecha_hora_reg:'" . $myrow["fecha_hora_reg"] . "',"
                        . "gps:'" . $myrow["gps"] . "',"
                        . "entradas:" . $myrow["entradas"] . ","
                        . "salidas:" . $myrow["salidas"] . ","
                        . "bordo: ". ($myrow["entradas"] - $myrow["salidas"]) .","
                        . "recaudo: '". ($myrow["entradas"] * 0.25) ."'},";
            }
            $json .="]";
            echo "{success: true, $json}";
        } else {
            echo "{failure: true, message:'No hay datos a la fecha actual $fechactual'}";
        }
    } else {
        echo "{failure: true, message: 'Problemas en la Construcción de la Consulta.'}";
    }
}