$(document).ready(onLoad);
function onLoad() {
      dateModal();
      $("#articulo").change(cargarDatos);
      $("#articulo").change(cargar_stock);
      $("#articulo").change(carga_existe_codigo);
      $("#numero").change(cargar_traspaso);
      $("#bodega_origen").change(get_secuencia);
      get_secuencia();
      get_recno();
      $("#btn_add_articulo").click(insertarForm);
      $("#tbl_traspaso").on('click','#btn_remover',removerAsig);
      get_fecha();
      $("#btn_confirmar").click(confimar_trapaso);
      get_count();
      get_user();
}

function comprobar(){   
    if (document.getElementById("chec").checked){
      document.getElementById('numero').readOnly = false;
        $('#numero').val('');
        
        alert("ATENCION : PROCESO DE EDICION DE TRASPASO");
    }else{
      document.getElementById('numero').readOnly = true;
    }
}
function limpiarForm() {
    $("#tbl_traspaso tbody tr").remove();
    //$('#bodega_origen').prop('disabled', false);
    //$('#bodega_destino').prop('disabled', false);
    //$('#fec_traspaso').prop('disabled', false);
}
function cargar_traspaso(){
    limpiarForm();
      var numero = $("#numero").val();
      //alert(numero);
      //var llave = substr(cod_llave,0,6);

  
    ////correlativo = $(this).attr('correlativo');
    //$(".modal-body").html("LLave <strong>"+cod_llave+"</strong> existe, desea cargar sus datos  ?");
    //$("#modal-delete").modal({
    //    backdrop:'static'
    //});
    //$("#si-del").click(function(){
        //$("#"+numFila).remove();
            $.ajax({
                url:'reserva_stock.php',
                type: 'GET',
                dataType: 'json',
                //contentType: false,
                data:{'cargar_inputs':'cargar_inputs','numero':numero},
                //processData: false,
                //cache: false
                //beforeSend:cargando,
                success:function(jsonphp){
                    //$("#frm_licencias_medicas");
                      $.each(jsonphp,function(indice, valores){
                        //alert(indice);
                           if (indice=='NUMERO') {
                            //$("#fec_traspaso").val(valores.C0_SECUENCIA);
                            $("#bodega_origen").val(valores.C0_ORIGEN);
                            $("#bodega_destino").val(valores.C0_DESTINO);
                            $("#fec_traspaso").val(valores.C0_FTRASPASO);
                             $('#bodega_origen').prop('disabled', 'disabled');
                              $('#bodega_destino').prop('disabled', 'disabled');
                              $('#fec_traspaso').prop('disabled', 'disabled');
                           }
                           if (indice=='ASIGNACION') {
                        //  i=i+1;
                           var numFila    = 0;   
                     for(var fila in valores){
                           numFila = valores[fila]['RECNO'];                    
                        var tr = $('<tr/>').attr({'id':valores[fila]['RECNO']});
                       var articulo = valores[fila]['C0_COD'];  
                       var cambio_articulo = valores[fila]['C0_CAMARTICULO'];  
                       var numero = valores[fila]['C0_SECUENCIA'];  

                        var btn_borrar = $("<button />").text("Remover");
                        btn_borrar.attr({'type':'button','class':'btn btn-block bg-gradient-danger', 'id':'btn_remover','articulo':articulo,'numero':numero,'numFila':numFila});
                        
                        //var btnBorrar = $("<button/>").text('Borrar');
                        //btnBorrar.attr({'type':'button','class':'btn btn-danger btn-xs','id':'btn_eliminar','correlativo':valores[i]['CORRELATIVO'],'nro':valores[i]['NRO'],'fila':numFila});
                        ////$('<td />').html(i).appendTo(tr);
                        $('<td />').html(valores[fila]['C0_SECUENCIA']).append($("<input/>").attr({'type':'hidden','value':valores.C0_SECUENCIA,'name':'asg-numero','id':'asg-numero'})).appendTo(tr);
                        $('<td />').html(valores[fila]['C0_ORIGEN']).append($("<input/>").attr({'type':'hidden','value':valores.C0_ORIGEN,'name':'asg-bodega_origen','id':'asg-bodega_origen'})).appendTo(tr);
                        $('<td />').html(valores[fila]['C0_DESTINO']).append($("<input/>").attr({'type':'hidden','value':valores.C0_DESTINO,'name':'asg-bodega_destino','id':'asg-bodega_destino'})).appendTo(tr);
                        $('<td />').html(valores[fila]['C0_COD']).append($("<input/>").attr({'type':'hidden','value':valores.C0_COD,'name':'asg-articulo','id':'asg-articulo'})).appendTo(tr);
                        $('<td />').html(valores[fila]['C0_CAMARTICULO']).append($("<input/>").attr({'type':'hidden','value':valores.C0_CAMARTICULO,'name':'asg-cambio_articulo','id':'asg-cambio_articulo'})).appendTo(tr);
                        $('<td />').html(valores[fila]['C0_DESCR']).append($("<input/>").attr({'type':'hidden','value':valores.C0_DESCR,'name':'asg-descripcion_articulo','id':'asg-descripcion_articulo'})).appendTo(tr);
                        $('<td />').html(valores[fila]['C0_FTRASPASO']).append($("<input/>").attr({'type':'hidden','value':valores.C0_FTRASPASO,'name':'asg-fec_traspaso','id':'asg-fec_traspaso'})).appendTo(tr);
                        $('<td />').html(valores[fila]['C0_CANTIDAD']).append($("<input/>").attr({'type':'hidden','value':valores.C0_CANTIDAD,'name':'asg-cantidad','id':'asg-cantidad'})).appendTo(tr);
                        //$('<td />').html(btnEditar).appendTo(tr);
                        $('<td />').html(btn_borrar).appendTo(tr);
                       //$('<td />').html(btnBorrar).appendTo(tr);
                        tr.appendTo("#tbl_traspaso");
                    
                   }
                           }
        
                    });
                //$("html, body").animate({ scrollTop: 0 }, "fast");
                 //$("#descripcion_articulo").focus();
                },
                //error:problemas
            });
     //});  
}
function removerAsig() { //REMOVER ASIGNACION INGRESADA,NO DE LA BD
    //alert($(this).attr('cod'));
    articulo = $(this).attr('articulo');
    numero = $(this).attr('numero');
    numFila = $(this).attr('numFila');
  
    swal.fire({
      title: "Desea Eliminar Articulo  "+articulo,
      text: "",
      icon: "warning",      
      width: '500px',
      customClass: 'swal-wide',
      buttons: true,
      dangerMode: true,
    })
      .then((confirmacion) => {
            if (confirmacion) {
       $("#"+numFila).remove();
        $.ajax({
            url:'reserva_stock.php',
            type: 'GET',
            dataType: 'text',
            //contentType: false,
            data:{'eliminar_articulo':'eliminar_articulo','articulo':articulo,'numero':numero},
        });
              swal.fire("Eliminado !", {
                icon: "success",
              });
            } else {
              swal.fire("No Eliminado");
            }
      });
  
  
  
  
  
  
   
    //});  
}
function dateModal() {
$.datepicker.setDefaults();
    $("#fec_traspaso").datepicker({
        dateFormat:'dd/mm/yy',
        firstDay:1,
        changeMonth:true,
        changeYear:true
    });

}
function get_fecha() {
    $.ajax({
        url:'reserva_stock.php',
        type: 'GET',
        dataType: 'json',
        //contentType: false,
        data:{'fecha':'fecha'},
        //processData: false,
        //cache: false
        //beforeSend:cargando,
        success:function(data){
            $.each(data, function(indice,valores){
      
             //$("#fec_ini").val(valores.FECHA2);
             $("#fec_traspaso").val(valores.FECHA1);
            
            });
            //$("#fec_ini").focus();
        },
        //error:problemas
    });
}

