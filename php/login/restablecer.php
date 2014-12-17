<?php

include ('../../dll/config.php');
include("class.phpmailer.php");
include("class.smtp.php");
extract($_POST);

if (strlen($us) > 0 & strlen($email) > 0) {
    if (!$mysqli = getConectionDb()) {
        $Error = "Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.";
        echo "<script>alert('$Error');</script>";
        echo "<script>location.href='../../index.php'</script>";
    } else {
        $salt = "KR@D@C";
        $clave = rand(100000, 999999);
        $encriptClave = md5(md5(md5($clave) . md5($salt)));

        $consultaSql = "SELECT u.id_usuario, u.usuario, u.id_rol_usuario, 
        u.id_persona, e.id_empresa, e.empresa, concat(p.nombres,' ',p.apellidos) as nom,u.clave
        FROM usuarios u, personas p, empresas e
        WHERE u.id_persona = p.id_persona
        AND u.id_empresa = e.id_empresa
        AND u.usuario = ?
        AND p.correo = ?";
        $stmt = $mysqli->prepare($consultaSql);
   
        
        if ($stmt) {
            $stmt->bind_param("ss", $us, $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $myrow = $result->fetch_assoc();
                if (isset($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"])) {
                    $ip = $HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"];
                    $array = split(",", $ip);
                    $host = @gethostbyaddr($ip_proxy);
                    $ip_proxy = $HTTP_SERVER_VARS["REMOTE_ADDR"];
                } else {
                    $ip = $_SERVER['REMOTE_ADDR'];
                    $host = @gethostbyaddr($ip);
                }
                $usuario = $myrow["usuario"];
                $consultaSql = "UPDATE usuarios SET clave=? WHERE clave = ? AND usuario = ?";
                $stmt1 = $mysqli->prepare($consultaSql);
                $stmt1->bind_param("sss", $encriptClave, $myrow['clave'], $usuario);
                $stmt1->execute();
                if ($stmt1) {
                    if ($stmt1->affected_rows > 0) {
                        $persona = $myrow['nom'];
                        $mensaje = '<font size=4>Saludos estimado(a), <b>' . $persona
                                . '</b> recibimos una solicitud para restablecer su contraseña,'
                                . ' temporalmente puede utilizar la siguiente:'
                                . '<br><br></font><font size=5><b>' . $clave . '</b></font><br><br>'
                                . '<font size=4>Si usted no realizo ninguna solicitud comuniquese '
                                . 'inmediatamente con nuestras oficinas, le informamos se '
                                . 'intentó ingresar al sistema con su usuario</font><br>'
                                . '<div dir="ltr"><div><br></div><img src="http://sphotos-h.ak.fbcdn.net/hphotos-ak-ash3/18095_422375177828837_1292816515_n.png" width="420" height="157"><br><div><br></div><div>SOPORTE KRADAC CIA. LTDA.</div><div>TELF: 072570275, &nbsp;OPCION 3, EXT. 2980</div><div>RESPONSABLE LOJA: ING. HUGO RAMIREZ&nbsp;0991287735</div><div>RESPONSABLE QUITO: ING TAYRON CORREA 0991503740</div><div>JEFA&nbsp;ADMINISTRATIVA: ING. ANDREA SOLANO - 0986311573<br></div><div><br></div><div><a href="http://www.kradac.com" target="_blank">www.kradac.com</a><br></div></div>';
                        $mail = new PHPMailer();
                        $mail->IsSMTP();
                        $mail->SMTPAuth = true;
                        $mail->DeNombre = 'Soporte K-Bus';
                        $mail->Subject = 'Restablecer clave';
                        $mail->MsgHTML($mensaje);
                        $mail->AddAddress($email, $persona);
                        $mail->IsHTML(true);
                        if (!$mail->Send()) {
                            echo "<script>alert('Clave no se pudo enviar, intente nuevamente');</script>";
                            echo "<script>location.href='../../restablecer.php'</script>";
                        } else {
                            echo "<script>alert('Clave fue enviada a su email');</script>";
                            echo "<script type='text/javascript'>location.href='../../index.php'</script>";
                        }
                    } else {
                        $Error = utf8_decode("Problemas al insertar en la tabla,intente nuevamente.");
                        echo "<script>alert('$Error');</script>";
                        echo "<script>location.href='../../restablecer.php'</script>";
                    }
                } else {
                    $Error = utf8_decode("Problemas en la construcción de la consulta,intente nuevamente.");
                    echo "<script>alert('$Error');</script>";
                    echo "<script>location.href='../../restablecer.php'</script>";
                }
            } else {
                $Error = utf8_decode("Usuario o email incorrectos, intente nuevamente");
                echo "<script>alert('$Error');</script>";
                echo "<script>location.href='../../restablecer.php'</script>";
            }
        } else {
            $Error = utf8_decode("Problemas en la construcción de la consulta,intente nuevamente.");
            echo "<script>alert('$Error');</script>";
            echo "<script>location.href='../../restablecer.php'</script>";
        }

        $stmt->close();
        $stmt1->close();
        $mysqli->close();
    }
} else {
    echo "<script>alert('Por favor debe ingresar el usuario y email registrados en su cuenta');</script>";
    echo "<script>location.href='../../restablecer.php'</script>";
}    


