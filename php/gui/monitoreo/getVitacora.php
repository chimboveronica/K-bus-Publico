<?php

include('../../login/isLogin.php');
include ('../../../dll/config.php');

extract($_POST);

if (!$mysqli = getConectionDb()) {
    echo "{failure: true, message: 'No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {
    if ($rb == 1) {
        $consultaSql = "SELECT vs.comentario, vs.fecha_hora_registro, p.apellidos, p.nombres "
                . "FROM  kbushistoricodb.comentario_equipos vs, kbusdb.usuarios u, kbusdb.equipos e, kbusdb.personas p "
                . "WHERE vs.id_usuario = u.id_usuario "
                . "AND u.id_persona = p.id_persona "
                . "AND vs.id_equipo = e.id_equipo "
                . "AND vs.id_equipo = $idDevice "
                . "ORDER BY vs.fecha_hora_registro DESC";
    } else {
        $consultaSql = "SELECT vs.comentario, vs.fecha_hora_registro, p.apellidos, p.nombres "
                . "FROM kbushistoricodb.comentario_vehiculos vs, kbusdb.usuarios u, kbusdb.vehiculos e, kbusdb.personas p "
                . "WHERE vs.id_usuario = u.id_usuario "
                . "AND u.id_persona = p.id_persona "
                . "AND vs.id_vehiculo = e.id_vehiculo "
                . "AND vs.id_vehiculo = $idVehicle "
                . "ORDER BY vs.fecha_hora_registro DESC";
    }
    $result = $mysqli->query($consultaSql);
    $mysqli->close();

    if ($result->num_rows > 0) {
        $objJson = "data : [";
        while ($myrow = $result->fetch_assoc()) {
            $objJson .= "{"
                    . "estadoVtc:'" . preg_replace("[\n|\r|\n\r]", "\\n", utf8_encode($myrow["comentario"])) . "',"
                    . "fechaHoraReg:'" . $myrow["fecha_hora_registro"] . "',"
                    . "tecnicoVtc: '" . utf8_encode($myrow["apellidos"] . ' ' . $myrow["nombres"]) . "'},";
        }

        $objJson .="]";

        echo "{success: true, " . $objJson . "}";
    } else {
        echo "{failure: true, message: 'No hay datos que obtener'}";
    }
}