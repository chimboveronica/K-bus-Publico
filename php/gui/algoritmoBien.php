<?php
require_once('../../dll/conect.php');

extract($_GET);
$cbxRutas=3;//$_GET['cbxRutas'];
$fechaIni="2013-09-12";//$_GET['fechaIni'];
$cbxBuses="BUS1532";//$_GET['cbxBuses'];
$hora_Ini="07:00";//$_GET['hora'];*/
$hora_fin=$hora_Ini;

$consultaPuntos=
	"SELECT R.ID_PT, R.ORDEN_PR, R.TIEMPOS, P.PUNTO
	FROM puntos_ruta R, puntos P
	WHERE R.ID_RUTA = $cbxRutas
 	AND R.ID_PT = P.ID_PT
 	ORDER BY ORDEN_PR";

consulta($consultaPuntos);
$resulset = variasFilas();
echo "<table>";
echo "<tr rowspan = '2'>";
echo "<td bgcolor = '#666'>ID_PT</td>";
echo "<td bgcolor = '#666'>ORDEN_PR</td>";
echo "<td bgcolor = '#666'>TIEMPOS</td>";
echo "<td bgcolor = '#666'>TIEMPOS DEBIO</td>";
echo "<td bgcolor = '#666'>PUNTO</td>";
echo "</tr>";

for ($i = 0; $i < count($resulset); $i++) {
	$fila = $resulset[$i];
    
    $id_punto[$i] = $fila["ID_PT"];    
    $orden_punto[$i] = $fila["ORDEN_PR"];
    $times[$i] = $fila["TIEMPOS"];
    $estado_puntos[$i] = 0;

    $hora_Ini_int = strtotime($hora_fin);
    $time_debio_int = strtotime($times[$i]);

    $minute = date("i", $time_debio_int);
	$second = date("s", $time_debio_int);
	$hour = date("H", $time_debio_int);

	$convert = strtotime("+$minute minutes", $hora_Ini_int);
	$convert = strtotime("+$second seconds", $convert);
	$convert = strtotime("+$hour hours", $convert);
	$hora_fin = date('H:i:s', $convert);

	if ($i == count($resulset)) {
		$hora_fin_ruta = $hora_fin;
	}

    $time_debio[$i] = $hora_fin;
    $name_punto[$i] = $fila["PUNTO"];
    
    echo "<tr>";
    echo "<td>". $id_punto[$i] . "</td>";
    echo "<td>". $orden_punto[$i] . "</td>";
    echo "<td>". $times[$i] . "</td>";
    echo "<td>". $time_debio[$i] . "</td>";
    echo "<td>". $name_punto[$i] . "</td>";            
}
echo "</table>";
echo "Total: ".count($id_punto)."<br>";

$consultaSql = 
"SELECT ID_PT,FECHA,HORA
FROM recorridos_udp 
WHERE FECHA = '$fechaIni' AND HORA BETWEEN '$hora_Ini' AND TIME(ADDTIME('$hora_fin', '00:20:00'))
AND COD_UNIDAD = (SELECT COD_UNIDAD FROM vehiculos WHERE ID_EQUIPO = '$cbxBuses')	
AND ID_PT <> 0
ORDER BY HORA";

echo $consultaSql;

consulta($consultaSql);
$resulset = variasFilas();

echo "Result : ".count($resulset);

/* Escojo solo puntos que sean de la Ruta*/
echo "<table>";
echo "<tr rowspan = '2'>";
echo "<td bgcolor = '#666'>ID_PT</td>";
echo "<td bgcolor = '#666'>FECHA</td>";
echo "<td bgcolor = '#666'>HORA</td>";
echo "<td bgcolor = '#666'>ESTADO</td>";
echo "</tr>";
$d=0;
for ($i=0; $i < count($resulset); $i++) { 
	$fila = $resulset[$i];

	for ($j=0; $j < count($id_punto); $j++) { 
		if ($fila["ID_PT"] == $id_punto[$j]) {
			$id_punto_rec_ant[$d] = $fila["ID_PT"];
			$fecha_rec_ant[$d] = $fila["FECHA"];
			$hora_rec_ant[$d] = $fila["HORA"];/*			
			echo "<tr>";
			echo "<td>". $id_punto_rec_ant[$d] . "</td>";	
			echo "<td>". $fecha_rec_ant[$d] . "</td>";	
			echo "<td>". $hora_rec_ant[$d] . "</td>";			*/
			$d++;
			$j = count($id_punto);
		}
	}
}
/*
echo "</table>";
echo "Total: ".count($id_punto_rec_ant)."<br>";*/

