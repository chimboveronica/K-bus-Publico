<?php
    
    require_once('../../../dll/conect.php');

    extract($_POST);

    $factorLAT = $meters / (1852 * 60);
    $factorLON = (($meters * 0.00001) / 0.000111 ) / 10000;

    $lat1 = $ypos - $factorLAT;
    $lat2 = $ypos + $factorLAT;
    $lon1 = $xpos - $factorLON;
    $lon2 = $xpos + $factorLON;

    $consultaSql = 
        " SELECT P.ID_PT, P.ID_GEO, PR.ORDEN_PR, P.PUNTO, 
        P.LATITUD, P.LONGITUD, P.DIRECCION, R.COLOR
        FROM puntos_ruta PR, puntos P, rutas R
        WHERE P.ID_PT = PR.ID_PT 
        AND PR.ID_RUTA = R.ID_RUTA        
        AND ( P.LONGITUD >= $lon1 AND P.LONGITUD <= $lon2 ) AND ( P.LATITUD >= $lat1 AND P.LATITUD <= $lat2)
    ";

    consulta($consultaSql);
    $resulset = variasFilas();

    if (count($resulset) > 0) {
        $json = "{puntos: [";
          
        for ($i = 0; $i < count($resulset); $i++) {
            $fila = $resulset[$i];

            $json .= "{
                idPunto:'".$fila["ID_PT"]."',
                ordenPunto:'" . $fila["ORDEN_PR"] . "',
                punto:'" . $fila["PUNTO"] . "',
                latitud:'" . $fila["LATITUD"] . "',
                longitud:'" . $fila["LONGITUD"] . "',
                direccion:'" . $fila["DIRECCION"] . "',
                idGeo:'" . $fila["ID_GEO"]. "',
                color:'" . $fila["COLOR"]. "'
            }";

            if ($i != count($resulset) - 1) {
                $json .= ",";
            }
        }    

        $json .="]}";
            
        $json = preg_replace("[\n|\r|\n\r]", "", utf8_encode($json));        

        $salida = "{success:true, string: ".json_encode($json)."}"; 
    } else {
        $salida = "{failure:true}";
    }

    echo $salida;
?>
