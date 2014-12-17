<?php

include ('../../../../dll/config.php');

extract($_POST);

if (!$mysqli = getConectionDb()) {
    echo "{failure: true, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {

    if ($idCompanyAcces == 1) {
        $consultaSql = "SELECT u.usuario,e.empresa"
                . ",concat(p.apellidos,' ',p.nombres) as persona,a.fecha_hora_registro, a.ip,a.host,a.latitud,a.longitud "
                . "FROM kbushistoricodb.accesos a, kbusdb.personas p, kbusdb.usuarios u ,kbusdb.empresas e "
                . "where a.id_usuario=u.id_usuario and u.id_persona=p.id_persona and p.id_empresa=e.id_empresa and date(a.fecha_hora_registro) between ? and ?";

        $stmt = $mysqli->prepare($consultaSql);

        if ($stmt) {
            /* ligar parámetros para marcadores */
            $stmt->bind_param("ss", $fechaIniAcces, $fechaFinAcces);
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
                            . "empresaAcces:'" . utf8_encode($myrow["empresa"]) . "',"
                            . "usuarioAcces:'" . utf8_encode($myrow["usuario"]) . "',"
                            . "personaAcces:'" . utf8_encode($myrow["persona"]) . "',"
                            . "fecha_horaAcces:'" . $myrow["fecha_hora_registro"] . "',"
                            . "ipAccess:'" . $myrow["ip"] . "',"
                            . "hostAcces:'" . $myrow["host"] . "',"
                            . "longitudAcces:" . $myrow["longitud"] . ","
                            . "latitudAcces:" . $myrow["latitud"] . ""
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
        $consultaSql = "SELECT u.usuario,e.empresa
                ,concat(p.apellidos,' ',p.nombres) as persona,a.fecha_hora_registro, a.ip,a.host,a.latitud,a.longitud 
                FROM kbushistoricodb.accesos a, kbusdb.personas p, kbusdb.usuarios u ,kbusdb.empresas e 
                where a.id_usuario=u.id_usuario and u.id_persona=p.id_persona and p.id_empresa=e.id_empresa and e.id_empresa=? and date(a.fecha_hora_registro) between ? and ?";

        $stmt = $mysqli->prepare($consultaSql);

        if ($stmt) {
            /* ligar parámetros para marcadores */
            $stmt->bind_param("iss", $idCompanyAcces, $fechaIniAcces, $fechaFinAcces);
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
                            . "empresaAcces:'" . utf8_encode($myrow["empresa"]) . "',"
                            . "usuarioAcces:'" . utf8_encode($myrow["usuario"]) . "',"
                            . "personaAcces:'" . utf8_encode($myrow["persona"]) . "',"
                            . "fecha_horaAcces:'" . $myrow["fecha_hora_registro"] . "',"
                            . "ipAccess:'" . $myrow["ip"] . "',"
                            . "hostAcces:'" . $myrow["host"] . "',"
                            . "longitudAcces:" . $myrow["longitud"] . ","
                            . "latitudAcces:" . $myrow["latitud"] . ""
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