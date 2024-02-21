$(document).ready(onLoad);
function onLoad() {
      $("#buscar").click(ver_reservas);
}




function limpiartabla(){
      $("#tbl_validatraspaso tbody tr").remove();
}

function ver_reservas() {
      limpiartabla();
       var requerimiento    = $("#requerimiento").val();
       var os   = $("#os").val();
       var reserva   = $("#reserva").val();

    $.ajax({
        url:'ver_reservas.php',
        type: 'GET',
        dataType: 'json',
        data:{'ver':'ver','requerimiento':requerimiento,'os':os, 'reserva':reserva},
  
        success:function(jsonphp){
            var i=0;
            $.each(jsonphp, function(indice,valores){
                    i=i+1;

                    var tr = $("<tr />").attr('id',valores.CANTMAYOR);
				
                    
                    $('<td />').html(i).appendTo(tr);
                    $('<td />').html(valores.OS).appendTo(tr);
                    $('<td />').html(valores.REQUERIMIENTO).appendTo(tr);
                    $('<td />').html(valores.RESERVA).appendTo(tr);
                    $('<td />').html(valores.ARTICULOS).appendTo(tr);
                    $('<td />').html(valores.CANTIDAD).appendTo(tr);
                    $('<td />').html(valores.COMPRA).appendTo(tr);
                    

                    if (valores.CANTMAYOR === 'X' && valores.COMPRA !== null && valores.COMPRA !== '0') {
                               // Agrega una clase a la celda de nueva cantidad para identificarla fácilmente
                               $('<td />').html('<input name="cant_new" id="cant_new" placeholder="Reserva" class="form-control input-sm border border-info mt-2" type="text" style="text-transform:uppercase;"> ').appendTo(tr).addClass('nuevaCantidadCell');
                               //<input name="reserva" id="reserva" placeholder="Reserva" class="form-control input-sm border border-info mt-2" type="text" style="text-transform:uppercase;"> 
                               
                        var editarBtn = $("<button/>", {
                            type: 'button', // Agregar esta línea para especificar el tipo de botón
                            class: 'btn btn-primary',
                            text: 'Editar'
                        });
                        editarBtn.on('click', function () {
                            abrirPopupEditar(valores.R_E_C_N_O_, valores.RESERVA, valores.OS, valores.REQUERIMIENTO, valores.ARTICULOS, valores.CANTIDAD, valores.COMPRA);
                        });
                        
                        $('<td />').append(editarBtn).appendTo(tr); 
                    }

                    tr.appendTo("#tbl_validatraspaso");
               
            });
            

        },
        error:function(jsonphp){
           limpiartabla();    
          Swal.fire('No hay datos para mostrar !', jsonphp, 'info');
           //$("#mensajes").html("<div class='alert alert-danger' role='alert'>"+textphp+"</div>");
           return false;        
        }
    });
    
}

function abrirPopupEditar(recno,reserva,os,requerimiento,articulos,cantidad,compra) {

    // Obtener el valor de cant_new
    var cant_new = $(".nuevaCantidadCell input[name='cant_new']").val();
    // Puedes ajustar las dimensiones y opciones del popup según tus necesidades
    var popup = window.open('aplicar_cambios.php?recno=' + recno + '&reserva=' + reserva + '&os=' + os + '&requerimiento=' + requerimiento + '&articulos=' + articulos + '&cantidad=' + cantidad + '&compra=' + compra + '&cant_new=' + cant_new, 'Editar Reserva', 'width=600,height=400');
    
    
}
