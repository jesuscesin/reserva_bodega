$(document).ready(onLoad);

function onLoad() {
   /*
   $("#buscar").click(proceso_ola);
   $("#tbl_ola").on('click','#btn_valida',valida_traspasook);
   */
   $("#btnGet").click(muestra_planilla);
   $("#tbl_select_oc").on('click', '#btn_procesar', procesa_c5);
   $("#tbl_select_oc").on('click', '#btn_borrar', revertir_planilla);
   $("#actualiza_precios").click(actualiza_precios);   
   $("#leyenda").click(leyenda);
}
function leyenda(){
    
     var sweetAlertContent =
            '<table border="1" cellpadding="5" style="width: 400px;">' +
            '<tr>' +
            '<th>Leyenda</th>' +
            '<th>Descripcion</th>' +
            '</tr>' +
             '<tr>' +
            '<td><p class="text-center" ><img src="img/icon/close.png"></img></p></td>' +
            '<td>Error de Precios</td>' +
            '</tr>' + // Add the complete table content here
             '<tr>' +
            '<td><p class="text-center" ><img src="img/icon/clock.png"></img></p></td>' +
            '<td>Pedido Pendiente de Digitacion</td>' +
            '</tr>' + // Add the complete table content here
            '<tr>' +
            '<td><p class="text-center" ><img src="img/icon/check.png"></img><img src="img/icon/check.png"></img></p></td>' +
            '<td>Pedido Digitado en TOTVS</td>' +
            '</tr>' + // Add the complete table content here
            '<tr>' +
            '<td><p class="text-center" ><img src="img/icon/check.png"></img><img src="img/icon/check.png"></img><img src="img/icon/check.png"></img></p></td>' +
            '<td>Pedido Facturado</td>' +
            '</tr>' + // Add the complete table content here
            '</table>';
    
    Swal.fire({
        icon: 'question',
        title: 'Leyenda',
        html: sweetAlertContent
        
});
}
function limpiar_tabla() {

   $("#tbl_select_oc tbody tr").remove();

}

function muestra_planilla() { //FUNCION DE INSERCION DE DATOS
   limpiar_tabla();

   var estado = $("#estado").val();

   $.ajax({
      url: 'proceso_dicotex.php',
      type: 'GET',
      dataType: 'json',
      //contentType: false,
      data: {
         'mostrarPlanillas': 'mostrarPlanillas',
         'estado': estado
      },
      //processData: false,
      //cache: false
      success: function (jsonphp) {

         var i = 0;
         $.each(jsonphp, function (indice, valores) {
            i = i + 1;
            //$("#id_traspaso").empty().text(planilla);
            var tr = $("<tr />");
            var status = '';

            if (valores.ESTADO == 1) {
               status = 'Pendiente';
            } else if (valores.ESTADO == 20) {
               status = 'Disponible para Reprocesar';
            } else if (valores.ESTADO == 30) {
               status = 'Procesada';
            } else {
               status = 'SIN ESTADO';
            }

            var fechaProceso = valores.FPROCESO +' ' + valores.HPROCESO;
            var ptlID = '0';

            var btn_procesar = $("<button/>").html("<i class='far fa-check-circle'></i>"); //Digitar en Totvs
            btn_procesar.attr({
               'title': 'Digita Planilla a TOTVS',
               'type': 'button',
               'class': 'btn btn-block bg-gradient-success',
               'id': 'btn_procesar',
               'indice': i,
               'planilla': valores.OCD_NOMPLA
            });

            var btn_borrar = $("<button/>").html("<i class='fa fa-undo' aria-hidden='true'></i>"); //"Borrar Planilla"
            btn_borrar.attr({
               'title': 'Revertir Digitacion',
               'type': 'button',
               'class': 'btn btn-block bg-gradient-primary',
               'id': 'btn_borrar',
               'indice': i,
               'planilla': valores.OCD_NOMPLA
            });


            $('<td />').html(i).appendTo(tr);
            $('<td />').html(valores.OCD_NOMPLA_DESCARGA).appendTo(tr); //PLANILLA
            $('<td />').html(valores.OCD_NOMPLA).appendTo(tr); //PLANILLA
            $('<td />').html(valores.FPLANILLA).appendTo(tr); //FECHA PLANILLA
            $('<td />').html(fechaProceso).appendTo(tr); //FECHA PROCESO
            $('<td />').html(valores.LOCALES).appendTo(tr); //LOCAL
            $('<td />').html(valores.PEDIDOS).appendTo(tr); //PEDIDOS
            $('<td />').html(valores.DOCENAS).appendTo(tr); //DOCENAS

            if (valores.ESTADO == 1) {
               $('<td />').html("<p class='text-center' ><img src='img/icon/clock.png'></img></p>").attr({
                  'title': 'Pendiente'
               }).appendTo(tr); //ESTADO    
            }else if (valores.ESTADO == 30){
               $('<td />').html("<p class='text-center' ><img src='img/icon/check.png'></img> <img src='img/icon/check.png'></img></p>").attr({
                  'title': 'Digitada en TOTVS'
               }).appendTo(tr);
            }else if (valores.ESTADO == 40){
               $('<td />').html("<p class='text-center' ><img src='img/icon/check.png'></img> <img src='img/icon/check.png'></img> <img src='img/icon/check.png'></img></p>").attr({
                  'title': 'FACTURADA'
               }).appendTo(tr);
            }

            if (valores.PREST != 1) {
               tr.attr('style', 'background-color:  #f9a5a5; border:1px solid;');
               $('<td />').html('<a href="archivos_subidos/proceso_dicotex_precios.php?planilla=' + valores.OCD_NOMPLA + '"> <p class="text-center" ><img src="img/icon/close.png"></img></p></a>').attr({
                  'title': 'Error de Precios'
               }).appendTo(tr);
            } else {
               $('<td />').html('<a href="archivos_subidos/proceso_dicotex_precios.php?planilla=' + valores.OCD_NOMPLA + '"> <p class="text-center" ><img src="img/icon/check.png"></img></p></a>').attr({
                  'title': 'Precios Correctos'
               }).appendTo(tr);
            }

            //$('<td />').html(ptlID).appendTo(tr); //PTL_ID

            $('<td />').html((valores.PREST == 1 && valores.ESTADO == 1 || valores.ESTADO == 20) ? btn_procesar : (btn_procesar).attr({'disabled':true})).appendTo(tr);
            //$('<td />').html(valores.ARCHIVO_PRECIOS).appendTo(tr); //Accion 1
            //$('<td />').html(btn_precios).appendTo(tr); //Accion 1
            //$('<td />').html(btnA_Procesar).appendTo(tr); //Accion 2
             $('<td />').html((valores.ESTADO != 40) ? btn_borrar : (btn_borrar).attr({'disabled':true})).appendTo(tr);


            tr.appendTo("#tbl_select_oc");

         });

      },
      error: function (jsonphp) {
         limpiar_tabla();
         Swal.fire('ERROR!', 'No se encuentran registros', 'warning');
      }
   });

}

