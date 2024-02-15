$(document).ready(onLoad);
function onLoad() {

    // var contador = 1;
    // if (contador) {
    //     contador = contador + 1   
    // }
    // console.log("cargando pagina");
    // console.log(contador);

}
//¿Por que se queda una cola de ejecución al momento de presionar "Solicitudes RF" en la barra lateral, si haciendo pruebas estos datos no quedan almacenados en ningun lado
//si lo ejecuto sin el $(document).on no toma el botón ya que no esta en la pantalla principal al renderizar el template.


ver_estados_radios();
$("#listado_radios").click(ver_estados_radios);
$("#descargas").hide();
$("#solicitar_rf").click(limpiarTabla_solicitud);
$("#devolver_rf").click(limpiarTabla_devoluciones);
$("#filtro_buscar").click(ver_tabla_filtros);
//$('#enviar_solicitud').click(enviar_solicitud);
$(document).on('click', '#enviar_solicitud', enviar_solicitud);
$(document).on('click', '#enviar_devolucion', modificar_solicitud);
$(document).on('click', '#atras', ventana_principal);


/////////////////// en esta parte estoy haciendo pruebas ///////////////////

//$(document).on('click','#solicitar_rf',limpiarTabla_solicitud);
// if (!peticionEnCurso) {
//     var peticionEnCurso = false;   
// }
// $(document).on('click', '#solicitar_rf', function() {
//     if (peticionEnCurso === false) {
//         peticionEnCurso = true;
//         limpiarTabla_solicitud();
//         console.log("entro a la funcion que estamos probando")
//     }else{
//         console.log("no entra a la funcion que estamos probando")
//     }
// });

/////////////////////////// Fin de las pruebas  ///////////////////////////

function limpiarTabla_solicitud() {
    $("#divbutton").hide(250);
    console.log("entró a la función solicitar");
    $("#titulo").text("Solicitud de Radio Frecuencia");

    $.ajax({
        url: 'solicitud_rf.php',
        type: 'GET',
        data: { solicitud:'obtener_formulario'}, // Enviar el parametro solicitud
        dataType: 'html',
        success: function(response) {
            // El contenido del formulario obtenido del archivo PHP se mostrara en un elemento HTML
            $('#pantalla_solicitar').html(response);
            $("#pantalla_solicitar").show(300);
        },
        error: function() {
            // Manejar errores si la solicitud falla
            alert('Error al obtener el formulario.');
        }
    });
}

function limpiarTabla_devoluciones() {
    $(document).on('click', 'button#devolver_rf', function () {
        $(this).parent().closest("#divbutton").hide(250);
        $("#titulo").text("Devolucion de RF");

        $.ajax({
            url: 'solicitud_rf.php',
            type: 'GET',
            data: { devolucion:'obtener_formulario'}, // Enviar el parametro devolucion
            dataType: 'html', //el formato de la respuesta que llega desde la peticion ajax 
            success: function(response) {
                // El contenido del formulario obtenido del archivo PHP se mostrara en un elemento HTML
                $('#pantalla_devolver').html(response);
                $("#pantalla_devolver").show(300);
            },
            error: function() {
                // Manejar errores si la solicitud falla
                alert('Error al obtener el formulario.');
            }
        });
    });
}

function ver_estados_radios() {
    console.log("entró en la función VER SOLICITUDES");
    actualizarTabla();
    $.ajax({
        url:'solicitud_rf.php',
        type: 'POST',
        dataType: 'json',
        data:{'ver_estados_radios':'ver_estados_radios'},
        success:function(jsonphp){
            $.each(jsonphp, function(indice, valores){
                var tr = $("<tr/>");
                $('<td/>').html(valores.CODIGO_QR).appendTo(tr);
                if (valores.ESTADO == 'Disponible') {
                    $('<td />').html(valores.ESTADO + " <img src='img/icon/check.png'></img>").appendTo(tr);
                } else {
                    $('<td />').html(valores.ESTADO + " <img src='img/icon/proceso.png' width='24' height='24'></img>").appendTo(tr);
                 }
                if (valores.USU_ACTUAL == 'Disponible') {
                    $('<td/>').html("Sin Asignar").appendTo(tr);   
                } else {
                    $('<td/>').html(valores.USU_ACTUAL).appendTo(tr);
                }
                $('<td/>').html(valores.FEC_HOR_MD).appendTo(tr);

                tr.appendTo("#radio_frecuencias");
            });
        },
        error: function() {

            alert('Error al obtener la información.');
        }
    })
}

