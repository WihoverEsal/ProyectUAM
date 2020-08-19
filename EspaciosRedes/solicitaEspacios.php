<?php
    include "../Recuperacion/recuperaDatos.php";

    function solicitaEspacios(){
        $recupera = new RecuperarDatos();
        $espaciosAcademicos = $recupera->recuperaEspacios();
        $espaciosAcademicos = json_decode($espaciosAcademicos, true);
        $i=0;

        $inputs = '';
        foreach($espaciosAcademicos as $espacio):
            $inputs .= '<input name="espacios" type="submit" class="btn_per" href="../GeneraGraficas/dashboard.php" id="espacio'.$i.'" value="'.$espacio['espacioAcademico'].'"> </input>';
            $i++;
        endforeach;                

        return $inputs;
    }    
    
    echo solicitaEspacios();
    
?>