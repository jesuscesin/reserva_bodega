$(document).ready(onLoad);  
function onLoad() {


    get_data();
    $("#btn_subir").click(confimacion_subida);
    $("#btn_limpiar").click(clear_inputFile);
    $("#tbl_b2b_homologa").on('click','#btn_Delete',borrarPlanilla);
    //$("#tbl_b2b_homologa").on('click','#btn_Download',descargaPlanilla);
   
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

function clear_table() {
    
    $("#tbl_b2b_homologa tbody tr").remove();
     
}

function cargarArchivo() {

    var input_file = document.getElementById('inputFileCargaMasiva');
    var file = input_file.files[0];
    var data = new FormData();

    data.append("inputFileCargaMasiva", file);
    //alert(data);
    $.ajax({
        url:'articulos_homologate.php',
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
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false
            });
        },
        success:function(data){
            $("#mensajes").html("<p></p>");
            Swal.fire('Archivo subido correctamente!', 'Se ha completado la carga', 'success');
            clear_inputFile();
            clear_table();
            get_data();
        },

    });
    
}

function get_data(){


    $.ajax({
        url:'articulos_homologate.php',
        type: 'GET',
        dataType: 'json',
        //contentType: false,
        data:{'getData':'isGet'},
        //cache: false
        ////////////////////HACER WHEN SUCCESS//////////////////////
        success:function(jsonphp){
            //if (data.success) {
            var i=0;
            $.each(jsonphp, function(indice, valores){

                    i=i+1;
                    var tr = $("<tr/>").attr({'id':i});

                    var domain = window.location.origin; //dominio url-principal
                    var pathname = '/grupomonarch/portal/'; //ruta al directorio
                    var metadata = 'articulos_homologate.php?getPlanilla=getPlanilla&archivo='
                    var filename = valores.NOM_ARCHIVO; //nombre del archivo

                    var url = domain+pathname+metadata+filename; //genera URL de descarga
                
                    
                    
                    //var btn_Download = $("<button/>").text("Download");
                    //btn_Download.attr({'type':'button','class':'btn btn-block bg-gradient-info btn-sm', 'id':'btn_Download','indice':i,'archivo':valores.NOM_ARCHIVO}); 
                    
                    var btnA_Download = $("<a/>").text("Descargar");
                    btnA_Download.attr({'class':'btn btn-block bg-gradient-success btn-sm', 'id':'btnA_Convertir', 'href':url, 'indice':i}); 
                    var btn_Delete = $("<button/>").text("Borrar");
                    btn_Delete.attr({'type':'button','class':'btn btn-block bg-gradient-danger btn-sm', 'id':'btn_Delete','indice':i,'archivo':valores.NOM_ARCHIVO}); 

                    

                    $('<td/>').html(i).appendTo(tr);
                    $('<td/>').html(valores.NOM_ARCHIVO).appendTo(tr);
                    $('<td/>').html(valores.FECHA_SUBIDA).appendTo(tr);
                    $('<td/>').html(valores.HORA_SUBIDA).appendTo(tr);
                    $('<td/>').html(valores.RESPONSABLE).appendTo(tr);
                    $('<td/>').html(valores.ROWS).appendTo(tr);
                    $('<td/>').html(valores.ROWSDB).appendTo(tr);
                    
                    if (valores.ROWSDB != valores.ROWS) {
                        tr.attr('style','background-color:  #f9a5a5; border:1px solid;');
                        $('<td />').html('CARGA CON ERRORES  <i class="fas fa-minus-circle"></i>' ).appendTo(tr);
                    }else{
                        $('<td />').html('CARGA CORRECTA <i class="fas fa-check-square"></i>').appendTo(tr);     
                    }

                    $('<td/>').html(btnA_Download).appendTo(tr);
                    $('<td/>').html(btn_Delete).appendTo(tr);
                    tr.appendTo("#tbl_b2b_homologa");

            });
            
        },
        error:function(xhr, status, error) {
            var err = eval("(" + xhr.responseText + ")");
            alert(err.Message);
            
            
        },
    });

}	

function borrarPlanilla(){

    archivo = $(this).attr('archivo');
    indice = $(this).attr('indice');

    $("#"+indice).remove();

    $.ajax({

        url:'articulos_homologate.php',
        type: 'GET',
        dataType: 'text',
        //contentType: false,
        data:{'borrarPlanilla':'borrarPlanilla','archivo':archivo},
         
        success:function(textphp){
            $("#mensajes").html("<div class='alert alert-success alert-dismissible fade show' role='alert'>"+textphp+"<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>");
        },
    
    });
    

}




