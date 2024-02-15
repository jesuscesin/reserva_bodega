$(document).ready(onLoad);
function onLoad() {
   ver_listado();
    $("#actualiza_token").click(actualiza_token);
    $("#intrucciones").click(intrucciones);

}

function limpiarTabla() {
    $("#tbl_token tbody tr").remove();
    //$("#frm_update").reset();
}
function ver_listado() {
    limpiarTabla();
   
     //var vta_code = $("#vta_code").val();
    //alert(oc);
    $.ajax({
        url:'token_multivende.php',
        type: 'GET',
        dataType: 'json',
        //contentType: false,
        data:{'ver':'ver'},
        //processData: false,
        //cache: false
        //beforeSend: cargando,
        success:function(jsonphp){
            //$("#code_seleccionada").empty().text(vta_code);
            //$("#btn_guardar_local").attr('planilla', planilla);
            //$("#tbl_selectLocal tbody tr").remove();
            var i=0;
                //alert(i);
            $.each(jsonphp, function(indice, valores){
                i+=1;
                var tr = $("<tr/>");
                var btn_procesa = $("<button/>").text('Editar');
                btn_procesa.attr({'type':'button','class':'btn btn-block btn-success btn-sm','id':'btn_procesa','client_id':valores.CLIENTID});
                $("<td/>").html(i).appendTo(tr);
                $('<td/>').html(valores.TOKEN).appendTo(tr);
                $('<td/>').html(valores.CLIENTID).appendTo(tr);
                $('<td/>').html(valores.CLI_SECRET).appendTo(tr);
                $('<td/>').html(valores.AUT_CODE).appendTo(tr);
                $('<td/>').html(valores.FECHA).appendTo(tr);
                $('<td/>').html(valores.HORA).appendTo(tr);
                //$('<td/>').html(btn_procesa).appendTo(tr);
               
                tr.appendTo("#tbl_token");
                //alert(valores.LOCAL);
                
            });

            //$("#tbl_pedido_falabella").dataTable();
            //$("html, body").animate({ scrollTop: 680 }, "fast");

        },
      
    });

}
function intrucciones(){
    
    Swal.fire({
        icon: 'question',
        title: 'Instruciones',
        html: '<br>Siga los siguientes pasos para recuperarlo.<br><br>		1.- Ingrese a http://app.multivende.com - Con su usuario y clave.<br>				2.- Dirijase a la Pestaña Aplicaciones. <br>				3.- Selecciones <strong>Listado de aplicaciones</strong>.<br>				4.- Presione en <strong> Monarch_produccion </strong> editar (lapiz). <br>				5.- Presione <strong> Generar codigo de autorizacion </strong> y copie el codigo. <br>				6.- Dirijase a <strong>www.grupomonarch.cl/utilitarios</strong> a la pestaña TOKEN MULTIVENDE. <br>				7.- Pegar el codigo copiado en el campo de texto vacio. <br>				8.- Por ultimo, Precionar el boton "Actualizar Token". Se abrirá una ventana con un mensaje.</strong> .<br>'
        
});
}
function actualiza_token() { 
    
    var auth_code = $("#auth_code").val();
       Swal.fire({
            icon: 'question',
            title: "Desea Actualizar Token Multivende ?",
            text: "",
            width: '500px',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, Actualizar !',
            //showLoaderOnConfirm: true
    }).then(resultado => {
        if (resultado.value) {
      
        $.ajax({
            url:'token_multivende.php',
            type: 'GET',
            dataType: 'text',
            //contentType: false,
            data:{'update_auth_code':'update_auth_code','auth_code':auth_code},
             
            //processData: false,
            //cache: false
            //beforeSend:cargando,
             beforeSend:function(){
            $("#mensajes").html('<img src="img/cargando2.gif">Cargando...</img>');
        },
        success:function(data){
            $("#mensajes").html("<div class='alert alert-success' role='alert'>"+data+"</div>");
            Swal.fire('TOKEN ACTUALIZADO ! !', 'Proceso Completado !', 'success');
             window.open("http://gestor.monarch.cl/mv/obtener_token_v2.php","Nueva ventana",'width=1200,height=900');
             ver_listado();
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