<?php

include('../../login/isLogin.php');
include ('../../../dll/config.php');

extract($_GET);

if (!$mysqli = getConectionDb()) {
    echo "{success:false, message: 'Error: No se ha podido conectar a la Base de Datos.'}";
} else {
    $idCompany = $_SESSION["IDEMPRESAKBUS" . $site_city];
    $idRol = $_SESSION["IDROLKBUS" . $site_city];

    $consultaSql = "SELECT s.id_equipo,v.reg_municipal,count(*) as total FROM kbushistoricodb.dato_skps s ,kbusdb.vehiculos v where s.id_equipo=v.id_equipo and s.fecha=date(now()) group by s.id_equipo";
    $consultaSql1 = "SELECT s.id_equipo,v.reg_municipal, count(*) as total  FROM kbushistoricodb.dato_fastracks  s, kbusdb.vehiculos v where s.id_equipo=v.id_equipo and s.fecha=date(now()) group by s.id_equipo";


    $result = $mysqli->query($consultaSql);
    $result1 = $mysqli->query($consultaSql1);

    $mysqli->close();

    if (($result->num_rows > 0) || ($result1->num_rows > 0)) {
        $objJson = "{data: [";

        while ($myrow = $result1->fetch_assoc()) {
            $objJson .= "{"
                    . "id_equipo: " . $myrow["id_equipo"] . ","
                    . "reg_municipal: '" . $myrow["reg_municipal"] . "',"
                    . "total: " . $myrow["total"] . "},";
        }
        while ($myrow = $result->fetch_assoc()) {
            $objJson .= "{"
                    . "id_equipo: " . $myrow["id_equipo"] . ","
                    . "reg_municipal: '" . $myrow["reg_municipal"] . "',"
                    . "total: " . $myrow["total"] . "},";
        }
        $objJson.= "]}";
        echo $objJson;
    } else {
        echo "{failure:true, message:'No hay datos que obtener'}";
    }
}