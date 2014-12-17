<?php

include('../../../login/isLogin.php');
include ('../../../../dll/config.php');

$proceso = 1;
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

    $consultaHoraFinRuta = "select addtime(addtime(time(sec_to_time(sum(time_to_sec(tiempos)))), '$horaIni'), '00:10:00') as hora_fin "
            . "from punto_rutas "
            . "where id_ruta = $idRoute";

    $consultaPuntos = "select pr.id_punto, pr.orden, pr.tiempos, p.punto, p.geocerca_skp, pr.imprimir, p.latitud, p.longitud "
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
        $consultaSql = "select ds.id_punto, ds.fecha, ds.hora, ds.velocidad "
                . "from kbushistoricodb.dato_skps ds, equipos e, vehiculos v "
                . "where ds.fecha = '$fechaIni' and ds.hora >= '$horaIni' and ds.hora <= time(addtime('$hora_fin', '00:20:00')) "
                . "and ds.id_equipo = e.id_equipo "
                . "and e.id_equipo = v.id_equipo "
                . "and v.id_vehiculo = $idVehicle "
                . "and ds.id_punto in (select id_punto from punto_rutas where id_ruta = $idRoute) "
                . "order by ds.hora";
    } else {
        $consultaSql = "select ds.id_punto, ds.fecha, ds.hora, ds.velocidad "
                . "from kbushistoricodb.dato_fastracks ds, equipos e, vehiculos v "
                . "where ds.fecha = '$fechaIni' and ds.hora >= '$horaIni' and ds.hora <= time(addtime('$hora_fin', '00:20:00')) "
                . "and ds.id_equipo = e.id_equipo "
                . "and e.id_equipo = v.id_equipo "
                . "and v.id_vehiculo = $idVehicle "
                . "and ds.id_punto in (select id_punto from punto_rutas where id_ruta = $idRoute) "
                . "order by ds.hora";
    }

    $resulsetRec = $mysqli->query($consultaSql);

    $mysqli->close();

    if ($resulsetRec->num_rows > 0) {
        $resulsetRec = allRows($resulsetRec);

        for ($i = 0; $i < count($resulsetPuntos); $i++) {
            $fila = $resulsetPuntos[$i];
            $imprimir[$i] = $fila["imprimir"];
            $orden_punto[$i] = $fila["orden"];
            $id_punto[$i] = $fila["id_punto"];
            $times[$i] = $fila["tiempos"];
            $latitud[$i] = $fila["latitud"];
            $longitud[$i] = $fila["longitud"];
            $estado_puntos[$i] = 0;

            $hora_ini_int = strtotime($horaIni);
            $time_debio_int = strtotime($times[$i]);

            $minute = date("i", $time_debio_int);
            $second = date("s", $time_debio_int);
            $hour = date("H", $time_debio_int);

            $convert = strtotime("+$minute minutes", $hora_ini_int);
            $convert = strtotime("+$second seconds", $convert);
            $convert = strtotime("+$hour hours", $convert);
            $horaIni = date('H:i:s', $convert);

            if ($i == count($resulsetPuntos)) {
                $hora_fin_ruta = $horaIni;
            }

            $time_debio[$i] = $horaIni;
            $name_punto[$i] = utf8_encode($fila["punto"]);
            $id_geo[$i] = $fila["geocerca_skp"];
        }

        $d = 0;

        for ($i = 0; $i < count($resulsetRec); $i++) {
            $fila = $resulsetRec[$i];

            for ($j = 0; $j < count($id_punto); $j++) {
                if ($fila["id_punto"] == $id_punto[$j]) {
                    $id_punto_rec_ant[$d] = $fila["id_punto"];
                    $fecha_rec_ant[$d] = $fila["fecha"];
                    $hora_rec_ant[$d] = $fila["hora"];
                    $vel_rec_ant[$d] = $fila["velocidad"];
                    $d++;
                    $j = count($id_punto);
                }
            }
        }

        $c = 0;
        for ($i = 0; $i < count($id_punto_rec_ant); $i++) {
            if ($i < count($id_punto_rec_ant) - 1) {
                $time_repetidosP = strtotime($hora_rec_ant[$i]);
                $time_repetidosS = strtotime($hora_rec_ant[$i + 1]);

                $minuteP = date("i", $time_repetidosP);
                $secondP = date("s", $time_repetidosP);
                $hourP = date("H", $time_repetidosP);

                $minuteS = date("i", $time_repetidosS);
                $secondS = date("s", $time_repetidosS);
                $hourS = date("H", $time_repetidosS);

                if ($id_punto_rec_ant[$i] != $id_punto_rec_ant[$i + 1]) {
                    $id_punto_rec_ant1[$c] = $id_punto_rec_ant[$i];
                    $fecha_rec_ant1[$c] = $fecha_rec_ant[$i];
                    $hora_rec_ant1[$c] = $hora_rec_ant[$i];
                    $vel_rec_ant1[$c] = $vel_rec_ant[$i];
                } else {
                    if ($minuteP != $minuteS) {
                        $convert = strtotime("-$minuteP minutes", $time_repetidosS);
                        $convert = strtotime("-$secondP seconds", $convert);
                        $convert = strtotime("-$hourP hours", $convert);
                        $dif = date('H:i:s', $convert);

                        $id_punto_rec_ant1[$c] = $id_punto_rec_ant[$i];
                        $fecha_rec_ant1[$c] = $fecha_rec_ant[$i];
                        $hora_rec_ant1[$c] = $hora_rec_ant[$i];
                        $vel_rec_ant1[$c] = $vel_rec_ant[$i];
                        if ($dif <= '00:03:00') {
                            $i++;
                        }
                    } else {
                        $id_punto_rec_ant1[$c] = $id_punto_rec_ant[$i];
                        $fecha_rec_ant1[$c] = $fecha_rec_ant[$i];
                        $hora_rec_ant1[$c] = $hora_rec_ant[$i];
                        $vel_rec_ant1[$c] = $vel_rec_ant[$i];
                        $i++;
                    }
                }

                $c++;
            } else {
                $id_punto_rec_ant1[$c] = $id_punto_rec_ant[$i];
                $fecha_rec_ant1[$c] = $fecha_rec_ant[$i];
                $hora_rec_ant1[$c] = $hora_rec_ant[$i];
                $vel_rec_ant1[$c] = $vel_rec_ant[$i];
                //  $estado_rec[$c] = 0;
                //    $estado_rec[$c] = 0;
            }
        }

        $c = 0;
        for ($i = 0; $i < count($id_punto_rec_ant1); $i++) {
            if ($i < count($id_punto_rec_ant1) - 1) {
                $time_repetidosP = strtotime($hora_rec_ant1[$i]);
                $time_repetidosS = strtotime($hora_rec_ant1[$i + 1]);

                $minuteP = date("i", $time_repetidosP);
                $secondP = date("s", $time_repetidosP);
                $hourP = date("H", $time_repetidosP);

                $minuteS = date("i", $time_repetidosS);
                $secondS = date("s", $time_repetidosS);
                $hourS = date("H", $time_repetidosS);

                if ($id_punto_rec_ant1[$i] != $id_punto_rec_ant1[$i + 1]) {
                    $id_punto_rec[$c] = $id_punto_rec_ant1[$i];
                    $fecha_rec[$c] = $fecha_rec_ant1[$i];
                    $hora_rec[$c] = $hora_rec_ant1[$i];
                    $vel_rec[$c] = $vel_rec_ant1[$i];
                    $estado_rec[$c] = 0;
                } else {
                    if ($minuteP != $minuteS) {
                        $convert = strtotime("-$minuteP minutes", $time_repetidosS);
                        $convert = strtotime("-$secondP seconds", $convert);
                        $convert = strtotime("-$hourP hours", $convert);
                        $dif = date('H:i:s', $convert);

                        $id_punto_rec[$c] = $id_punto_rec_ant1[$i];
                        $fecha_rec[$c] = $fecha_rec_ant1[$i];
                        $hora_rec[$c] = $hora_rec_ant1[$i];
                        $vel_rec[$c] = $vel_rec_ant1[$i];
                        $estado_rec[$c] = 0;

                        if ($dif <= '00:03:00') {
                            $i++;
                        }
                    } else {
                        $id_punto_rec[$c] = $id_punto_rec_ant1[$i];
                        $fecha_rec[$c] = $fecha_rec_ant1[$i];
                        $hora_rec[$c] = $hora_rec_ant1[$i];
                        $vel_rec[$c] = $vel_rec_ant1[$i];
                        $estado_rec[$c] = 0;
                        $i++;
                    }
                }

                $c++;
            } else {
                $id_punto_rec[$c] = $id_punto_rec_ant1[$i];
                $fecha_rec[$c] = $fecha_rec_ant1[$i];
                $hora_rec[$c] = $hora_rec_ant1[$i];
                $vel_rec[$c] = $vel_rec_ant1[$i];
                $estado_rec[$c] = 0;
            }
        }

        $y = 0;
        $cantidad = 0;
        for ($i = 0; $i < count($id_punto); $i++) {
            for ($j = 0; $j < count($id_punto_rec); $j++) {
                if ($id_punto[$i] == $id_punto_rec[$j] && $estado_rec[$j] == 0) {
                    //Aqui Calcular diferencia y si es mayor a 5 minutos pasa al siguiente punto.
                    $correct = difTime($time_debio[$i], $hora_rec[$j]);
                    //Si es correcto ingresa al proceso normal, caso contrario no lo toma en cuenta.
                    //Recordar si algo falla quitar la condicion.
                    if ($correct) {
                        if ($cantidad > 0) {
                            $id_puntos_rec_lleno[$y] = $id_punto[$i];
                            $fecha_rec_lleno[$y] = $fecha_rec[$j];
                            $hora_rec_lleno[$y] = $hora_rec[$j];
                            $vel_rec_lleno[$y] = $vel_rec[$j];
                            $estado_rec[$j] = 1;

                            for ($p = 0; $p < count($estado_rec); $p++) {
                                if ($estado_rec[$p] == 2) {
                                    $estado_rec[$p + 1] = $estado_rec[$p] = 1;
                                }
                            }
                        } else {
                            $id_puntos_rec_lleno[$y] = $id_punto[$i];
                            $fecha_rec_lleno[$y] = $fecha_rec[$j];
                            $hora_rec_lleno[$y] = $hora_rec[$j];
                            $vel_rec_lleno[$y] = $vel_rec[$j];
                            $estado_rec[$j] = 1;
                        }

                        $j = count($id_punto_rec);
                        $y++;
                        $cantidad = 0;
                    }
                } else {
                    if ($estado_rec[$j] == 0 && $cantidad < 4) {
                        $cantidad++;

                        if ($cantidad == 4) {
                            $id_puntos_rec_lleno[$y] = $id_punto[$i];
                            $fecha_rec_lleno[$y] = $fecha_rec[0];
                            $hora_rec_lleno[$y] = "";
                            $vel_rec_lleno[$y] = "";

                            for ($p = 0; $p < count($estado_rec); $p++) {
                                if ($estado_rec[$p] == 2) {
                                    $estado_rec[$p] = 0;
                                }
                            }

                            $y++;
                            $cantidad = 0;
                            $j = count($id_punto_rec);
                        } else {
                            if ($j == count($id_punto_rec) - 1 && $cantidad > 0) {
                                $id_puntos_rec_lleno[$y] = $id_punto[$i];
                                $fecha_rec_lleno[$y] = $fecha_rec[0];
                                $hora_rec_lleno[$y] = "";
                                $vel_rec_lleno[$y] = "";

                                for ($p = 0; $p < count($estado_rec); $p++) {
                                    if ($estado_rec[$p] == 2) {
                                        $estado_rec[$p] = 0;
                                    }
                                }

                                $y++;
                                $cantidad = 0;
                            } else {
                                $estado_rec[$j] = 2;
                            }
                        }
                    } else {
                        if ($j == count($id_punto_rec) - 1) {
                            $id_puntos_rec_lleno[$y] = $id_punto[$i];
                            $fecha_rec_lleno[$y] = $fecha_rec[0];
                            $hora_rec_lleno[$y] = "";
                            $vel_rec_lleno[$y] = "";
                            $y++;
                        }
                    }
                }
            }
        }

        $atraso_int = strtotime("00:00:00");
        $adelanto_int = strtotime("00:00:00");

        $b = 0;
        for ($i = 0; $i < count($id_puntos_rec_lleno); $i++) {
            for ($j = 0; $j < count($id_punto); $j++) {
                if ($id_puntos_rec_lleno[$i] == $id_punto[$j]) {

                    $id_punto_nuevo[$b] = $id_punto[$j];
                    $time_llego_nuevo[$b] = $hora_rec_lleno[$i];
                    $vel_nuevo[$b] = $vel_rec_lleno[$i];

                    if ($time_llego_nuevo[$b] != "") {
                        if ($imprimir[$i] == 1) {
                            $time_debio_nuevo_int = strtotime($time_debio[$b]);
                            $time_llego_nuevo_int = strtotime($time_llego_nuevo[$b]);
                            if ($time_debio[$b] > $time_llego_nuevo[$b]) {

                                $minute = date("i", $time_llego_nuevo_int);
                                $second = date("s", $time_llego_nuevo_int);
                                $hour = date("H", $time_llego_nuevo_int);

                                $convert = strtotime("-$minute minutes", $time_debio_nuevo_int);
                                $convert = strtotime("-$second seconds", $convert);
                                $convert = strtotime("-$hour hours", $convert);
                                $time_dif[$b] = "-" . date('H:i:s', $convert);
                                $time_dif1 = date('H:i:s', $convert);

                                //if ($time_dif1 <= '00:05:00') {
                                $dif_int1 = strtotime($time_dif1);

                                $minuteDif1 = date("i", $dif_int1);
                                $secondDif1 = date("s", $dif_int1);
                                $hourDif1 = date("H", $dif_int1);

                                $convertDif1 = strtotime("+$minuteDif1 minutes", $adelanto_int);
                                $convertDif1 = strtotime("+$secondDif1 seconds", $convertDif1);
                                $convertDif1 = strtotime("+$hourDif1 hours", $convertDif1);
                                $adelanto_int = $convertDif1;
                                /* } else {
                                  $time_dif[$b] = "";
                                  $time_llego_nuevo[$b] = "";
                                  } */
                            } else {
                                $minute = date("i", $time_debio_nuevo_int);
                                $second = date("s", $time_debio_nuevo_int);
                                $hour = date("H", $time_debio_nuevo_int);

                                $convert = strtotime("-$minute minutes", $time_llego_nuevo_int);
                                $convert = strtotime("-$second seconds", $convert);
                                $convert = strtotime("-$hour hours", $convert);
                                $time_dif[$b] = date('H:i:s', $convert);

                                //if ($time_dif[$b] <= '00:05:00') {
                                $dif_int = strtotime($time_dif[$b]);

                                $minuteDif = date("i", $dif_int);
                                $secondDif = date("s", $dif_int);
                                $hourDif = date("H", $dif_int);

                                $convertDif = strtotime("+$minuteDif minutes", $atraso_int);
                                $convertDif = strtotime("+$secondDif seconds", $convertDif);
                                $convertDif = strtotime("+$hourDif hours", $convertDif);
                                $atraso_int = $convertDif;
                                /* } else {
                                  $time_dif[$b] = "";
                                  $time_llego_nuevo[$b] = "";
                                  } */
                            }
                        } else {
                            $time_dif[$b] = "No Sancionable";
                        }
                    } else {
                        $time_dif[$b] = "";
                    }

                    $b++;
                    $j = count($id_punto);
                }
            }
        }

        $json = "puntos: [";

        for ($i = 0; $i < count($id_punto_nuevo); $i++) {
            $pun = $name_punto[$i];
            $json .= "{"
                    . "idPunto:'" . $id_punto_nuevo[$i] . "',"
                    . "idGeo:'" . $id_geo[$i] . "',"
                    . "ordenPunto:'" . $orden_punto[$i] . "',"
                    . "punto:'" . $pun . "',"
                    . "fecha : '" . $fechaIni . "',"
                    . "tiempos:'" . $times[$i] . "',"
                    . "tiempoDeb:'" . $time_debio[$i] . "',"
                    . "tiempoLl:'" . $time_llego_nuevo[$i] . "',"
                    . "tiempoDif:'" . $time_dif[$i] . "',"
                    . "latitud:'" . $latitud[$i] . "',"
                    . "longitud:'" . $longitud[$i] . "',"
                    . "velocidad:'" . $vel_nuevo[$i] . "',"
                    . "sancionable:'" . $imprimir[$i] . "',"
                    . "bateria:'1'}";

            if ($i != count($id_punto) - 1) {
                $json .= ",";
            }
        }

        $json .= ",{idPunto:' ',ordenPunto:' ',idGeo:' ',tiempos:' ',tiempoDeb:' ', tiempoLl:'', tiempoDif:'',"
                . "latitud:0.0, longitud:0.0, velocidad: '', sancionable:' ', bateria:' ', punto:' '},";
        $json .= "{"
                . "idPunto:' ',"
                . "ordenPunto:' ',"
                . "idGeo:' ',"
                . "tiempos:' ',"
                . "tiempoDeb:'<b>ATRA: </b>',"
                . "tiempoLl:'<B>" . date('H:i:s', $atraso_int) . "</B>',"
                . "tiempoDif:' ',"
                . "latitud:0.0,"
                . "longitud:0.0,"
                . "velocidad: '',"
                . "sancionable:' ',"
                . "bateria:' ',"
                . "punto:' '},";
        $json .= "{"
                . "idPunto:' ',"
                . "ordenPunto:' ',"
                . "idGeo:' ',"
                . "tiempos:' ',"
                . "tiempoDeb:'<b>ADEL: </b>',"
                . "tiempoLl:'<B><font color=black>" . date('H:i:s', $adelanto_int) . "</font></B>',"
                . "tiempoDif:' ',"
                . "latitud:0.0,"
                . "longitud:0.0,"
                . "velocidad:'',"
                . "sancionable:' ',"
                . "bateria:' ',"
                . "punto:' '}";

        $json .="]";
        if (count($resulsetPuntos) == count($id_punto_nuevo)) {
            echo "{success:true, $json}";
        } else {
            echo "{failure:true, message: 'Datos Incompletos.'}";
        }
    } else {
        echo "{failure:true, message: 'No hay datos que obtener.'}";
    }
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

        if ($timeDif <= '00:05:00') {
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

        if ($timeDif <= '00:05:00') {
            return true;
        } else {
            return false;
        }
    }
}
