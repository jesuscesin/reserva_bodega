$(document).ready(onLoad);
function onLoad() {
    /*
        $("#btn_agregar").click(validarForm);
        $("#btn_guardar").click(guardarMenu);
        $("#btn_limpiar").click(limpiarForm);
        $("#cmb_nivel").change(cargaParametrosNivel);
        $("#tbl_menu").on('click','#btnEditar',cargarMenu);
        $("#tbl_menu").on('click','#btn_borrar',removerMenu);
        $("#tbl_itemsMenu").on('click','#btn_editar',cargarInputMenu);
        $("#tbl_itemsMenu").on('click','#btnBorrar',borrarMenu);
        $("#btn_update_menu").click(updateMenu);
    */
    $("#btn_buscar").click(cargarArticulos);
    $("#exportExcel").click(getPlanilla);
    
    //cargarArticulos();
    
}

function clear_table() {
    
    $("#tbl_articulos_bodega tbody tr").remove();
     
}

function getPlanilla(){
    var selBodega = document.getElementById("bodega");
    var optBodega = selBodega.options[selBodega.selectedIndex].value;

    var domain = window.location.origin; //dominio url-principal
    var pathname = '/grupomonarch/portal/'; //ruta al directorio
    var metadata = 'articulos_bodega.php?getPlanilla=getPlanilla&bodega='
    var bodega = optBodega; //nombre del archivo

    var url = domain+pathname+metadata+bodega;

    window.open(url, "_blank");

}

function cargarArticulos() {    
    //alert($(this).attr('cod')); 
    var selBodega = document.getElementById("bodega");
    var optBodega = selBodega.options[selBodega.selectedIndex].value;
    
    $.ajax({
        url:'articulos_bodega.php',
        type: 'GET',
        dataType: 'json',
        //contentType: false,
        data:{
            'getArticulos':'getArticulos',
            'bodega':optBodega
        },
        //processData: false,
        //cache: false,
        //beforeSend:cargando,
        success:function(jsonphp){
            clear_table();
            var i = 0;
            $.each(jsonphp,function(indice, valores){
                
                var tr = $('<tr/>');/*.attr({'id':valores.CODIGO});*/
                i+=1;
                
                $("<td />").html(i).appendTo(tr);
                $("<td />").html(valores.CODIGO).appendTo(tr);
                $("<td />").html(valores.BARRA).appendTo(tr);
                $("<td />").html(valores.DESCRIPCION).appendTo(tr);
                $("<td />").html(valores.GRUPO).appendTo(tr);
                
                tr.appendTo("#tbl_articulos_bodega");
                
            });
        },
        error:function(data){
            var text = "Bodega N°"+optBodega;
            clear_table();
            Swal.fire('¡ATENCION! ', 'No hay coincidencias válidas para la <b>Bodega N°'+optBodega+'</b>', 'warning');			
        },
        
        //error:problemas
    });
}