function ver_tabla_filtros() {
    var fecha_ini = $("#fec_ini").val();
    var fecha_fin = $("#fec_final").val();
    limpiarTabla();
    $.ajax({
        url:'solicitud_rf.php',
        type: 'POST',
        dataType: 'json',
        data:{
            'ver_tabla_filtros':'ver_tabla_filtros',
            'fecha_ini': fecha_ini,
            'fecha_fin': fecha_fin
        },
        success:function(response){

            $.each(response, function(indice, valores){
                var tr = $("<tr/>");
            
                $('<td/>').html(valores.ID).appendTo(tr);
                $('<td/>').html(valores.CODIGO_QR).appendTo(tr);
                $('<td/>').html(valores.USU_ACTUAL).appendTo(tr);
                $('<td/>').html(valores.FEC_HOR_MD).appendTo(tr);
                $('<td/>').html(valores.COD_US_DEV).appendTo(tr);
                if (valores.FEC_HOR_DE === "Pendiente") {
                    $('<td/>').html(valores.FEC_HOR_DE).appendTo(tr);   
                }else{
                    fecha = formato_fecha(valores.FEC_HOR_DE)
                    $('<td/>').html(fecha).appendTo(tr);   
                }
                $('<td/>').html(valores.ESTADO).appendTo(tr);

                tr.appendTo("#tbl_pedido_solicitud_rf");
            });

            $("#descargas").show();

        },
        error: function(response) {
            // Manejar errores si la solicitud falla
            console.log(response);
        }
    });

}

function enviar_solicitud(){
    var cod_solicitante = $('#cod_solicitante').val();
    var cod_rf_solicitud = $('#cod_rf_solicitud').val();
    var coment_solicitud = $('#coment_solicitud').val();

    var con = 'valor 0 ';

    $.ajax({
        url:'solicitud_rf.php',
        type: 'POST',
        data:{
            cod_solicitante: cod_solicitante,
            cod_rf_solicitud: cod_rf_solicitud,
            coment_solicitud: coment_solicitud
        },
        success:function(response){
            //confirmar esto para modificarlo por un null y trim()
            if (response.length !== 0){
                if (response != 'No existe') {
                    if (response == 'Disponible') {
                        Swal.fire({
                            title: 'Solicitud ingresada',
                            text: "Se asignó la radio " + cod_rf_solicitud + " al usuario " + cod_solicitante,
                            icon: 'success',
                    
                        }).then(() => {
                            con = con + ' funciona el ingresar ';
                            ver_estados_radios();
                            ventana_principal();                  
                        })
                    } else {
                        //En el caso que el "$v_existe_radio" no sea igual a "Disponible" significa que la radio la esta utilizando un usuario 
                        Swal.fire({
                            title: 'Radio no disponible',
                            text: "La radio se encuentra asignada a " + response + ", no es posible asignarla",
                            icon: 'warning'
                    
                        })
                        con = con + ' no disponible ';
                        return;
                    }
                }else {
                    Swal.fire({
                        title: 'No existe',
                        text: 'La radio ingresada no existe en la base de datos',
                        icon: 'error'
                
                    })
                    con = con + ' error ';
                    return;
                }
            }else {
                Swal.fire({
                    title: 'Falta información',
                    text: "Se deben ingresar todos los campos en pantalla",
                    icon: 'info'
                })
                con = con + ' falta ';
                return;
            }
        },
        error: function(error) {
            // Manejar errores si la solicitud falla
            console.log("problemas al generar la solicitud")
        },
        complete: function() {
            // Se ejecutará sin importar si la solicitud fue exitosa o no
            console.log("mostrando")
            console.log(con + " veces entra en el loop");
        }
    });
    //}
    //alert('Se envia el formulario');
}

function modificar_solicitud(){
    console.log("Ingresamos al botón");

    var cod_devolucion = $('#cod_devolucion').val();
    var cod_rf_devolucion = $('#cod_frec_devolucion').val();
    var coment_devolucion = $('#coment_devolucion').val();

    console.log("codigo Soli: "+cod_devolucion);
    console.log("codigo RF: "+cod_rf_devolucion);
    console.log("comentario: "+coment_devolucion);


    $.ajax({
        url:'solicitud_rf.php',
        type: 'POST',
        data:{
            cod_devolucion: cod_devolucion,
            cod_rf_devolucion: cod_rf_devolucion,
            coment_devolucion: coment_devolucion
        },
        success:function(response){ 
            //confirmar esto para modificarlo por un null y trim()
            if (response.length !== 0){
                if (response != 'No existe') {
                    if (response != 'Disponible') {
                        Swal.fire({
                            title: 'Devolución ingresada',
                            text: "Se devolvió la radio " + cod_rf_devolucion + " por el usuario " + cod_devolucion,
                            icon: 'success',
                            //showConfirmButton: false,
                            //timer: 3000,
                            //showCancelButton: true,
                            // confirmButtonColor: '#e82020',
                            // cancelButtonColor: '#3085d6',
                            // confirmButtonText: 'Si, Eliminar Datos'
                      
                         }).then(() => {
                            ventana_principal();
                            ver_estados_radios();
                         })
                    } else {
                        //En el caso que el "$v_existe_radio" es igual a "Disponible" significa que la radio no la tiene asignada ningun usuario
                        Swal.fire({
                            title: 'Radio no asignar',
                            text: 'La radio ingresada no se encuentra vinculada a ningun usuario ',
                            icon: 'warning'
                         })
                    }
                }else {
                    Swal.fire({
                        title: 'No existe',
                        text: 'La radio ingresada no existe en la base de datos',
                        icon: 'error'
                     })
                }
            }else{
                console.log(response);
                Swal.fire({
                    title: 'Falta información',
                    text: "Se deben ingresar todos los campos en pantalla",
                    icon: 'info'
                    //showCancelButton: true,
                    // confirmButtonColor: '#e82020',
                    // cancelButtonColor: '#3085d6',
                    // confirmButtonText: 'Si, Eliminar Datos'
              
                 })
            }
        },
        error: function(error) {
            // Manejar errores si la solicitud falla
            //alert('Error al generar la solicitud');
            console.log("problemas al generar la solicitud" + error)
        }
    });
    //alert('Se envia el formulario');
}

