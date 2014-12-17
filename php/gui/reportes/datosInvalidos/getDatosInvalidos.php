<?php

include ('../../../../dll/config.php');

extract($_POST);

if (!$mysqli = getConectionDb()) {
    echo "{failure: true, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {

    $consultaSql = "SELECT ((length(trama))/1048576) as megas,(((length(trama)/1048576)*11.19)/300) as precio,di.descripcion,i.fecha_hora_registro,i.equipo,i.trama,i.excepcion "
            . "FROM kbushistoricodb.dato_invalidos i,  kbusdb.tipo_dato_invalidos di "
            . "where i.id_tipo_dato_invalido=di.id_tipo_dato_invalido and  date(i.fecha_hora_registro)between ? and ? order by i.fecha_hora_registro desc";

    $stmt = $mysqli->prepare($consultaSql);

    if ($stmt) {
        /* ligar parámetros para marcadores */
        $stmt->bind_param("ss", $fechaIniDi, $fechaFinDi);
        /* ejecutar la consulta */
        $stmt->execute();

        $result = $stmt->get_result();

        $stmt->close();
        $mysqli->close();

        if ($result->num_rows > 0) {
            $isFirst = false;

            $json = "data: [";
            while ($myrow = $result->fetch_assoc()) {

                $json .= "{"
                        . "megasDI:" . $myrow["megas"] . ","
                        . "precioDI:" . $myrow["precio"] . ","
                        . "descripcionDI:'" . utf8_encode($myrow["descripcion"]) . "',"
                        . "fecha_hora_regDI:'" . $myrow["fecha_hora_registro"] . "',"
                        . "equipoDI:'" . $myrow["equipo"] . "',"
                        . "tramaDI:'" . utf8_encode((preg_replace("[\n|\r|\n\r]", "", $myrow["trama"]))) . "',"
                        . "excepcionDI:'" . $myrow["excepcion"] . "'},";
            }

            $json .="]";

            echo "{success: true, $json}";
        } else {
            echo "{failure: true, message:'No hay reportes entre estas Fechas.'}";
        }
    } else {
        echo "{failure: true, message: 'Problemas en la Construcción de la Consulta.'}";
    }
}