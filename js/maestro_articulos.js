$(document).ready(onLoad);
function onLoad() {
      cmb_familia();
      cmb_um();
      cmb_segum();
      cmb_proveedor();
      cmb_marca();
      cmb_linea();
      cmb_temporada();

      input_barra();

      input_recno();

      input_sequ();

      $("#btn_add_articulo").click(existe_articulo);

      $("#txt_estilo").change(cargarDatos);

      $("#cmb_familia").change(cmb_clase);

      $("#tbl_fcodigos").on('click','#btn_remover',removerArt);

      $("#btn_confirmar").click(confirmaForm);      
      
      $("#cmb_origen").change(muestraGenero);      
      
      $("#cmb_genero").change(isSpecial);      




      $("#btn_limpiar").click(limpiarForm);      
      $("#btn_limpiarTbl").click(limpiarTbl);      
  
}

function existe_articulo(){

    var origen = $("#cmb_origen option:selected").val();
    var bodega = $("#cmb_bodega option:selected").val();
    var tipo = $("#cmb_tipo option:selected").val();
    var estilo = $("#txt_estilo").val();
    var familia = $("#cmb_familia option:selected").val();
    var clase = $("#cmb_clase option:selected").val();
    var descripcion = $("#txt_descripcion").val();
    var talla = $("#cmb_talla option:selected").val();
    var color = $("#txt_color").val();
    var um = $("#cmb_um option:selected").val();
    var segum = $("#cmb_2um option:selected").val();
    var conversion = $("#txt_conv").val();
    var proveedor = $("#cmb_proveedor option:selected").val();
    var marca = $("#cmb_marca option:selected").val();
    var linea = $("#cmb_linea option:selected").val();
    var composicion = $("#cmb_composicion option:selected").val();
    var temporada = $("#cmb_temporada option:selected").val();
    var maquina = $("#cmb_maquina option:selected").val();

    var conta = $("#ctaContable").val(); 
    var contav = $("#ctaVenta").val(); 
    var contac = $("#ctaCosto").val(); 
    var itemGasto = $("#itemGasto").val();
    var centroCosto = $("#centroCosto").val();
    var claseValor = $("#claseValor").val(); 
    
    var barras = parseInt($("#barras").val()); 
    
    var recno = parseInt($("#recno").val()); 
    
    var sequ = parseInt($("#sequ").val()); 

    var fila = parseInt($("#filas").val());   
        

    if (origen == '' || estilo == '' || tipo == '' || descripcion == '' || 
        familia == '' || clase == '' || bodega == '' || proveedor == '' || marca == '' || linea == '' ||
        composicion == '' || temporada == '' || maquina == '' || um == '' || segum == '' || conversion == ''){

        Swal.fire('¡Faltan datos!', 'Debes completar todos los campos', 'info');
            
    }else{        

        $.ajax({
            url:'maestro_articulos.php',
            type: 'GET',
            dataType: 'json',
            //contentType: false,
            data:{
                'existe_articulo': 'existe_articulo',
                'origen': origen,
                'bodega': bodega,
                'tipo': tipo,
                'estilo': estilo,
                'familia': familia,
                'clase': clase, 
                'descripcion': descripcion, 
                'talla': talla, 
                'color': color, 
                'um': um, 
                'segum': segum, 
                'conversion': conversion,
                'proveedor': proveedor,
                'marca': marca,
                'linea': linea,
                'composicion': composicion,
                'temporada': temporada,
                'maquina': maquina,
                'conta':conta,
                'contav':contav,
                'contac':contac,
                'itemcc':itemGasto,
                'cc':centroCosto,
                'clvl':claseValor,
                'barras':barras,
                'recno':recno                
            },
            //processData: false,
            //cache: false
            //beforeSend:cargando,
            success:function(data){            
                $.each(data, function(indice,valores){
    
                    fila=fila+1;

                    var tr = $("<tr/>").attr('id',fila);
                    
                    var cod = JSON.stringify(valores.B1_COD);
                    var desc = JSON.stringify(valores.DESCRIPCION);
                    var bod = JSON.stringify(valores.BODEGA);
                    var fam = JSON.stringify(valores.FAMILIA);
                    var cla = JSON.stringify(valores.CLASE);
                    var conv = JSON.stringify(valores.CONVERSION);
                    var bar = JSON.stringify(valores.BARRA);
                    var prov = JSON.stringify(valores.PROVEEDOR);
                    var mar = JSON.stringify(valores.MARCA);
                    var lin = JSON.stringify(valores.LINEA);
                    var comp = JSON.stringify(valores.COMPOSICION);
                    var temp = JSON.stringify(valores.TEMPORADA);
    
                    var tipo = JSON.stringify(valores.TIPO);
                    var um = JSON.stringify(valores.UM);
                    var segum = JSON.stringify(valores.SEGUM);
                    var maquina = JSON.stringify(valores.MAQUINA);
    
                    var cta_contable = JSON.stringify(valores.CONTA);
                    var cta_venta = JSON.stringify(valores.CONTAV);
                    var cta_costo = JSON.stringify(valores.CONTAC);
                    var item_gasto = JSON.stringify(valores.ITEMCC);
                    var centro_costo = JSON.stringify(valores.CC);
                    var clase_valor = JSON.stringify(valores.CLVL);
                    
                    var rec = JSON.stringify(valores.RECNO);
    
                    //var barra = parseInt(JSON.parse(bar));                    
                                    
                    
                    var btn_borrar = $("<button />").text("Remover");
                    btn_borrar.attr({'type':'button','class':'btn btn-block bg-gradient-danger', 'id':'btn_remover','articulo':cod,'numFila':fila});
        
    
                    $('<td />').html(fila).append($("<input/>").attr({'type':'hidden','value':fila,'name':'asg-num_fila','id':'asg-num_fila'})).appendTo(tr);
                    $('<td />').html(JSON.parse(cod)).append($("<input/>").attr({'type':'hidden','value':JSON.parse(cod),'name':'asg-articulo-'+fila,'id':'asg-articulo-'+fila})).appendTo(tr);
                    $('<td />').html(JSON.parse(desc)).append($("<input/>").attr({'type':'hidden','value':JSON.parse(desc),'name':'asg-descripcion-'+fila,'id':'asg-descripcion-'+fila})).appendTo(tr);
                    $('<td />').html(JSON.parse(bod)).append($("<input/>").attr({'type':'hidden','value':JSON.parse(bod),'name':'asg-bodega-'+fila,'id':'asg-bodega-'+fila})).appendTo(tr);
                    $('<td />').html(JSON.parse(fam)).append($("<input/>").attr({'type':'hidden','value':JSON.parse(fam),'name':'asg-familia-'+fila,'id':'asg-familia-'+fila})).appendTo(tr);
                    $('<td />').html(JSON.parse(cla)).append($("<input/>").attr({'type':'hidden','value':JSON.parse(cla),'name':'asg-clase-'+fila,'id':'asg-clase-'+fila})).appendTo(tr);
                    $('<td />').html(JSON.parse(tipo)).append($("<input/>").attr({'type':'hidden','value':JSON.parse(tipo),'name':'asg-tipo-'+fila,'id':'asg-tipo-'+fila})).appendTo(tr);
                    $('<td />').html(JSON.parse(bar)).append($("<input/>").attr({'type':'hidden','value':JSON.parse(bar),'name':'asg-barra-'+fila,'id':'asg-barra-'+fila})).appendTo(tr);
                    $('<td />').html(JSON.parse(prov)).append($("<input/>").attr({'type':'hidden','value':JSON.parse(prov),'name':'asg-proveedor-'+fila,'id':'asg-proveedor-'+fila})).appendTo(tr);
                    $('<td />').html(JSON.parse(mar)).append($("<input/>").attr({'type':'hidden','value':JSON.parse(mar),'name':'asg-marca-'+fila,'id':'asg-marca-'+fila})).appendTo(tr);
                    $('<td />').html(JSON.parse(lin)).append($("<input/>").attr({'type':'hidden','value':JSON.parse(lin),'name':'asg-linea-'+fila,'id':'asg-linea-'+fila})).appendTo(tr);
                    $('<td />').html(JSON.parse(comp)).append($("<input/>").attr({'type':'hidden','value':JSON.parse(comp),'name':'asg-composicion-'+fila,'id':'asg-composicion-'+fila})).appendTo(tr);
                    $('<td />').html(JSON.parse(temp)).append($("<input/>").attr({'type':'hidden','value':JSON.parse(temp),'name':'asg-temporada-'+fila,'id':'asg-temporada-'+fila})).appendTo(tr);
                    $('<td />').html(JSON.parse(maquina)).append($("<input/>").attr({'type':'hidden','value':JSON.parse(maquina),'name':'asg-maquina-'+fila,'id':'asg-maquina-'+fila})).appendTo(tr);
                    $('<td />').html(JSON.parse(um)).append($("<input/>").attr({'type':'hidden','value':JSON.parse(um),'name':'asg-um-'+fila,'id':'asg-um-'+fila})).appendTo(tr);
                    $('<td />').html(JSON.parse(segum)).append($("<input/>").attr({'type':'hidden','value':JSON.parse(segum),'name':'asg-segum-'+fila,'id':'asg-segum-'+fila})).appendTo(tr);
                    $('<td />').html(JSON.parse(conv)).append($("<input/>").attr({'type':'hidden','value':JSON.parse(conv),'name':'asg-conversion-'+fila,'id':'asg-conversion-'+fila})).appendTo(tr);
    
                    $('<td />').html(btn_borrar).append($("<input/>").attr({'type':'hidden','value':JSON.parse(rec),'name':'asg-recno-'+fila,'id':'asg-recno-'+fila})).appendTo(tr);

                    var txt_ctaContable = $("<input />");
                    txt_ctaContable.attr({'type':'hidden','class':'form-control form-control-sm','value':JSON.parse(cta_contable),'name':'asg-ctacontable-'+fila, 'id':'asg-ctacontable-'+fila});
                    
                    var txt_ctaVenta = $("<input />");
                    txt_ctaVenta.attr({'type':'hidden','class':'form-control form-control-sm','value':JSON.parse(cta_venta),'name':'asg-ctaventa-'+fila, 'id':'asg-ctaventa-'+fila});
                    
                    var txt_ctaCosto = $("<input />");
                    txt_ctaCosto.attr({'type':'hidden','class':'form-control form-control-sm','value':JSON.parse(cta_costo),'name':'asg-ctacosto-'+fila, 'id':'asg-ctacosto-'+fila});
    
                    var txt_itemGasto = $("<input />");
                    txt_itemGasto.attr({'type':'hidden','class':'form-control form-control-sm','value':JSON.parse(item_gasto),'name':'asg-itemgasto-'+fila, 'id':'asg-itemgasto-'+fila});
    
                    var txt_centroCosto = $("<input />");
                    txt_centroCosto.attr({'type':'hidden','class':'form-control form-control-sm','value':JSON.parse(centro_costo),'name':'asg-centrocosto-'+fila, 'id':'asg-centrocosto-'+fila});
    
                    var txt_claseValor = $("<input />");
                    txt_claseValor.attr({'type':'hidden','class':'form-control form-control-sm','value':JSON.parse(clase_valor),'name':'asg-clasevalor-'+fila, 'id':'asg-clasevalor-'+fila});
                    
                    var txt_sequ = $("<input />");
                    txt_sequ.attr({'type':'hidden','class':'form-control form-control-sm','value':JSON.parse(sequ),'name':'asg-sequ-'+fila, 'id':'asg-sequ-'+fila});

                    $('<td style="display:none;"/>').html(txt_ctaContable).appendTo(tr);
                    $('<td style="display:none;"/>').html(txt_ctaVenta).appendTo(tr);
                    $('<td style="display:none;"/>').html(txt_ctaCosto).appendTo(tr);
                    $('<td style="display:none;"/>').html(txt_itemGasto).appendTo(tr);
                    $('<td style="display:none;"/>').html(txt_centroCosto).appendTo(tr);
                    $('<td style="display:none;"/>').html(txt_claseValor).appendTo(tr);
                   
                    tr.appendTo('#tbl_fcodigos');                    
    
                    barras=barras+1;
                    recno=recno+1;
                    $("#filas").val(fila);   
                    $("#barras").val(barras);   
                    $("#recno").val(recno);   
    
                });
    
            },
            //error:problemas
        });
    }

    
}


