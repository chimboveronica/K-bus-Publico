<?php

include('../../login/isLogin.php');
include('../../../dll/config.php');
if (!$mysqli = getConectionDb()) {
    echo "{success:false, message: 'No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {

    $idRol = $_SESSION["IDROLKBUS" . $site_city];
    $idCompany = $_SESSION["IDEMPRESAKBUS" . $site_city];

    if ($idRol == 1 || $idRol == 3 || $idRol == 6) {
        $consultaSql = "SELECT v.id_empresa, b.id_equipo, v.id_vehiculo, b.id_ruta, b.id_sky_evento, TIMESTAMPDIFF(MINUTE,  b.fecha_hora_conex, NOW()) AS conexion, "
                . "TIMESTAMPDIFF(MINUTE, b.fecha_hora_ult_dato, NOW()) AS desconexion, b.fecha_hora_conex, b.fecha_hora_ult_dato, e.equipo, v.reg_municipal, "
                . "p.punto, b.latitud, b.longitud, b.velocidad, b.rumbo, b.g1, b.g2, b.sal, b.bateria, b.v1, b.v2, b.gsm, b.gps, b.ign, v.estado_reten, "
                . "b.direccion, e.comentario AS comentario_equipo, v.comentario AS comentario_vehiculo, "
                . "CONCAT(pe.nombres, ' ', pe.apellidos) AS persona_comentario_equipo, CONCAT(pe2.nombres, ' ', pe2.apellidos) AS persona_comentario_vehiculo, "
                . "e.fecha_hora_comentario AS fecha_hora_comentario_equipo, v.fecha_hora_comentario AS fecha_hora_comentario_vehiculo "
                . "FROM ultimo_dato_skps b, equipos e, puntos p, vehiculos v, usuarios u, usuarios u2, personas pe, personas pe2 "
                . "WHERE b.id_equipo = e.id_equipo "
                . "AND b.id_punto = p.id_punto "
                . "AND e.id_equipo = v.id_equipo "
                . "AND e.id_usuario = u.id_usuario "
                . "AND u.id_persona = pe.id_persona "
                . "AND v.id_usuario = u2.id_usuario "
                . "AND u2.id_persona = pe2.id_persona";
    } else {
        $consultaSql = "SELECT v.id_empresa, b.id_equipo, v.id_vehiculo, b.id_ruta, b.id_sky_evento, TIMESTAMPDIFF(MINUTE,  b.fecha_hora_conex, NOW()) AS conexion, "
                . "TIMESTAMPDIFF(MINUTE, b.fecha_hora_ult_dato, NOW()) AS desconexion, b.fecha_hora_conex, b.fecha_hora_ult_dato, e.equipo, v.reg_municipal, "
                . "p.punto, b.latitud, b.longitud, b.velocidad, b.rumbo, b.g1, b.g2, b.sal, b.bateria, b.v1, b.v2, b.gsm, b.gps, b.ign, v.estado_reten, "
                . "b.direccion, e.comentario AS comentario_equipo, v.comentario AS comentario_vehiculo, "
                . "CONCAT(pe.nombres, ' ', pe.apellidos) AS persona_comentario_equipo, CONCAT(pe2.nombres, ' ', pe2.apellidos) AS persona_comentario_vehiculo, "
                . "e.fecha_hora_comentario AS fecha_hora_comentario_equipo, v.fecha_hora_comentario AS fecha_hora_comentario_vehiculo "
                . "FROM ultimo_dato_skps b, equipos e, puntos p, vehiculos v, usuarios u, usuarios u2, personas pe, personas pe2 "
                . "WHERE b.id_equipo = e.id_equipo "
                . "AND b.id_punto = p.id_punto "
                . "AND e.id_equipo = v.id_equipo "
                . "AND e.id_usuario = u.id_usuario "
                . "AND u.id_persona = pe.id_persona "
                . "AND v.id_usuario = u2.id_usuario "
                . "AND u2.id_persona = pe2.id_persona "
                . "AND v.id_empresa = $idCompany";
    }

    $result = $mysqli->query($consultaSql);
    $mysqli->close();

    $objJson = "{data: [";

    while ($myrow = $result->fetch_assoc()) {
        $objJson .= "{"
                . "idEmpresaTcp:" . $myrow["id_empresa"] . ","
                . "idEquipoTcp:" . $myrow["id_equipo"] . ","
                . "idVehiculoTcp:" . $myrow["id_vehiculo"] . ","
                . "idSkyEventoTcp:" . $myrow["id_sky_evento"] . ","
                . "idRutaTcp:" . utf8_encode($myrow["id_ruta"]) . ","
                . "equipoTcp:'" . $myrow["equipo"] . "',"
                . "regMunicipalTcp:'" . $myrow["reg_municipal"] . "',"
                . "puntoTcp:'" . utf8_encode($myrow["punto"]) . "',"
                . "conexionTcp:" . $myrow["conexion"] . ","
                . "desconexionTcp:" . $myrow["desconexion"] . ","
                . "fechaHoraConexionTcp:'" . $myrow["fecha_hora_conex"] . "',"
                . "fechaHoraUltimoDatoTcp:'" . $myrow["fecha_hora_ult_dato"] . "',"
                . "latitudTcp:" . $myrow["latitud"] . ","
                . "longitudTcp:" . $myrow["longitud"] . ","
                . "velocidadTcp:" . $myrow["velocidad"] . ","
                . "rumboTcp:" . $myrow["rumbo"] . ","
                . "g1Tcp:" . $myrow["g1"] . ","
                . "g2Tcp:" . $myrow["g2"] . ","
                . "salTcp:" . $myrow["sal"] . ","
                . "bateriaTcp:" . $myrow["bateria"] . ","
                . "v1Tcp:" . $myrow["v1"] . ","
                . "v2Tcp:" . $myrow["v2"] . ","
                . "gsmTcp:" . $myrow["gsm"] . ","
                . "gpsTcp:" . $myrow["gps"] . ","
                . "ignTcp:" . $myrow["ign"] . ","
                . "estadoRetenTcp:" . $myrow["estado_reten"] . ","
                . "direccionTcp:'" . utf8_encode($myrow["direccion"]) . "',"
                . "comentarioEquipoTcp:'" . preg_replace("[\n|\r|\n\r]", "\\n", utf8_encode($myrow["comentario_equipo"])) . "',"
                . "comentarioVehiculoTcp:'" . preg_replace("[\n|\r|\n\r]", "\\n", utf8_encode($myrow["comentario_vehiculo"])) . "',"
                . "personaComentarioEquipoTcp:'" . utf8_encode($myrow["persona_comentario_equipo"]) . "',"
                . "personaComentarioVehiculoTcp:'" . utf8_encode($myrow["persona_comentario_vehiculo"]) . "',"
                . "fechaHoraEstadoEquipoTcp:'" . utf8_encode($myrow["fecha_hora_comentario_equipo"]) . "',"
                . "fechaHoraEstadoVehiculoTcp:'" . utf8_encode($myrow["fecha_hora_comentario_vehiculo"]) . "'},";
    }
    $objJson .= "]}";
    echo $objJson;
}
