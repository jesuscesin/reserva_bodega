$(document).ready(onLoad);
function onLoad() {
      
      dateModal();   
      get_recno();
      get_fecha();
      get_count();
      $("#bodega_origen").change(get_secuencia);
      $("#tbl_traspaso").on('click','#btn_remover',removerAsig);
      $("#btn_confirmar").click(confimar_trapaso);

}
function handle(e){

      barra = $("#barra").val();
      fila = $("#filas").val();
        if(e.keyCode === 13){
            e.preventDefault(); // Ensure it is only this code that rusn
            //alert("");
             $.ajax({
                url:'traspaso_scbodegas.php',
                type: 'GET',
                dataType: 'json',
                //contentType: false,
                data:{'cargar':'cargar','barra':barra},
                //processData: false,
                //cache: false
                //beforeSend:cargando,
                success:function(data){
                    //$("#frm_licencias_medicas");
                         //alert(data);
                         var i=0;
                      $.each(data,function(indice, valores){
                            i+=1;
                          //if(validaAgregadosTbl()){
                              
                              //fila = $("#filas").val();
                              var numFila = fila;
                              
                              var numero              = $("#numero").val();
                              var origen              = $("#bodega_origen").val();
                              var destino             = $("#bodega_destino").val();
                              var articulo            = valores.COD;
                              var cambio_articulo     = valores.COD;
                              var desc                = valores.DESCR;                             
                              var cantidad            = $("#cantidad").val();
                             var ftraspaso            = $("#fec_traspaso").val();
                              //Swal.fire(origen);
                              
                              var btn_borrar = $("<button />").text("Remover");
                              btn_borrar.attr({'type':'button','class':'btn btn-block btn-outline-danger btn-sm', 'id':'btn_remover','articulo':articulo,'numero':numero,'numFila':numFila});
                              
                              var tr = $("<tr/>").attr('id',numFila);
                              
                              //$("<td/>").html(numFila).append($("<input/>").attr({'type':'hidden','value':numFila,'name':'asg-num_fila','id':'asg-num_fila'})).appendTo(tr);/*/*////////////*/*/////////////////////////tratar de pasar el numfila a php
                              $("<td/>").html(numFila).append($("<input/>").attr({'type':'hidden'})).appendTo(tr);
                              $("<td/>").html(numero).append($("<input/>").attr({'type':'hidden','value':numero,'name':'asg-numero','id':'asg-numero'})).appendTo(tr);
                              $("<td/>").html(origen).append($("<input/>").attr({'type':'hidden','value':origen,'name':'asg-bodega_origen','id':'asg-bodega_origen'})).appendTo(tr);
                              $("<td/>").html(destino).append($("<input/>").attr({'type':'hidden','value':destino,'name':'asg-bodega_destino','id':'asg-bodega_destino'})).appendTo(tr);
                              $("<td/>").html(articulo).append($("<input/>").attr({'type':'hidden','value':articulo,'name':'asg-articulo','id':'asg-articulo'})).appendTo(tr);
                              $("<td/>").html(cambio_articulo).append($("<input/>").attr({'type':'hidden','value':cambio_articulo,'name':'asg-cambio_articulo','id':'asg-cambio_articulo'})).appendTo(tr);
                              $("<td/>").html(desc).append($("<input/>").attr({'type':'hidden','value':desc,'name':'asg-descripcion_articulo','id':'asg-descripcion_articulo'})).appendTo(tr);
                              $("<td/>").html(ftraspaso).append($("<input/>").attr({'type':'hidden','value':ftraspaso,'name':'asg-fec_traspaso','id':'asg-fec_traspaso'})).appendTo(tr);
                              $("<td/>").html(cantidad).append($("<input/>").attr({'type':'hidden','value':cantidad,'name':'asg-cantidad','id':'asg-cantidad'})).appendTo(tr);
                               //$("<td/>").html('').appendTo(tr);
                              $("<td/>").html(btn_borrar).appendTo(tr);
                              tr.appendTo("#tbl_traspaso");
                              insertarForm();
                              
                              
                              setTimeout(() => { get_count(); }, 200);                        
                              get_recno();
                              $("#barra").focus();
                              $("#barra").val('');
                              $("#cantidad").val(1);
                              
                            });
                      
                $("html, body").animate({ scrollTop: 1000 }, "fast");
                 //$("#descripcion_articulo").focus();
                },
                //error:problemas
            });
        }
        //return false;
}
function removerAsig() { //REMOVER ASIGNACION INGRESADA,NO DE LA BD
    //alert($(this).attr('cod'));

    articulo = $(this).attr('articulo');
    numero = $(this).attr('numero');
    numFila = $(this).attr('numFila');
  
     Swal.fire({
            icon: 'question',
            title: "Desea Eliminar Numero de Fila : <br> "+articulo+" y Fila "+numFila+"  ?",
            // text: "Estos datos seran enviandos de manera automatica a TOTVS",
            width: '500px',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, Confirmar !',
            showLoaderOnConfirm: true
    }).then(resultado => {
        if (resultado.value) {
            $("#"+numFila).remove();
                  $.ajax({
                      url:'traspaso_scbodegas.php',
                      type: 'GET',
                      dataType: 'text',
                      //contentType: false,
                      data:{'eliminar_articulo':'eliminar_articulo','articulo':articulo,'numero':numero,'numFila':numFila},
                  });
          Swal.fire('Eliminado ! ', '', 'success');
            } else {
              Swal.fire('No Eliminado ! ', 'Cancelado', 'info');
            }
      });
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
        url:'traspaso_scbodegas.php',
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

function get_recno() {
      
    //var bodega_origen = $("#bodega_origen").val();
    
    $.ajax({
        url:'traspaso_scbodegas.php',
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
        url:'traspaso_scbodegas.php',
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
        url:'traspaso_scbodegas.php',
        type: 'GET',
        dataType: 'json',
        //contentType: false,
        data:{'bo_secuencia':'bo_secuencia','bodega_origen':bodega_origen},
        //processData: false,
        //cache: false
        //beforeSend:cargando,
        success:function(data){
            $.each(data, function(indice,valores){
                  //bodega_origen = str.substr(0, 2);
                 
             $("#numero").val(valores.BO_TRASPASO);
             //alert(valores.BO_TRASPASO);
             
            
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
   if(in_filas === '0'){      
      contador = contador+1;
      
      alert("Articulo no existe en bodega de Origen");
      $('#articulo').val('');
      
    }else if (contador === 0) {
        return true;
    }else{
        alert("Articulo ya ingresado !");

    }
    
}
function confimar_trapaso() { //REMOVER ASIGNACION INGRESADA,NO DE LA BD
    //alert($(this).attr('cod'));
   
     var numero   = $("#numero").val();
    //correlativo = $(this).attr('correlativo');
       Swal.fire({
            icon: 'question',
            title: "Desea confirmar y Validar Traspaso : <br> #"+numero+" ?",
             text: "Estos datos seran enviandos de manera automatica a TOTVS",
            width: '500px',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, Confirmar !',
            showLoaderOnConfirm: true
    }).then(resultado => {
        if (resultado.value) {
                  $.ajax({
                 url:'traspaso_scbodegas.php',
                 type: 'GET',
                 dataType: 'text',
                 //contentType: false,
                 data:{'confima_traspaso':'confima_traspaso','numero':numero},
                  
                 //processData: false,
                 //cache: false
                 //beforeSend:cargando,
                  beforeSend:function(){
                 $("#mensajes").html('<img src="img/cargando2.gif">Cargando...</img>');
                 Swal.fire({
						  title: 'Cargando Traspaso !',
						  html: 'Por favor espere...',
						  timerProgressBar: true,
						  didOpen: () => {
							Swal.showLoading();
							//const b = Swal.getHtmlContainer().querySelector('b')
							//timerInterval = setInterval(() => {
							//  b.textContent = Swal.getTimerLeft()
							//}, 100)
						  },
						});
             },
             success:function(data){
                 $("#mensajes").html("<div class='alert alert-success' role='alert'>"+data+"</div>");
                 Swal.fire('Traspaso Confirmado y Validado con Exito! !', ' ', 'success');
                 $('#numero').val('');
                 $('#bodega_origen').val('');
                 $('#bodega_destino').val('');
                 $('#bodega_origen').prop('disabled', false);
                 $('#bodega_destino').prop('disabled', false);
                 //$('#cantidad').val('');
                 $("#tbl_traspaso tbody tr").remove();
                 //cargaPage();
             },
                 //error:problemas
             });
            } else {
              Swal.fire('No Confirmado ! ', 'Cancelado', 'info');
            }
      });
       
}

function insertarForm() { //FUNCION DE INSERCION DE DATOS
    var fila   = $("#filas").val();//cantidad filas tabla asignacion
    var dataForm = $("form").serialize();
    //alert(dataForm);
    var data = dataForm+"&asg-max="+fila+"&insertar=insertar";
   //alert(fila);
    //alert(data);
    $.ajax({
        url:'traspaso_scbodegas.php',
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
            $("#mensajes").html("<div  role='alert'>"+data+"</div>");
        },
        error:function(data,estado,errorr){
            $("#mensajes").html("<div class='alert alert-danger' role='alert'>"+data+"_"+estado+"_"+errorr+"</div>");
        }
    });
    
}
