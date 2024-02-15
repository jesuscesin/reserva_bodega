$(document).ready(onLoad);
function onLoad() {
    $("#buscar").click(ver_listado);
    $("#search").keyup(function(){
        _this = this;
        $.each($("#tbl_ordenes tbody tr"), function() {
            if($(this).text().toLowerCase().indexOf($(_this).val().toLowerCase()) === -1)
                $(this).hide();
            else
            $(this).show();
        });
    });
}

function ver_listado() {
    limpiarTabla();
    var empresa = $("#empresa").val();
    var inicio = $("#fch_inicio").val().replaceAll("-","");
    var fin = $("#fch_fin").val().replaceAll("-","");
    $.ajax({
        url:'ordenes_emitidas.php',
        type: 'GET',
        dataType: 'json',
        data:{'ver':'ver','empresa':empresa, 'inicio':inicio, 'fin':fin},
   
        success:function(jsonphp){
            $("#lbl_ocem").empty().text(empresa);
            var i=0;
            $.each(jsonphp, function(indice, valores){
                i+=1;
                var tr = $("<tr />").attr('id',valores.NUMERO);

                //CAMBIO DE COLOR DE FILA DE TABLA SEGUN ESTADO DE APROBACION DE ORDEN DE COMPRA
                if(valores.ESTADO == 'PENDIENTE'){
                    tr.attr('style','background-color:  #AADAFF;');
                }else if(valores.ESTADO == 'APROBADO'){
                    tr.attr('style','background-color:  #AAFFAA;');
                }else if(valores.ESTADO == 'RECHAZADO'){
                    tr.attr('style','background-color:  #F4A9A4;');
                }                
                $("<td/>").html(i).appendTo(tr);
                $('<td/>').html(valores.PROVEEDOR).appendTo(tr);
                $('<td/>').html(valores.RUT).appendTo(tr);
                $('<td/>').html(valores.CONTACTO).appendTo(tr);
                $('<td/>').html(valores.SOLICITANTE).appendTo(tr);
                $('<td/>').html(valores.FECHA).appendTo(tr);
                $('<td/>').html(valores.NUMERO).appendTo(tr);
                $('<td/>').html(valores.BTNPDF).appendTo(tr);
                tr.appendTo("#tbl_ordenes");
            });
        },      
    });
}
function limpiarTabla() {
    $("#tbl_ordenes tbody tr").remove();
}