/*Quito puntos que estan en el mismo minuto y son iguales*/
/*
echo "<table>";
echo "<tr>Sin puntos en el mismo minuto y que sean iguales</tr>";
echo "<tr rowspan = '2'>";
echo "<td bgcolor = '#666'>ID_PT</td>";
echo "<td bgcolor = '#666'>FECHA</td>";
echo "<td bgcolor = '#666'>HORA</td>";
echo "</tr>";*/
$c=0;
for ($i=0; $i < count($id_punto_rec_ant); $i++) { 
	if ($i < count($id_punto_rec_ant)-1) {
		$time_repetidosP = strtotime($hora_rec_ant[$i]);
		$time_repetidosS = strtotime($hora_rec_ant[$i+1]);
		
		$minuteP = date("i", $time_repetidosP);
		$secondP = date("s", $time_repetidosP);
		$hourP = date("H", $time_repetidosP);

		$minuteS = date("i", $time_repetidosS);
		$secondS = date("s", $time_repetidosS);
		$hourS = date("H", $time_repetidosS);

		if ($id_punto_rec_ant[$i] != $id_punto_rec_ant[$i+1]) {
			$id_punto_rec_ant1[$c] = $id_punto_rec_ant[$i];
			$fecha_rec_ant1[$c] = $fecha_rec_ant[$i];
			$hora_rec_ant1[$c] = $hora_rec_ant[$i];			
		} else {
			if ($minuteP != $minuteS) {
				$convert = strtotime("-$minuteP minutes", $time_repetidosS);
				$convert = strtotime("-$secondP seconds", $convert);
				$convert = strtotime("-$hourP hours", $convert);
				$dif = date('H:i:s', $convert);

				$id_punto_rec_ant1[$c] = $id_punto_rec_ant[$i];
				$fecha_rec_ant1[$c] = $fecha_rec_ant[$i];
				$hora_rec_ant1[$c] = $hora_rec_ant[$i];

				if ($dif <= '00:03:00' ) {
					$i++;
				} 				
			} else {
				$id_punto_rec_ant1[$c] = $id_punto_rec_ant[$i];
				$fecha_rec_ant1[$c] = $fecha_rec_ant[$i];
				$hora_rec_ant1[$c] = $hora_rec_ant[$i];				

				$i++;
			}
		}
	//	echo "<tr>";
	  //  echo "<td>". $id_punto_rec_ant1[$c] . "</td>";	
	    //echo "<td>". $fecha_rec_ant1[$c] . "</td>";	
	    //echo "<td>". $hora_rec_ant1[$c] . "</td>";
		$c++;
	} else {
		$id_punto_rec_ant1[$c] = $id_punto_rec_ant[$i];
		$fecha_rec_ant1[$c] = $fecha_rec_ant[$i];
		$hora_rec_ant1[$c] = $hora_rec_ant[$i];				
		//echo "<tr>";
		//echo "<td>". $id_punto_rec1[$c] . "</td>";	
		//echo "<td>". $fecha_rec1[$c] . "</td>";	
		//echo "<td>". $hora_rec1[$c] . "</td>";
	}
}

//echo "</table>";
//echo "Total: ".count($id_punto_rec_ant1)."<br>";

$c=0;
for ($i=0; $i < count($id_punto_rec_ant1); $i++) { 
	if ($i < count($id_punto_rec_ant1)-1) {
		$time_repetidosP = strtotime($hora_rec_ant1[$i]);
		$time_repetidosS = strtotime($hora_rec_ant1[$i+1]);
		
		$minuteP = date("i", $time_repetidosP);
		$secondP = date("s", $time_repetidosP);
		$hourP = date("H", $time_repetidosP);

		$minuteS = date("i", $time_repetidosS);
		$secondS = date("s", $time_repetidosS);
		$hourS = date("H", $time_repetidosS);

		if ($id_punto_rec_ant1[$i] != $id_punto_rec_ant1[$i+1]) {
			$id_punto_rec[$c] = $id_punto_rec_ant1[$i];
			$fecha_rec[$c] = $fecha_rec_ant1[$i];
			$hora_rec[$c] = $hora_rec_ant1[$i];
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
				$estado_rec[$c] = 0;

				if ($dif <= '00:03:00' ) {
					$i++;
				}
			} else {
				$id_punto_rec[$c] = $id_punto_rec_ant1[$i];
				$fecha_rec[$c] = $fecha_rec_ant1[$i];
				$hora_rec[$c] = $hora_rec_ant1[$i];
				$estado_rec[$c] = 0;

				$i++;
			}
		}
		$c++;
	} else {
		$id_punto_rec[$c] = $id_punto_rec_ant1[$i];
		$fecha_rec[$c] = $fecha_rec_ant1[$i];
		$hora_rec[$c] = $hora_rec_ant1[$i];
		$estado_rec[$c] = 0;
	}
}

