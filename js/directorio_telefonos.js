$(document).ready(onLoad);
function onLoad() {
    $("#buscar").click(ver_listado);
      
}

function limpiarTabla() {
    $("#tbl_telefonos tbody tr").remove();
    //$("#frm_update").reset();
}
function ver_listado() {
    limpiarTabla();
    var empresa = $("#empresa").val();
    //var planilla = $(this).attr('planilla');
    //alert(oc);
    $.ajax({
        url:'directorio_telefonos.php',
        type: 'GET',
        dataType: 'json',
        //contentType: false,
        data:{'ver':'ver','empresa':empresa},
        //processData: false,
        //cache: false
        //beforeSend: cargando,
        success:function(jsonphp){
            $("#empresa_seleccionada").empty().text(empresa);
            //$("#btn_guardar_local").attr('planilla', planilla);
            //$("#tbl_selectLocal tbody tr").remove();
            var i=0;
                //alert(i);
            $.each(jsonphp, function(indice, valores){
                i+=1;
                var tr = $("<tr/>");
                
                $("<td/>").html(i).appendTo(tr);
                $('<td/>').html(valores.NOMBRE).appendTo(tr);
                $('<td/>').html(valores.CARGO).appendTo(tr);
                $('<td/>').html(valores.NUMERO).appendTo(tr);
                $('<td/>').html(valores.ANEXO).appendTo(tr);
                $('<td/>').html(valores.EMPRESA).appendTo(tr);
                $('<td/>').html(valores.DEPARTAMENTO).appendTo(tr);
               
                tr.appendTo("#tbl_telefonos");
                //alert(valores.LOCAL);
                
            });

            //$("#tbl_pedido_falabella").dataTable();
            //$("html, body").animate({ scrollTop: 680 }, "fast");

        },
      
    });

}
function confirmar_bultos(){

     Swal.fire({
          title: 'Desea Gestionar Bultos Cerrados?',
          text: "Estos Datos Seran Enviados a TOTVS",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Si, Gestionar Bultos'
        }).then((result) => {

          if (result.isConfirmed) {
            guardarPermiso();
          }

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