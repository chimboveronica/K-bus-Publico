<?php

/* DATOS DE MI APLICACION */
$site_name = "Kradac: K-Bus";
$site_city = "LOJATEST";
$latMap = -3.9912;
$lonMap = -79.20733;
$zoomMap = 14;

function getConectionDb() {
    /* DATOS DE MI SERVIDOR */
//    $db_name = "kbusdb";
//    $db_host = "172.16.57.11";
//    $db_user = "chimboveronica";
//    $db_password = "chimboveronicades2";

    $db_name = "kbusdb";
    $db_host = "localhost";
    $db_user = "root";
    $db_password = "";

    @$mysqli = new mysqli($db_host, $db_user, $db_password, $db_name);
    return ($mysqli->connect_errno) ? false : $mysqli;
}

function getEncryption($text) {
    $salt = "KR@D@C";
    $encriptClave = md5(md5(md5($text) . md5($salt)));
    return $encriptClave;
}

function allRows($result) {
    $vector = null;
    $pos = 0;

    while ($myrow = $result->fetch_row()) {
        $fila = "";
        for ($i = 0; $i < count($myrow); $i++) {
            $infoCampo = $result->fetch_field_direct($i);
            $fila[$infoCampo->name] = $myrow[$i];
        }
        $vector[$pos] = $fila;
        $pos++;
    }
    return $vector;
}

/*
 * Example of use for allRows

if (!$mysqli = getConectionDb()) {
    echo "{success:false, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {
    $consultaSql = "select * from report where state = 0";

    $result = allRows($mysqli->query($consultaSql));
    $mysqli->close();
    
    for ($i = 0; $i < count($result); $i++) {
        $fila = $result[$i];
        echo $fila["id_report"]. "\n";
    }
    
}*/