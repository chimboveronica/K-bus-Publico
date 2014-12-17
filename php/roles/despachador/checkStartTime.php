<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include('../../login/isLogin.php');
include ('../../../dll/config.php');

extract($_POST);

if (!$mysqli = getConectionDb()) {
    echo "{failure:true, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {
    $idUser = $_SESSION["IDUSUARIOKBUS" . $site_city];
    
    $consultaSql = "select count(id_usuario) as c "
            . "FROM kbushistoricodb.registro_labores "
            . "WHERE id_usuario = $idUser "
            . "AND DATE(fecha_hora_registro) = DATE(NOW()) "
            . "AND estado = $state";
    $result = $mysqli->query($consultaSql);
    $myrow = $result->fetch_assoc();
    
    if ($myrow["c"] == 1) {
        if ($state == 1) {
            echo "{success: true}";
        } else {
            echo "{failure: true, message: 'Usted ya ha registrado su salida.'}";
        }
    } else {
        if ($state == 1) {
            echo "{failure: true, message: 'Por favor, registre su entrada.'}";
        } else {
            echo "{success: true}";
        }
    }
}