function removerArt() { //REMOVER ASIGNACION INGRESADA,NO DE LA BD
    //alert($(this).attr('cod'));
    articulo = $(this).attr('articulo');    
    numFila = $(this).attr('numFila');
  
    Swal.fire({
             icon: 'question',
             title: " Desea quitar el Articulo "+articulo +" ?, de la fila: "+numFila,
             text: "Este producto no se creará",
             width: '500px',
             showCancelButton: true,
             confirmButtonColor: '#3085d6',
             cancelButtonColor: '#d33',
             confirmButtonText: 'Si, Eliminar !',
    }).then(resultado => {
        if (resultado.value) {
            $("#"+numFila).remove();
        }else {
            Swal.fire('No Procesado ! ', 'Cancelado', 'info');
        }
    });
}

function insertarForm() { //FUNCION DE INSERCION DE DATOS
    var fila   = $("#filas").val();//cantidad filas tabla asignacion
    var dataForm = $("form").serialize();
    var data = dataForm+"&asg-max="+fila+"&insertar=insertar";

    $.ajax({
        url:'maestro_articulos.php',
        type: 'POST',
        dataType: 'text',
        data:data,
        beforeSend:function(){
            $("#mensajes").html('<img src="img/cargando2.gif">Cargando...</img>');
        },
        success:function(data){
            limpiarForm();
            limpiarTbl();            
            $("#mensajes").html("<div  role='alert'><strong>INSERCIÓN COMPLETADA </strong><i class='fas fa-check-square'></i></div>");
        },
        error:function(data,estado,errorr){
            $("#mensajes").html("<div class='alert alert-danger' role='alert'>"+data+"_"+estado+"_"+errorr+"</div>");
        }
    });
    
}

