$(document).ready(onLoad);    
function onLoad() {

    // datos_subidos();
    //dateModal();
    //$("#enviar").click(cargarInforme);
    $("#btn_guardar").click(confimacion_subida);
    // $("#tbl_planillas_falabella").on('click','#btn_borrar',borrarPlanilla);
    

}



function borrarPlanilla(){

    archivo = $(this).attr('archivo');
    indice = $(this).attr('indice');

    alert(indice);
    $("#"+indice).remove();

    $.ajax({

        url:'subida_falabella.php',
        type: 'GET',
        dataType: 'text',
        //contentType: false,
        data:{'borrarPlanilla':'borrarPlanilla','archivo':archivo},
         
        success:function(textphp){
            $("#mensajes").html("<div class='alert alert-success alert-dismissible fade show' role='alert'>"+textphp+"<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>");
        },
    
    });
    

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



function datos_subidos() {

    $.ajax({
        
        url:'subida_falabella.php',
        type: 'GET',
        dataType: 'json',
        //contentType: false,
        data:{'ver':'ver'},
        //processData: false,
        //cache: false
        //beforeSend:cargando,
        success:function(jsonphp){
            //if (data.success) {
            var i=0;

            $.each(jsonphp, function(indice, valores){

                    i=i+1;
                    var tr = $("<tr/>").attr({'id':i});
                    // TODO:
                    var btn_borrar = $("<button/>").text("Borrar Planilla");
                    btn_borrar.attr({'type':'button','class':'btn btn-block bg-gradient-danger btn-sm', 'id':'btn_borrar','indice':i,'archivo':valores.NOM_ARCHIVO}); 
                    
                    $('<td/>').html(i).appendTo(tr);
                    $('<td/>').html(valores.NOM_ARCHIVO).appendTo(tr);
                    $('<td/>').html(valores.NRO_OC).appendTo(tr);
                    $('<td/>').html(valores.FECHA_SUBIDA).appendTo(tr);
                    $('<td/>').html(valores.UNIDADES).appendTo(tr);
                    $('<td/>').html(valores.EMPAQUES).appendTo(tr);
                    $('<td/>').html(btn_borrar).appendTo(tr);
                    
                    tr.appendTo("#tbl_planillas_falabella");
               
            });
            
        },
        
    });
    
}



function validarArchivo() {

    var archivo = $("#file_cventas").val();
    var xls  = archivo.substr(-3);
    var xlsx = archivo.substr(-4);
    var txt  = archivo.substr(-3);
    var csv  = archivo.substr(-3);

    //alert(zip);
    
    if (archivo === null || archivo === '') {
        
        alert('Ningun archivo seleccionado');
        
    }else if(xls != 'xls' && xlsx != 'xlsx' && txt != 'txt' && csv != 'csv'){
        
        alert('Tipo de archivo incorrecto');
       
    }else{
        
        cargarArchivo();
        
    }


}



function cargarArchivo() {

    var input_file = document.getElementById('file_cventas');
    var file = input_file.files[0];
    var data = new FormData();

    data.append("file_cventas", file);
    //alert(file);
    //alert(data);
    $.ajax({

        url: 'honorarios.php',
        type: 'POST',
        dataType: 'text',
        contentType: false,
        data:data,
        processData: false,
        //cache: false
        beforeSend:function(){

            $("#mensajes").html('<img src="img/cargando2.gif">Generando Datos Solicitados,Por Favor Espere...</img>');
                    Swal.fire({
						  title: 'Generando Datos Solicitados !',
						  html: 'Por favor espere...',
						  timerProgressBar: true,
						  didOpen: () => {
						  Swal.showLoading();
							/*
                            const b = Swal.getHtmlContainer().querySelector('b');
							timerInterval = setInterval(() => {
							  b.textContent = Swal.getTimerLeft();
							}, 100);
                            */
						  },

					});

        },


        success:function(data){

            $("#mensajes").html("<div class='alert alert-info alert-dismissible fade show' role='alert'>" + data + "<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>");
            Swal.fire('Datos Cargados ! !', 'Datos Generados con exito !', 'success');

            //datos_subidos();

        },


    });
    
    
    
}



