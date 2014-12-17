<?php

include('../../login/isLogin.php');
include ('../../../dll/config.php');

extract($_GET);

if (!$mysqli = getConectionDb()) {
    //echo "Error: No se ha podido conectar a la Base de Datos.";
    echo "{success:false, message: 'Error: No se ha podido conectar a la Base de Datos.'}";
} else {
    $consultaSql = "select pd.id_punto, pd.orden, pd.tiempos, pd.tiempo_d, pd.tiempo_ll, pd.diferencia, p.punto, pr.imprimir "
            . "from kbushistoricodb.papeleta_despachos pd, kbusdb.puntos p, kbusdb.punto_rutas pr "
            . "where p.id_punto = pd.id_punto "
            . "and pr.id_punto = p.id_punto "
            . "and pd.orden = pr.orden "
            . "and pd.id_ruta = pr.id_ruta "
            . "and pd.id_vehiculo = $idVehicle "
            . "and pd.fecha = date(now()) "
            . "and pd.hora_ini = '$timeStart'";

    $result = $mysqli->query($consultaSql);
    $mysqli->close();

    if ($result->num_rows > 0) {
        $objJson = "{data: [";
        while ($myrow = $result->fetch_assoc()) {
            $imprimir = $myrow["imprimir"];
            if ($imprimir == 1) {
                $diferencia = $myrow["diferencia"];
            } else {
                $diferencia = "No Sancionable";
            }
            $objJson .= "{"
                    . "idPointView:" . $myrow["id_punto"] . ","
                    . "orderPointView:" . $myrow["orden"] . ","
                    . "pointView:'" . utf8_encode($myrow["punto"]) . "',"
                    . "timeView:'" . $myrow["tiempos"] . "',"
                    . "timeDebView:'" . $myrow["tiempo_d"] . "',"
                    . "timeLlView:'" . $myrow["tiempo_ll"] . "',"
                    . "timeDifView:'" . $diferencia . "'},";
        }

        $objJson .= "{idPointView:' ', orderPointView:' ', pointView:' ', timeView:' ', timeDebView:'<b>ATRA: </b>', timeLlView:'<b>$delay</b>', timeDifView:' '},"
                . "{idPointView:' ', orderPointView:' ', pointView:' ', timeView:' ', timeDebView:'<b>ADEL: </b>', timeLlView:'<b>$advance</b>', timeDifView:' '}";


        $objJson.= "]}";

        echo $objJson;
    } else {
        //echo "No hay datos que Obtener";
        echo "{failure:true, message:'No hay datos que obtener'}";
    }
}