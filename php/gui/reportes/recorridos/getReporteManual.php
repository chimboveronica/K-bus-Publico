<?php

include('../../../login/isLogin.php');
include ('../../../../dll/config.php');

extract($_POST);

if (!$mysqli = getConectionDb()) {
    echo "{failure:true, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {
    //no es necesario.. ya que
    //en la base del procedure esta el campo 
    //como un Time :: $hora = 2013-05-16 08:00:00
    //solo toma la hora 
    //$hora = substr($hora, 11); 

    if (isset($horaIniC)) {
        $horaIni = $horaIniC;
    }
    $numeroreinicios = 0;
    $numerogsm = 0;
    $numerogps = 0;
    $numerotramas = 0;
    $velocidad = 0;
    $consultaHoraFinRuta = "select addtime(addtime(time(sec_to_time(sum(time_to_sec(tiempos)))), '$horaIni'), '00:05:00') as hora_fin "
            . "from punto_rutas "
            . "where id_ruta = $idRoute";

    $consultaPuntos = "select pr.id_punto, pr.orden, pr.tiempos, p.punto, p.geocerca_skp, pr.imprimir "
            . "from punto_rutas pr, puntos p "
            . "where pr.id_punto = p.id_punto "
            . "and pr.id_ruta = $idRoute "
            . "order by pr.orden";

    $consultaTipoEqp = "select e.id_tipo_equipo "
            . "from vehiculos v, equipos e "
            . "where v.id_equipo = e.id_equipo "
            . "and v.id_vehiculo = $idVehicle";

    $resulsetPuntos = allRows($mysqli->query($consultaPuntos));
    $resultsetTipoEqp = $mysqli->query($consultaTipoEqp)->fetch_assoc();
    $resultsetHoraFin = $mysqli->query($consultaHoraFinRuta)->fetch_assoc();
    $hora_fin = $resultsetHoraFin["hora_fin"];

    if ($resultsetTipoEqp["id_tipo_equipo"] == 1) {
        $consultaSql = "SELECT  r.distancia ,ds.id_punto, ds.fecha, ds.hora, ds.velocidad, ds.velocidad, ds.latitud, ds.longitud "
                . "from kbushistoricodb.dato_skps ds, equipos e, vehiculos v ,rutas r "
                . "where ds.fecha = '$fechaIni' and ds.hora >= time(subtime('$horaIni', '00:10:00')) and ds.hora <= '$hora_fin' "
                . "and ds.id_equipo = e.id_equipo "
                . "and e.id_equipo = v.id_equipo "
                . "and v.id_vehiculo = $idVehicle "
                . "and r.id_ruta = $idRoute "
                . "and ds.id_punto in (select id_punto from punto_rutas where id_ruta = $idRoute) "
                . "order by ds.hora";
        $queryvelocidad = "SELECT avg(velocidad) as velocidadpromedio FROM kbushistoricodb.dato_skps where fecha='$fechaIni' and id_equipo = (select id_equipo from kbusdb.vehiculos where id_vehiculo= $idVehicle)  and fecha = '$fechaIni' and hora >= time(subtime('$horaIni', '00:10:00')) and hora <= '$hora_fin'";
        $resultvl = $mysqli->query($queryvelocidad);
        if ($resultvl->num_rows > 0) {
            while ($myrowvl = $resultvl->fetch_assoc()) {
                $velocidad = $myrowvl["velocidadpromedio"];
            }
        }
    } else {
        $consultaSql = "SELECT r.distancia, ds.id_punto, ds.fecha, ds.hora, ds.velocidad, ds.velocidad, ds.latitud, ds.longitud "
                . "FROM kbushistoricodb.dato_fastracks ds, equipos e, vehiculos v,rutas r "
                . "WHERE ds.fecha = '$fechaIni' and ds.hora >= time(subtime('$horaIni', '00:10:00')) and ds.hora <= '$hora_fin' "
                . "and ds.id_equipo = e.id_equipo "
                . "and e.id_equipo = v.id_equipo "
                . "and v.id_vehiculo = $idVehicle "
                . "and r.id_ruta = $idRoute "
                . "and ds.id_punto in (select id_punto from punto_rutas where id_ruta = $idRoute) "
                . "order by ds.hora";
        // avg(ds.velocidad) as velocidadpromedio,
        $queryvelocidad = "SELECT avg(velocidad) as velocidadpromedio FROM kbushistoricodb.dato_fastracks where fecha='$fechaIni' and id_equipo = (select id_equipo from kbusdb.vehiculos where id_vehiculo= $idVehicle)  and fecha = '$fechaIni' and hora >= time(subtime('$horaIni', '00:10:00')) and hora <= '$hora_fin'";
        $resultvl = $mysqli->query($queryvelocidad);
        if ($resultvl->num_rows > 0) {
            while ($myrowvl = $resultvl->fetch_assoc()) {
                $velocidad = $myrowvl["velocidadpromedio"];
            }
        }
        $consultafsk = "SELECT count(distinct  hora) as conteo  FROM kbushistoricodb.dato_fastracks where fecha='$fechaIni' and id_equipo = (select id_equipo from kbusdb.vehiculos where id_vehiculo= $idVehicle) and (id_encabezado=3 or id_encabezado=8 ) and fecha = '$fechaIni' and hora >= time(subtime('$horaIni', '00:10:00')) and hora <= '$hora_fin'";
        $resultsf = $mysqli->query($consultafsk);
        if ($resultsf->num_rows > 0) {
            while ($myrowsf = $resultsf->fetch_assoc()) {
                $numeroreinicios = $myrowsf["conteo"];
            }
        }
        $consultagsm = "SELECT count(distinct  hora)  as conteo  FROM kbushistoricodb.dato_fastracks where fecha='$fechaIni' and id_equipo = (select id_equipo from kbusdb.vehiculos where id_vehiculo= $idVehicle) and (id_encabezado=7) and fecha = '$fechaIni' and hora >= time(subtime('$horaIni', '00:10:00')) and hora <= '$hora_fin'";
        $resultgsm = $mysqli->query($consultagsm);
        if ($resultsf->num_rows > 0) {
            while ($myrowgsm = $resultgsm->fetch_assoc()) {
                $numerogsm = $myrowgsm["conteo"];
            }
        }
        $consultagps = "SELECT count(distinct  hora)  as conteo  FROM kbushistoricodb.dato_fastracks where fecha='$fechaIni' and id_equipo = (select id_equipo from kbusdb.vehiculos where id_vehiculo= $idVehicle) and (id_encabezado=6 ) and fecha = '$fechaIni' and hora >= time(subtime('$horaIni', '00:10:00')) and hora <= '$hora_fin'";
        $resultgps = $mysqli->query($consultagps);
        if ($resultgps->num_rows > 0) {
            while ($myrowgps = $resultgps->fetch_assoc()) {
                $numerogps = $myrowgps["conteo"];
            }
        }
        $consultatramas = "SELECT count(distinct hora)  as conteo  FROM kbushistoricodb.dato_fastracks where fecha='$fechaIni' and id_equipo = (select id_equipo from kbusdb.vehiculos where id_vehiculo= $idVehicle) and (id_encabezado=2 ) and fecha = '$fechaIni' and hora >= time(subtime('$horaIni', '00:10:00')) and hora <= '$hora_fin'";
        $resulttramas = $mysqli->query($consultatramas);
        if ($resulttramas->num_rows > 0) {
            while ($myrowtramas = $resulttramas->fetch_assoc()) {
                $numerotramas = $myrowtramas["conteo"];
            }
        }
    }

    $resulsetRec = $mysqli->query($consultaSql);

    $mysqli->close();

    if ($resulsetRec->num_rows > 0) {

        $resulsetRec = allRows($resulsetRec);

        for ($i = 0; $i < count($resulsetPuntos); $i++) {
            $fila = $resulsetPuntos[$i];

            $hora_ini_int = strtotime($horaIni);
            $time_debio_int = strtotime($fila["tiempos"]);

            $minute = date("i", $time_debio_int);
            $second = date("s", $time_debio_int);
            $hour = date("H", $time_debio_int);

            $convert = strtotime("+$minute minutes", $hora_ini_int);
            $convert = strtotime("+$second seconds", $convert);
            $convert = strtotime("+$hour hours", $convert);
            $horaIni = date('H:i:s', $convert);

            //Array de la Papeleta ha llenar.
            $papeleta[$i] = array(
                'idPunto' => $fila["id_punto"],
                'orden' => $fila["orden"],
                'punto' => utf8_encode($fila["punto"]),
                'tiempos' => $fila["tiempos"],
                'tiempoDebio' => $horaIni,
                'imprimir' => $fila["imprimir"]
            );
        }

        //Presentacion de el resultado de los puntos del recorridos real.
        if (count($resulsetRec) > 0) {
            //2º -> Se realiza la comparacion entre los puntos y el tiempo que deberia llegar
            //y el tiempo que realmente llego, tomando en cuenta que la diferencia entre ambos
            //debe ser menor a 5 minutos, luego estos se agregan a un arreglo para proceder a
            //eliminar los repetidos.
            $c = 0;
            foreach ($papeleta as $value) {
                for ($i = 0; $i < count($resulsetRec); $i++) {
                    $fila = $resulsetRec[$i];
                    //     $velocidad = $fila["velocidadpromedio"];
                    $distancia = $fila["distancia"];
                    if ($value["idPunto"] == $fila["id_punto"] && difTimeCorrect($value["tiempoDebio"], $fila["hora"], '00:08:00')) {
                        $recorridos[$c] = array(
                            'idPunto' => $value["idPunto"],
                            'orden' => $value["orden"],
                            'punto' => $value["punto"],
                            'tiempos' => $value["tiempos"],
                            'tiempoDebio' => $value["tiempoDebio"],
                            'tiempoLlego' => $fila["hora"],
                            'tiempoDif' => "",
                            'latitud' => $fila["latitud"],
                            'longitud' => $fila["longitud"],
                            'velocidad' => $fila["velocidad"],
                            'imprimir' => $value["imprimir"],
                            'estado' => false
                        );
                        $c++;
                    }
                }
            }

            if (isset($recorridos)) {
                $result = $recorridos;
                //Se elimina puntos repetidos, considerando si se encuentran juntos,
                //y solo saltando el punto si la diferencia de los tiempos de llegada
                //es menor al dato enviado como parametro en este caso '00:05:00',
                //si es mayor a ese tiempo, no lo salta.
                for ($j = 0; $j < 5; $j++) {
                    $result = removeRepeatedByTime($result);
                }

                //Verificar si los puntos en la papeleta se duplican
                $repeatedPointsPapeleta = repeated($papeleta);
                if ($repeatedPointsPapeleta == null) {
                    $result = removeRepeatedGeneral($result);
                } else {
                    $repeatedPointsRecorrido = repeated($result);
                    if ($repeatedPointsRecorrido != null) {
                        foreach ($repeatedPointsRecorrido as $value) {
                            $repeat = true;
                            foreach ($repeatedPointsPapeleta as $value2) {
                                if ($value["idPunto"] == $value2["idPunto"]) {
                                    if (!isJuntos($papeleta, $value2["idPunto"])) {
                                        $result = removeRepeatedByPointAndTime($result, $value2["idPunto"]);
                                    }
                                    $repeat = false;
                                    break;
                                }
                            }
                            if ($repeat) {
                                $result = removeRepeatedByPoint($result, $value["idPunto"]);
                            }
                        }
                    }
                }

                $cFinish = 0;
                $resultFinish;
                $json = "puntos: [";
                foreach ($papeleta as $value1) {
                    $limite = 0;
                    $limiteRecorridos = false;
                    for ($i = 0; $i < count($result); $i++) {
                        $value = $result[$i];
                        if (!$limiteRecorridos) {
                            if ($limite < 4) {
                                if (!$value["estado"]) {
                                    if ($value["idPunto"] == $value1["idPunto"] && difTimeCorrect($value1["tiempoDebio"], $value["tiempoLlego"], '00:08:00')) {
                                        $json .= "{"
                                                . "idVehicleView:" . $idVehicle . ","
                                                . "idPointView:'" . $value1["idPunto"] . "',"
                                                . "orderPointView:'" . $value1["orden"] . "',"
                                                . "pointView:'" . $value1["punto"] . "',"
                                                . "fecha:'" . $fechaIni . "',"
                                                . "timeView:'" . $value1["tiempos"] . "',"
                                                . "timeDebView:'" . $value1["tiempoDebio"] . "',"
                                                . "timeLlView:'" . $value["tiempoLlego"] . "',"
                                                . "timeDifView:'" . $value["tiempoDif"] . "',"
                                                . "latitud:'" . $value["latitud"] . "',"
                                                . "longitud:'" . $value["longitud"] . "',"
                                                . "velocidad:'" . $value["velocidad"] . "',"
                                                . "sancionable:'" . $value1["imprimir"] . "',"
                                                . "bateria:'1'},";
                                        $result[$i]["estado"] = true;

                                        $resultFinish[$cFinish] = array(
                                            'tiempoDebio' => $value1["tiempoDebio"],
                                            'tiempoLlego' => $value["tiempoLlego"],
                                            'imprimir' => $value1["imprimir"]
                                        );
                                        $cFinish++;
                                        break;
                                    } else {
                                        $limite++;
                                        if ($i == count($result) - 1) {
                                            $limiteRecorridos = true;
                                        }
                                    }
                                } else {
                                    if ($i == count($result) - 1) {
                                        $limiteRecorridos = true;
                                    }
                                }
                            } else {
                                $json .= "{"
                                        . "idVehicleView:" . $idVehicle . ","
                                        . "idPointView:'" . $value1["idPunto"] . "',"
                                        . "orderPointView:'" . $value1["orden"] . "',"
                                        . "pointView:'" . $value1["punto"] . "',"
                                        . "fecha:'" . $fechaIni . "',"
                                        . "timeView:'" . $value1["tiempos"] . "',"
                                        . "timeDebView:'" . $value1["tiempoDebio"] . "',"
                                        . "timeLlView:'',"
                                        . "timeDifView:'',"
                                        . "latitud:'" . $value["latitud"] . "',"
                                        . "longitud:'" . $value["longitud"] . "',"
                                        . "velocidad:'',"
                                        . "sancionable:'" . $value1["imprimir"] . "',"
                                        . "bateria:'1'},";
                                if ($i == count($result) - 1) {
                                    $limiteRecorridos = false;
                                }
                                break;
                            }
                        } else {
                            $json .= "{"
                                    . "idVehicleView:" . $idVehicle . ","
                                    . "idPointView:'" . $value1["idPunto"] . "',"
                                    . "orderPointView:'" . $value1["orden"] . "',"
                                    . "pointView:'" . $value1["punto"] . "',"
                                    . "fecha:'" . $fechaIni . "',"
                                    . "timeView:'" . $value1["tiempos"] . "',"
                                    . "timeDebView:'" . $value1["tiempoDebio"] . "',"
                                    . "timeLlView:'',"
                                    . "timeDifView:'',"
                                    . "latitud:'" . $value["latitud"] . "',"
                                    . "longitud:'" . $value["longitud"] . "',"
                                    . "velocidad:'',"
                                    . "sancionable:'" . $value1["imprimir"] . "',"
                                    . "bateria:'1'},";
                        }
                    }
                    if ($limiteRecorridos) {
                        $json .= "{"
                                . "idVehicleView:" . $idVehicle . ","
                                . "idPointView:'" . $value1["idPunto"] . "',"
                                . "orderPointView:'" . $value1["orden"] . "',"
                                . "pointView:'" . $value1["punto"] . "',"
                                . "fecha:'" . $fechaIni . "',"
                                . "timeView:'" . $value1["tiempos"] . "',"
                                . "timeDebView:'" . $value1["tiempoDebio"] . "',"
                                . "timeLlView:'',"
                                . "timeDifView:'',"
                                . "latitud:'" . $value["latitud"] . "',"
                                . "longitud:'" . $value["longitud"] . "',"
                                . "velocidad:'',"
                                . "sancionable:'" . $value1["imprimir"] . "',"
                                . "bateria:'1'},";
                    }
                }

                $json .= "{idVehicleView:" . $idVehicle . ",idPointView:' ',orderPointView:' ', fecha: ' ', timeView:' ',timeDebView:' ', timeLlView:' ', timeDifView:' ',"
                        . "latitud:' ', longitud: ' ', velocidad: ' ', sancionable:' ', bateria:' ', pointView:' '},";
                $json .= "{"
                        . "idVehicleView:" . $idVehicle . ","
                        . "idPointView:' ',"
                        . "orderPointView:' ',"
                        . "fecha: ' ',"
                        . "timeView:' ',"
                        . "timeDebView:'<b>ATRA: </b>',"
                        . "timeLlView:'<B>" . sumaTiempos($resultFinish, true) . "</B>',"
                        . "timeDifView:' ',"
                        . "latitud:' ',"
                        . "longitud:' ',"
                        . "velocidad:' ',"
                        . "sancionable:' ',"
                        . "bateria:' ',"
                        . "pointView:' '},";
                $json .= "{"
                        . "idVehicleView:" . $idVehicle . ","
                        . "idPointView:' ',"
                        . "orderPointView:' ',"
                        . "fecha: ' ',"
                        . "timeView:' ',"
                        . "timeDebView:'<b>ADEL: </b>',"
                        . "timeLlView:'<B><font color=black>" . sumaTiempos($resultFinish, false) . "</font></B>',"
                        . "timeDifView:' ',"
                        . "latitud: ' ',"
                        . "longitud: ' ',"
                        . "velocidad:' ',"
                        . "sancionable:' ',"
                        . "bateria:' ',"
                        . "velocidadoperacion:'" . round($velocidad, 2) . " ',"
                        . "distancia:'" . $distancia . " ',"
                        . "reinicios:'" . $numeroreinicios . " ',"
                        . "gps:'" . $numerogps . " ',"
                        . "gsm:'" . $numerogsm . " ',"
                        . "tramas:'" . $numerotramas . " ',"
                        . "pointView:' '}";


                $json .="]";

                echo "{success:true, $json}";
            } else {
                echo "{failure:true, message: 'No hay datos de tiempos correctos.'}";
            }
        } else {
            echo "{failure:true, message: 'No hay datos para realizar el calculo de la papeleta.'}";
        }
    } else {
        echo "{failure:true, message: 'No hay datos para generar la Papeleta.'}";
    }
}

