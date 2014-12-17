<?php

include ('../../../../dll/config.php');



extract($_POST);


if (!$mysqli = getConectionDb()) {
    echo "{success:false, message: 'Error: No se ha podido conectar a la Base de Datos.'}";
} else {
    $consultaSql = "SELECT d.fecha,d.id_usuario, concat(p.nombres,' ',p.apellidos )as persona,time(r.fecha_hora_registro) as hora , r.estado FROM kbushistoricodb.despachos d, kbushistoricodb.registro_labores r,kbusdb.usuarios u, kbusdb.personas p where d.id_usuario=u.id_usuario and u.id_persona=p.id_persona and d.id_usuario=r.id_usuario and d.id_ruta='$idRoute' and d.fecha='$fechaIni' and date(r.fecha_hora_registro)='$fechaIni' and r.estado in(0,1) group by r.fecha_hora_registro";
    $consultaSql1 = "SELECT d.id_vehiculo,v.id_conductor,v.reg_municipal, concat(p.nombres,' ', p.apellidos)as conductor,year(now())-year(p.fecha_nacimiento)as edad ,p.id_tipo_licencia, v.id_ayudante FROM kbushistoricodb.despachos d, kbusdb.vehiculos v,kbusdb.personas p, kbusdb.tipo_licencias t where d.id_vehiculo=v.id_vehiculo and v.id_conductor=p.id_persona  and t.id_tipo_licencia=p.id_tipo_licencia and fecha='$fechaIni' and d.id_ruta='$idRoute' group by v.reg_municipal";
    $result = $mysqli->query($consultaSql);
    $result1 = $mysqli->query($consultaSql1);

    $despachadores = '';
    $cont = 0;

    while ($myrow = $result->fetch_assoc()) {
        $despachadores = $despachadores . utf8_encode($myrow["persona"]) . ';' . 'hora' . ';' . $myrow["hora"] . ';';
        $cont++;
    }
    if ($cont == 0) {
        $consultaSql = "SELECT  concat(p.nombres,' ',p.apellidos )as persona FROM kbushistoricodb.despachos d, kbusdb.usuarios u,kbusdb.personas p where d.id_usuario=u.id_usuario and u.id_persona=p.id_persona and d.fecha='$fechaIni' and id_ruta='$idRoute' order by d.fecha desc ";
        $result = $mysqli->query($consultaSql);
        while ($myrow = $result->fetch_assoc()) {
            $hora = 'Sin Registrar';
            $despachadores = $despachadores . utf8_encode($myrow["persona"]) . ';' . 'hora' . ';' . $hora . ';';
            $cont++;
        }
    }
    switch ($cont) {
        case 0:
            for ($index = 0; $index < 4; $index++) {
                $persona = 'Sin Registrar';
                $hora = 'Sin Registrar';
                $despachadores = $despachadores . utf8_encode($persona) . ';' . 'hora' . ';' . $hora . ';';
            }
            break;
        case 1:
            for ($index = 0; $index < 3; $index++) {
                $persona = 'Sin Registrar';
                $hora = 'Sin Registrar';
                $despachadores = $despachadores . utf8_encode($persona) . ';' . 'hora' . ';' . $hora . ';';
            }
            break;
        case 2:
            for ($index = 0; $index < 2; $index++) {
                $persona = 'Sin Registrar';
                $hora = 'Sin Registrar';
                $despachadores = $despachadores . utf8_encode($persona) . ';' . 'hora' . ';' . $hora . ';';
            }
            break;
        case 3:
            for ($index = 0; $index < 1; $index++) {
                $persona = 'Sin Registrar';
                $hora = 'Sin Registrar';
                $despachadores = $despachadores . utf8_encode($persona) . ';' . 'hora' . ';' . $hora . ';';
            }
            break;
    };


    $haveData = false;
    if ($result->num_rows > 0) {
        if ($result1->num_rows > 0) {
            $haveData = true;
        }
    }
    $json = "data: [";

    
    while ($myrow = $result1->fetch_assoc()) {
        $ayudante = $myrow['id_ayudante'];
        $condutor = $myrow['id_conductor'];
        if ($ayudante == 1) {
            $ayudante = 'No seleccionado';
            $edadA = 0;
        } else {
            $consultaSql = "select concat(nombres,' ', apellidos)as ayudante,year(now())-year(fecha_nacimiento)as edad from kbusdb.personas where id_persona='$ayudante'";
            $result = $mysqli->query($consultaSql);
            $myrow1 = $result->fetch_assoc();
            $ayudante = $myrow1['ayudante'];
            $edadA = $myrow1['edad'];
        }
        if ($condutor == 1) {
            $condutor = 'No seleccionado';
            $edadC = 0;
            $licencia = '';
        } else {
            $condutor = $myrow["conductor"];
            $edadC = $myrow["edad"];
            $licencia = $myrow["id_tipo_licencia"];
        }

        $v1 = $v2 = $v3 = $v4 = $v5 = $v6 = $v7 = $v8 = $v9 = $v10 = '00:00:00';
        $id_vehiculo = $myrow['id_vehiculo'];
        $consultaSql2 = "SELECT hora_ini, observacion FROM kbushistoricodb.despachos where id_vehiculo='$id_vehiculo' and fecha='$fechaIni' and id_ruta='$idRoute' order by hora_ini asc";
        $result2 = $mysqli->query($consultaSql2);
        $cont = 1;
        $observacionesDes1 = '';

        while ($myrow2 = $result2->fetch_assoc()) {
            switch ($cont) {
                case 1:
                    if ($myrow2['observacion'] != '') {
                        $observacionesDes1 = $observacionesDes1 . $cont . ':' . $myrow2['observacion'] . ' ';
                    }
                    $v1 = $myrow2['hora_ini'];
                    break;
                case 2:

                    if ($myrow2['observacion'] != '') {
                        $observacionesDes1 = $observacionesDes1 . $cont . ':' . $myrow2['observacion'] . ' ';
                    }
                    $v2 = $myrow2['hora_ini'];
                    break;
                case 3:
                    if ($myrow2['observacion'] != '') {
                        $observacionesDes1 = $observacionesDes1 . $cont . ':' . $myrow2['observacion'] . ' ';
                    }
                    $v3 = $myrow2['hora_ini'];
                    break;
                case 4:
                    if ($myrow2['observacion'] != '') {
                        $observacionesDes1 = $observacionesDes1 . $cont . ':' . $myrow2['observacion'] . ' ';
                    }
                    $v4 = $myrow2['hora_ini'];
                    break;
                case 5:
                    if ($myrow2['observacion'] != '') {
                        $observacionesDes1 = $observacionesDes1 . $cont . ':' . $myrow2['observacion'] . ' ';
                    }
                    $v5 = $myrow2['hora_ini'];
                    break;
                case 6:
                    if ($myrow2['observacion'] != '') {
                        $observacionesDes1 = $observacionesDes1 . $cont . ':' . $myrow2['observacion'] . ' ';
                    }
                    $v6 = $myrow2['hora_ini'];
                    break;
                case 7:
                    if ($myrow2['observacion'] != '') {
                        $observacionesDes1 = $observacionesDes1 . $cont . ':' . $myrow2['observacion'] . ' ';
                    }
                    $v7 = $myrow2['hora_ini'];
                    break;
                case 8:
                    if ($myrow2['observacion'] != '') {
                        $observacionesDes1 = $observacionesDes1 . $cont . ':' . $myrow2['observacion'] . ' ';
                    }
                    $v8 = $myrow2['hora_ini'];
                    break;
                case 9:
                    if ($myrow2['observacion'] != '') {
                        $observacionesDes1 = $observacionesDes1 . $cont . ':' . $myrow2['observacion'] . ' ';
                    }
                    $v9 = $myrow2['hora_ini'];
                    break;
                case 10:
                    if ($myrow2['observacion'] != '') {
                        $observacionesDes1 = $observacionesDes1 . $cont . ':' . $myrow2['observacion'] . ' ';
                    }
                    $v10 = $myrow2['hora_ini'];
                    break;
            };
            $cont++;
        };
        $json .= "{"
                . "reg_municipal: '" . utf8_encode($myrow["reg_municipal"]) . "',"
                . "conductor: '" . utf8_encode($condutor) . "',"
                . "edadC: " . $edadC . ","
                . "id_tipo_licencia: '" . utf8_encode($licencia) . "',"
                . "ayudante:' " . utf8_encode($ayudante) . "',"
                . "edadA: " . $edadA . ","
                . "v1:'" . utf8_encode($v1) . "',"
                . "v2:'" . utf8_encode($v2) . "',"
                . "v3:'" . utf8_encode($v3) . "',"
                . "v4:'" . utf8_encode($v4) . "',"
                . "v5:'" . utf8_encode($v5) . "',"
                . "v6:'" . utf8_encode($v6) . "',"
                . "v7:'" . utf8_encode($v7) . "',"
                . "v8:'" . utf8_encode($v8) . "',"
                . "v9:'" . utf8_encode($v9) . "',"
                . "v10:'" . utf8_encode($v10) . "',"
                . "obs:'" . utf8_encode($observacionesDes1) . "',"
                . "},";
    }

    $json .="],despachadores:'" . $despachadores . "'";
    if ($haveData) {
        echo "{success: true, $json}";
    } else {
        echo "{failure: true, message:'No hay despachos en esta fecha para esta Ruta.'}";
    }
    $mysqli->close();
}