function cargarDatos(){
    //limpiarForm();
      var articulo = $("#articulo").val();
    //alert(nro);
    $.ajax({
        url:'reserva_stock.php',
        type: 'GET',
        dataType: 'json',
        //contentType: false,
        data:{'cargar':'cargar','articulo':articulo},
        //processData: false,
        //cache: false
        //beforeSend:cargando,
        success:function(jsonphp){
            $("#frm_usuario");
              $.each(jsonphp,function(indice, valores){
                 if (indice=='CODIGO') {
                    $("#descripcion_articulo").val(valores.DESCR);
                 }

            });
        //$("html, body").animate({ scrollTop: 0 }, "fast");
        },
        //error:problemas
    });
}
function cargar_stock(){
    //limpiarForm();
      var articulo      = $("#articulo").val();
      var bodega_origen = $("#bodega_origen").val();
    //alert(articulo);
    //alert(bodega_origen);
    $.ajax({
        url:'reserva_stock.php',
        type: 'GET',
        dataType: 'json',
        //contentType: false,
        data:{'carga_stock':'carga_stock','articulo':articulo,'bodega_origen':bodega_origen},
        //processData: false,
        //cache: false
        //beforeSend:cargando,
        success:function(jsonphp){
            //$("#frm_usuario");
              $.each(jsonphp,function(indice, valores){
           
                    $("#stock_borigen").val(valores.STOCK);
                    $("#cantidad").attr('max',valores.STOCK);

                 

            });
        //$("html, body").animate({ scrollTop: 0 }, "fast");
        },
        //error:problemas
    });
}
function valida(){
    //limpiarForm();
      var articulo_salida      = $("#articulo").val();
      var articulo_entrada     = $("#cambio_articulo").val();
      var cantidad             = $("#cantidad").val();
    //alert(articulo);
    //alert(bodega_origen);
    $.ajax({
        url:'reserva_stock.php',
        type: 'GET',
        dataType: 'json',
        //contentType: false,
        data:{'valida_resto':'valida_resto','articulo':articulo_salida,'cambio_articulo':articulo_entrada,'cantidad':cantidad},
        //processData: false,
        //cache: false
        //beforeSend:cargando,
        success:function(jsonphp){
            //$("#frm_usuario");
              $.each(jsonphp,function(indice, valores){
           
                    $("#valida").val(valores.VALIDA);
                 

            });
        //$("html, body").animate({ scrollTop: 0 }, "fast");
        },
        //error:problemas
    });
}
function carga_existe_codigo(){
    //limpiarForm();
      var articulo      = $("#articulo").val();
      var bodega_origen       = $("#bodega_origen").val();
    //alert(articulo);
    //alert(bodega_origen);
    $.ajax({
        url:'reserva_stock.php',
        type: 'GET',
        dataType: 'json',
        //contentType: false,
        data:{'existe_codigo':'existe_codigo','articulo':articulo,'bodega_origen':bodega_origen},
        //processData: false,
        //cache: false
        //beforeSend:cargando,
        success:function(jsonphp){
            //$("#frm_usuario");
              $.each(jsonphp,function(indice, valores){
           
                    $("#in_existe").val(valores.FILAS);
                 

            });
        //$("html, body").animate({ scrollTop: 0 }, "fast");
        },
        //error:problemas
    });
}
function valida_bodega(){
    //limpiarForm();
      var b_origen = $("#bodega_origen").val();
      var b_destino = $("#bodega_destino").val();
    
    if(b_origen == b_destino){
      alert('Bodega de origen debe ser distinta a la de destino');
      $('#bodega_origen').val('');
      $('#bodega_destino').val('');      
    }

}
function limparselect() {
    //code
   
     $("#articulo").val("");
}
function get_recno() {
      
    //var bodega_origen = $("#bodega_origen").val();
    
    $.ajax({
        url:'reserva_stock.php',
        type: 'GET',
        dataType: 'json',
        //contentType: false,
        data:{'recno':'recno'},
        //processData: false,
        //cache: false
        //beforeSend:cargando,
        success:function(data){
            $.each(data, function(indice,valores){
      
             //$("#fec_ini").val(valores.FECHA2);
             $("#recno").val(valores.RECNO);
            
            });
            //$("#fec_ini").focus();
        },
        //error:problemas
    });
    
}
function get_count() {
      
    var numero = $("#numero").val();
    
    $.ajax({
        url:'reserva_stock.php',
        type: 'GET',
        dataType: 'json',
        //contentType: false,
        data:{'count':'count','numero':numero},
        //processData: false,
        //cache: false
        //beforeSend:cargando,
        success:function(data){
            $.each(data, function(indice,valores){
      
             //$("#fec_ini").val(valores.FECHA2);
             $("#filas").val(valores.FILAS);
            
            });
            //$("#fec_ini").focus();
        },
        //error:problemas
    });
    
}
function get_secuencia() {
      
     var bodega_origen = $("#bodega_origen").val();
     //alert(bodega_origen);
    $.ajax({
        url:'reserva_stock.php',
        type: 'GET',
        dataType: 'json',
        //contentType: false,
        data:{'C0_secuencia':'C0_secuencia','bodega_origen':bodega_origen},
        //processData: false,
        //cache: false
        //beforeSend:cargando,
        success:function(data){
            $.each(data, function(indice,valores){
                  //bodega_origen = str.substr(0, 2);
                 
             $("#numero").val(valores.C0_TRASPASO);
             //alert(valores.C0_TRASPASO);
             
            
            });
            $("#numero").focus();
        },
        //error:problemas
    });
}
function validaAgregadosTbl() {
     //get_recno();
    fila = $("#filas").val();
    in_filas = $("#in_existe").val();
    //alert(in_filas);
    //var trab = $("#nro_trabajador").val();
    var origen               = $("#bodega_origen option:selected").val();
    var destino              = $("#bodega_destino option:selected").val();
    var articulo             = $("#articulo").val();
    var cam_articulo         = $("#cambio_articulo").val();
    var desc                 = $("#descripcion_articulo").val();
    var fec_traspaso         = $("#fec_traspaso").val();
    //var cantidad             = $("#cantidad").val();
    var contador = 0;
       // alert(cmb_cliente);
      //alert(fila);
    for(var i=0; i<=fila; i++){
        var tr_ori = $("#asg-bodega_origen").val();
        var tr_dest = $("#asg-bodega_destino").val();
        var tr_art = $("#asg-articulo").val();
        var tr_cart = $("#asg-cambio_articulo").val();
        var tr_desc = $("#asg-descripcion_articulo").val();
        var tr_ftra = $("#asg-fec_traspaso").val();
        //var tr_cant = $("#asg-cantidad"+i).val();
        //alert(t+'-'+ti+'-'+fa+'-'+d);
        //alert(articulo);
        //alert(tr_art);
        
        if (origen==tr_ori && destino==tr_dest && articulo==tr_art && desc==tr_desc && fec_traspaso==tr_ftra && cam_articulo==tr_cart) {//&& cantidad==tr_cant
            //alert(articulo);
            //alert(tr_art);
            contador = contador+1;
            //alert("contador : "+contador);
        }
    }

    in_filas = 1;
    if(in_filas === '0'){      
      contador = contador+1;
      
      alert("Articulo no existe en bodega de Origen");
      $('#articulo').val('');
      
    }else if (articulo==cam_articulo){
            swal.fire("ERROR", "Articulo de SALIDA es igual al de ENTRADA", "error");
            $('#articulo').val('');
            $("#cambio_articulo").val('');
    }else if (contador === 0) {
        return true;
    }else{
        swal.fire("ERROR", "Articulo ya ingresado", "error");

    }
    
}
function validaVacio() {
     //get_recno();
  var contador=0;
    $("input[class~='requeridoAsig'],input[class~='requerido'],select[class~='requeridoAsig']").each(function(){
        var valor = $(this).val();
        if (valor ==="" || valor ===' ') {
            contador=contador+1;
            var nombre = $(this).attr("name");
            var lbl = nombre;
            
            //alert(lbl);
            //var texto = $("label[for~='"+lbl+"']").text();
            //var texto = $(nombre).text();
            
            alert("Rellenar campo "+lbl);
            $(this).css("border-color","orange");
        }
        
    });
    if (contador===0) {
        return true;//Todos lo campos estan rellenados.
    }
}
function validaFormAsig() { //AGREGAR ASIGNACION A TABLA
    if (validaVacio() && validaAgregadosTbl()) {      //&&  valida_articulo_bodega()
   
    
  
                
                 //alert("VALIDACION 2 "+valida_campo);
                  get_recno();
                addAsignacion();
                $('#fec_traspaso').attr("readonly", true); 
                $('#bodega_origen').attr("readonly", true); 
                $('#bodega_destino').attr("readonly", true); 
                 //sleep(10000);
                $('#articulo').val('');
                $('#cambio_articulo').val('');
                $('#descripcion_articulo').val('');
                $('#cantidad').val('');
                get_count();
                 //$("#bodega_origen").change(get_recno)             
    }    
}
function valida_articulo_bodega(){
      articulo = $("#articulo").val();
      bodega_destino = $("#bodega_destino").val();
     $.ajax({
        url:'reserva_stock.php',
        type: 'GET',
        dataType: 'json',
        //contentType: false,
        data:{'existe_articulo_bodega':'existe_articulo_bodega','articulo':articulo,'bodega_destino':bodega_destino},
        //processData: false,
        //cache: false
        //beforeSend:cargando,
        success:function(data){
            $.each(data, function(indice,valores){
            
                  
                var option = valores.FILAS;
                if(option ===0){
                  //alert(option);
                  alert("ARTICULO NO EXISTE EN BODEGA DE DESTINO");
                }else{
                  return true;
                }
                //alert(option);
                        

            });
        },
        //error:problemas
    });
    
}
function sleep(milliseconds) {
 var start = new Date().getTime();
 for (var i = 0; i < 1e7; i++) {
  if ((new Date().getTime() - start) > milliseconds) {
   break;
  }
 }
}
function confimar_trapaso() { //REMOVER ASIGNACION INGRESADA,NO DE LA BD
    //alert($(this).attr('cod'));
   
     var numero   = $("#numero").val();
    //correlativo = $(this).attr('correlativo');
   swal.fire({
      title: "Confirmar Reserva # "+numero,
      text: "Se enviara correo de confirmacion al Administrador",
      icon: "warning",      
      width: '500px',
      buttons: true,
      dangerMode: true,
    })
      .then((confirmacion) => {
            if (confirmacion) {
                  $.ajax({
                 url:'reserva_stock.php',
                 type: 'GET',
                 dataType: 'text',
                 //contentType: false,
                 data:{'confima_traspaso':'confima_traspaso','numero':numero},
                  
                 //processData: false,
                 //cache: false
                 //beforeSend:cargando,
                  beforeSend:function(){
                 $("#mensajes").html('<img src="img/cargando2.gif">Cargando...</img>');
             },
             success:function(data){
                 $("#mensajes").html("<div class='alert alert-success' role='alert'>"+data+"</div>");
                 $('#numero').val('');
                 $('#articulo').val('');
                 $('#bodega_origen').val('');
                 $('#bodega_destino').val('');
                 $('#bodega_origen').prop('disabled', false);
                 $('#bodega_destino').prop('disabled', false);
                 $('#cambio_articulo').val('');
                 $('#cambio_articulo').val('');
                 $('#descripcion_articulo').val('');
                 $('#cantidad').val('');
                 $("#tbl_traspaso tbody tr").remove();
                 //cargaPage();
             },
                 //error:problemas
             });
              swal.fire("Confirmado !", {
                icon: "success",
              });
            } else {
              swal.fire("No Confirmado");
            }
      });
       
}
function addAsignacion() {//AGREGAR ASIGNACION A TABLA
       //get_recno();
       //alert('fwefewfwe');
    //insertarForm();
    fila = $("#filas").val();
    var numFila = fila;

    var numero                 = $("#numero").val();
    var origen                 = $("#bodega_origen option:selected").text();
    var destino                = $("#bodega_destino option:selected").text();
    var articulo               = $("#articulo").val();
    var cambio_articulo        = $("#cambio_articulo").val();
    var desc                   = $("#descripcion_articulo").val();
    var ftraspaso              = $("#fec_traspaso").val();
    var cantidad               = $("#cantidad").val();
    var btn_borrar = $("<button />").text("Remover");
    btn_borrar.attr({'type':'button','class':'btn btn-block bg-gradient-danger', 'id':'btn_remover','articulo':articulo,'numero':numero,'numFila':numFila});
    
    var tr = $("<tr/>").attr('id',numFila);
    
    //$("<td/>").html(numFila).append($("<input/>").attr({'type':'hidden','value':numFila,'name':'asg-num_fila','id':'asg-num_fila'})).appendTo(tr);/*/*////////////*/*/////////////////////////tratar de pasar el numfila a php
    $("<td/>").html(numero).append($("<input/>").attr({'type':'hidden','value':$("#numero").val(),'name':'asg-numero','id':'asg-numero'})).appendTo(tr);
    $("<td/>").html(origen).append($("<input/>").attr({'type':'hidden','value':$("#bodega_origen option:selected").val(),'name':'asg-bodega_origen','id':'asg-bodega_origen'})).appendTo(tr);
    //$("<td/>").html(destino).append($("<input/>").attr({'type':'hidden','value':$("#bodega_destino option:selected").val(),'name':'asg-bodega_destino','id':'asg-bodega_destino'})).appendTo(tr);
    $("<td/>").html(articulo).append($("<input/>").attr({'type':'hidden','value':$("#articulo").val(),'name':'asg-articulo','id':'asg-articulo'})).appendTo(tr);
    //$("<td/>").html(cambio_articulo).append($("<input/>").attr({'type':'hidden','value':$("#cambio_articulo").val(),'name':'asg-cambio_articulo','id':'asg-cambio_articulo'})).appendTo(tr);
    $("<td/>").html(desc).append($("<input/>").attr({'type':'hidden','value':$("#descripcion_articulo").val(),'name':'asg-descripcion_articulo','id':'asg-descripcion_articulo'})).appendTo(tr);
    $("<td/>").html(ftraspaso).append($("<input/>").attr({'type':'hidden','value':$("#fec_traspaso").val(),'name':'asg-fec_traspaso','id':'asg-fec_traspaso'})).appendTo(tr);
    $("<td/>").html(cantidad).append($("<input/>").attr({'type':'hidden','value':$("#cantidad").val(),'name':'asg-cantidad','id':'asg-cantidad'})).appendTo(tr);
     //$("<td/>").html('').appendTo(tr);
    $("<td/>").html(btn_borrar).appendTo(tr);
    tr.appendTo("#tbl_traspaso");
    
    //insertarForm();
    //get_recno();
    $("#articulo").focus();
}


