$(document).ready(onLoad);
function onLoad() {
    $("#buscar").click(ver_listado);
    $("#tbl_mv").on('click','#btn_procesa',procesa_articulo);
    $("#correr_proceso").click(ventana2);
    $("#proceso_odoo").click(ventana3);
}
function ventana2(){   
   
    window.open("http://gestor.monarch.cl/mv/callapimv_v50.php","Nueva ventana",'width=1200,height=900');   
}
function ventana3(){   
   
    window.open("http://192.168.100.24/web?debug#id=11&action=11&model=ir.cron&view_type=form&menu_id=4","Nueva ventana",'width=1200,height=900');   
}
function limpiarTabla() {
    $("#tbl_mv tbody tr").remove();
    //$("#frm_update").reset();
}
function ver_listado() {
    limpiarTabla();
   
     var vta_code = $("#vta_code").val();
    //alert(oc);
    $.ajax({
        url:'proceso_mv.php',
        type: 'GET',
        dataType: 'json',
        //contentType: false,
        data:{'ver':'ver','vta_code':vta_code},
        //processData: false,
        //cache: false
        //beforeSend: cargando,
        success:function(jsonphp){
            $("#code_seleccionada").empty().text(vta_code);
            //$("#btn_guardar_local").attr('planilla', planilla);
            //$("#tbl_selectLocal tbody tr").remove();
            var i=0;
                //alert(i);
            $.each(jsonphp, function(indice, valores){
                i+=1;
                var tr = $("<tr/>");
                var btn_procesa = $("<button/>").text('Procesar Codigo');
                btn_procesa.attr({'type':'button','class':'btn btn-block btn-success btn-sm','id':'btn_procesa','articulo':valores.SKU});
                $("<td/>").html(i).appendTo(tr);
                $('<td/>').html(valores.VTA_CODE).appendTo(tr);
                $('<td/>').html(valores.NOMBRE).appendTo(tr);
                $('<td/>').html(valores.SKU).appendTo(tr);
                $('<td/>').html(valores.DESCRIPCION).appendTo(tr);
                $('<td/>').html(btn_procesa).appendTo(tr);
               
                tr.appendTo("#tbl_mv");
                //alert(valores.LOCAL);
                
            });

            //$("#tbl_pedido_falabella").dataTable();
            //$("html, body").animate({ scrollTop: 680 }, "fast");

        },
      
    });

}
function procesa_articulo() { 
    
    articulo = $(this).attr('articulo');
       Swal.fire({
            icon: 'question',
            title: "Desea Procesar Codigo <br> "+articulo+" ?",
            text: "",
            width: '500px',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, Procesar !',
            //showLoaderOnConfirm: true
    }).then(resultado => {
        if (resultado.value) {
      
        $.ajax({
            url:'proceso_mv.php',
            type: 'GET',
            dataType: 'text',
            //contentType: false,
            data:{'update_articulo':'update_articulo','articulo':articulo},
             
            //processData: false,
            //cache: false
            //beforeSend:cargando,
             beforeSend:function(){
            $("#mensajes").html('<img src="img/cargando2.gif">Cargando...</img>');
        },
        success:function(data){
            $("#mensajes").html("<div class='alert alert-success' role='alert'>"+data+"</div>");
        },
            //error:problemas
        });
        } else {
            // Dijeron que no
           Swal.fire('No Editado ! ', 'Proceso Cancelado', 'info');
        }
    });
    //});  
}