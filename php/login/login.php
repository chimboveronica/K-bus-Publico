<?php

include ('../../dll/config.php');
extract($_POST);

if (!$mysqli = getConectionDb()) {
    $Error = "Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.";
    echo "<script>alert('$Error');</script>";
    echo "<script>location.href='../../index.php'</script>";
} else {
    $salt = "KR@D@C";
    $encriptClave = md5(md5(md5($ps) . md5($salt)));

    $consultaSql = "select u.id_usuario, u.usuario, u.id_rol_usuario, "
            . "u.id_persona, e.id_empresa, e.empresa, p.nombres, p.apellidos "
            . "from usuarios u, personas p, empresas e "
            . "where u.id_persona = p.id_persona "
            . "and u.id_empresa = e.id_empresa "
            . "and u.usuario = ? "
            . "and u.clave = ? "
            . "and activo = 1";

    /* crear una sentencia preparada */
    $stmt = $mysqli->prepare($consultaSql);
    if ($stmt) {
        /* ligar parámetros para marcadores */
        $stmt->bind_param("ss", $us, $encriptClave);
        /* ejecutar la consulta */
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $myrow = $result->fetch_assoc();

            // Deteccion de la ip y del proxy
            if (isset($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"])) {
                $ip = $HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"];
                $array = split(",", $ip);
                $host = @gethostbyaddr($ip_proxy);
                $ip_proxy = $HTTP_SERVER_VARS["REMOTE_ADDR"];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
                $host = @gethostbyaddr($ip);
            }

            $idUser = $myrow["id_usuario"];
            //$fecha = @date("Y-m-d");
            //$hora = @date("H:i:s");

            $consultaSql = "insert into kbushistoricodb.accesos (ip, host, id_usuario, latitud, longitud) "
                    . "values (?, ?, ?, ?, ?) ";
            $stmt = $mysqli->prepare($consultaSql);

            if ($stmt) {
                $stmt->bind_param("ssidd", $ip, $host, $idUser, $latitud, $longitud);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {

                    session_start();
                    $_SESSION["IDEMPRESAKBUS" . $site_city] = utf8_encode($myrow["id_empresa"]);
                    $_SESSION["EMPRESAKBUS" . $site_city] = utf8_encode($myrow["empresa"]);
                    $_SESSION["IDUSUARIOKBUS" . $site_city] = $myrow["id_usuario"];
                    $_SESSION["USUARIOKBUS" . $site_city] = utf8_encode($myrow["usuario"]);
                    $_SESSION["IDROLKBUS" . $site_city] = $myrow["id_rol_usuario"];
                    $_SESSION["IDPERSONAKBUS" . $site_city] = $myrow["id_persona"];
                    $_SESSION["PERSONKBUS" . $site_city] = utf8_encode($myrow["nombres"] . " " . $myrow["apellidos"]);
                    $_SESSION["SESIONKBUS" . $site_city] = true;

                    switch ($myrow["id_rol_usuario"]) {
                        case 1:
                            $_SESSION["NAMESESIONKBUS" . $site_city] = "index_admin.php";
                            echo "<script type='text/javascript'>location.href='../../index_admin.php'</script>";
                            break;
                        case 2:
                            $_SESSION["NAMESESIONKBUS" . $site_city] = "index_empresa.php";
                            echo "<script type='text/javascript'>location.href='../../index_empresa.php'</script>";
                            break;
                        case 3:
                            $_SESSION["NAMESESIONKBUS" . $site_city] = "index_mun_inspector.php";
                            echo "<script type='text/javascript'>location.href='../../index_mun_inspector.php'</script>";
                            break;
                        case 4:
                            $_SESSION["NAMESESIONKBUS" . $site_city] = "index_propietario.php";
                            echo "<script type='text/javascript'>location.href='../../index_propietario.php'</script>";
                            break;
                        case 5:
                            $_SESSION["NAMESESIONKBUS" . $site_city] = "index_despachador.php";
                            echo "<script type='text/javascript'>location.href='../../index_despachador.php'</script>";
                            break;
                        case 6:
                            $_SESSION["NAMESESIONKBUS".$site_city] = "index_asociacion.php";
                            echo "<script type='text/javascript'>location.href='../../index_asociacion.php'</script>";
                            break;
                        case 6:
                            $_SESSION["NAMESESIONKBUS".$site_city] = "index_asociacion.php";
                            echo "<script type='text/javascript'>location.href='../../index_asociacion.php'</script>";
                            break;
                    }
                } else {
                    $Error = utf8_decode("Problemas al Insertar en la Tabla.");
                    echo "<script>alert('$Error');</script>";
                    echo "<script>location.href='../../index.php'</script>";
                }
            } else {
                $Error = utf8_decode("Problemas en la construcción de la consulta.");
                echo "<script>alert('$Error');</script>";
                echo "<script>location.href='../../index.php'</script>";
            }
        } else {
            $Error = utf8_decode("Usuario o Contraseña Incorrectas");
            echo "<script>alert('$Error');</script>";
            echo "<script>location.href='../../index.php'</script>";
        }
    } else {
        $Error = utf8_decode("Problemas en la construcción de la consulta.");
        echo "<script>alert('$Error');</script>";
        echo "<script>location.href='../../index.php'</script>";
    }

    $stmt->close();
    $mysqli->close();
}
