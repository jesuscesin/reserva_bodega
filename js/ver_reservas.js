$(document).ready(onLoad);
function onLoad() {
      //traspasos();
      $("#buscar").click(ver_reservas);
      //$("#btn_valida").click(valida_traspasook);
}




function limpiartabla(){
      $("#tbl_validatraspaso tbody tr").remove();
}

function ver_reservas() {
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
                    var tr = $("<tr />").attr('id',valores.CANTMAYOR);
                     if (valores.CANTMAYOR === 'X') {
                        if(valores.D_E_L_E_T_ === '*')  {
                            tr.attr('style','background-color:  #808080');  
                        }else if(valores.D_E_L_E_T_ === ''){
                            tr.attr('style','background-color:  #9B9B9B');  
                        }                
                     }else if(valores.CANTMAYOR === 'RB') {
                              tr.attr('style','background-color:  #83D2F7');                   
                     }else if(valores.CANTMAYOR === 'NC') {
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
                    $('<td />').html(valores.COMPRA).appendTo(tr);
                    if (valores.CANTMAYOR === 'X') {
                        var editarBtn = $("<button/>").addClass('btn btn-primary').text('Editar');
                        editarBtn.on('click', function () {
                            editarReserva(valores.RESERVA);
                        });
                        $('<td />').append(editarBtn).appendTo(tr);
                    }

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
