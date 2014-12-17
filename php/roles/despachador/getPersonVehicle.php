<?php

include ('../../../dll/config.php');

extract($_POST);

if (!$mysqli = getConectionDb()) {
    echo "{failure:true, message: 'No se ha podido conectar a la Base de Datos.'}";
} else {
    $consultaSql = "SELECT id_conductor, id_ayudante FROM vehiculos WHERE id_vehiculo = $idVehicle";

    $result = $mysqli->query($consultaSql);
    $mysqli->close();

    if ($result->num_rows > 0) {
        $myrow = $result->fetch_assoc();
        $objJson = "data: {"
                . "idConductorVehicle : " . $myrow["id_conductor"] . ","
                . "idAyudanteVehicle : " . $myrow["id_ayudante"] . "}";

        echo "{success: true, $objJson}";
    } else {
        echo "{failure:true, message:'No hay un Conductor y Ayudante asociados a este vehiculos previamente.'}";
    }
}