function procesa_c5() {

   planilla = $(this).attr('planilla');

   Swal.fire({
      icon: 'question',
      title: "Desea procesar la Planilla " + planilla + "?",
      text: "Estos datos seran enviados de manera automática a TOTVS",
      width: '500px',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Si, Procesar!',
      //showLoaderOnConfirm: true
   }).then(resultado => {
      if (resultado.value) {
        $.ajax({
            url: 'proceso_dicotex.php',
            type: 'POST',
            dataType: 'text',
            //contentType: false,
            data: {
               'procesaC5': 'procesaC5',
               'nompla': planilla
            },
            //processData: false,
            //cache: false
            beforeSend: function () {
               $("#mensajes").html('<img src="img/cargando2.gif">Cargando...</img>');
               Swal.fire({
                  title: 'Procesando Planilla  !',
                  html: 'Por favor espere...',
                  timerProgressBar: true,
                  didOpen: () => {
                     Swal.showLoading();

                  },
               });
            },
            success: function (data) {
               $("#mensajes").html("<div class='alert alert-success' role='alert'>" + data + "</div>");
               Swal.fire('Procesado !', data, 'info');
               limpiar_tabla();
               //muestra_planilla();

            },
            error: function (data) {
               $("#mensajes").html("<p>ERROR</p>");
                Swal.fire('Procesado !', data, 'warning');
            }
         });
      } else {
         Swal.fire('No Procesado ! ', 'Cancelado', 'info');
      }
   });


}

function revertir_planilla() {

planilla = $(this).attr('planilla');
   Swal.fire({
      icon: 'question',
      title: "Desea revertir digitacion de Planilla"+planilla +" ? ",
      text: "Se borrará la información de TOTVS",
      width: '500px',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Si, Revertir!',
      //showLoaderOnConfirm: true
   }).then(resultado => {
      if (resultado.value) {
         $.ajax({
            url: 'proceso_dicotex.php',
            type: 'POST',
            dataType: 'text',
            //contentType: false,
            data: {
               'deleteNOMPLA': 'deleteNOMPLA',
               'planilla': planilla
            },
            //processData: false,
            //cache: false
            success: function (data) {
                Swal.fire('Procesado !', data, 'info');
               limpiar_tabla();
               //muestra_planilla();

            },
            error: function (data) {
                Swal.fire('Error !', data, 'info');
            }
         });
      } else {
         Swal.fire('No Procesado ! ', 'Cancelado', 'info');
      }
   });


}


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function actualiza_precios() { //FUNCION DE INSERCION DE DATOS
   //alert('aqo');
   $.ajax({
      url: 'proceso_dicotex.php',
      type: 'GET',
      dataType: 'text',
      //contentType: false,
      data: {
         'actualiza_precios': 'actualiza_precios'
      },
      //processData: false,
      //cache: false
      beforeSend: function () {
         $("#mensajes").html('<img src="img/cargando2.gif">Cargando...</img>');

         Swal.fire({
            title: 'Actualizando precios segun Lista 008 !',
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
      success: function (data) {
         $("#mensajes").html("<div  role='alert'>" + data + "</div>");
         Swal.fire('PRECIOS ACTUALIZADOS ! !', data, 'success');
      },
      error: function (data, estado, errorr) {
         $("#mensajes").html("<div class='alert alert-danger' role='alert'>" + data + "_" + estado + "_" + errorr + "</div>");
      }
   });

}