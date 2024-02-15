$(document).ready(onLoad);
function onLoad() {
      //traspasos();
      $("#buscar").click(traspasos);
      $("#tbl_validatraspaso").on('click','#btn_editar',ver_traspaso);
      $("#tbl_validatraspaso").on('click','#btn_anular',anula_traspaso);
      $("#tbl_validatraspaso").on('click','#btn_valida',valida_traspasook);
      //$("#btn_valida").click(valida_traspasook);
}
function ventana2(){
    
   traspaso_id = $(this).attr('traspaso');
    window.open("traspaso_edit.php?pedido="+traspaso_id+"","Nueva ventana",'width=1200,height=900'); 
}

function valida_traspasook() { 
    //alert($(this).attr('cod'));
    
 
    id_traspaso = $(this).attr('id_traspaso');
    //ntraspaso = $(this).attr('id');
    //alert(ntraspaso);
       Swal.fire({
            icon: 'question',
            title: "Desea Validar Transferencia <br> # "+id_traspaso+" ?",
            text: "Estos datos seran enviandos de manera automatica a TOTVS",
            width: '500px',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, Validar !',
            //showLoaderOnConfirm: true
    }).then(resultado => {
        if (resultado.value) {
      $.ajax({
            url:'traspaso_valida_2.php',
            type: 'GET',
            dataType: 'text',
            //contentType: false,
            data:{'valida_ok':'valida_ok','secuencia':id_traspaso},
             
            //processData: false,
            //cache: false,
            //beforeSend:cargando,
             beforeSend:function(){
            $("#mensajes").html('<img src="img/cargando2.gif">Cargando...</img>');
						Swal.fire({
						  title: 'Cargando Transferencia !',
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
                $("#"+id_traspaso).remove();
            Swal.fire('Procesado !', data, 'info');
            },
                //error:problemas
        });
        } else {
            // Dijeron que no
           Swal.fire('No Procesado ! ', 'Cancelado', 'info');
        }
    });

   
    $("html, body").animate({ scrollTop: 0 }, "fast");
            //$(data).val(''); //limpia registros que quedan en el js
}

function anula_traspaso() { 
    
    secuencia = $(this).attr('id_traspaso');
       Swal.fire({
            icon: 'question',
            title: "Desea Anular Traspaso # "+secuencia+" ?",
            text: "Porceso de Traspaso Quedara Anulado",
            width: '500px',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, Anular !',
            //showLoaderOnConfirm: true
    }).then(resultado => {
        if (resultado.value) {
      
        $.ajax({
            url:'traspaso_valida_2.php',
            type: 'GET',
            dataType: 'text',
            //contentType: false,
            data:{'anula_traspaso':'anula_traspaso','secuencia':secuencia},
             
            //processData: false,
            //cache: false
            //beforeSend:cargando,
             beforeSend:function(){
            $("#mensajes").html('<img src="img/cargando2.gif">Cargando...</img>');
        },
        success:function(data){
            $("#mensajes").html("<div class='alert alert-success' role='alert'>"+data+"</div>");
            $("#"+secuencia).remove();
            Swal.fire('Transferencia Anulada !', data, 'info');
        },
            //error:problemas
        });
        } else {
            // Dijeron que no
           Swal.fire('No Anulado ! ', 'Proceso Cancelado', 'info');
        }
    });
    //});  
}
function limpiartabla(){
      $("#tbl_validatraspaso tbody tr").remove();
}
function traspasos() {
      limpiartabla();
       var bodega_origen    = $("#bodega").val();
       var bodega_destino   = $("#bodega_destino").val();
       var status 			= $("#status").val();
    $.ajax({
        url:'traspaso_valida_2.php',
        type: 'GET',
        dataType: 'json',
        //contentType: false,
        data:{'ver':'ver','bodega_origen':bodega_origen,'bodega_destino':bodega_destino,'status':status},
        //processData: false,
        //cache: false
        //beforeSend:cargando,
        success:function(jsonphp){
            //if (data.success) {
            var i=0;
            $.each(jsonphp, function(indice,valores){
                    i=i+1;
                    //$("#id_traspaso").empty().text(planilla);
                    var tr = $("<tr />").attr('id',valores.ID);
                     if (valores.ESTADO === '20') {
                                tr.attr('style','background-color:  #61C666');
                     }else if(valores.ESTADO==40 && valores.ESTADO_SD3=='S') {
                              tr.attr('style','background-color:  #F2A2A3');                   
                     }else if(valores.ESTADO === '40') {
                              tr.attr('style','background-color:  #83D2F7');                   
                     }else if(valores.ESTADO === '10') {
                                tr.attr('class','bg-warning color-palette');                        
                     }else if(valores.ESTADO === '30') {
                               tr.attr('style','background-color:  #F4848D');                 
                     }
                    //tr.attr('class','danger');
                    
                    var bodega = valores.ORIGEN;
                    var bodega_str = bodega.substr(0,2);
                    //alert(bodega_str);
                    var btneditar_traspaso = $("<button/>").text("Editar Traspaso");
                    btneditar_traspaso.attr({'type':'button','class':'btn btn-block bg-gradient-info btn-sm','id':'btn_editar','traspaso':valores.ID});  
                    var btn_anula = $("<button/>").text("Anular Traspaso");
                    btn_anula.attr({'type':'button','class':'btn btn-block bg-gradient-danger btn-sm','id':'btn_anular','id_traspaso':valores.ID});
                    var btn_anula_disable = $("<button/>").text('Anular Traspaso');
                    btn_anula_disable.attr({'type':'button','class':'btn btn-block bg-gradient-danger btn-sm'});
                    var btn_ok = $("<button/>").text('Validar Traspaso');
                    btn_ok.attr({'type':'button','class':'btn btn-block bg-gradient-success btn-sm','id':'btn_valida','id_traspaso':valores.ID});
                    var btn_disable = $("<button/>").text('Validar Traspaso');
                    btn_disable.attr({'type':'button','class':'btn btn-block bg-gradient-success btn-sm'});
                    var btn_disable_solicitud = $("<button/>").text('Ver Solicitud');
                    btn_disable_solicitud.attr({'type':'button','class':'btn btn-block bg-gradient-info btn-sm'});   

					// var ic = $("<i/>").attr({'class':'nav-icon fab fa-dochub'});					
                    
                    $('<td />').html(i).appendTo(tr);
                    $('<td />').html(valores.ID).appendTo(tr);
                    $('<td />').html(valores.ORIGEN).appendTo(tr);
                    $('<td />').html(valores.DESTINO).appendTo(tr);
                    $('<td />').html(valores.ARTICULOS).appendTo(tr);
                    $('<td />').html(valores.CANTIDAD).appendTo(tr);
                    $('<td />').html(valores.CANTIDAD_ENTRADA).appendTo(tr);
                    $('<td />').html(valores.FTRASPASO).appendTo(tr);
                    //$('<td />').html(valores.FEMISION).appendTo(tr);
                    if(valores.ESTADO==10){
                        $('<td />').html('PROCESANDO  <i class="fas fa-spinner"></i>' ).appendTo(tr);
                    }else if(valores.ESTADO==20){
                         $('<td />').html("CONFIRMADO <img src='img/icon/check.png'></img>").appendTo(tr);     
                    }else if(valores.ESTADO==30){
                        $('<td />').html("ANULADO <img src='img/icon/close.png'></img>").appendTo(tr);
                    }else if(valores.ESTADO==40 && valores.ESTADO_SD3=='S'){
                        $('<td />').html('REVERTIDO <i class="fas fa-compress-alt"></i> ').appendTo(tr);
                    }else if(valores.ESTADO==40){
                        $('<td />').html('VALIDADO <img src="img/icon/check.png"></img><img src="img/icon/check.png"></img> '+valores.FTRASPASO).appendTo(tr);
                    }else{
                        $('<td />').html(' ').appendTo(tr);
                    }
                    //$("<a>").attr({'name': 'doc','id': 'doc','href': 'docs/' + valores.DOC,'target': '_blank'}).text(valores.DOC).appendTo($("<td>").appendTo(tr));
                    if(valores.DOC == null){
                        $("<a>").attr({'title':'Sin Archivo cargado...','class':'btn btn-block'}).html('<i class="fa-solid fa-file-circle-xmark"></i>').appendTo($("<td>").appendTo(tr));
                    }else{
                        $("<a>").attr({'href': 'docs/' + valores.DOC, 'target': '_blank','title':valores.DOC,'class':'btn btn-block'}).html('<i class="fa-solid fa-file-lines"></i>').appendTo($("<td>").appendTo(tr));
                    }
                    //$('<td />').html(valores.VER_SOLICITUD).appendTo(tr);
                    $('<td />').html((valores.ESTADO != '10')?valores.VER_SOLICITUD:(btn_disable_solicitud).attr({'disabled':true})).appendTo(tr);
                    $('<td />').html((valores.ESTADO != '40' &&  valores.ESTADO != '30')?btn_anula:(btn_anula_disable).attr({'disabled':true})).appendTo(tr);
                    //$('<td />').html(btn_disable).appendTo(tr);
                    $('<td />').html((valores.ESTADO === '20')?btn_ok:(btn_disable).attr({'disabled':true})).appendTo(tr);

                    tr.appendTo("#tbl_validatraspaso");
               
            });
            
            //$("#tbl_validatraspaso").dataTable();
        },
        error:function(jsonphp){
           limpiartabla();    
          Swal.fire('No hay datos para mostrar !', jsonphp, 'info');
           //$("#mensajes").html("<div class='alert alert-danger' role='alert'>"+textphp+"</div>");
           return false;        
        }
    });
    
}
function ver_traspaso() {
    
    var id_traspaso = $(this).attr('traspaso');
    //alert(id_traspaso);
    $.ajax({
        url:'traspaso_valida_2.php',
        type: 'GET',
        dataType: 'json',
        //contentType: false,
        data:{'ver_traspaso':'ver_traspaso','id_traspaso':id_traspaso},
        //processData: false,
        //cache: false
        //beforeSend:cargando,
        success:function(jsonphp){
           $("#secuencia_traspaso").empty().text(id_traspaso);
            //$("#btn_guardar_local").attr('planilla',planilla);
            $("#tbl_valida_edit tbody tr").remove();
            var i=0;
                //alert(i);
            $.each(jsonphp, function(indice, valores){
                i+=1;
                var tr = $("<tr/>").attr('id',valores.RECNO);
            
                //var checkbox = $("<input/>");
                //checkbox.attr({'type':'checkbox','class' :'form-check-input' ,'name':valores.LOCAL,'value':1,'checked':valores.CHECKED});
                 var btn_eliminar = $("<button/>").text('Eliminar');
                  btn_eliminar.attr({'type':'button','class':'btn btn-danger btn-xs','id':'btn_eliminar','articulo':valores.ARTICULOS,'recno':valores.RECNO});
                $("<td/>").html(i).appendTo(tr);
                $("<td/>").html(valores.ID);
                $("<td/>").html(valores.RECNO);
                $("<td/>").html(valores.ARTICULOS).appendTo(tr);
                $("<td/>").html(valores.DESCR).appendTo(tr);
                $("<td/>").html(valores.CANTIDAD).appendTo(tr);
                $("<td/>").html(btn_eliminar).appendTo(tr);
                tr.appendTo("#tbl_valida_edit");
                //alert(valores.LOCAL);
                
            });
             //$("html, body").animate({ scrollTop: 680 }, "fast");
        },
        // MANEJO DE FILTRO DE ERRORES - PRV
        // error:function(textphp){
        //   limpiarForm();    
        //  alert("SIN LOCALES PENDIENTES");
        //   //$("#mensajes").html("<div class='alert alert-danger' role='alert'>"+textphp+"</div>");
        //   return false;        
        //}
    });
}