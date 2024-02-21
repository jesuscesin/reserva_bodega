$(document).ready(onLoad);
function onLoad() {
      $("#buscar").click(ver_reservas);
      $("#editar").click(editar_reservas);
}



function ver_reservas() {
     
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
                    $('<td />').html(valores.R_E_C_N_O_).prop({'hidden':true}).appendTo(tr);
                    

                    if (valores.CANTMAYOR === 'X' && valores.COMPRA !== null && valores.COMPRA !== '0') {
                               // Agrega una clase a la celda de nueva cantidad para identificarla fácilmente
                               $('<td />').html('<input name="cant_new" id="cant_new" placeholder="Reserva" dividir="si" class="form-control input-sm border border-info mt-2" type="text" style="text-transform:uppercase;"> ').appendTo(tr).addClass('nuevaCantidadCell');

                    }

                    tr.appendTo("#tbl_validatraspaso");
               
            });
            

        },
        error:function(jsonphp){
              
          Swal.fire('No hay datos para mostrar !', jsonphp, 'info');
           //$("#mensajes").html("<div class='alert alert-danger' role='alert'>"+textphp+"</div>");
           return false;        
        }
    });
    
}

function editar_reservas() {
    // Array para almacenar los datos de las filas seleccionadas
    var filasSeleccionadas = [];

    // Recorrer cada fila de la tabla
    $('#tbl_validatraspaso tbody tr').each(function(index, row) {
        // Verificar si la fila tiene la clase 'nuevaCantidadCell' y el input tiene un valor
        if ($(row).find('input[name="cant_new"]').attr('dividir') === 'si' && $(row).find('input[name="cant_new"]').val() !== '') {
            
           
            // Obtener los valores de la fila
            var os = $(row).find('td:nth-child(2)').text();
            var requerimiento = $(row).find('td:nth-child(3)').text();
            var reserva = $(row).find('td:nth-child(4)').text();
            var articulos = $(row).find('td:nth-child(5)').text();
            var cantidad = $(row).find('td:nth-child(6)').text();
            var compra = $(row).find('td:nth-child(7)').text();
            var recno = $(row).find('td:nth-child(8)').text();
            var nuevaCantidad = $(row).find('input[name="cant_new"]').val();

            // Agregar los datos al array
            filasSeleccionadas.push({
                'OS': os,
                'REQUERIMIENTO': requerimiento,
                'RESERVA': reserva,
                'ARTICULOS': articulos,
                'CANTIDAD': cantidad,
                'COMPRA': compra,
                'R_E_C_N_O_': recno,
                'NUEVA_CANTIDAD': nuevaCantidad
            });
        }
    });

    // Puedes imprimir el array en la consola para verificar los datos
    console.log(filasSeleccionadas);

    $.each(filasSeleccionadas, function(index, fila) {
        // Acceder a los datos de cada fila
        var reserva = fila.RESERVA;
        var articulos = fila.ARTICULOS;
        var cantidad = fila.CANTIDAD;
        var recno = fila.R_E_C_N_O_;
        var nuevaCantidad = fila.NUEVA_CANTIDAD;


        $.ajax({
            url:'ver_reservas.php',
            type: 'GET',
            dataType: 'json',
            data:{'editar':'editar',
            'reserva':reserva,
            'articulos':articulos, 
            'cantidad':cantidad,
            'recno':recno,
            'nuevaCantidad':nuevaCantidad
            },
      
            success:function(jsonphp){
            }
    })
    });

        // Redirección a la página actual después de editar las reservas
        window.location.reload();
 
    // Abre el popup con los datos recopilados
    //abrirPopupEditar(filasSeleccionadas);
}



/* function abrirPopupEditar(recno,reserva,os,requerimiento,articulos,cantidad,compra) {

    // Obtener el valor de cant_new
    var cant_new = $(".nuevaCantidadCell input[name='cant_new']").val();
    // Puedes ajustar las dimensiones y opciones del popup según tus necesidades
    var popup = window.open('aplicar_cambios.php?recno=' + recno + '&reserva=' + reserva + '&os=' + os + '&requerimiento=' + requerimiento + '&articulos=' + articulos + '&cantidad=' + cantidad + '&compra=' + compra + '&cant_new=' + cant_new, 'Editar Reserva', 'width=600,height=400');
    
    
}
 */