function repeated($array) {
    $c = 0;
    $result = null;
    $pointRepeated;
    for ($i = 0; $i < count($array); $i++) {
        $fila = $array[$i];
        if ($result != null) {
            for ($k = 0; $k < count($result); $k++) {
                $fila3 = $result[$k];
                if ($fila["idPunto"] == $fila3["idPunto"]) {
                    $pointRepeated = $fila["idPunto"];
                }
            }
        } else {
            $pointRepeated = -1;
        }
        if ($pointRepeated != $fila["idPunto"]) {
            for ($j = 0; $j < count($array); $j++) {
                $fila2 = $array[$j];
                if ($i != $j && $fila["idPunto"] == $fila2["idPunto"]) {
                    $result[$c] = array(
                        'idPunto' => $fila["idPunto"]
                    );
                    $c++;
                }
            }
        }
    }
    return $result;
}

function removeRepeatedByTime($array) {
    $c = 0;
    for ($i = 0; $i < count($array); $i++) {
        $fila = $array[$i];
        if ($i < count($array) - 1) {
            $filanext = $array[$i + 1];
            if ($fila["idPunto"] != $filanext["idPunto"]) {
                $result[$c] = array(
                    'idPunto' => $fila["idPunto"],
                    'orden' => $fila["orden"],
                    'punto' => $fila["punto"],
                    'tiempos' => $fila["tiempos"],
                    'tiempoDebio' => $fila["tiempoDebio"],
                    'tiempoLlego' => $fila["tiempoLlego"],
                    'tiempoDif' => sanction(difTime($fila["tiempoDebio"], $fila["tiempoLlego"]), $fila["imprimir"]),
                    'latitud' => $fila["latitud"],
                    'longitud' => $fila["longitud"],
                    'velocidad' => $fila["velocidad"],
                    'imprimir' => $fila["imprimir"],
                    'estado' => false
                );
                $c++;
            } else {
                if (!difTimeCorrect($fila["tiempoLlego"], $filanext["tiempoLlego"], '00:08:00')) {
                    $result[$c] = array(
                        'idPunto' => $fila["idPunto"],
                        'orden' => $fila["orden"],
                        'punto' => $fila["punto"],
                        'tiempos' => $fila["tiempos"],
                        'tiempoDebio' => $fila["tiempoDebio"],
                        'tiempoLlego' => $fila["tiempoLlego"],
                        'tiempoDif' => sanction(difTime($fila["tiempoDebio"], $fila["tiempoLlego"]), $fila["imprimir"]),
                        'latitud' => $fila["latitud"],
                        'longitud' => $fila["longitud"],
                        'velocidad' => $fila["velocidad"],
                        'imprimir' => $fila["imprimir"],
                        'estado' => false
                    );
                    $c++;
                } else {
                    $result[$c] = array(
                        'idPunto' => $fila["idPunto"],
                        'orden' => $fila["orden"],
                        'punto' => $fila["punto"],
                        'tiempos' => $fila["tiempos"],
                        'tiempoDebio' => $fila["tiempoDebio"],
                        'tiempoLlego' => $fila["tiempoLlego"],
                        'tiempoDif' => sanction(difTime($fila["tiempoDebio"], $fila["tiempoLlego"]), $fila["imprimir"]),
                        'latitud' => $fila["latitud"],
                        'longitud' => $fila["longitud"],
                        'velocidad' => $fila["velocidad"],
                        'imprimir' => $fila["imprimir"],
                        'estado' => false
                    );
                    $c++;
                    $i++;
                }
            }
        } else {
            $result[$c] = array(
                'idPunto' => $fila["idPunto"],
                'orden' => $fila["orden"],
                'punto' => $fila["punto"],
                'tiempos' => $fila["tiempos"],
                'tiempoDebio' => $fila["tiempoDebio"],
                'tiempoLlego' => $fila["tiempoLlego"],
                'tiempoDif' => sanction(difTime($fila["tiempoDebio"], $fila["tiempoLlego"]), $fila["imprimir"]),
                'latitud' => $fila["latitud"],
                'longitud' => $fila["longitud"],
                'velocidad' => $fila["velocidad"],
                'imprimir' => $fila["imprimir"],
                'estado' => false
            );
        }
    }
    return $result;
}