function confirmaForm() { //REMOVER ASIGNACION INGRESADA,NO DE LA BD

    Swal.fire({
             icon: 'question',
             title: "Desea realizar la carga?",
             text: "Estos artículos van a ser creados en el Maestro",
             width: '500px',
             showCancelButton: true,
             confirmButtonColor: '#3085d6',
             cancelButtonColor: '#d33',
             confirmButtonText: 'Si, Crear!',
    }).then(resultado => {
        if (resultado.value) {
            insertarForm();
        }else {
            Swal.fire('No Procesado ! ', 'Cancelado', 'info');
        }
    });
}

function validaAgregadosTbl() {
    //get_recno();
   fila = $("#filas").val();
   var articulo             = $("#articulo").val();
   var contador = 0;
   for(var i=0; i<=fila; i++){

       var tr_art = $("#asg-articulo").val();
     
       
       if (articulo==tr_art) {
           contador = contador+1;
           //alert("contador : "+contador);
       }
   }
if (contador === 0) {
       return true;
   }else{
            alert("Articulo ya ingresado !");
        $("#articulo").val('');
          $('#docenas').val('');
          $('#unidades').val('');
          $('#precio_lista').val('');
          $('#precio_liquidacion').val('');
          $('#descripcion').val('');
          $('#codigo_barra').val('');

   }
   
}