$y = 0;
$cantidad = 0;
for ($i=0; $i < count($id_punto); $i++) {	
	for ($j=0; $j < count($id_punto_rec); $j++) {
		//echo $id_punto[$i]." :: ".$id_punto_rec[$j]." :: ". $estado_rec[$j]."<br>";
		if ($id_punto[$i] == $id_punto_rec[$j] && $estado_rec[$j] == 0) {
			
			if ($cantidad > 0) {
				//echo "Se Encontro luego<br>";
				$id_puntos_rec_lleno[$y] = $id_punto[$i];
				$fecha_rec_lleno[$y] = $fecha_rec[$j];
				$hora_rec_lleno[$y] = $hora_rec[$j];						
				$estado_rec[$j] = 1;

				for ($p=0; $p < count($estado_rec); $p++) { 
					//echo "Econtrado Posicion: ".$estado_rec[$p]. " = ".$p."<br>";
					if ($estado_rec[$p] == 2) {
						//echo "Econtrado Cambiando: de 2 a 1 en Posicion  = ".$p."<br>";
						//$estado_rec[$p] = 1;
						$estado_rec[$p+1] = $estado_rec[$p] = 1;
						//echo "Se cambio :  ".$estado_rec[$p+1]."<br>";

						/*for ($q=0; $q < count($estado_rec); $q++) { 
							echo "Posicion : ".$q. " = ".$estado_rec[$q]."<br>";
						}*/
					}

					/*if ($p == 3) {
						echo "Problema : ".$estado_rec[7]. "<br>";
					}*/
				}				
			} else {
				//echo "Igual y cantidad == 0<br>";
				$id_puntos_rec_lleno[$y] = $id_punto[$i];
				$fecha_rec_lleno[$y] = $fecha_rec[$j];
				$hora_rec_lleno[$y] = $hora_rec[$j];						
				$estado_rec[$j] = 1;
			}

			$j = count($id_punto_rec);
			$y++;
			$cantidad = 0;			
		} else {
			//echo "Aqui ".$estado_rec[$j]." = 0 AND ".$cantidad. "< 4<br>";
			if ($estado_rec[$j] == 0 && $cantidad < 4) {
				$cantidad++;
			//	echo "Cantidad: ".$cantidad."<br>";
				if ($cantidad == 4) {
					$id_puntos_rec_lleno[$y] = $id_punto[$i];
					$fecha_rec_lleno[$y] = $fecha_rec[0];
					$hora_rec_lleno[$y] = "";

					for ($p=0; $p < count($estado_rec); $p++) {

						if ($estado_rec[$p] == 2) {
							//echo "Econtrado Pos 4 -> Cambiando: de 2 a 0 en Posicion  = ".$p."<br>";
							$estado_rec[$p] = 0;
							//echo "Se cambio :  ".$estado_rec[$p]."<br>";
						}
					}

					/*for ($q=0; $q < count($estado_rec); $q++) { 
						echo "Igual a 4: ".$q. " = ".$estado_rec[$q]."<br>";
					}*/
					$y++;
					$cantidad = 0;
					$j = count($id_punto_rec);
				} else {					
					if ($j == count($id_punto_rec)-1 && $cantidad > 0) {
						echo "LLego al Final<br>";
						$id_puntos_rec_lleno[$y] = $id_punto[$i];
						$fecha_rec_lleno[$y] = $fecha_rec[0];
						$hora_rec_lleno[$y] = "";						

						//echo "Llego al Final<br>";
						for ($p=0; $p < count($estado_rec); $p++) {
							if ($estado_rec[$p] == 2) {
								//echo "Posicion: ".$p."<br>";
								//echo "Econtrado Llego al Final -> Cambiando: de 2 a 0 en Posicion  = ".$p."<br>";
								$estado_rec[$p] = 0;
							}
						}

						$y++;
						$cantidad = 0;
					} else {
						echo "No se encontro<br>";
						$estado_rec[$j] = 2;						
					}
				}
			} else {
				if ($j == count($id_punto_rec)-1) {
					echo "LLego al Final Final<br>";
					$id_puntos_rec_lleno[$y] = $id_punto[$i];
					$fecha_rec_lleno[$y] = $fecha_rec[0];
					$hora_rec_lleno[$y] = "";

					$y++;							
				}
			}
		}
	}
}