function insertarForm() { //FUNCION DE INSERCION DE DATOS
    var cantidad = $("#cantidad").val();

    // Expresión regular para verificar que no haya puntos ni comas
    var regex = /^[0-9]+$/;

    if (!regex.test(cantidad)) {
        Swal.fire('Error en Cantidad', 'La cantidad a Reservar debe ser un número entero válido', 'error');
    } else {
        var cantidadEntera = parseInt(cantidad);
        var stockOrigen = parseInt($("#stock_borigen").val());

        if (cantidadEntera > stockOrigen) {
            Swal.fire('Error en Reserva !', 'Cantidad a Reservar es Mayor al Stock.', 'error');
        } else if (cantidadEntera <= 0) {
            Swal.fire('Error en Cantidad', 'La cantidad a Reservar debe ser mayor a 0', 'error');
        } else {

    var fila   = $("#filas").val();//cantidad filas tabla asignacion
    var dataForm = $("form").serialize();
    //alert(dataForm);
    var data = dataForm+"&asg-max="+fila+"&insertar=insertar";
   // alert(fila);
    //alert(data);
    if(true){
    $.ajax({
        url:'reserva_stock.php',
        type: 'POST',
        dataType: 'text',
        //contentType: false,
        data:data,
        //processData: false,
        //cache: false
        beforeSend:function(){
            $("#mensajes").html('<img src="img/cargando2.gif">Cargando...</img>');
        },
        success:function(data){
           var error = data.substr(0,5);
            if(error === "ERROR"){
				Swal.fire('Error en Reserva ! !', 'Cantidad Transferida no es Divisible<br> Revise Cantidad y vuelva a intentarlo.', 'error');
                $("#mensajes").remove();
			}else{
                // alert("paso");
                cargarArchivo();
               validaFormAsig();
                $("#mensajes").html("<div class='alert alert-info alert-dismissible fade show' role='alert'>" + data + "<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>");
                Swal.fire('Item agregado! ', 'Archivo e información agregada correctamente!', 'success');
			}
        },
        
    });
    
}}}}