function ventana_principal(){
    $("#divbutton").show(400);
    $("#pantalla_solicitar").hide(300);
    $("#pantalla_devolver").hide(300);
    $("#titulo").text("Opciones");
}

function limpiarTabla() {
    $("#tbl_pedido_solicitud_rf tbody tr").remove();
}

function actualizarTabla() {
    $("#radio_frecuencias tbody tr").remove();
}

if (!download_xls) {
    let download_xls = document.querySelector("#download_xls");   
}

if (!download_csv) {
    let download_csv = document.querySelector("#download_csv");    
}

if (!download_xlsx) {
    let download_xlsx = document.querySelector("#download_xlsx");    
}


download_xls.addEventListener("click", ()=>{   
    let fecha_ini = $("#fec_ini").val();
    let fecha_fin = $("#fec_final").val();
    let { inicio, fin } = fecha_ini_fin(fecha_ini, fecha_fin);
    
    if (inicio === fin) {
    ExcellentExport.convert({ anchor: download_xls, filename: 'Solicitudes del ' + inicio, format: 'xls'},[{name: 'Sheet Name Here 1', from: {table: 'tbl_pedido_solicitud_rf'}}])   
    }else{
    ExcellentExport.convert({ anchor: download_xls, filename: 'Solicitudes desde ' + inicio + ' hasta ' + fin, format: 'xls'},[{name: 'Sheet Name Here 1', from: {table: 'tbl_pedido_solicitud_rf'}}])   
    }
});

download_csv.addEventListener("click", ()=>{    
    let fecha_ini = $("#fec_ini").val();
    let fecha_fin = $("#fec_final").val();
    let { inicio, fin } = fecha_ini_fin(fecha_ini, fecha_fin);
    
    if (inicio === fin) {
    ExcellentExport.convert({ anchor: download_csv, filename: 'Solicitudes del ' + inicio, format: 'csv'},[{name: 'Sheet Name Here 1', from: {table: 'tbl_pedido_solicitud_rf'}}])   
    }else{
    ExcellentExport.convert({ anchor: download_csv, filename: 'Solicitudes desde ' + inicio + ' hasta ' + fin, format: 'csv'},[{name: 'Sheet Name Here 1', from: {table: 'tbl_pedido_solicitud_rf'}}])   
    }
});

download_xlsx.addEventListener("click", ()=>{
    let fecha_ini = $("#fec_ini").val();
    let fecha_fin = $("#fec_final").val();
    let { inicio, fin } = fecha_ini_fin(fecha_ini, fecha_fin);

    if (inicio === fin) {
    ExcellentExport.convert({ anchor: download_xlsx, filename: 'Solicitudes del ' + inicio , format: 'xlsx'},[{name: 'Sheet Name Here 1', from: {table: 'tbl_pedido_solicitud_rf'}}])  
    } else {
    ExcellentExport.convert({ anchor: download_xlsx, filename: 'Solicitudes desde ' + inicio + ' hasta ' + fin , format: 'xlsx'},[{name: 'Sheet Name Here 1', from: {table: 'tbl_pedido_solicitud_rf'}}])
    }
});

function fecha_ini_fin(fecha_ini, fecha_fin) {
    let di = fecha_ini.substring(0, 2);
    let mi = fecha_ini.substring(3, 5);
    let ai = fecha_ini.substring(6);
    let inicio = di + mi + ai;

    let df = fecha_fin.substring(0, 2);
    let mf = fecha_fin.substring(3, 5);
    let af = fecha_fin.substring(6);
    let fin = df + mf + af;
    return { inicio: inicio, fin: fin }
}

function formato_fecha(fecha) {
    let ai = fecha.substring(0, 4);
    let mi = fecha.substring(4, 6);
    let di = fecha.substring(6,8);
    let formato = di + '/' + mi + '/'+ ai;
    return formato
}