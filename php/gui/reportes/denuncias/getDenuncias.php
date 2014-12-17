<?php

include ('../../../../dll/config.php');

extract($_POST);

if (!$mysqli = getConectionDb()) {
    echo "{failure: true, message: 'No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {

    $consultaSql = "SELECT CONCAT(d.apellidos, ' ', d.nombres) AS denunciante, d.cedula, d.telefono, d.direccion, "
            . "d.motivo, d.placa, d.reg_municipal, d.fecha_hora_registro, CONCAT(p.nombres,' ',p.apellidos) AS conductor, "
            . "d.latitud, d.longitud, d.observacion "
            . "FROM kbushistoricodb.denuncias d, vehiculos v, personas p "
            . "WHERE (d.reg_municipal = v.reg_municipal OR d.placa = v.placa) "
            . "AND v.id_persona = p.id_persona "
            . "AND DATE(d.fecha_hora_registro) BETWEEN ? AND ? ORDER BY d.fecha_hora_registro DESC";

    $stmt = $mysqli->prepare($consultaSql);

    if ($stmt) {
        /* ligar parámetros para marcadores */
        $stmt->bind_param("ss", $fechaIni, $fechaFin);
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
                        . "denuncianteDenuncia:'" . utf8_encode($myrow["denunciante"]) . "',"
                        . "cedulaDenuncia:'" . $myrow["cedula"] . "',"
                        . "telefonoDenuncia:'" . $myrow["telefono"] . "',"
                        . "latitudDenuncia:" . $myrow["latitud"] . ","
                        . "longitudDenuncia:" . $myrow["longitud"] . ","
                        . "observacionDenuncia:'" . utf8_encode($myrow["observacion"]) . "',"
                        . "direccionDenuncia:'" . utf8_encode($myrow["direccion"]) . "',"
                        . "motivoDenuncia:'" . utf8_encode($myrow["motivo"]) . "',"
                        . "placaDenuncia:'" . $myrow["placa"] . "',"
                        . "registroMunicipalDenuncia:'" . $myrow["reg_municipal"] . "',"
                        . "fechaHoraRegistroDenuncia:'" . $myrow["fecha_hora_registro"] . "',"
                        . "conductorDenuncia:'" . utf8_encode($myrow["conductor"]) . "'},";
            }

            $json .="]";

            echo "{success: true, $json}";
        } else {
            echo "{failure: true, message: 'No hay datos entre estas Fechas y Horas.'}";
        }
    } else {
        echo "{failure: true, message: 'Problemas en la Construcción de la Consulta.'}";
    }
}