echo "<table>";
echo "<tr rowspan = '2'>";
echo "<td bgcolor = '#666'>ID</td>";
echo "<td bgcolor = '#666'>ID_PT</td>";
echo "<td bgcolor = '#666'>FECHA</td>";
echo "<td bgcolor = '#666'>HORA</td>";
echo "<td bgcolor = '#666'>ESTADO</td>";
echo "</tr>";
for ($i=0; $i < count($id_puntos_rec_lleno); $i++) { 
	echo "<tr>";
	echo "<td>". $i . "</td>";    
	echo "<td>". $id_puntos_rec_lleno[$i] . "</td>";    
	echo "<td>". $fecha_rec_lleno[$i] . "</td>";
	echo "<td>". $hora_rec_lleno[$i] . "</td>";
	echo "<td>". $estado_puntos[$i] . "</td>";				
}

echo "</table>";
echo "Total: ".count($id_puntos_rec_lleno)."<br>";

$b = 0;
for ($i=0; $i < count($id_puntos_rec_lleno); $i++) {
	for ($j=0; $j < count($id_punto); $j++) { 
		if ($id_puntos_rec_lleno[$i] == $id_punto[$j]) {

			$id_punto_final[$b] = $id_punto[$j];
			$time_llego[$b] = $hora_rec_lleno[$i];


			if ($time_llego[$b] != "") {
				$time_debio_nuevo_int = strtotime($time_debio[$b]);
		    	$time_llego_nuevo_int = strtotime($time_llego[$b]);

		    	if ($time_debio[$b] > $time_llego[$b]) {
		    		$minute = date("i", $time_llego_nuevo_int);
					$second = date("s", $time_llego_nuevo_int);
					$hour = date("H", $time_llego_nuevo_int);

					$convert = strtotime("-$minute minutes", $time_debio_nuevo_int);
					$convert = strtotime("-$second seconds", $convert);
					$convert = strtotime("-$hour hours", $convert);
					$time_dif[$b] = "-".date('H:i:s', $convert);			
		    	} else {
		    		$minute = date("i", $time_debio_nuevo_int);
					$second = date("s", $time_debio_nuevo_int);
					$hour = date("H", $time_debio_nuevo_int);

					$convert = strtotime("-$minute minutes", $time_llego_nuevo_int);
					$convert = strtotime("-$second seconds", $convert);
					$convert = strtotime("-$hour hours", $convert);
					$time_dif[$b] = date('H:i:s', $convert);
		    	}
			} else {
				$time_dif[$b] = "";
			}
			
			$b++;
			$j = count($id_punto);			
		}
	}
}

echo "<table border=2>";
echo "<tr rowspan = '2'>";
echo "<td bgcolor = '#666'>ID_PT</td>";
echo "<td bgcolor = '#666'>ORDEN_PR</td>";
echo "<td bgcolor = '#666'>PUNTO</td>";
echo "<td bgcolor = '#666'>TIEMPOS</td>";
echo "<td bgcolor = '#666'>TIEMPOS DEBIO</td>";
echo "<td bgcolor = '#666'>TIEMPOS LLEGO</td>";
echo "<td bgcolor = '#666'>DIFERENCIA</td>";
echo "<td bgcolor = '#666'>ESTADO</td>";
echo "</tr>";

for ($i=0; $i < count($id_punto_final); $i++) {
	echo "<tr>";
    echo "<td>". $id_punto_final[$i] . "</td>";    
	echo "<td>". $orden_punto[$i] . "</td>";
	echo "<td>". $name_punto[$i] . "</td>";
	echo "<td>". $times[$i] . "</td>";
	echo "<td>". $time_debio[$i] . "</td>";
	echo "<td>". $time_llego[$i] . "</td>";
	echo "<td>". $time_dif[$i] . "</td>";	
	echo "<td>". $estado_puntos[$i] . "</td>";	
}
echo "</table>";
echo "Total: ".count($id_punto_final)."<br>";