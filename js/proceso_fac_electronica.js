$(document).ready(onLoad);
function onLoad() {
    //$("#buscar").click(ver_listado);
       $("#buscar").click(ver_listado);
      $("#tbl_fact").on('click','#btn_procesa',proceso);
}
function confirmar_proceso(){

     Swal.fire({
          title: 'Correr Proceso Seleccionado ?',
          text: "Se abrira una ventana con el proceso seleccionado",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Si, Correr Proceso'
          }).then((result) => {

         if (result.isConfirmed) {
            proceso();
          }

        });


}
function proceso(){
    
    var link      = $(this).attr('link');
    var servidor  = $(this).attr('servidor');
    var usuario   = $(this).attr('usuario');
    var pass      = $(this).attr('pass');
    var archivo   = $(this).attr('archivo');
    var tipo_proceso   = $(this).attr('tipo_proceso');
    
      Swal.fire({
                title: 'Correr Proceso '+ archivo+' En Servidor IP: '+servidor+'?',
                text: "Se abrira una ventana con el proceso seleccionado",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, Correr Proceso'
                }).then((result) => {
      
               if (result.isConfirmed) {
                  
                  //alert(tipo_proceso);
                  //alert(link);
                  //alert(servidor);
                  //alert(usuario);
                  //alert(pass);
                  switch (tipo_proceso){
                    case '01':
                         window.open(link);
                         break;
                    case '02':
                          window.open("procesos/procesos_ssh/ssh.php?link="+link+"&servidor="+servidor+"&usuario="+usuario+"&pass="+pass+"&archivo="+archivo+"","Nueva ventana",'width=600,height=900'); 
                    }
               }
          });
} 
function limpiarTabla() {
    $("#tbl_fact tbody tr").remove();
    //$("#frm_update").reset();
}
function ver_listado() {
    limpiarTabla();
   
    var tipo_proceso = $("#tipo_proceso").val();
    //alert(tipo_proceso);
    $.ajax({
        url:'proceso_fac_electronica.php',
        type: 'GET',
        dataType: 'json',
        //contentType: false,
        data:{'ver':'ver','tipo_proceso':tipo_proceso},
        //processData: false,
        //cache: false
        //beforeSend: cargando,
        success:function(jsonphp){
            //$("#empresa_seleccionada").empty().text(empresa);
            //$("#btn_guardar_local").attr('planilla', planilla);
            //$("#tbl_selectLocal tbody tr").remove();
            var i=0;
                //alert(i);
            $.each(jsonphp, function(indice, valores){
                i+=1;
                var tr = $("<tr/>");
                var btn_procesa = $("<button/>").text('Correr Proceso');
                btn_procesa.attr({'type':'button','class':'btn btn-block btn-success btn-sm','id':'btn_procesa','tipo_proceso':tipo_proceso,'link':valores.LINK,'servidor':valores.SERVIDOR,'usuario':valores.BD_USER,'pass':valores.BD_PASS,'archivo':valores.ARCHIVO});
                $("<td/>").html(i).appendTo(tr);
                $('<td/>').html(valores.NOMBRE).appendTo(tr);
                $('<td/>').html(valores.DESCRIPCION).appendTo(tr);
                $('<td/>').html(valores.SERVIDOR).appendTo(tr);
                $('<td/>').html(valores.BD_USER);
                $('<td/>').html(valores.BD_PASS);
                $('<td/>').html(valores.ARCHIVO).appendTo(tr);
                $('<td/>').html(valores.LINK).appendTo(tr);
                $('<td/>').html(btn_procesa).appendTo(tr);
               
                tr.appendTo("#tbl_fact");
                //alert(valores.LOCAL);
                
            });

            //$("#tbl_pedido_falabella").dataTable();
            //$("html, body").animate({ scrollTop: 680 }, "fast");

        },
      
    });

}



//TODO: 

function guardarPermiso() {
    
    var dataCheckbox ='';
    var contador = 0;
    var cmb_ola = $("#cmb_ola").val();
    //alert(cmb_ola);
    $("input:checkbox").each(function(){

        var valor = $(this).attr('name');
        //var oc = $(this).attr('orcom');
        var check = $(this).prop('checked');       
        var value = $(this).attr('value');
       
        dataCheckbox +=valor+'='+check+'&';     
        
        if(check===true){
          check = true;
          value = parseInt(value);
          contador = (parseInt(contador) + parseInt(value));
        
        }
    });

        data = dataCheckbox+"cmb_ola="+cmb_ola+"&insertPermiso=insertPermiso";
        //alert(data);
            $.ajax({
                url:'facturacion_cajas.php',
                type: 'POST',
                dataType: 'text',
                //contentType: false,
                data:data,
                //processData: false,
                //cache: false
                beforeSend:function(){
                        $("#mensajes").html('<img src="img/cargando2.gif">Cargando...</img>');
                        //let timerInterval;
                        Swal.fire({
                            title: 'Generando Datos !',
                            html: 'Por favor espere...',
                            timerProgressBar: true,
                            didOpen: () => {
                            Swal.showLoading();
                        },
                    });

                },
                success:function(textphp){
                    $("#mensajes").html("<div class='alert alert-success alert-dismissible fade show' role='alert'>"+textphp+"<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>");
                    Swal.fire('Datos Generados ! !', 'Pedidos Generados con Exito en TOTVS !', 'success');
                },
                        //error:problemas
            });
          //}
}