function cmb_familia(){
    $.ajax({
       url:'maestro_articulos.php',
       type: 'GET',
       dataType: 'json',
       //contentType: false,
       data:{'cargar_familia':'cargar_familia'},
       //processData: false,
       //cache: false
       //beforeSend:cargando,
       success:function(data){
           $.each(data, function(indice,valores){
           
               var option = $("<option />").attr({'value':valores.B1_GFAMILI});
               option.text(valores.B1_DGFAMIL);
                       
               option.appendTo("#cmb_familia");
           });
       },
       //error:problemas
   });
   
}

function cmb_clase(){
    var optValue = $("#cmb_familia option:selected").val();   

    switch(optValue){
        case "1000":
            $("#cmb_clase").empty();
            $("#cmb_clase").append('<option value="1010">1010 - CALCETINES</option>');
            $("#cmb_clase").append('<option value="1030">1030 - CALZA Y BUCANERA GRUESA</option>');        
            $("#cmb_clase").append('<option value="1070">1070 - ACCESORIOS CALCETINES</option>');    
            
            break;    
        case "2000":
            $("#cmb_clase").empty();
            $("#cmb_clase").append('<option value="2010">2010 - PANTYHOSE</option>');        
            $("#cmb_clase").append('<option value="2020">2020 - BALLERINA TRAMA</option>');        
            $("#cmb_clase").append('<option value="2030">2030 - MEDIAS Y MINIMEDIAS</option>');        
            $("#cmb_clase").append('<option value="2040">2040 - CALZA Y BUCANERA</option>');        
            $("#cmb_clase").append('<option value="2050">2050 - CHEMISETTE</option>');        
            $("#cmb_clase").append('<option value="2055">2055 - POLERA</option>');        
            $("#cmb_clase").append('<option value="2060">2060 - ROPA INTERIOR</option>');        
            $("#cmb_clase").append('<option value="2070">2070 - ACCESORIOS TRAMA</option>');     
            
            break;
        case "3000":
            $("#cmb_clase").empty();
            $("#cmb_clase").append('<option value="3010">3010 - BALLERINA ALGODON</option>');        
            $("#cmb_clase").append('<option value="3020">3020 - BALLERINAS LANA</option>');   
            
            break;
        case "5000":
            $("#cmb_clase").empty();
            $("#cmb_clase").append('<option value="5100">5100 - BODY BEBE</option>');        
            $("#cmb_clase").append('<option value="5200">5200 - CONJUNTO BEBE</option>');        
            $("#cmb_clase").append('<option value="5300">5300 - PIJAMAS</option>');    
            
            break;
        case "6000":
            $("#cmb_clase").empty();
            $("#cmb_clase").append('<option value="6010">6010 - SLIP</option>');        
            $("#cmb_clase").append('<option value="6020">6020 - BOXER</option>');        
            $("#cmb_clase").append('<option value="6030">6030 - CALZONCILLO</option>');        
            $("#cmb_clase").append('<option value="6040">6040 - CAMISETA</option>');        
            $("#cmb_clase").append('<option value="6050">6050 - COLALESS</option>');        
            $("#cmb_clase").append('<option value="6060">6060 - CUADROS</option>');        
            $("#cmb_clase").append('<option value="6070">6070 - HOTS PANTS</option>');        
            $("#cmb_clase").append('<option value="6080">6080 - PETOS Y SOSTENES</option>');
            
            break;
        case "7000":
            $("#cmb_clase").empty();
            $("#cmb_clase").append('<option value="7010">7010 - PIJAMAS LARGOS</option>');  
            $("#cmb_clase").append('<option value="7020">7020 - PIJAMAS LARGOS</option>');  
            $("#cmb_clase").append('<option value="7030">7030 - CAMISOLAS</option>');  

            break;
        case "8000":
            $("#cmb_clase").empty();
            $("#cmb_clase").append('<option value="">................</option>');  

            break;
        case "9000":
            $("#cmb_clase").empty();
            $("#cmb_clase").append('<option value="9010">9010 - OVILLOS</option>');  

            break;
        default:
            $("#cmb_clase").append('<option value="">................</option>'); 
            

            break;

    }
}

