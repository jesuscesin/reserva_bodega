

var queryString = window.location.search.substring(1);
    console.log(queryString);
    
function ver_reservas() {

    var queryString = window.location.search.substring(1);
    console.log(queryString);
    limpiartabla();
     var requerimiento    = $("#requerimiento").val();
     var os   = $("#os").val();
     var reserva   = $("#reserva").val();

  $.ajax({
      url:'editar_reservas.php?'+queryString,
      type: 'GET',
      dataType: 'json',
      //contentType: false,
      data:{'ver':'ver','requerimiento':requerimiento,'os':os, 'reserva':reserva},
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
