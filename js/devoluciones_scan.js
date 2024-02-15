$(document).ready(onLoad);
function onLoad() {
          get_recno();
          get_secuencia();
          // get_sesion();
          $("#btn_confirmar").click(confimar_scan);
          $("#tbl_scan").on('click','#btn_remover',removerAsig);
          $("#numero").focus();
		  ver_suma();
}
function minuscula() {
    var x = document.getElementById("numero");
    x.value = x.value.toLowerCase();
}
function ver_suma(){
	 var numero   = $("#asg-secuencia").val();
	 $.ajax({
        url:'devoluciones_scan.php',
        type: 'GET',
        dataType: 'json',
        //contentType: false,
        data:{'totales':'totales','numero':numero},
        //processData: false,
        //cache: false
        //beforeSend:cargando,
        success:function(data){
            $.each(data, function(indice,valores){
      
             //$("#fec_ini").val(valores.FECHA2);
             $("#suma_total").val(valores.SUMA);
            
            });
            //$("#fec_ini").focus();
        },
        //error:problemas
    });
}
function removerAsig() { //REMOVER ASIGNACION INGRESADA,NO DE LA BD
    //alert($(this).attr('cod'));
    codigo = $(this).attr('recno');
    numero = $(this).attr('numero');
    numFila = $(this).attr('numFila');
  
   Swal.fire({
          title: 'Terminar Proceso ?',
          text: "Se Iniciara el proceso de carga",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Si, Confirmar'
        }).then((result) => {
             if (result.isConfirmed) {
       $("#"+numFila).remove();
        $.ajax({
            url:'devoluciones_scan.php',
            type: 'GET',
            dataType: 'text',
            //contentType: false,
            data:{'eliminar_linea':'eliminar_linea','recno':numFila},
        });
               Swal.fire('Eliminado ! !', data, 'success');
            } else {
               Swal.fire('No Eliminado ! !', data, 'error');
            }
      });   
    //});  
}
function confimar_scan() { //REMOVER ASIGNACION INGRESADA,NO DE LA BD
    //alert($(this).attr('cod'));
   
     var numero   = $("#asg-secuencia").val();
    //correlativo = $(this).attr('correlativo');
    Swal.fire({
          title: 'Terminar Proceso ?',
          text: "Se Iniciara el proceso de carga",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Si, Confirmar'
        }).then((result) => {
             if (result.isConfirmed) {
                  $.ajax({
                 url:'devoluciones_scan.php',
                 type: 'GET',
                 dataType: 'text',
                 //contentType: false,
                 data:{'confirma_scan':'confirma_scan','numero':numero},
                  
                 //processData: false,
                 //cache: false
                 //beforeSend:cargando,
                  beforeSend:function(){
                 $("#mensajes").html('<img src="img/cargando2.gif">Cargando...</img>');
             },
             success:function(data){
                 $("#mensajes").html("<div class='alert alert-success' role='alert'>"+data+"</div>");
				  Swal.fire('Confirmado ! !', data, 'success');
                 $('#numero').val('');
              
                 $("#tbl_scan tbody tr").remove();
                 get_secuencia();
                 //cargaPage();
             },
                 //error:problemas
             });
        
            } else {
              Swal.fire('No Confirmado ! !', data, 'error');
            }
      });
                 //get_secuencia();
       
}
function get_recno() {
      
    //var bodega_origen = $("#bodega_origen").val();
    
    $.ajax({
        url:'devoluciones_scan.php',
        type: 'GET',
        dataType: 'json',
        //contentType: false,
        data:{'recno':'recno'},
        //processData: false,
        //cache: false
        //beforeSend:cargando,
        success:function(data){
            $.each(data, function(indice,valores){
      
             //$("#fec_ini").val(valores.FECHA2);
             $("#filas").val(valores.RECNO);
            
            });
            //$("#fec_ini").focus();
        },
        //error:problemas
    });
    
}
function get_secuencia() {
      
    //var bodega_origen = $("#bodega_origen").val();
    
    $.ajax({
        url:'devoluciones_scan.php',
        type: 'GET',
        dataType: 'json',
        //contentType: false,
        data:{'secuencia':'secuencia'},
        //processData: false,
        //cache: false
        //beforeSend:cargando,
        success:function(data){
            $.each(data, function(indice,valores){
      
             //$("#fec_ini").val(valores.FECHA2);
             $("#asg-secuencia").val(valores.ID);
            
            });
            //$("#fec_ini").focus();
        },
        //error:problemas
    });
    
}
function get_sesion() {
      
    //var bodega_origen = $("#bodega_origen").val();
    
    $.ajax({
        url:'devoluciones_scan.php',
        type: 'GET',
        dataType: 'json',
        //contentType: false,
        data:{'sesion':'sesion'},
        //processData: false,
        //cache: false
        //beforeSend:cargando,
        success:function(data){
            $.each(data, function(indice,valores){
      
             //$("#fec_ini").val(valores.FECHA2);
             $("#sesion").val(valores.SESION);
            
            });
            //$("#fec_ini").focus();
        },
        //error:problemas
    });
    
}
function validaAgregadosTbl() {
     //get_recno();
    fila = $("#filas").val();

    var numero = $("#numero").val();
    
    var contador = 0;
        //alert("numero : "+numero);
      //alert(fila);
    for(var i=0; i<=fila; i++){
        var tr_numero = $("#asg-idnumero").val();
            //alert("tr_numero : "+tr_numero);
        
        if (numero==tr_numero ) {//&& cantidad==tr_cant
            //alert(articulo);
            //alert(tr_art);
            contador = contador+1;
            //alert("contador : "+contador);
        }
    }
    /*if(in_filas === '0'){      
      contador = contador+1;
      
      alert("Articulo no existe en bodega de destino");
      
    }else*/ if (contador === 0) {
        return true;
    }else{
        //alert("Articulo ya ingresado !");
        //Swal.fire("Articulo ya ingresado !");
        let timerInterval;
      Swal.fire({
        title: 'PEDIDO YA INGRESADO',
        html: 'Ventana se cerrara en <b></b> ',
        timer: 2000,
        timerProgressBar: true,
        didOpen: () => {
          Swal.showLoading();
          //const b = Swal.getHtmlContainer().querySelector('b');
          //timerInterval = setInterval(() => {
          //  b.textContent = Swal.getTimerLeft();
          //}, 200);
        },
        willClose: () => {
          clearInterval(timerInterval);
        }
      }).then((result) => {
        /* Read more about handling dismissals below */
        if (result.dismiss === Swal.DismissReason.timer) {
          console.log('I was closed by the timer');
          $("#numero").val('');
        }
      });

    }
    
}
 function handle(e){
	 
	 
       get_recno();
      numero 	= $("#numero").val();
      guia 		= $("#guia").val();
      
	  
	  // alert(numero);
        if(e.keyCode === 13){
            e.preventDefault(); // Ensure it is only this code that rusn
            //alert("");
             $.ajax({
                url:'devoluciones_scan.php',
                type: 'GET',
                dataType: 'json',
                //contentType: false,
                data:{'lee_pedido':'lee_pedido','numero':numero},
                //processData: false,
                //cache: false
                //beforeSend:cargando,
                success:function(data){
                    //$("#frm_licencias_medicas");
                         //alert(data);
                      $.each(data,function(indice, valores){
                          if (indice=='CODIGO') {
                    if(valores.VALIDA == 99){
						
						
						
                             let timerInterval;
                                Swal.fire({
                                    title: 'UPC '+numero+' no existe en equivalencia ',
                                    html: '',
                                    timer: 3000,
                                    timerProgressBar: true,
                                    didOpen: () => {
                                      Swal.showLoading();
                                    },
                                    willClose: () => {
                                      clearInterval(timerInterval);
                                    }
                                  }).then((result) => {
                                    /* Read more about handling dismissals below */
                                    if (result.dismiss === Swal.DismissReason.timer) {
                                      console.log('I was closed by the timer');
                                      $("#numero").val('');
                                    }
                                  });  
								  var sound = new Howl({
							  src: ['sonido/error.wav'],
							  volume: 1.0,
							  onend: function () {
								// alert('We finished with the setup!');
							  }
							});
							sound.play()
                    }else{
                          //if(validaAgregadosTbl()){
                              
                              fila = $("#filas").val();
                              var numFila = fila;
                              
                            var upc      = valores.EQU_UPC;
                            var intcod   = valores.EQU_INTCOD;
                            var barcod   = valores.EQU_BARCOD;
                            var valida   = valores.VALIDA;
                            var origen   = valores.EQU_DCANAL;
                            var rut      = valores.EQU_RUT;
							var cantidad 	= $("#cantidad").val();
							  
                              // Swal.fire(upc);
                              
                              var btn_borrar = $("<button />").text("Remover");
                              btn_borrar.attr({'type':'button','class':'btn btn-block btn-outline-info btn-sm', 'id':'btn_remover','codigo':upc,'numero':numero,'numFila':numFila});
                              
                              var tr = $("<tr/>").attr('id',numFila);
                              
                              //$("<td/>").html(numFila).append($("<input/>").attr({'type':'hidden','value':numFila,'name':'asg-num_fila','id':'asg-num_fila'})).appendTo(tr);/*/*////////////*/*/////////////////////////tratar de pasar el numfila a php
                              $("<td/>").html(guia).append($("<input/>").attr({'type':'hidden','value':guia,'name':'asg-guia','id':'asg-guia'})).appendTo(tr);
                              $("<td/>").html(upc).append($("<input/>").attr({'type':'hidden','value':numero,'name':'asg-upc','id':'asg-upc'})).appendTo(tr);
                              $("<td/>").html(intcod).append($("<input/>").attr({'type':'hidden','value':intcod,'name':'asg-intcod','id':'asg-intcod'})).appendTo(tr);
                              $("<td/>").html(barcod).append($("<input/>").attr({'type':'hidden','value':barcod,'name':'asg-barcod','id':'asg-barcod'})).appendTo(tr);
                              $("<td/>").html(rut).append($("<input/>").attr({'type':'hidden','value':rut,'name':'asg-rut','id':'asg-rut'})).appendTo(tr);;
                              $("<td/>").html(origen).append($("<input/>").attr({'type':'hidden','value':origen,'name':'asg-origen','id':'asg-origen'})).appendTo(tr);
                              $("<td/>").html(cantidad).append($("<input/>").attr({'type':'hidden','value':cantidad,'name':'asg-cantidad','id':'asg-cantidad'})).appendTo(tr);
                              $("<td/>").html(numFila).append($("<input/>").attr({'type':'hidden','value':numFila,'name':'asg-filas','id':'asg-filas'})).appendTo(tr);
                              
                              //$("<td/>").html('').appendTo(tr);
                              $("<td/>").html(btn_borrar).appendTo(tr);
                              tr.appendTo("#tbl_scan");
                              insertarForm();
                              get_recno();
							  ver_suma();
                              $("#numero").val('');
                              $("#cantidad").val('1');
                              $("#numero").focus();
                          //}
                         
                         
					}
						  }
                            });
               //$("html, body").animate({ scrollTop: 1000 }, "fast");
                 //$("#descripcion_articulo").focus();
                },
                //error:problemas
            });
        }
        //return false;
}
function insertarForm() { //FUNCION DE INSERCION DE DATOS
    var fila   = $("#filas").val();//cantidad filas tabla asignacion
    var dataForm = $("form").serialize();
    //alert(dataForm);
    var data = dataForm+"&asg-max="+fila+"&insertar=insertar";
   //alert(fila);
    //alert(data);
    $.ajax({
        url:'devoluciones_scan.php',
        type: 'POST',
        dataType: 'text',
        //contentType: false,
        data:data,
        //processData: false,
        //cache: false
        beforeSend:function(){
            $("#mensajes").html('<img src="img/cargando2.gif">Cargando...</img>');
        },
        success:function(data){
            $("#mensajes").html("<div  role='alert'>"+data+"</div>");
        },
        error:function(data,estado,errorr){
            $("#mensajes").html("<div class='alert alert-danger' role='alert'>"+data+"_"+estado+"_"+errorr+"</div>");
        }
    });
    
}