function removeRepeatedGeneral($array) {
    $c = 0;
    for ($i = 0; $i < count($array); $i++) {
        $fila = $array[$i];
        if ($i < count($array) - 1) {
            $filanext = $array[$i + 1];
            if ($fila["idPunto"] != $filanext["idPunto"]) {
                $result[$c] = $fila;
                $c++;
            } else {
                $result[$c] = $fila;
                $c++;
                $i++;
            }
        } else {
            $result[$c] = $fila;
        }
    }
    return $result;
}

function removeRepeatedByPointAndTime($array, $idPoint) {
    $c = 0;
    for ($i = 0; $i < count($array); $i++) {
        $fila = $array[$i];
        if ($i < count($array) - 1) {
            $filanext = $array[$i + 1];
            if ($fila["idPunto"] != $filanext["idPunto"]) {
                $result[$c] = $fila;
                $c++;
            } else {
                if ($fila["idPunto"] == $idPoint && difTimeCorrect($fila["tiempoLlego"], $filanext["tiempoLlego"], '00:08:00')) {
                    $result[$c] = $fila;
                    $c++;
                    $i++;
                } else {
                    $result[$c] = $fila;
                    $c++;
                }
            }
        } else {
            $result[$c] = $fila;
        }
    }
    return $result;
}

