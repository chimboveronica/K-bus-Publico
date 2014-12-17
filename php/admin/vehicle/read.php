<?php

include ('../../../dll/config.php');

if (!$mysqli = getConectionDb()) {
    echo "{success:false, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.',state: false}";
} else {

    $consultaSql = "select v.id_vehiculo, v.id_equipo, v.id_empresa, v.personas_sentadas, v.personas_paradas,v.id_servicio_ruta, v.id_persona, "
            . "v.placa, v.reg_municipal, v.marca, v.modelo, v.year, v.num_motor, v.num_chasis, "
            . "v.num_disco, v.fecha_matricula, v.soat, v.imagen, eq.equipo, "
            . "e.empresa, concat(p.apellidos, ' ', p.nombres) as persona, sr.servicio_ruta "
            . "from vehiculos v, equipos eq, empresas e, personas p, servicio_rutas sr "
            . "where v.id_equipo = eq.id_equipo "
            . "and v.id_empresa = e.id_empresa "
            . "and v.id_persona = p.id_persona "
            . "and v.id_servicio_ruta = sr.id_servicio_ruta "
            . "order by eq.equipo";

    $result = $mysqli->query($consultaSql);
    $mysqli->close();

    $objJson = "{data: [";

    while ($myrow = $result->fetch_assoc()) {
        $capacidad = $myrow["personas_sentadas"];
        $capacidad1 = $myrow["personas_paradas"];
        $capacidad = $capacidad + $capacidad1;

        $objJson .= "{"
                . "idVehicle:" . $myrow["id_vehiculo"] . ","
                . "idDeviceVehicle:" . $myrow["id_equipo"] . ","
                . "idCompanyVehicle:" . $myrow["id_empresa"] . ","
                . "idRouServVehicle:" . $myrow["id_servicio_ruta"] . ","
                . "idPersonVehicle:" . $myrow["id_persona"] . ","
                . "sentadasVehicle:" . $myrow["personas_sentadas"] . ","
                . "paradasVehicle:" . $myrow["personas_paradas"] . ","
                . "plateVehicle:'" . utf8_encode($myrow["placa"]) . "',"
                . "muniRegVehicle:'" . utf8_encode($myrow["reg_municipal"]) . "',"
                . "makeVehicle:'" . utf8_encode($myrow["marca"]) . "',"
                . "modelVehicle:'" . utf8_encode($myrow["modelo"]) . "',"
                . "yearVehicle:" . $myrow["year"] . ","
                . "capacidadVehicle:" . $capacidad . ","
                . "numMotorVehicle:'" . utf8_encode($myrow["num_motor"]) . "',"
                . "numChasisVehicle:'" . utf8_encode($myrow["num_chasis"]) . "',"
                . "numDiscoVehicle:" . $myrow["num_disco"] . ","
                . "enrollDateVehicle:'" . $myrow["fecha_matricula"] . "',"
                . "soatVehicle:'" . utf8_encode($myrow["soat"]) . "',"
                . "imageVehicle:'" . utf8_encode($myrow["imagen"]) . "',"
                . "deviceVehicle:'" . utf8_encode($myrow["equipo"]) . "',"
                . "companyVehicle:'" . utf8_encode($myrow["empresa"]) . "',"
                . "personVehicle:'" . utf8_encode($myrow["persona"]) . "',"
                . "rouServVehicle:'" . utf8_encode($myrow["servicio_ruta"]) . "'},";
    }
    $objJson .= "]}";
    echo $objJson;
}