function cmb_um(){
    $.ajax({
       url:'maestro_articulos.php',
       type: 'GET',
       dataType: 'json',
       //contentType: false,
       data:{'cargar_um':'cargar_um'},
       //processData: false,
       //cache: false
       //beforeSend:cargando,
       success:function(data){
           $.each(data, function(indice,valores){
           
               var option = $("<option />").attr({'value':valores.B1_UM});
               option.text(valores.B1_UM);
                       
               option.appendTo("#cmb_um");
           });
       },
       //error:problemas
   });
   
}

function cmb_segum(){
    $.ajax({
       url:'maestro_articulos.php',
       type: 'GET',
       dataType: 'json',
       //contentType: false,
       data:{'cargar_segum':'cargar_segum'},
       //processData: false,
       //cache: false
       //beforeSend:cargando,
       success:function(data){
           $.each(data, function(indice,valores){
           
               var option = $("<option />").attr({'value':valores.B1_SEGUM});
               option.text(valores.B1_SEGUM);
                       
               option.appendTo("#cmb_2um");
           });
       },
       //error:problemas
   });
   
}

function cmb_proveedor(){
    $.ajax({
       url:'maestro_articulos.php',
       type: 'GET',
       dataType: 'json',
       //contentType: false,
       data:{'cargar_proveerdor':'cargar_proveerdor'},
       //processData: false,
       //cache: false
       //beforeSend:cargando,
       success:function(data){
           $.each(data, function(indice,valores){
           
               var option = $("<option />").attr({'value':valores.B1_COD});
               option.text(valores.DPROVEED);
                       
               option.appendTo("#cmb_proveedor");
              
           });
       },
       //error:problemas
   });
   
}

