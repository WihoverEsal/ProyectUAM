<?php
    
    require_once "jpgraph\src\jpgraph.php";    
    require_once "jpgraph\src\jpgraph_line.php";
    require_once "jpgraph\src\jpgraph_date.php";
    require_once "jpgraph\src\jpgraph_bar.php";    
    
    function construyeGraficas($datos, $numNodos, $espacio, $filtro){
        $arr = array();
        for ($i=0; $i < $numNodos; $i++){
            $xdataLineas[] = array();
            $ydataLineas[] = array();

            $xdataBarras[] = array();
            $ydataMinBarras[] = array();
            $ydataMaxBarras[] = array();
        }

        $i = 0;
        /**
         * El 4 es porque el arreglo $datos siempre sera de ese tamaño 
         * sin importar la cantidad de nodos con registro en la BD
         */
        for ($i=0; $i < 4; $i++){
            if($i < 3){
                for($j = 0 ; $j < $numNodos ; $j++){
                    $numDias = sizeof($datos[$i][$j]);
                    for($k = 0 ; $k < $numDias ; $k++){
                        $ydataLineas[$j][$i][$k] = $datos[$i][$j][$k][0];
                        $xdataLineas[$j][$i][$k] = $datos[$i][$j][$k][1];
                    }
                }
            }else{        
                for($j=0; $j < sizeof( $datos[$i] ) ; $j++) { 
                    for ($k=0; $k < sizeof( $datos[$i][$j] ); $k++){                  
                        for ($l=0; $l < sizeof( $datos[$i][$j][$k] ) ; $l++){
                            $xdataBarras[$j][$k][$l] = $datos[$i][$j][$k][$l]["fecha"];
                            $ydataMinBarras[$j][$k][$l] = $datos[$i][$j][$k][$l]["menor"];
                            $ydataMaxBarras[$j][$k][$l] = $datos[$i][$j][$k][$l]["mayor"];
                        }
                    }
                }
                
            }
        }
                
        $nombresGraficasLineas = array("gLineasTemp_$espacio"."_$filtro", "gLineasH_$espacio"."_$filtro", "gLineasIL_$espacio"."_$filtro");
        $nombresGraficasBarrasMin = array("gBarrasMinTemp_$espacio"."_$filtro", "gBarrasMinH_$espacio"."_$filtro", "gBarrasMinIL_$espacio"."_$filtro");
        $nombresGraficasBarrasMax = array("gBarrasMaxTemp_$espacio"."_$filtro", "gBarrasMaxH_$espacio"."_$filtro", "gBarrasMaxIL_$espacio"."_$filtro");

        $titulosMedia = array("Media sensor Temperatura", "Media sensor Humedad", "Media sensor Intensidad Luminosa");
        $titulosMax = array("Maximo por dia, sensor Temperatura", "Maximo por dia, sensor Humedad", "Maximo por dia, sensor Intensidad Luminosa");
        $titulosMin = array("Minimo por dia, sensor Temperatura", "Minimo por dia, sensor Humedad", "Minimo por dia, sensor Intensidad Luminosa");
        $rutasGraficasLineas = array();
        $rutasGraficasBarrasMax = array();
        $rutasGraficasBarrasMin = array();

        for ($i=0; $i < $numNodos ; $i++){
            for ($j=0; $j < 3 ; $j++){                
                $rutasGraficasLineas[$j][] = hazGraficaLineas($xdataLineas[$i][$j], $ydataLineas[$i][$j], $nombresGraficasLineas[$j]."_Nodo$i.png", $titulosMedia[$j], ($i+1));
                $rutasGraficasBarrasMin[$j][] = hazGraficaBarras($xdataBarras[$i][$j], $ydataMinBarras[$i][$j], $nombresGraficasBarrasMin[$j]."_Nodo$i.png", $titulosMin[$j], ($i+1));
                $rutasGraficasBarrasMax[$j][] = hazGraficaBarras($xdataBarras[$i][$j], $ydataMaxBarras[$i][$j], $nombresGraficasBarrasMax[$j]."_Nodo$i.png", $titulosMax[$j], ($i+1));
            }
        }        

        
        $arr[0] = $rutasGraficasLineas;
        $arr[1] = $rutasGraficasBarrasMin;
        $arr[2] = $rutasGraficasBarrasMax;

        return $arr;
    }

    function hazGraficaLineas($xdata, $ydata, $nombreGrafica, $titulo, $nodo){
        $graph = new Graph(600, 350);        
        $graph->title->Set($titulo." Nodo $nodo");
        $graph->SetScale('texlin');

        //Ajusta el margen para el campo de las etiquetas del eje x
        $graph->SetMargin(40, 30, 40, 120);

        //Ubica las etiquetas de cada eje fuera del area del grafico
        $graph->xaxis->SetTickSide(SIDE_BOTTOM);
        $graph->yaxis->SetTickSide(SIDE_LEFT);

        $p0 = new LinePlot($ydata);
        $p0->SetColor('red');        
        $p0->SetLegend('Nodo '.$nodo);      

        $ap = new AccLinePlot(array($p0));

        $graph->xaxis->SetTickLabels($xdata);
        $graph->xaxis->SetTextLabelInterval(2); //Toma cada 2da etiqueta para el eje x        

        $graph->Add($ap);

        //Asignar el angulo para las etiquetas del eje x
        $graph->xaxis->SetLabelAngle(90);

        //Ajustar la posicion de las leyendas
        $graph->legend->SetLayout(LEGEND_HOR);
        $graph->legend->Pos(0.4, 0.95, "center", "bottom");

        $filename = dirname(__FILE__)."\\".$nombreGrafica;
        
        @unlink($filename);
        $graph->Stroke( $filename );
        
        
        return $nombreGrafica;
    }

    function hazGraficaBarras($xdata, $ydata, $nombreGrafica, $titulo, $nodo){
        $graph = new Graph(600, 350);        
        $graph->title->Set($titulo." Nodo $nodo");
        $graph->SetScale('texlin');

        //Ajusta el margen para el campo de las etiquetas del eje x
        $graph->SetMargin(40, 30, 40, 120);

        //Ubica las etiquetas de cada eje fuera del area del grafico
        $graph->xaxis->SetTickSide(SIDE_BOTTOM);
        $graph->yaxis->SetTickSide(SIDE_LEFT);

        $p0 = new BarPlot($ydata);
        $p0->SetColor('red');        
        $p0->SetLegend('Nodo '.$nodo);      

        $ap = new AccLinePlot(array($p0));

        $graph->xaxis->SetTickLabels($xdata);
        $graph->xaxis->SetTextLabelInterval(2); //Toma cada 2da etiqueta para el eje x

        $graph->Add($ap);

        //Asignar el angulo para las etiquetas del eje x
        $graph->xaxis->SetLabelAngle(90);

        //Ajustar la posicion de las leyendas
        $graph->legend->SetLayout(LEGEND_HOR);
        $graph->legend->Pos(0.4, 0.95, "center", "bottom");

        $filename = dirname(__FILE__)."\\".$nombreGrafica;
        
        @unlink($filename);
        $graph->Stroke( $filename );        

        return $nombreGrafica;
    }
    
?>