function removeRepeatedByPoint($array, $idPoint) {
    $c = 0;
    for ($i = 0; $i < count($array); $i++) {
        $fila = $array[$i];
        if ($i < count($array) - 1) {
            $filanext = $array[$i + 1];
            if ($fila["idPunto"] != $filanext["idPunto"]) {
                $result[$c] = $fila;
                $c++;
            } else {
                if ($fila["idPunto"] == $idPoint && difTimeCorrect($fila["tiempoLlego"], $filanext["tiempoLlego"], '00:08:00')) {
                    $result[$c] = $fila;
                    $c++;
                    $i++;
                } else {
                    $result[$c] = $fila;
                    $c++;
                }
            }
        } else {
            $result[$c] = $fila;
        }
    }
    return $result;
}

function isJuntos($array, $idPoint) {
    for ($i = 0; $i < count($array); $i++) {
        $fila = $array[$i];
        if ($i < count($array) - 1) {
            $filanext = $array[$i + 1];
            if ($fila["idPunto"] == $filanext["idPunto"] && $fila["idPunto"] == $idPoint) {
                return true;
            }
        }
    }
    return false;
}

function sanction($time, $imprimir) {
    if ($imprimir == 1) {
        return $time;
    } else {
        return "No Sancionable";
    }
}

function sumaTiempos($array, $isDelay) {
    $suma = strtotime("00:00:00");
    foreach ($array as $value) {
        if ($value["imprimir"] == 1) {
            if ($isDelay) {
                if ($value["tiempoDebio"] < $value["tiempoLlego"]) {
                    $timeInt = strtotime(difTime($value["tiempoDebio"], $value["tiempoLlego"]));

                    $minute = date("i", $timeInt);
                    $second = date("s", $timeInt);
                    $hour = date("H", $timeInt);

                    $convert = strtotime("+$minute minutes", $suma);
                    $convert = strtotime("+$second seconds", $convert);
                    $convert = strtotime("+$hour hours", $convert);
                    $suma = $convert;
                }
            } else {
                if ($value["tiempoDebio"] > $value["tiempoLlego"]) {
                    $timeInt = strtotime(substr(difTime($value["tiempoDebio"], $value["tiempoLlego"]), 1));

                    $minute = date("i", $timeInt);
                    $second = date("s", $timeInt);
                    $hour = date("H", $timeInt);

                    $convert = strtotime("+$minute minutes", $suma);
                    $convert = strtotime("+$second seconds", $convert);
                    $convert = strtotime("+$hour hours", $convert);
                    $suma = $convert;
                }
            }
        }
    }

    return date('H:i:s', $suma);
}