function cmb_marca(){
    $.ajax({
       url:'maestro_articulos.php',
       type: 'GET',
       dataType: 'json',
       //contentType: false,
       data:{'cargar_marca':'cargar_marca'},
       //processData: false,
       //cache: false
       //beforeSend:cargando,
       success:function(data){
           $.each(data, function(indice,valores){
           
               var option = $("<option />").attr({'value':valores.B1_MARCA});
               option.text(valores.DMARCA);
                       
               option.appendTo("#cmb_marca");
           });
       },
       //error:problemas
   });
   
}

function cmb_linea(){
    $.ajax({
       url:'maestro_articulos.php',
       type: 'GET',
       dataType: 'json',
       //contentType: false,
       data:{'cargar_linea':'cargar_linea'},
       //processData: false,
       //cache: false
       //beforeSend:cargando,
       success:function(data){
           $.each(data, function(indice,valores){
           
               var option = $("<option />").attr({'value':valores.B1_LINEA});
               option.text(valores.DLINEA);
                       
               option.appendTo("#cmb_linea");
           });
       },
       //error:problemas
   });
   
}

function cmb_temporada(){
    $.ajax({
       url:'maestro_articulos.php',
       type: 'GET',
       dataType: 'json',
       //contentType: false,
       data:{'cargar_temporada':'cargar_temporada'},
       //processData: false,
       //cache: false
       //beforeSend:cargando,
       success:function(data){
           $.each(data, function(indice,valores){
           
               var option = $("<option />").attr({'value':valores.B1_TEMPORA});
               option.text(valores.DTEMPORADA);
                       
               option.appendTo("#cmb_temporada");
           });
       },
       //error:problemas
   });
   
}

function cargarDatos(){
    //limpiarForm();
    var txtEstilo = $("#txt_estilo").val();
    
    //var rut_cliente =  rut_clientec.substring(0,9);
        
    //var local = rut_clientec.substring(13,15);
        
        //alert(local);
    $.ajax({
        url:'maestro_articulos.php',
        type: 'GET',
        dataType: 'json',
        //contentType: false,
        data:{'cargar':'cargar','estilo':txtEstilo},
        //processData: false,
        //cache: false
        //beforeSend:cargando,
        success:function(jsonphp){
            //$("#frm_usuario");
            $.each(jsonphp,function(indice, valores){
                if (indice=='CODIGO') {
                
                    $("#txt_descripcion").val(valores.DESCRI);
                    $("#cmb_familia").val(valores.FAMILIA);

                    cmb_clase();

                    $("#cmb_clase").val(valores.CLASE);

                    $("#cmb_bodega").val(valores.BODEGA);   //NOT READONLY
                    $("#cmb_proveedor").val(valores.PROVEEDOR);
                    $("#cmb_marca").val(valores.MARCA);
                    $("#cmb_linea").val(valores.LINEA);
                    $("#cmb_composicion").val(valores.COMPOSI);
                    $("#cmb_temporada").val(valores.TEMPORA);
                    $("#cmb_maquina").val(valores.MAQUINA); //NOT READONLY
                    
                    $("#ctaContable").val(valores.CONTA); //NOT READONLY
                    $("#ctaVenta").val(valores.CONTAV); //NOT READONLY
                    $("#ctaCosto").val(valores.CONTAC); //NOT READONLY
                    $("#itemGasto").val(valores.ITEMCC); //NOT READONLY
                    $("#centroCosto").val(valores.CC); //NOT READONLY
                    $("#claseValor").val(valores.CLVL); //NOT READONLY

                    //$("#barras").val(valores.BARRAS); 

                    $('#txt_descripcion').prop('readonly', 'readonly');
                    $('#cmb_familia').prop('disabled', 'disabled');
                    $('#cmb_clase').prop('disabled', 'disabled');
                    $('#cmb_proveedor').prop('disabled', 'disabled');
                    $('#cmb_marca').prop('disabled', 'disabled');
                    $('#cmb_linea').prop('disabled', 'disabled');
                    //$('#cmb_composicion').prop('disabled', 'disabled');
                    $('#cmb_temporada').prop('disabled', 'disabled');
                    //$('#cmb_maquina').prop('disabled', 'disabled');
                
                }

            });
        //$("html, body").animate({ scrollTop: 0 }, "fast");
        },
        //error:problemas
    });
}

