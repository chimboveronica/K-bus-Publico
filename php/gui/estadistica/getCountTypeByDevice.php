<?php

include('../../login/isLogin.php');
include ('../../../dll/config.php');

extract($_GET);

if (!$mysqli = getConectionDb()) {
    echo "{success:false, message: 'Error: No se ha podido conectar a la Base de Datos.'}";
} else {
    $idCompany = $_SESSION["IDEMPRESAKBUS" . $site_city];
    $idRol = $_SESSION["IDROLKBUS" . $site_city];

    $consultaSql = "SELECT k.sky_evento ,  k.acronimo ,count(*) as total FROM kbushistoricodb.dato_skps s  ,kbusdb.sky_eventos k, kbusdb.vehiculos v where s.id_equipo=v.id_equipo and s.id_sky_evento=k.id_sky_evento and s.fecha=date(now()) and v.reg_municipal='$idEquipo'  group by s.id_sky_evento";
    $consultaSql1 = "SELECT  k.descripcion_encabezado ,k.encabezado ,count(*)as total FROM kbushistoricodb.dato_fastracks s ,kbusdb.encabezados k ,kbusdb.vehiculos v where s.id_equipo=v.id_equipo and s.id_encabezado=k.id_encabezado and s.fecha=date(now()) and v.reg_municipal='$idEquipo' group by s.id_encabezado";


    $result = $mysqli->query($consultaSql);
    $result1 = $mysqli->query($consultaSql1);

    $mysqli->close();

    if (($result->num_rows > 0) || ($result1->num_rows > 0)) {
        $objJson = "{data: [";
        while ($myrow = $result->fetch_assoc()) {
            $objJson .= "{"
                    . "evento: '" . utf8_encode($myrow["sky_evento"]) . "',"
                    . "abv: '" . utf8_encode($myrow["acronimo"]) . "',"
                    . "total: " . $myrow["total"] . "},";
        }
        while ($myrow = $result1->fetch_assoc()) {
            $objJson .= "{"
                    . "evento: '" . utf8_encode($myrow["descripcion_encabezado"]) . "',"
                    . "abv: '" . utf8_encode($myrow["encabezado"]) . "',"
                    . "total: " . $myrow["total"] . "},";
        }
        $objJson .= "]}";
        echo $objJson;
    } else {
        echo "{failure:true, message:'No hay datos que obtener'}";
    }
}