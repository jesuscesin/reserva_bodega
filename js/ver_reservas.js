$(document).ready(onLoad);
function onLoad() {
      //traspasos();
      $("#buscar").click(traspasos);
      //$("#btn_valida").click(valida_traspasook);
}




function limpiartabla(){
      $("#tbl_validatraspaso tbody tr").remove();
}
function traspasos() {
      limpiartabla();
       var requerimiento    = $("#requerimiento").val();
       var os   = $("#os").val();
       var reserva   = $("#reserva").val();
       var status 			= $("#status").val();
    $.ajax({
        url:'ver_reservas.php',
        type: 'GET',
        dataType: 'json',
        //contentType: false,
        data:{'ver':'ver','requerimiento':requerimiento,'os':os, 'reserva':reserva, 'status':status},
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
                     if (valores.ESTADO === 'ID') {
                        if(valores.D_E_L_E_T_ === '*')  {
                            tr.attr('style','background-color:  #808080');  
                        }else if(valores.D_E_L_E_T_ === ''){
                            tr.attr('style','background-color:  #9B9B9B');  
                        }                
                     }else if(valores.ESTADO === 'RB') {
                              tr.attr('style','background-color:  #83D2F7');                   
                     }else if(valores.ESTADO === 'NC') {
                                tr.attr('class','bg-warning color-palette');                        
                     }
                    //tr.attr('class','danger');
                    

                    
					// var ic = $("<i/>").attr({'class':'nav-icon fab fa-dochub'});					
                    
                    $('<td />').html(i).appendTo(tr);
                    $('<td />').html(valores.OS).appendTo(tr);
                    $('<td />').html(valores.REQUERIMIENTO).appendTo(tr);
                    $('<td />').html(valores.RESERVA).appendTo(tr);
                    $('<td />').html(valores.ARTICULOS).appendTo(tr);
                    $('<td />').html(valores.CANTIDAD).appendTo(tr);

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
        url:'ver_reservas.php',
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