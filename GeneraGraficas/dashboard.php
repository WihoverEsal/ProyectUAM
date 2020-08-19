<DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/estilos.css?".<?php echo rand()?>>
    <script src="../js/jquery-3.4.1.min.js"></script>
	<script type="text/javascript" src="../js/functions.js"></script>

    <title>Dashboard</title>
      
</head>

<body onload="intervalo(<?php echo '\''.$_GET['espacios'].'\',\''.(isset($_GET['filtros']) ? $_GET['filtros'] : '').'\',\''.$_GET['pagina'].'\'';?>); ">
    <?php
        $espacio = "";
        if(isset($_GET['espacios']))
            $espacio = $_GET['espacios'];
        if(!isset($_GET['pagina'])  ){
            header("HTTP/1.1 301 Moved Permanently"); 
            header('Location: dashboard.php?pagina=1&espacios='.$espacio);
        }        
    ?>

    <nav class="navbar navbar-dark bg-dark">
        <div class="navbar-header">
            <a href="" class="navbar-brand mx-auto">Dashboard</a>
        </div>
    </nav>
    
    <div class="container-fluid">
        <div class="form-row">
            <div class="col-md-2 mt-2 card list-group-flush" id="menu">                    
                
                <a href="" class="nav-link list-group-item" >
                    <span class="nav-link">Dashboard</span>
                </a>
                
                <a href="#" onclick="enviaDatosReporte(<?php echo '\''.$_GET['espacios'].'\',\''.(isset($_GET['filtros']) ? $_GET['filtros'] : '').'\'';?>)" class="nav-link list-group-item">
                    <span class="nav-link">Generar Reporte</span>
                </a>

                <a href="../EspaciosRedes/muestraEspacios.php" class="nav-link list-group-item">
                    <span class="nav-link">Regresar</span>
                </a>                    
        
            </div>
            <div class="col-md-10 mt-2">
                <div class="col">
                    Espacio Académico seleccionado:                     
                        <?php
                            echo $espacio;
                        ?>                            
                </div>
                <div class="col-md-12 mt-2">
                                        
                        <form action="#" method="get" name="frmPeticion" >
                            <div class="form-row">
                                <div class="col-md-6">
                                    <label for="">Filtar</label>
                                    <select name="filtros" id="filtros">                                        
                                        <option value="1_Semana" selected>Última Semana</option>
                                        <option value="15_Dias" >Últimos 15 Dias</option>
                                        <option value="1_Mes" >Último Mes</option>
                                    </select>                    
                                </div>
                                <!--div class="col-md-6">
                                    <label for="">Tipo Gráfica</label>
                                    <select name="tGrafica" id="tGrafica">
                                        <option value="Barras" >Gráfica de Barras</option>
                                        <option value="Lineas">Gráfica de Lineas</option>
                                        <option value="Pastel">Gráfica Pastel</option>                
                                    </select>
                                </div-->

                                <div style="display:none">
                                    <input type="text" name="espacios" id="espacios" value="<?php echo $espacio;?>" >
                                    <input type="text" name="pagina" id="pagina" value="<?php echo $_GET['pagina'];?>" >
                                </div>

                            </div>
                            <div >
                                <br>
                                <!-- input type="button" value="Solicitar Datos" onclick="solicitaDatosHistorial()" -->
                                <input type="submit" value="Enviar">
                            </div>
                        </form>

                    <div class = "form-row mt-3">
						<div class="col-md-12 card">
							<span>Último registro</span>
                            <section id="ultimoReg"></section>
						</div>
                    </div>

                    <div class="form-row mt-3">
                        <div class="col-md-12 card">
                            <span>Gráficas</span>                            
                            <section id="imagen"></section>                           
                        </div>                        
                    </div>                    

                    <div class="form-row mt-3">
                        <div class="col-md-12 card">
                            <span>Historial</span>                            
                            
                            <div id="historial" class="container">                                
                                <section id="tablaH"></section>

                                 <!--Navegacion de paginas-->
                                <section id="paginador"></section>                                                                                                                                                                                                                                                                                        
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>            
    </div>
   
</body>
</html>