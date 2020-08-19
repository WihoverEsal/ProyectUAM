<?php
    
    function templateReporte($fecha, $hora, $espacio, $uReg, $filtro){
        $i=0;
        $templateReporte = '\documentclass{article}
        \usepackage[utf8]{inputenc}
        \usepackage{parskip}
        \usepackage{amsfonts}
        \usepackage{vmargin}
        \usepackage{fancyhdr}
        \usepackage{graphicx}
        \usepackage[spanish,es-tabla]{babel}
        \usepackage{subcaption}
        \setpapersize{A4}
        \setmargins{2.1cm} % margen izquierdo
        {1.5cm} % margen superior
        {16.5cm} % anchura del texto
        {23.42cm} % altura del texto
        {10pt} % altura de los encabezados
        {1cm} % espacio entre el texto y los encabezados
        {0pt} % altura del pie de página
        {2cm} % espacio entre el texto y el pie de página
        
        \\graphicspath{{img/}}
        \\renewcommand{\headrulewidth}{0.8pt}
        \\renewcommand{\\footrulewidth}{0.8pt}
        \cfoot{}
        \\rfoot{\\thepage}
        
        \begin{document}
        \\newpage
        \pagestyle{fancy}
        \\textit{  \huge \centerline{Reporte de Estatus} }
        \\newline
        \\large
        \\textit{ \LARGE \centerline{Espacio Académico: '.$espacio[0].'} }
        \\newline
        \\normalsize
        \\newline Fecha: '.$fecha.'
        \\newline Hora: '.$hora.'
        \\newline Intervalo de tiempo de '.$filtro.'
        \\newline
        \\newline Ultimo registro capturado por los sensores del espacio académico:
        \\newline
        \\begin{table}[h!]
            \\begin{center}
                \\begin{tabular}{|| c | c | c | c ||}
                        \\hline
                        N$^{\\circ}$ Sensor & Temperatura ($^{\\circ}$C) & Humedad (\%) & Intensidad Luminosa (cd) \\\\ \\hline            
                        '; 
                        while($i < sizeof($uReg)){
                            $templateReporte .= $uReg[$i]["nombreSensor"].' & '.$uReg[$i]["temperatura"].' & '.$uReg[$i]["humedad"].' & '.$uReg[$i]["int_luminosa"].'\\\\';
                            $i++;
                        }
                        $i = 0;
            $templateReporte.='\\hline
                \\end{tabular}
                \\caption{Ultimo registro.}
                \\label{tab:ultReg}
            \\end{center}
        \\end{table}
        \\newline Las siguientes gráficas muestran la media calculada por dia para el sensor de temperatura de cada nodo.
        \\begin{figure}[h!]
            \\centering';
        while($i < sizeof($uReg)){
            $templateReporte .= '\\begin{subfigure}[h]{0.42\linewidth}
                                    \\includegraphics[width=\linewidth]{gLineasTemp_'.$espacio[0].'_'.$espacio[1].'_Nodo'.$i.'.png}
                                    \\caption{Sensor Temperatura nodo '.($i+1).'}
                                    \\label{fig:Temp'.($i+1).'}
                                \\end{subfigure}
                                ';
            $i++;
        }
        $i=0;
        $templateReporte .= '\\caption{Sensor Temperatura por nodo}
		    \\label{fig:fig1}
        \\end{figure}
        \\newpage
        Media por dia del sensor de Humedad segun el i-ésimo nodo en el espacio académico.
        \\begin{figure}[h!]
            \\centering';
        while($i < sizeof($uReg)){
            $templateReporte .= '\\begin{subfigure}[h]{0.42\linewidth}
                                    \\includegraphics[width=\linewidth]{gLineasH_'.$espacio[0].'_'.$espacio[1].'_Nodo'.$i.'.png}
                                    \\caption{Sensor de Humedad nodo '.($i+1).'}
                                    \\label{fig:Humedad'.($i+1).'}
                                \\end{subfigure}
                                ';
            $i++;
        }
        $i=0;
        $templateReporte .= '\\caption{Sensor Humedad por nodo}
            \\label{fig:fig2}
        \\end{figure}
        \\newline
        \\newline La media para el sensor de intensidad luminosa se muestra en las siguientes gráficas.
        \\begin{figure}[h!]
            \\centering';
        while($i < sizeof($uReg)){
            $templateReporte .= '\\begin{subfigure}[h]{0.42\linewidth}
                                    \\includegraphics[width=\linewidth]{gLineasIL_'.$espacio[0].'_'.$espacio[1].'_Nodo'.$i.'.png}
                                    \\caption{Sensor de Intensidad Luminosa nodo '.($i+1).'}
                                    \\label{fig:IntL'.($i+1).'}
                                \\end{subfigure}
                                ';
            $i++;
        }
        $i=0;
        $templateReporte .= '\\caption{Sensor Intensidad Luminosa por nodo}
            \\label{fig:fig3}
        \\end{figure}
        \\newpage
        Máximo valor alcanzado por el sensor de temperatura en cada uno de los nodos.
        \\begin{figure}[h!]
            \\centering';
        while($i < sizeof($uReg)){
            $templateReporte .= '\\begin{subfigure}[h]{0.42\linewidth}
                                    \\includegraphics[width=\linewidth]{gBarrasMaxTemp_'.$espacio[0].'_'.$espacio[1].'_Nodo'.$i.'.png}
                                    \\caption{Valor máximo sensor de Temperatura nodo '.($i+1).'}
                                    \\label{fig:TempMax'.($i+1).'}
                                \\end{subfigure}
                                ';
            $i++;
        }
        $i=0;
        $templateReporte .= '\\caption{Sensor Temperatura por nodo}
            \\label{fig:fig4}
        \\end{figure}
        \\newline El valor máximo capturado por el sensor de humedad para cada nodo se puede observar en la siguiente figura
        \\begin{figure}[h!]
            \\centering';
        while($i < sizeof($uReg)){
            $templateReporte .= '\\begin{subfigure}[h]{0.42\linewidth}
                                    \\includegraphics[width=\linewidth]{gBarrasMaxH_'.$espacio[0].'_'.$espacio[1].'_Nodo'.$i.'.png}
                                    \\caption{Mayor valor de sensor Humedad nodo '.($i+1).'}
                                    \\label{fig:HumedadMax'.($i+1).'}
                                \\end{subfigure}
                                ';
            $i++;
        }
        $i=0;
        $templateReporte .= '\\caption{Sensor Humedad por nodo}
            \\label{fig:fig5}
        \\end{figure}
        \\newpage
        Para el sensor de intensidad luminosa en la siguiente figura se tienen los valores maximos alcanzados.
        \\begin{figure}[h!]
            \\centering';
        while($i < sizeof($uReg)){
            $templateReporte .= '\\begin{subfigure}[h]{0.42\linewidth}
                                    \\includegraphics[width=\linewidth]{gBarrasMaxIL_'.$espacio[0].'_'.$espacio[1].'_Nodo'.$i.'.png}
                                    \\caption{Sensor Intensidad Luminosa nodo '.($i+1).'}
                                    \\label{fig:ILMax'.($i+1).'}
                                \\end{subfigure}
                                ';
            $i++;
        }
        $i=0;
        $templateReporte .= '\\caption{Sensor Intensidad luminosa por nodo}
            \\label{fig:fig6}
        \\end{figure}
        \\newline
        \\newline Valor minimo capturado por el sensor de Temperatura en cada uno de los nodos.
        \\begin{figure}[h!]
            \\centering';
        while($i < sizeof($uReg)){
            $templateReporte .= '\\begin{subfigure}[h]{0.42\linewidth}
                                    \\includegraphics[width=\linewidth]{gBarrasMinTemp_'.$espacio[0].'_'.$espacio[1].'_Nodo'.$i.'.png}
                                    \\caption{Minimo valor sensor Temperatura nodo '.($i+1).'}
                                    \\label{fig:TempMin'.($i+1).'}
                                \\end{subfigure}
                                ';
            $i++;
        }
        $i=0;
        $templateReporte .= '\\caption{Sensor Temperatura por nodo}
            \\label{fig:fig7}
        \\end{figure}
        \\newpage
        En el sensor de humedad se capturaron los valores minimos que se presentan la siguiente figura.
        \\begin{figure}[h!]
            \\centering';
        while($i < sizeof($uReg)){
            $templateReporte .= '\\begin{subfigure}[h]{0.42\linewidth}
                                    \\includegraphics[width=\linewidth]{gBarrasMinH_'.$espacio[0].'_'.$espacio[1].'_Nodo'.$i.'.png}
                                    \\caption{Minimo valor sensor Humedad nodo '.($i+1).'}
                                    \\label{fig:HumedadMin'.($i+1).'}
                                \\end{subfigure}
                                ';
            $i++;
        }
        $i=0;
        $templateReporte .= '\\caption{Sensor Humedad por nodo}
            \\label{fig:fig8}
        \\end{figure}        
        \\newline Minimo valor capturado por el sensor de Intensidad Luminosa en cada nodo.
        \\begin{figure}[h!]
            \\centering';
        while($i < sizeof($uReg)){
            $templateReporte .= '\\begin{subfigure}[h]{0.42\linewidth}
                                    \\includegraphics[width=\linewidth]{gBarrasMinIL_'.$espacio[0].'_'.$espacio[1].'_Nodo'.$i.'.png}
                                    \\caption{Minimo valor sensor Intensidad Luminosa nodo '.($i+1).'}
                                    \\label{fig:ILMin'.($i+1).'}
                                \\end{subfigure}
                                ';
            $i++;
        }
        $i=0;
        $templateReporte .= '\\caption{Sensor Intensidad Luminosa por nodo}
                \\label{fig:fig9}
            \\end{figure}
        \\end{document}';        

        return $templateReporte;
    }

    if(isset($_POST['array']))
        $espacio = json_decode($_POST['array'], true);//Arreglo de datos proveniente de dashboard.php
    
    $uReg = $espacio[2];
    
    $espacioCn = str_replace(" ", "_", $espacio[0]);
    $filtro = str_replace("_", " ", $espacio[1]);
    
    //Fecha y hora
    date_default_timezone_set("America/Mexico_City");
    $dia = date('l');
    $mes = date('M');
    $dia = ($dia == "Monday") ? "Lunes" : (($dia == "Tuesday") ? "Martes" : (($dia == "Wednesday") ? "Miercoles" : (($dia == "Thursday") ? "Jueves" : (($dia == "Friday") ? "Viernes" : (($dia == "Saturday") ? "Sabado" : (($dia == "Sunday") ? "Domingo" : "0" ))))));
    $mes = ($mes == "Jan") ? "Enero" : (($mes == "Feb") ? "Febrero" : (($mes == "Mar") ? "Marzo" : (($mes == "Apr") ? "Abril" : (($mes == "May") ? "Mayo" : (($mes == "Jun") ? "Junio" : (($mes == "Jul") ? "Julio" : (($mes == "Aug") ? "Agosto" : (($mes == "Sep") ? "Septiembre" : (($mes == "Oct") ? "Octubre" : (($mes == "Nov") ? "Noviembre" : "December" ))))))))));
    $fecha = $dia ." ". date('d') ." de ". $mes ." de " . date('Y');
    $hora = date('H') . ":" .date('i')." hrs";
    $nombreReporte = date('Y').date('m').date('d')."_".date('H').date('i');

    $fileTex = $espacioCn."_".$nombreReporte.".tex";
    $filePDF = $espacioCn."_".$nombreReporte.".pdf";

    exec("cd reportes && mkdir reporte$espacioCn ");
    exec("cd reportes/reporte$espacioCn && mkdir reporte$nombreReporte && cd reporte$nombreReporte && mkdir img");
    
    $templateLatex = templateReporte($fecha, $hora, $espacio, $uReg, $filtro);
    
    $file = fopen("reportes/reporte$espacioCn/reporte$nombreReporte/".$fileTex, "w+") or die("Se produjo un error al crear el archivo");
    fwrite($file, $templateLatex);
    fclose($file);

    if( strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ){
        //echo 'Este un servidor usando Windows!';
        shell_exec('copy ..\GeneraGraficas\graficas\g*'.$espacio[0].'_'.$espacio[1].'*.png .\\reportes\\reporte'.$espacioCn.'\\reporte'.$nombreReporte.'\\img');

        //$data = shell_exec('cd reportes/reporte'.$espacioCn.'/reporte'.$nombreReporte.'/ && C:\MiKTeX\miktex\bin\\x64\pdflatex.exe --interaction=nonstopmode '.$fileTex);
        $data = shell_exec('cd reportes/reporte'.$espacioCn.'/reporte'.$nombreReporte.'/ && pdflatex.exe --interaction=nonstopmode '.$fileTex);
	}else{
        //echo 'Este es un servidor que no usa Windows!';
        shell_exec("cp ../GeneraGraficas/graficas/g*$espacio[0]_$espacio[1]*.png ./reportes/reporte$espacioCn/reporte$nombreReporte/img");    

        $data = shell_exec("cd reportes/reporte$espacioCn/reporte$nombreReporte/ && /usr/bin/pdflatex --interaction=batchmode $fileTex");
	}
    // /usr/bin/   --output-directory=reportes/reporte$espacioCn/reporte$nombreReporte/    

    //Descargar PDF
    $ruta = "../GeneraReportes/reportes/reporte$espacioCn/reporte$nombreReporte/$filePDF";    

    echo $ruta;        

?>


