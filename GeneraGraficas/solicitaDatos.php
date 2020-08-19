<?php     
    require_once "../Recuperacion/recuperaDatos.php";
    require_once "graficas/construyeGrafica.php";
        
    
    function solicitaDatosH($espacio, $pagina, $filtro){        
        $solicita = new RecuperarDatos();
        $datosGH = $solicita->datosEspacio($espacio, -1, -1);
        $datosGH = json_decode($datosGH, true);        
        $numRegistros = 80;
        $numPaginas = ceil($datosGH[0]["total"] / $numRegistros);        
        $desde = ($pagina-1) * $numRegistros;        
        $datosGH = $solicita->datosEspacio($espacio, $desde, $numRegistros);
        $datosGH = json_decode($datosGH, true);    
        $result = array();        
        $i = 0;
        $tabla = '<table id = "history" class = "table tabe-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>Sensor</th>
                                <th>Temperatura (°C)</th>
                                <th>Humedad (%)</th>
                                <th>Intensidad Luminosa (cd)</th>
                                <th>Fecha de Lectura</th>
                            </tr>
                        </thead>
                        <tbody>';
                            while($i < sizeof($datosGH)){
                                $tabla .= '<tr>
                                               <td>'.$datosGH[$i]['nombreSensor'].'</td>
                                               <td>'.$datosGH[$i]['temperatura'].'</td>
                                               <td>'.$datosGH[$i]['humedad'].'</td>
                                               <td>'.$datosGH[$i]['int_luminosa'].'</td>
                                               <td>'.$datosGH[$i]['fecha_lectura'].'</td>
                                           </tr>';
                                $i++;
                            }
        $tabla .=       '</tbody>
                  </table>';
        $navegacionPags = '<nav aria-label="Page navigation example" > 
                                <ul class="pagination" >
                                    <li class="page-item '.($pagina <= 1 ? 'disabled' : '').'">
                                        <a class="page-link" href="dashboard.php?pagina='.($pagina-1).'&espacios='.$espacio.($filtro != '' ? '&filtros='.$filtro : '').'"> Anterior </a>
                                    </li>
                                    <li class="page-item '.($pagina >= $numPaginas ? 'disabled' : '').'">
                                        <a class="page-link" href="dashboard.php?pagina='.($pagina+1).'&espacios='.$espacio.($filtro != '' ? '&filtros='.$filtro : '').'"> Siguiente </a>
                                    </li>
                                </ul>
                            </nav>';
        
        $result[] = $tabla;
        $result[] = $navegacionPags;
        
        return json_encode($result);
    }

    function solicitaDatosGraficas($espacio, $filtro){
        $solicita = new RecuperarDatos();
        
        $nodos = json_decode($solicita->consultaSensores($espacio), true);
        $datosSenores = array();
        $tamSensores = array();
        $datosDistPSensor_Fecha = array();
        $numNodos = sizeof($nodos);
        foreach ($nodos as $nodo) {
            $datosSensores[] = json_decode( $solicita->datosEspacioSensor($espacio, $filtro, $nodo[0]) , true);
        }
        
        for($i = 0 ; $i < $numNodos ; $i++){
            $tamSensores[] = sizeof($datosSensores[$i]);
            $datosDistPSensor_Fecha[] = array();            
        }
        $i = 0;
        $k = 0;
                
        $fecha = $datosSensores[0][0]["fecha_lectura"];

        if( strcmp($filtro, "1_Dia") != 0 ){
            /**
             * Separa por fechas los registros cuando se selecciona 1 semana, 15 dias o 1 mes
            */
            for($m = 0 ; $m < $numNodos ; $m++){
                for($j = 0 ; $j < $tamSensores[$m] ; $j++){                    
                    if(strcmp($fecha,$datosSensores[$m][$j]["fecha_lectura"]) == 0 ){
                        $i++;
                        $datosDistPSensor_Fecha[$m][$k][] = [ "temperatura" => $datosSensores[$m][$j]["temperatura"], "humedad" => $datosSensores[$m][$j]["humedad"], "int_luminosa" => $datosSensores[$m][$j]["int_luminosa"], "fecha_lectura" => $datosSensores[$m][$j]["fecha_lectura"] ];
                    }else{
                        
                        $i = 0;
                        $fecha = $datosSensores[$m][$j]["fecha_lectura"];
                        $k++;
                        $datosDistPSensor_Fecha[$m][$k] = array();
                        
                        $j -= 1;
                    }
                }
            }
        }else{            
            for($m = 0 ; $m < sizeof($nodos) ; $m++){
                $i=0;
                for($j = 0 ; $j < $tamSensores[$m] ; $j++){
                    $i++;
                    $datosDistPSensor_Fecha[$m][0][] = [ "temperatura" => $datosSensores[$m][$j]["temperatura"], "humedad" => $datosSensores[$m][$j]["humedad"], "int_luminosa" => $datosSensores[$m][$j]["int_luminosa"], "fecha_lectura" => $datosSensores[$m][$j]["fecha_lectura"] ];
                }
            }            
        }        
        /**
         * Trabajar con el arreglo $datosDistPSensor_Fecha
        */
        
        $mediaTempSensores    = array();
        $mediaHumedadSensores = array();
        $mediaIntLumSensores  = array();

        for($l = 0 ; $l < $numNodos ; $l++){
            $mediaTempSensores[$l] = array();
            $mediaHumedadSensores[$l] = array();
            $mediaIntLumSensores[$l] = array();
        }
        for($l = 0 ; $l < $numNodos ; $l++){
            for($n = 0 ; $n < sizeof($datosDistPSensor_Fecha[$l]) ; $n++){
                $mediaTempSensores[$l][$n][0] = 0;
                $mediaTempSensores[$l][$n][1] = "";

                $mediaHumedadSensores[$l][$n][0] = 0;
                $mediaHumedadSensores[$l][$n][1] = "";

                $mediaIntLumSensores[$l][$n][0] = 0;
                $mediaIntLumSensores[$l][$n][1] = "";
            }
        }

        $minMaxTempDias[] = array();
        $minMaxHumedadDias[] = array();
        $minMaxIntLumDias[] = array();
        
        $sensorMinMax[] = array();
        $mayorT = $mayorH = $mayorIL = 0;
        $menorT = $menorH = $menorIL = 1000000;
        $numSensor = $day = 0;
        
        foreach($datosDistPSensor_Fecha as $sensor):
            foreach($sensor as $dia):
                foreach($dia as $dato):
                    
                    settype($dato["temperatura"], "double");
                    settype($dato["humedad"], "int");
                    settype($dato["int_luminosa"], "int");
                    
                    $mediaTempSensores[$numSensor][$day][0] += $dato["temperatura"];
                    $mediaTempSensores[$numSensor][$day][1] = $dato["fecha_lectura"];
                    
                    $mediaHumedadSensores[$numSensor][$day][0] += $dato["humedad"];
                    $mediaHumedadSensores[$numSensor][$day][1] = $dato["fecha_lectura"];
                    
                    $mediaIntLumSensores[$numSensor][$day][0] += $dato["int_luminosa"];
                    $mediaIntLumSensores[$numSensor][$day][1] = $dato["fecha_lectura"];

                    if($dato["temperatura"] < $menorT){
                        $menorT = $dato["temperatura"];                        
                    }
                    if($dato["temperatura"] > $mayorT){
                        $mayorT = $dato["temperatura"];                        
                    }                    

                    if($dato["humedad"] < $menorH){
                        $menorH = $dato["humedad"];                        
                    }
                    if($dato["humedad"] > $mayorH){
                        $mayorH = $dato["humedad"];
                    }
                    
                    if($dato["int_luminosa"] < $menorIL){
                        $menorIL = $dato["int_luminosa"];
                    }
                    if($dato["int_luminosa"] > $mayorIL){
                        $mayorIL = $dato["int_luminosa"];
                    }
                    $fecha = $dato["fecha_lectura"];
                endforeach;
                
                $minMaxTempDias[$day] = [ "menor" => $menorT, "mayor" => $mayorT, "fecha" => $fecha ];                
                $minMaxHumedadDias[$day] = [ "menor" => $menorH, "mayor" => $mayorH, "fecha" => $fecha ];                
                $minMaxIntLumDias[$day] = [ "menor" => $menorIL, "mayor" => $mayorIL, "fecha" => $fecha ];
                $day++;
                
                $mayorT = $mayorH = $mayorIL = 0;
                $menorT = $menorH = $menorIL = 1000000;
            endforeach;
            
            $sensorMinMax[$numSensor] = [ $minMaxTempDias, $minMaxHumedadDias, $minMaxIntLumDias ];
            $numSensor++;
            $day = 0;
        endforeach;
        $day = 0;
        
        for($j = 0 ; $j < $numNodos ; $j++){
            foreach($mediaTempSensores[$j] as $dia):
                $mediaTempSensores[$j][$day][0] /= $i;
                $day++;
            endforeach;
            $day = 0;
            foreach($mediaHumedadSensores[$j] as $dia):
                $mediaHumedadSensores[$j][$day][0] /= $i;
                $day++;
            endforeach;
            $day = 0;
            foreach($mediaIntLumSensores[$j] as $dia):
                $mediaIntLumSensores[$j][$day][0] /= $i;
                $day++;
            endforeach;
            $day = 0;
        }
        
        $datos = [$mediaTempSensores, $mediaHumedadSensores, $mediaIntLumSensores, $sensorMinMax];        
        
        $imagesPath = construyeGraficas($datos, $numNodos, $espacio, $filtro);
        $srcLineas = $imagesPath[0];
        $srcBarrasMin = $imagesPath[1];
        $srcBarrasMax = $imagesPath[2];        

        $img = array(array(), array(), array());
        $arr[0] = '';
        $img[0][0] = '<br> <p class="texto">Media de los valores del sensor Temperatura por cada nodo del espacio académico '.$espacio.'</p> <br>';
        $img[0][1] = '<br><br> <p class="texto">Media de los valores del sensor Humedad por cada nodo del espacio académico '.$espacio.'</p> <br>';
        $img[0][2] = '<br><br> <p class="texto">Media de los valores del sensor Intensidad Luminosa por cada nodo del espacio académico '.$espacio.'</p> <br>';
        $img[1][0] = '<br><br> <p class="texto">Minimo por dia sensor Temperatura por cada nodo del espacio académico '.$espacio.'</p> <br>';
        $img[1][1] = '<br><br> <p class="texto">Minimo por dia sensor Humedad por cada nodo del espacio académico '.$espacio.'</p> <br>';
        $img[1][2] = '<br><br> <p class="texto">Minimo por dia sensor Intensidad Luminosa por cada nodo del espacio académico '.$espacio.'</p> <br>';
        $img[2][0] = '<br><br> <p class="texto">Maximo por dia sensor Temperatura por cada nodo del espacio académico '.$espacio.'</p> <br>';
        $img[2][1] = '<br><br> <p class="texto">Maximo por dia sensor Humedad por cada nodo del espacio académico '.$espacio.'</p> <br>';
        $img[2][2] = '<br><br> <p class="texto">Maximo por dia sensor Intensidad Luminosa por cada nodo del espacio académico '.$espacio.'</p> <br>';
        
        for ($j=0; $j < sizeof($imagesPath) ; $j++){            
            for ($k=0; $k < $numNodos ; $k++){                                
                $img[0][$j] .= '<img src="graficas\\'.$srcLineas[$j][$k].'" hspace = "10" height="250" width="245">';
                $img[1][$j] .= '<img src="graficas\\'.$srcBarrasMin[$j][$k].'" hspace = "10" height="250" width="245">';
                $img[2][$j] .= '<img src="graficas\\'.$srcBarrasMax[$j][$k].'" hspace = "10" height="250" width="245">';                
            }            
        }

        for ($j=0; $j < sizeof($img); $j++){
            for ($k=0; $k < sizeof($img[$j]); $k++) { 
                $arr[0] .= $img[$j][$k];
            }
        }
                
        return json_encode($arr);
    }

    function solicitaUltimoReg($espacio){
        $solicita = new RecuperarDatos();
        $ultimoReg = $solicita->ultimoReg($espacio);
        $ultimoReg = json_decode($ultimoReg, true);
        
        $i = 0;
        $tabla = '<table id = "history" class = "table tabe-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>Sensor</th>
                                <th>Temperatura (°C)</th>
                                <th>Humedad (%)</th>
                                <th>Intensidad Luminosa (cd)</th>                                
                            </tr>
                        </thead>
                        <tbody>';
                            while($i < sizeof($ultimoReg)){
                                $tabla .= '<tr>
                                               <td>'.$ultimoReg[$i]['nombreSensor'].'</td>
                                               <td>'.$ultimoReg[$i]['temperatura'].'</td>
                                               <td>'.$ultimoReg[$i]['humedad'].'</td>
                                               <td>'.$ultimoReg[$i]['int_luminosa'].'</td>                                               
                                           </tr>';
                                $i++;
                            }
        $tabla .=       '</tbody>
                  </table>';
        $res[] = $tabla;
        $res[] = $ultimoReg;
        $res[] = 1;

        return json_encode($res);
    }  
    
    if( isset($_POST['arr']) ){
        $data = json_decode($_POST['arr']);
        
        switch($data[2]){
            case 0:
                echo solicitaDatosGraficas($data[0], $data[1]);
            break;
            case 1:
                echo solicitaUltimoReg($data[0]);
            break;
            case 2:                
                echo solicitaDatosH($data[0], $data[3] ,$data[1]);
            break;
        }
    }            
    
?>