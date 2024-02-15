$(document).ready(onLoad);
function onLoad() {
      //traspasos();
      $("#buscar").click(traspasos);
      $("#tbl_info_devoluciones").on('click','#btn_eliminar',anula_cid);
      //$("#btn_valida").click(valida_traspasook);
}

function anula_cid() { 
    
    numero = $(this).attr('numero');
       Swal.fire({
            icon: 'question',
            title: "Desea Anular CID # "+numero+" ?",
            text: "CID Quedara Anulado",
            width: '500px',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, Anular !',
            //showLoaderOnConfirm: true
    }).then(resultado => {
        if (resultado.value) {
      
        $.ajax({
            url:'devoluciones_info.php',
            type: 'GET',
            dataType: 'text',
            //contentType: false,
            data:{'anula_cid':'anula_cid','numero':numero},
             
            //processData: false,
            //cache: false
            //beforeSend:cargando,
             beforeSend:function(){
            $("#mensajes").html('<img src="img/cargando2.gif">Cargando...</img>');
        },
        success:function(data){
            $("#mensajes").html("<div class='alert alert-success' role='alert'>"+data+"</div>");
            $("#"+numero).remove();
            Swal.fire('Cid Anulada !', data, 'info');
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
      $("#tbl_info_devoluciones tbody tr").remove();
}
function traspasos() {
      limpiartabla();
       var canal    = $("#canal").val();
       var guia    = $("#guia").val();
       var estado    = $("#status").val();

    $.ajax({
        url:'devoluciones_info.php',
        type: 'GET',
        dataType: 'json',
        //contentType: false,
        data:{'ver':'ver','canal':canal,'guia':guia,'estado':estado},
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
                    
                    //tr.attr('class','danger');
                    
                    var bodega = valores.ORIGEN;
                    var bodega_str = bodega.substr(0,2);
                    //alert(bodega_str);
                    
                     var btn_eliminar = $("<button/>").text('Eliminar');
					btn_eliminar.attr({'type':'button','class':'btn btn-block bg-gradient-danger btn-sm','id':'btn_eliminar','numero':valores.ID});
                    $('<td />').html(i).appendTo(tr);
                    $('<td />').html(valores.ID).appendTo(tr);
                    $('<td />').html(valores.GUIA).appendTo(tr);
                    
                    $('<td />').html(valores.ORIGEN).appendTo(tr);
                    $('<td />').html(valores.CODIGOS).appendTo(tr);
                    $('<td />').html(valores.CANTIDAD).appendTo(tr);
                    $('<td />').html(valores.FECHA).appendTo(tr);					
                    if(valores.ESTADO==10){
                        $('<td />').html('PROCESANDO  <i class="fas fa-spinner"></i>' ).appendTo(tr);
                    }else if(valores.ESTADO==20){
                         $('<td />').html('CONFIRMADO <i class="far fa-check-circle"></i>').appendTo(tr);     
                    }
                   
                    $('<td />').html(valores.VER_SOLICITUD).appendTo(tr);
                    $('<td />').html(btn_eliminar).appendTo(tr);
                   

                    tr.appendTo("#tbl_info_devoluciones");
               
            });
            
            //$("#tbl_validatraspaso").dataTable();
        },
        //error:problemas
    });
    
}
function ver_traspaso() {
    
    var id_traspaso = $(this).attr('traspaso');
    //alert(id_traspaso);
    $.ajax({
        url:'devoluciones_info.php',
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