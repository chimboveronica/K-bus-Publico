<?php

include('../../login/isLogin.php');
include ('../../../dll/config.php');

if (!$mysqli = getConectionDb()) {
    echo "{success:false, message: 'Error: No se ha podido conectar a la Base de Datos.<br>Compruebe su conexión a Internet.'}";
} else {
    $queryCompany = "SELECT id_empresa, COUNT(id_vehiculo) as total "
            . "FROM vehiculos  "
            . "GROUP BY id_empresa "
            . "ORDER BY id_empresa";

    $resultCompany = $mysqli->query($queryCompany);
    if ($resultCompany->num_rows > 0) {
        $totalConect = 0;
        $totalDesconect = 0;
        $totalTotal = 0;

        $objJson = "{data : [";
        while ($myrowcompany = $resultCompany->fetch_assoc()) {
            $idCompany = $myrowcompany["id_empresa"];
            $conect = 0;

            $querySkp = "SELECT COUNT(f.id_equipo) as total "
                    . "FROM ultimo_dato_skps f, vehiculos v "
                    . "WHERE f.id_equipo = v.id_equipo "
                    . "AND TIMESTAMPDIFF(MINUTE, f.fecha_hora_ult_dato, NOW()) <= 3 "
                    . "AND v.id_empresa = $idCompany";

            $resultSkp = $mysqli->query($querySkp);
            if ($resultSkp->num_rows > 0) {
                $myrowskp = $resultSkp->fetch_assoc();
                $conect = $myrowskp["total"];
            }

            $queryFastrack = "SELECT COUNT(f.id_equipo) as total "
                    . "FROM kbusdb.ultimo_dato_fastracks f, vehiculos v "
                    . "WHERE f.id_equipo = v.id_equipo "
                    . "AND TIMESTAMPDIFF(MINUTE, f.fecha_hora_ult_dato, NOW()) <= 15 "
                    . "AND v.id_empresa = $idCompany";

            $resultFastrack = $mysqli->query($queryFastrack);

            if ($resultFastrack->num_rows > 0) {
                $myrowfastack = $resultFastrack->fetch_assoc();
                $conect += $myrowfastack["total"];
            }

            $desconect = $myrowcompany["total"] - $conect;
            
            $totalConect += $conect;
            $totalDesconect += $desconect;
            $totalTotal += $myrowcompany["total"];
            
            $objJson .= "{"
                    . "idCompanyResume: " . $myrowcompany["id_empresa"] . ","
                    . "deviceConectResume: " . $conect . ","
                    . "deviceDesconectResume: " . $desconect . ","
                    . "deviceTotalResume: " . $myrowcompany["total"] . "},";
        }
        
        $objJson .= "{"
                    . "idCompanyResume: 1,"
                    . "deviceConectResume: " . $totalConect . ","
                    . "deviceDesconectResume: " . $totalDesconect . ","
                    . "deviceTotalResume: " . $totalTotal . "}";

        $objJson .="]}";
        $mysqli->close();
        echo $objJson;
    } else {
        echo "{success:false, message:'No hay datos que obtener'}";
    }
}