<?php
    
    class DbConnection{
        private $_dbConn;
        private static $_instance = null;

        public static function getInstance(){
            if(!(self::$_instance instanceof DbConnection)){
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        public function __construct(){        
            try{
                $this->_dbConn = new PDO("mysql:host=localhost;dbname=sensores", "root", "mysql123");
                $this->_dbConn->exec("SET CHARACTER SET utf8");                
            }catch(PDOException $ex){
                echo "ERROR: No se pudo conectar a la base de datos: ".$ex->getMessage();
                die();
            }
        }
    
        public function getConn(){
            if($this->_dbConn === null){
                self::getInstance();
            }

            return $this->_dbConn;
        }
        
    }    
    
?>