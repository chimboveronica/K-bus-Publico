<?php

include('../../login/isLogin.php');
include('../../../dll/config.php');
if (!$mysqli = getConectionDb()) {
    echo "{success:false, message: 'No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {

    $idRol = $_SESSION["IDROLKBUS" . $site_city];
    $idCompany = $_SESSION["IDEMPRESAKBUS" . $site_city];

    if ($idRol == 1 || $idRol == 3 || $idRol == 6) {
        $consultaSql = "SELECT v.id_empresa, v.id_equipo, v.id_vehiculo, b.id_ruta, b.id_encabezado, b.id_estado_bus, b.id_display, "
                . "b.id_estado_mecanico, TIMESTAMPDIFF(MINUTE, b.fecha_hora_ult_dato, NOW()) AS desconexion, b.fecha_hora_ult_dato, e.equipo, "
                . "v.reg_municipal, p.punto, b.latitud, b.longitud, b.velocidad, v.estado_reten, b.direccion, "
                . "e.comentario AS comentario_equipo, v.comentario AS comentario_vehiculo, "
                . "CONCAT(pe.nombres, ' ', pe.apellidos) AS persona_comentario_equipo, CONCAT(pe2.nombres, ' ', pe2.apellidos) AS persona_comentario_vehiculo, "
                . "e.fecha_hora_comentario AS fecha_hora_comentario_equipo, v.fecha_hora_comentario AS fecha_hora_comentario_vehiculo "
                . "FROM  kbusdb.ultimo_dato_fastracks b, equipos e, puntos p, vehiculos v, usuarios u, usuarios u2, personas pe, personas pe2 "
                . "WHERE b.id_equipo = e.id_equipo "
                . "AND b.id_punto = p.id_punto "
                . "AND e.id_equipo = v.id_equipo "
                . "AND e.id_usuario = u.id_usuario "
                . "AND u.id_persona = pe.id_persona "
                . "AND v.id_usuario = u2.id_usuario "
                . "AND u2.id_persona = pe2.id_persona";
    } else {
        $consultaSql = "SELECT v.id_empresa, v.id_equipo, v.id_vehiculo, b.id_ruta, b.id_encabezado, b.id_estado_bus, b.id_display, "
                . "b.id_estado_mecanico, TIMESTAMPDIFF(MINUTE, b.fecha_hora_ult_dato, NOW()) AS desconexion, b.fecha_hora_ult_dato, e.equipo, "
                . "v.reg_municipal, p.punto, b.latitud, b.longitud, b.velocidad, v.estado_reten, b.direccion, "
                . "e.comentario AS comentario_equipo, v.comentario AS comentario_vehiculo, "
                . "CONCAT(pe.nombres, ' ', pe.apellidos) AS persona_comentario_equipo, CONCAT(pe2.nombres, ' ', pe2.apellidos) AS persona_comentario_vehiculo, "
                . "e.fecha_hora_comentario AS fecha_hora_comentario_equipo, v.fecha_hora_comentario AS fecha_hora_comentario_vehiculo "
                . "FROM  kbusdb.ultimo_dato_fastracks b, equipos e, puntos p, vehiculos v, usuarios u, usuarios u2, personas pe, personas pe2 "
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
                . "idEmpresaUdp:" . $myrow["id_empresa"] . ","
                . "idEquipoUdp:" . $myrow["id_equipo"] . ","
                . "idVehiculoUdp:" . $myrow["id_vehiculo"] . ","
                . "idRutaUdp:" . $myrow["id_ruta"] . ","
                . "idEncabezadoUdp:" . $myrow["id_encabezado"] . ","
                . "idEstadoBusUdp:" . $myrow["id_estado_bus"] . ","
                . "idDisplayUdp:" . $myrow["id_display"] . ","
                . "idEstadoMecanicoUdp:" . $myrow["id_estado_mecanico"] . ","
                . "desconexionUdp:" . $myrow["desconexion"] . ","
                . "fechaHoraUltimoDatoUdp:'" . $myrow["fecha_hora_ult_dato"] . "',"
                . "equipoUdp:'" . $myrow["equipo"] . "',"
                . "regMunicipalUdp:'" . $myrow["reg_municipal"] . "',"
                . "puntoUdp:'" . utf8_encode($myrow["punto"]) . "',"
                . "latitudUdp:" . $myrow["latitud"] . ","
                . "longitudUdp:" . $myrow["longitud"] . ","
                . "velocidadUdp:" . $myrow["velocidad"] . ","
                . "estadoRetenUdp:" . $myrow["estado_reten"] . ","
                . "direccionUdp:'" . utf8_encode($myrow["direccion"]) . "',"
                . "comentarioEquipoUdp:'" . preg_replace("[\n|\r|\n\r]", "\\n", utf8_encode($myrow["comentario_equipo"])) . "',"
                . "comentarioVehiculoUdp:'" . preg_replace("[\n|\r|\n\r]", "\\n", utf8_encode($myrow["comentario_vehiculo"])) . "',"
                . "personaComentarioEquipoUdp:'" . utf8_encode($myrow["persona_comentario_equipo"]) . "',"
                . "personaComentarioVehiculoUdp:'" . utf8_encode($myrow["persona_comentario_vehiculo"]) . "',"
                . "fechaHoraEstadoEquipoUdp:'" . utf8_encode($myrow["fecha_hora_comentario_equipo"]) . "',"
                . "fechaHoraEstadoVehiculoUdp:'" . utf8_encode($myrow["fecha_hora_comentario_vehiculo"]) . "'},";
    }
    $objJson .= "]}";
    echo $objJson;
}
    
