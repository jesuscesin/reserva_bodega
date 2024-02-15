$(document).ready(onLoad);  
function onLoad() {

    $("#btn_subir").click(confimacion_subida);
}

function clear_inputFile(){
    document.getElementById("inputFileCargaMasiva").value = "";
}

function confimacion_subida(){

    Swal.fire({

         title: 'Desea Cargar Datos?',
         text: "Se Iniciara el proceso de carga",
         icon: 'warning',
         showCancelButton: true,
         confirmButtonColor: '#3085d6',
         cancelButtonColor: '#d33',
         confirmButtonText: 'Si, Subir Datos'

       }).then((result) => {

         if (result.isConfirmed) {
               validarArchivo();                
               
         }

       });

       
}

function validarArchivo() {
    var archivo = $("#inputFileCargaMasiva").val();
    var xls = archivo.substr(-3);
    var xlsx = archivo.substr(-4);
    var zip = archivo.substr(-3);
    var csv = archivo.substr(-3);
    //alert(zip);
    
    if (archivo === null || archivo === '')   {
        Swal.fire('Selecciona un archivo:', '.xls - .xlsx - .zip - .csv', 'warning');            
        
    }else if(xls != 'xls' && xlsx != 'xlsx' && zip != 'zip' && csv != 'csv'){
        
        alert('Tipo de archivo incorrecto');
       
    }else{
        
        cargarArchivo();
        
    }

}


function cargarArchivo() {

    var input_file = document.getElementById('inputFileCargaMasiva');
    var file = input_file.files[0];
    var data = new FormData();

    data.append("inputFileCargaMasiva", file);
    //alert(data);
    $.ajax({
        url:'ventasb2b_metadata.php',
        type: 'POST',
        dataType: 'text',
        contentType: false,
        data:data,
        processData: false,
        //cache: false
        ////////////////////HACER WHEN SUCCESS//////////////////////
        beforeSend:function(){
            $("#mensajes").html('<img src="img/cargando2.gif">Cargando...</img>');
            Swal.fire({
                icon: 'info',
                title: 'Procesando Planilla.. !',
                html: 'Por favor espere...',
                showCancelButton: false,
                showConfirmButton: false
                });
        },
        success:function(data){
            $("#mensajes").html("<p></p>");
            Swal.fire('Archivo subido correctamente!', 'Se ha completado la carga'+data, 'success');
            clear_inputFile();
        },

    });
    
}





