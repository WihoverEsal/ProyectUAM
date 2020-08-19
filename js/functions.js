
var resUltimoReg, pagina, filtro, espacio;
function pideDatosG(espacio, filtro, nFun){
    var datosG = [espacio, filtro, nFun];    
    
    $.ajax({
        "method": "POST",
        "url": "../GeneraGraficas/solicitaDatos.php",
        "data": {'arr': JSON.stringify(datosG)},        
        success: function (response){
            res = JSON.parse(response);
            
            if(res[2] === 1){
                resUltimoReg = res[1];
                muestraUltimoReg(res[0]);
            }else{
            
                muestraGraficas(res);
            }
        }
    });
}

function muestraUltimoReg(res){    

    var tablaReg = res;
    document.getElementById("ultimoReg").innerHTML = tablaReg;    
}

function muestraGraficas(res){
    
    console.log("Cargando graficas...");
    var img = res;
    document.getElementById("imagen").innerHTML = img;
}

function enviaDatosReporte(espacio, filtro){
    if(filtro == ""){
        alert("Seleccione un filtro");
    }else{
        var datosR = [espacio, filtro, resUltimoReg];
        $.ajax({
            "method": "POST",
            "url": "../GeneraReportes/generaReporte.php",
            "data": {'array': JSON.stringify(datosR)},
            success: function (response) {

                window.open(response,'Download');//Esta linea me permite mostrar el pdf generado y asi poder descargarlo
            }
        });
    }
}

function solicitaHistorial(espacio, filtro, pagina, fun){    
    var datos = [espacio, filtro, fun, pagina];
    $.ajax({
        "method": "POST",
        "url": "../GeneraGraficas/solicitaDatos.php",
        "data": {'arr': JSON.stringify(datos)},
        success: function(response){
            res = JSON.parse(response);            

            muestraHistorial(res);
        }
    });
}
function muestraHistorial(res){
    /**
     * Tabla para mostrar el historial
    */
    
    var tablaH = res[0];
    var paginador = res[1];
    
    document.getElementById("tablaH").innerHTML = tablaH;
    document.getElementById("paginador").innerHTML = paginador;
}

function toOnload(){
    pideDatosG(espacio, filtro, 1);
    solicitaHistorial(espacio, filtro, pagina, 2);
}

function intervalo(esp, flt, pag){ 
    espacio = esp;
    filtro = flt;
    pagina = pag;    
    if(flt != "")
        pideDatosG(esp, flt, 0);
    setInterval(toOnload, 3000);
}

function solicitaEspacios(){
    $.ajax({
        "method": "POST",
        "url": "../EspaciosRedes/solicitaEspacios.php",
        //"data": "",
        success: function(response){            
            muestraEspacios(response);
        }
    });    
}

function muestraEspacios(res){    
    document.getElementById("espaciosA").innerHTML = res;
}