function difTime($tiempoDebio, $tiempoLlego) {
    $time_debio_nuevo_int = strtotime($tiempoDebio);
    $time_llego_nuevo_int = strtotime($tiempoLlego);

    if ($tiempoDebio > $tiempoLlego) {
        $minute = date("i", $time_llego_nuevo_int);
        $second = date("s", $time_llego_nuevo_int);
        $hour = date("H", $time_llego_nuevo_int);

        $convert = strtotime("-$minute minutes", $time_debio_nuevo_int);
        $convert = strtotime("-$second seconds", $convert);
        $convert = strtotime("-$hour hours", $convert);
        $timeDif = date('H:i:s', $convert);
        return '-' . $timeDif;
    } else {
        $minute = date("i", $time_debio_nuevo_int);
        $second = date("s", $time_debio_nuevo_int);
        $hour = date("H", $time_debio_nuevo_int);

        $convert = strtotime("-$minute minutes", $time_llego_nuevo_int);
        $convert = strtotime("-$second seconds", $convert);
        $convert = strtotime("-$hour hours", $convert);
        $timeDif = date('H:i:s', $convert);
        return $timeDif;
    }
}

function difTimeCorrect($tiempoDebio, $tiempoLlego, $condicion) {
    $time_debio_nuevo_int = strtotime($tiempoDebio);
    $time_llego_nuevo_int = strtotime($tiempoLlego);

    if ($tiempoDebio > $tiempoLlego) {
        $minute = date("i", $time_llego_nuevo_int);
        $second = date("s", $time_llego_nuevo_int);
        $hour = date("H", $time_llego_nuevo_int);

        $convert = strtotime("-$minute minutes", $time_debio_nuevo_int);
        $convert = strtotime("-$second seconds", $convert);
        $convert = strtotime("-$hour hours", $convert);
        $timeDif = date('H:i:s', $convert);

        if ($timeDif <= $condicion) {
            return true;
        } else {
            return false;
        }
    } else {
        $minute = date("i", $time_debio_nuevo_int);
        $second = date("s", $time_debio_nuevo_int);
        $hour = date("H", $time_debio_nuevo_int);

        $convert = strtotime("-$minute minutes", $time_llego_nuevo_int);
        $convert = strtotime("-$second seconds", $convert);
        $convert = strtotime("-$hour hours", $convert);
        $timeDif = date('H:i:s', $convert);

        if ($timeDif <= $condicion) {
            return true;
        } else {
            return false;
        }
    }
}
