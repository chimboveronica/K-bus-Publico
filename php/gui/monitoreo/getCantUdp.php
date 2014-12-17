<?php

include('../../login/isLogin.php');
include ('../../../dll/config.php');

if (!$mysqli = getConectionDb()) {
    echo "{success:false, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {


    $consultaSql = "select d.conect, t.total, e.id_empresa "
            . "from (select count(*) as conect, v.id_empresa "
            . "from ultimo_dato_fastracks ee, vehiculos v "
            . "where ee.id_equipo = v.id_equipo and  timestampdiff(minute,ee.fecha_hora_ult_dato, now())<=15 group by v.id_empresa) as d, (select count(*) as total, v.id_empresa "
            . "from ultimo_dato_fastracks ee, vehiculos v  "
            . "where ee.id_equipo = v.id_equipo group by v.id_empresa) as t, empresas e "
            . "where d.id_empresa = t.id_empresa  and t.id_empresa = e.id_empresa order by e.empresa";

    $result = $mysqli->query($consultaSql);
    $mysqli->close();
    if ($result->num_rows > 0) {
        $cantConect = 0;
        $cantTotal = 0;
        $cantDesconect = 0;

        $objJson = "{cantUdp : [";
        while ($myrow = $result->fetch_assoc()) {
            $desconect = $myrow["total"] - $myrow["conect"];

            $cantConect += $myrow["conect"];
            $cantTotal += $myrow["total"];
            $cantDesconect += $desconect;

            $objJson .= "{
                desco:" . $desconect . ",
                conect:" . $myrow["conect"] . ",
                total:" . $myrow["total"] . ",
                empresa:" . $myrow["id_empresa"] . "
            },";
        }

        $objJson .= "{
        desco:'<b>" . $cantDesconect . "</b>',
        conect:'<b>" . $cantConect . "</b>',
        total:'<b>" . $cantTotal . "</b>',
        empresa: '<b>Total</b>'
    }";

        $objJson .="]}";

        echo $objJson;
    } else {
        echo "{failure:true, message:'No hay datos que obtener'}";
    }
}