function validarArchivo() {

    var archivo = $("#doc").val();

    if (archivo === null || archivo === '') {

        //Swal.fire('y el reporte?', 'No se ha encontrado ningun archivo', 'question');
        return 0;
       
    }else{
        
        return 1;
    }
}



function cargarArchivo() {



    if(validarArchivo()){

    var input_file = document.getElementById('doc');
    var file = input_file.files[0];
    var data = new FormData();
    const numero = $('#numero').val();
    const np = $('#articulo').val();

    data.append("file_doc", file);
    $.ajax({
        url: 'reserva_stock.php?archivo=si&num=' + numero + "&np=" + np,
        type: 'POST',
        dataType: 'text',
        contentType: false,
        data:data,
        processData: false,
        //cache: false
        beforeSend:function(){
            $("#mensajes").html('<img src="img/cargando2.gif">Subiendo Archivo , Porfavor Espere...</img>');

        },
        success:function(data){
            var error = data.substr(0,5);
            //alert(error);
            $("#mensajes").html("<div class='alert alert-info alert-dismissible fade show' role='alert'>" + data + "<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>");
            if(error === 'ERROR'){
                Swal.fire('¡Archivo con problemas!', 'No se ha logrado cargar el archivo correctamente :/', 'error');
            }else{
                //datos_subidos();
                console.log(data);
                //Swal.fire('Datos Cargados!', 'Planilla Subida con Exito !', 'success');
            }    
            ////datos_subidos();
        },
    });   
}}

function get_doc() {
    articulo = $("#articulo").val();
    num = $('#numero').val();

    $.ajax({
        url:'reserva_stock.php',
        type: 'GET',
        dataType: 'text',
        data:{'get_doc':'get_doc','articulo':articulo,'num':num},
        success:function(data){
            console.log(data);
            return data;
        },
    });
    
}


function get_user(){

    var queryString = window.location.search.substring(1);
    var pares = queryString.split("&");

    for (var i = 0; i < pares.length; i++) {
        var par = pares[i].split("=");
        if (par[0] === 'user') {
            $.ajax({
                url:'reserva_stock.php',
                type: 'GET',
                dataType: 'text',
                data:{'get_user':'si','userid':par[1]},
                success:function(data){
                    var error = data.substr(0,3);
                    if(error === "NOT"){
                        swal.fire("Usuario?", "Usuario no encontrado", "warning");
                    }
                    $("#usuario").val(data);
                },
            });
        }
    }
}