function limpiarForm(){
    $('#cmb_origen').val("");
    $('#txt_estilo').val('');
    $('#cmb_tipo').val("");
    $('#txt_descripcion').val(''); $('#txt_descripcion').prop('readonly', false);
    $('#cmb_talla').val(""); $('#cmb_talla').prop('disabled', false);
    $('#txt_color').val(''); $('#txt_color').prop('readonly', false);
    $('#cmb_familia').val(""); $('#cmb_familia').prop('disabled', false);
    $('#cmb_clase').val(""); $('#cmb_clase').prop('disabled', false);
    $('#cmb_bodega').val(""); $('#cmb_bodega').prop('disabled', false);
    $('#cmb_proveedor').val(""); $('#cmb_proveedor').prop('disabled', false);
    $('#cmb_marca').val(""); $('#cmb_marca').prop('disabled', false);
    $('#cmb_linea').val(""); $('#cmb_linea').prop('disabled', false);
    $('#cmb_composicion').val(""); 
    $('#cmb_temporada').val(""); $('#cmb_temporada').prop('disabled', false);
    $('#cmb_maquina').val(""); 
    $('#cmb_um').val("");
    $('#cmb_2um').val("");
    $('#txt_conv').val('');

    $('#ctaContable').val('');
    $('#ctaVenta').val('');
    $('#ctaCosto').val('');
    $('#itemGasto').val('');
    $('#centroCosto').val('');
    $('#claseValor').val('');

    $('#txt_estilo').focus();

}

function limpiarTbl(){
    $('#tbl_fcodigos tbody').empty();
}

function muestraGenero(){
    //var optOrigen = document.getElementById("cmb_origen").value;

    var optOrigen = $("#cmb_origen option:selected").val();  

    var divGenero = document.getElementById("div_genero");

    if (optOrigen === "NA") {
        divGenero.style.display = "none";
        $('#cmb_genero').val("NO");
        isSpecial();
    } else if (optOrigen === "IM") {
        divGenero.style.display = "inline";
    } else {
        divGenero.style.display = "none";        
        $('#cmb_genero').val("NO");
        isSpecial();
    }
}

function isSpecial(){
    var optGenero = $("#cmb_genero option:selected").val();  

    if (optGenero === "ES") {
        $('#cmb_talla').prop('disabled', 'disabled');
        $('#txt_color').prop('readonly', 'readonly');
        $('#txt_estilo').attr('maxlength', '25');
    } else{
        $('#cmb_talla').prop('disabled', false);
        $('#txt_color').prop('readonly', false);
        $('#txt_estilo').attr('maxlength', '6');
    }
}

function input_barra(){
    $.ajax({
       url:'maestro_articulos.php',
       type: 'GET',
       dataType: 'json',
       //contentType: false,
       data:{'carga_barra':'carga_barra'},
       //processData: false,
       //cache: false
       //beforeSend:cargando,
       success:function(data){
            $.each(data, function(indice,valores){
           
                $("#barras").val(valores.CODBARRA);
            
              
            });
       },
       //error:problemas
   });
   
}

function input_recno(){
    $.ajax({
       url:'maestro_articulos.php',
       type: 'GET',
       dataType: 'json',
       //contentType: false,
       data:{'carga_recno':'carga_recno'},
       //processData: false,
       //cache: false
       //beforeSend:cargando,
       success:function(data){
            $.each(data, function(indice,valores){
           
                $("#recno").val(valores.RECNO);
            
            });
       },
       //error:problemas
   });
   
}

function input_sequ(){
    $.ajax({
       url:'maestro_articulos.php',
       type: 'GET',
       dataType: 'json',
       //contentType: false,
       data:{'carga_sequ':'carga_sequ'},
       //processData: false,
       //cache: false
       //beforeSend:cargando,
       success:function(data){
            $.each(data, function(indice,valores){
           
                $("#sequ").val(valores.SEQU);
            
            });
       },
       //error:problemas
   });
   
}

