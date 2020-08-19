<?php
    
    require_once "DbConnection.php";

    class RecuperarDatos{

        protected function _getDbConn(){
            return DbConnection::getInstance()->getConn();
        }        
        
        public function recuperaEspacios(){
                     
            $sql = "select DISTINCT(espacioAcademico) from datosSensores";            
            
            $stm = ($this->_getDbConn())->prepare($sql);
            $res = $stm->execute();            
            
            $espaciosAcademicos = [];            
            $espaciosAcademicos = $stm->fetchAll();

            return json_encode($espaciosAcademicos);
        }

        public function consultaSensores($espacio){
            $sql1 = "select distinct(nombreSensor) from datosSensores where espacioAcademico = '$espacio'";
            $stm1 = ($this->_getDbConn())->prepare($sql1);
            $stm1->execute();
            $nodos = $stm1->fetchAll();

            return json_encode($nodos);
        }

        public function ultimoReg($espacio){
            //$sql = "select nombreSensor, temperatura, humedad, int_luminosa from datosSensores where espacioAcademico = '$espacio' AND fecha_lectura = ( select MAX(fecha_lectura) from datosSensores limit 1);";
            // Select 1 = select distinct(nombreSensor) from datosSensores where espacioAcademico = 'H294';
            // Select 2 = select nombreSensor, temperatura, humedad, int_luminosa from datosSensores where espacioAcademico = 'H294' and nombreSensor = 'Sensor 1' order by fecha_lectura desc limit 1;
            /**
             * Iterar sobre el resultado que devuelva el Select 1 ejecutando el Select 2 para el i-esimo nodo
             * El resultado del Select 2 guardarlo en un arreglo que contendra el resultado de todas
             * las iteraciones
             */
            $ultimosRegistros = array();           
                        
            $nodos = json_decode($this->consultaSensores($espacio), true);            

            foreach ($nodos as $nodo) {                
                $sql2 = "select nombreSensor, temperatura, humedad, int_luminosa from datosSensores where espacioAcademico = '$espacio' and nombreSensor = '$nodo[0]' order by fecha_lectura desc limit 1";
                $stm2 = ($this->_getDbConn())->prepare($sql2);
                $stm2->execute();
                $ultR = $stm2->fetchAll();
                array_push($ultimosRegistros, $ultR[0]);
            }            
            
            return json_encode($ultimosRegistros);
        }

        public function datosEspacioSensor($espacio, $filtro, $sensor){
            $sql = "";
            if(strcmp($filtro, "1_Dia") == 0){
                
                $sql = "select temperatura, humedad, int_luminosa, DATE(fecha_lectura) as fecha_lectura FROM datosSensores WHERE espacioAcademico = '$espacio' AND DATE(fecha_lectura) = ( select DATE(MAX(fecha_lectura)) from datosSensores limit 1)";

            }else if(strcmp($filtro, "1_Semana") == 0){
                //Asignar una variable con el valor del ultimo dia que se tomo lectura                
                $sqlSetDia = "SET @ultimaFecha = (select DATE(fecha_lectura) from datosSensores order by fecha_lectura desc limit 1)";
                $stm1 = ($this->_getDbConn())->prepare($sqlSetDia);
                $stm1->execute();

                //Obtener la ultima semana apartir del ultimo dia en $sqlSetDia                
                $sqlRango = "select DATE(fecha_lectura) as iniciaFecha, @ultimaFecha as terminaFecha from datosSensores where YEARWEEK(fecha_lectura) = YEARWEEK(@ultimaFecha - INTERVAL 1 WEEK) limit 1;";
                $stm1 = ($this->_getDbConn())->prepare($sqlRango);
                $stm1->execute();
                $rangoSemana = array();
                $rangoSemana = $stm1->fetchAll();
                $inicia = $rangoSemana[0][0];
                $termina = $rangoSemana[0][1];

                $sql = "select temperatura, humedad, int_luminosa, DATE(fecha_lectura) as fecha_lectura FROM datosSensores WHERE espacioAcademico = '$espacio' AND DATE(fecha_lectura) BETWEEN '$inicia' AND '$termina' ";
                
            }else if(strcmp($filtro, "15_Dias") == 0){
                
                $sqlSetDia = "SET @ultimaFecha = (select DATE(fecha_lectura) from datosSensores order by fecha_lectura desc limit 1)";
                $stm1 = ($this->_getDbConn())->prepare($sqlSetDia);
                $stm1->execute();

                //Obtener las ultimas 2 semanas apartir del ultimo dia en $sqlSetDia                
                $sqlRango = "select DATE(fecha_lectura) as iniciaFecha, @ultimaFecha as terminaFecha from datosSensores where YEARWEEK(fecha_lectura) = YEARWEEK(@ultimaFecha - INTERVAL 2 WEEK) limit 1";
                $stm1 = ($this->_getDbConn())->prepare($sqlRango);
                $stm1->execute();
                $rangoSemana = [];
                $rangoSemana = $stm1->fetchAll();
                $inicia = $rangoSemana[0][0];
                $termina = $rangoSemana[0][1];

                $sql = "select temperatura, humedad, int_luminosa, DATE(fecha_lectura) as fecha_lectura FROM datosSensores WHERE espacioAcademico = '$espacio' AND DATE(fecha_lectura) BETWEEN '$inicia' AND '$termina' ";                
            }else if(strcmp($filtro, "1_Mes") == 0){

                $sqlSetDia = "SET @ultimaFecha = (select DATE(fecha_lectura) from datosSensores order by fecha_lectura desc limit 1)";
                $stm1 = ($this->_getDbConn())->prepare($sqlSetDia);
                $stm1->execute();

                //Obtener las ultimas 4 semanas apartir del ultimo dia en $sqlSetDia
                $sqlRango = "select DATE(fecha_lectura) as iniciaFecha, @ultimaFecha as terminaFecha from datosSensores where YEARWEEK(fecha_lectura) = YEARWEEK(@ultimaFecha - INTERVAL 4 WEEK) limit 1";
                $stm1 = ($this->_getDbConn())->prepare($sqlRango);
                $stm1->execute();
                $rangoSemana = [];
                $rangoSemana = $stm1->fetchAll();
                $inicia = $rangoSemana[0][0];
                $termina = $rangoSemana[0][1];

                $sql = "select temperatura, humedad, int_luminosa, DATE(fecha_lectura) as fecha_lectura FROM datosSensores WHERE espacioAcademico = '$espacio' AND DATE(fecha_lectura) BETWEEN '$inicia' AND '$termina' ";                
            }

            $sql .= " AND nombreSensor = '$sensor' ";            
            $stm = ($this->_getDbConn())->prepare($sql);
            $stm->execute();
            $datosEspacioSensores = $stm->fetchAll();

            return json_encode($datosEspacioSensores);
        }
    
        public function datosEspacio($espacio, $desde, $cantidad){
            $sql = "";
            $limit = false;
            
            if($desde == -1 || $cantidad == -1){
                $columnas = "COUNT(*) as total";                
            }else{
                $columnas = " nombreSensor, temperatura, humedad, int_luminosa, fecha_lectura ";
                $limit = true;
            }
            
            $sql = "select $columnas FROM datosSensores WHERE espacioAcademico = '$espacio' ";
            if($limit == true)
                $sql .= " order by fecha_lectura desc limit $desde, $cantidad ";
                                    
            $stm = ($this->_getDbConn())->prepare($sql);
            $stm->execute();
                        
            $datosEspacio = $stm->fetchAll();            

            return json_encode($datosEspacio);            
        }
    }
    
?>