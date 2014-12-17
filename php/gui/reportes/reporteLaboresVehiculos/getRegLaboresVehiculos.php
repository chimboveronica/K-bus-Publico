<?php

include ('../../../../dll/config.php');

extract($_POST);

if (!$mysqli = getConectionDb()) {
    echo "{failure: true, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {

    if ($idCompanyRegLabVehiculos == 1) {
        $consultaSql = "SELECT v. reg_municipal, concat(p.nombres,'', p.apellidos)as persona, lv.estado, lv.fecha_hora_equipo,lv.fecha_hora_registro FROM kbushistoricodb.registro_labores_vehiculos lv, kbusdb.vehiculos v ,kbusdb.personas p  where lv.id_vehiculo=v.id_vehiculo and v.id_persona=p.id_persona and  date(fecha_hora_registro)between ? and ? order by lv.id_vehiculo";

        $stmt = $mysqli->prepare($consultaSql);

        if ($stmt) {
            /* ligar parámetros para marcadores */
            $stmt->bind_param("ss", $fechaIniRegLabVehiculos, $fechaFinRegLabVehiculos);
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
                            . "regMunicipalLV:'" . utf8_encode($myrow["reg_municipal"]) . "',"
                            . "personaLV:'" . utf8_encode($myrow["persona"]) . "',"
                            . "fecha_horaEquipoLV:'" . $myrow["fecha_hora_equipo"] . "',"
                            . "fecha_horaLV:'" . $myrow["fecha_hora_registro"] . "',"
                            . "estadoLV:" . $myrow["estado"] . ""
                            . "},";
                }

                $json .="]";

                echo "{success: true, $json}";
            } else {
                echo "{failure: true, message:'No hay accesos entre estas Fechas.'}";
            }
        } else {
            echo "{failure: true, message: 'Problemas en la Construcción de la Consulta.'}";
        }
    } else {
        $consultaSql = "SELECT v. reg_municipal, concat(p.nombres,'', p.apellidos)as persona, lv.estado, lv.fecha_hora_equipo,lv.fecha_hora_registro FROM kbushistoricodb.registro_labores_vehiculos lv, kbusdb.vehiculos v ,kbusdb.personas p  where lv.id_vehiculo=v.id_vehiculo and v.id_persona=p.id_persona and v.id_empresa=? and date(fecha_hora_registro)between ? and ? order by lv.id_vehiculo";

        $stmt = $mysqli->prepare($consultaSql);

        if ($stmt) {
            /* ligar parámetros para marcadores */
            $stmt->bind_param("iss", $idCompanyRegLabVehiculos, $fechaIniRegLabVehiculos, $fechaFinRegLabVehiculos);
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
                            . "regMunicipalLV:'" . utf8_encode($myrow["reg_municipal"]) . "',"
                            . "personaLV:'" . utf8_encode($myrow["persona"]) . "',"
                            . "fecha_horaEquipoLV:'" . $myrow["fecha_hora_equipo"] . "',"
                            . "fecha_horaLV:'" . $myrow["fecha_hora_registro"] . "',"
                            . "estadoLV:" . $myrow["estado"] . ""
                            . "},";
                }

                $json .="]";

                echo "{success: true, $json}";
            } else {
                echo "{failure: true, message:'No hay accesos entre estas Fechas.'}";
            }
        } else {
            echo "{failure: true, message: 'Problemas en la Construcción de la Consulta.'}";
        }
    }
}