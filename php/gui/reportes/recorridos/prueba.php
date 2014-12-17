<?php

include('../../../login/isLogin.php');
include ('../../../../dll/config.php');

$proceso = 1;
extract($_POST);

if (!$mysqli = getConectionDb()) {
    echo "{failure:true, msg: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {
    //no es necesario.. ya que
    //en la base del procedure esta el campo 
    //como un Time :: $hora = 2013-05-16 08:00:00
    //solo toma la hora 
    //$hora = substr($hora, 11); 
    $fechaIni = "2014-08-26";
    $horaIni = "08:20";
    $idRoute = 11;
    $idVehicle = 213;

    if (isset($horaIniC)) {
        $horaIni = $horaIniC;
    }

    $consultaHoraFinRuta = "select addtime(addtime(time(sec_to_time(sum(time_to_sec(tiempos)))), '$horaIni'), '00:10:00') as hora_fin "
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
        $consultaSql = "select ds.id_punto, ds.fecha, ds.hora, ds.latitud, ds.longitud, ds.velocidad, p.punto "
                . "from kbushistoricodb.dato_skps ds, equipos e, vehiculos v, puntos p "
                . "where ds.fecha = '$fechaIni' and ds.hora >= time(subtime('$horaIni', '00:05:00')) and ds.hora <= time(addtime('$hora_fin', '00:20:00')) "
                . "and ds.id_equipo = e.id_equipo "
                . "and e.id_equipo = v.id_equipo "
                . "and p.id_punto = ds.id_punto "
                . "and v.id_vehiculo = $idVehicle "
                . "and ds.id_punto in (select id_punto from punto_rutas where id_ruta = $idRoute) "
                . "order by ds.hora";
    } else {
        $consultaSql = "select ds.id_punto, ds.fecha, ds.hora, ds.latitud, ds.longitud, ds.velocidad, p.punto "
                . "from kbushistoricodb.dato_fastracks ds, equipos e, vehiculos v, puntos p "
                . "where ds.fecha = '$fechaIni' and ds.hora >= time(subtime('$horaIni', '00:05:00')) and ds.hora <= time(addtime('$hora_fin', '00:20:00')) "
                . "and ds.id_equipo = e.id_equipo "
                . "and e.id_equipo = v.id_equipo "
                . "and p.id_punto = ds.id_punto "
                . "and v.id_vehiculo = $idVehicle "
                . "and ds.id_punto in (select id_punto from punto_rutas where id_ruta = $idRoute) "
                . "order by ds.hora";
    }

    $resulsetRec = $mysqli->query($consultaSql);

    $mysqli->close();

    //1º -> Se agrega puntos a un array de la papeleta ya sumandole el tiempo que deberia llegar.
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
                'tiempoDebio' => $horaIni,
                'imprimir' => $fila["imprimir"],
                'estado' => 0
            );
        }

        //Presentacion de la Papeleta
        foreach ($papeleta as $value) {
            echo $value["idPunto"] . " => " . $value["punto"] . " => " . $value["tiempoDebio"] . "<br>";
        }

        echo "-------------------------------------------------------------<br>";

        //Presentacion de el resultado de los puntos del recorridos real.
        if (count($resulsetRec) > 0) {
            for ($i = 0; $i < count($resulsetRec); $i++) {
                $fila = $resulsetRec[$i];
                echo $fila["id_punto"] . " => " . $fila["punto"] . " => " . $fila["hora"] . "<br>";
            }

            //2º -> Se realiza la comparacion entre los puntos y el tiempo que deberia llegar
            //y el tiempo que realmente llego, tomando en cuenta que la diferencia entre ambos
            //debe ser menor a 5 minutos, luego estos se agregan a un arreglo para proceder a
            //eliminar los repetidos.
            $c = 0;
            foreach ($papeleta as $value) {
                for ($i = 0; $i < count($resulsetRec); $i++) {
                    $fila = $resulsetRec[$i];
                    if ($value["idPunto"] == $fila["id_punto"] && difTimeCorrect($value["tiempoDebio"], $fila["hora"], '00:08:00')) {
                        $recorridos[$c] = array(
                            'idPunto' => $value["idPunto"],
                            'orden' => $value["orden"],
                            'punto' => $value["punto"],
                            'tiempoDebio' => $value["tiempoDebio"],
                            'tiempoLlego' => $fila["hora"],
                            'imprimir' => $value["imprimir"],
                            'estado' => false
                        );
                        $c++;
                    }
                }
            }

            echo "-------------------------------------------------------------<br>";

            if (isset($recorridos)) {
                //Presentacion de las papeletas
                foreach ($recorridos as $value) {
                    echo $value["idPunto"] . " => " . $value["punto"] . " => " . $value["tiempoDebio"] . " => " . $value["tiempoLlego"] . "<br>";
                }

                echo "-------------------------------------------------------------<br>";

                $result = $recorridos;
                for ($j = 0; $j < 5; $j++) {
                    $result = removeRepeatedByTime($result);
                }

                foreach ($result as $value) {
                    echo $value["idPunto"] . " => " . $value["punto"] . " => " . $value["tiempoDebio"] . " => " . $value["tiempoLlego"] . "<br>";
                }

                echo "-------------------------------------------------------------<br>";

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
                                echo $value["idPunto"] . " == " . $value2["idPunto"] . "<br>";
                                if ($value["idPunto"] == $value2["idPunto"]) {
                                    if (!isJuntos($papeleta, $value2["idPunto"])) {
                                        echo "Estan juntos en recorridos pero no estan Juntos en la papeleta [" . $value2["idPunto"] . "]<br>";
                                        $result = removeRepeatedByPointAndTime($result, $value2["idPunto"]);
                                    }
                                    $repeat = false;
                                    break;
                                }
                            }

                            if ($repeat) {
                                echo "Se elimina ya que en la papeleta no esta repetido pero en los recorridos si se repite" . $value["idPunto"] . "<br>";
                                $result = removeRepeatedByPoint($result, $value["idPunto"]);
                            }
                        }
                    }
                }
                
                echo "-------------------------------------------------------------<br>";

                foreach ($result as $value) {
                    echo $value["idPunto"] . " => " . $value["punto"] . " => " . $value["tiempoDebio"] . " => " . $value["tiempoLlego"] . "<br>";
                }

                echo "-------------------------------------------------------------<br>";

                foreach ($papeleta as $value1) {
                    $limite = 0;
                    $limiteRecorridos = false;
                    for ($i = 0; $i < count($result); $i++) {
                        $value = $result[$i];
                        if (!$limiteRecorridos) {
                            if ($limite < 4) {
                                if (!$value["estado"]) {
                                    if ($value1["idPunto"] == $value["idPunto"]) {
                                        echo $value1["idPunto"] . " => " . $value1["punto"] . "<br>";
                                        $result[$i]["estado"] = true;

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
                                echo $value1["idPunto"] . " => " . $value1["punto"] . "<br>";
                                if ($i == count($result) - 1) {
                                    $limiteRecorridos = false;
                                }
                                break;
                            }
                        } else {
                            echo $value1["idPunto"] . " => " . $value1["punto"] . "<br>";
                        }
                    }
                    if ($limiteRecorridos) {
                        echo $value1["idPunto"] . " => " . $value1["punto"] . "<br>";
                    }
                }
            } else {
                echo "No hay datos para tiempos correctos.";
            }
        } else {
            echo "No hay datos para realizar el calculo de la papeleta.";
        }
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
                    'tiempoDebio' => $fila["tiempoDebio"],
                    'tiempoLlego' => $fila["tiempoLlego"],
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
                        'tiempoDebio' => $fila["tiempoDebio"],
                        'tiempoLlego' => $fila["tiempoLlego"],
                        'imprimir' => $fila["imprimir"],
                        'estado' => false
                    );
                    $c++;
                } else {
                    $result[$c] = array(
                        'idPunto' => $fila["idPunto"],
                        'orden' => $fila["orden"],
                        'punto' => $fila["punto"],
                        'tiempoDebio' => $fila["tiempoDebio"],
                        'tiempoLlego' => $fila["tiempoLlego"],
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
                'tiempoDebio' => $fila["tiempoDebio"],
                'tiempoLlego' => $fila["tiempoLlego"],
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
