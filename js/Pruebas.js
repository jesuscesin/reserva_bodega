 $(document).ready(onLoad);
 function onLoad() {
	
	agregar_total_orden();
	$("#oc_proveedor").blur(carga_direccion);
	$("#oc_codArticulo").blur(carga_articulo);
	$("#btn_cargaBBDD").click(subir_formulario);
	$("#btn_cargaBBDD").click(subir_tabla);
}

//BOTONES PARA ABRIR Y CERRAR VENTANA MODAL PARA AGREGAR NUEVO PROVEEDOR
var btn_proveedor = document.querySelector("#btn_proveedor");
var pup_proveedores = document.querySelector("#pup_proveedores");
var dialForm = document.getElementById("frm_AggProveedor");
var btn_cerrarPupPro = document.querySelector("#btn_cerrarPupPro");
btn_proveedor.addEventListener("click",()=>{pup_proveedores.showModal();})
btn_cerrarPupPro.addEventListener("click",()=>{dialForm.reset(); pup_proveedores.close();})

//EVENTO PARA AGREGAR UNA FILA EN TABLA DETALLE DE ORDEN DE COMPRA
var btn_AggProd = document.getElementById('btn_AggProd');
btn_AggProd.addEventListener("click", (e) =>{
	validador();
	e.preventDefault();//EVITA LA RECARGA DE LA PAGINA AL HACER CLICK EN BOTON AGGPROD
	const form = document.getElementById("frm_orcom");
	let form_ordenCompraRef = new FormData(form);
	agrega_columnas(form_ordenCompraRef);
})

//EVENTOS PARA GUARDAR DATOS DE PROVEEDOR Y DETALLES DE TABLA EN BASE DE DATOS
var btn_cargaBBDD = document.getElementById('btn_cargaBBDD');
btn_cargaBBDD.addEventListener("click", (e) => {subir_formulario})
btn_cargaBBDD.addEventListener("click", (e) => {subir_tabla})

//EVENTO CLICK PARA LISTA DE PROVEEDORES
//RELLENA CAMPO OC_PROVEEDOR CON EL RUT DEL PROVEEDOR SELECICONADO
var listaProv = document.getElementById('listaProv');
listaProv.addEventListener('click', (e) => {
	if (e.target && e.target.tagName == 'LI'){
		var valor = e.target.innerHTML.trim().replace('"','');
		let indice = valor.indexOf(" ");//ENCUENTRA POSICION DEL ESPACIO EN BLANCO PARA SEPARAR CADENA EN 2	
		$prove = document.querySelector("#oc_proveedor");
		$p =valor.substring(3,indice).trim();
		$prove.value = $p;//formatRut($p);
		listaProv.style.display = 'none';//LUEGO DEL CLICK, OCULTA LA LISTA
		$prove.focus();
		$prove.blur();
	}	
})

//EVENTO CLICK PARA LISTA DE OPCIONES DE COMPRA
//RELLENA LOS CAMPOS SELPAGO Y CONPAGO CON LA OPCION SELECCIONADA
var listaOP = document.getElementById('listaOP');
listaOP.addEventListener('click', (e) => {
	if (e.target && e.target.tagName == 'LI'){
		var valorA = e.target.innerHTML.trim().replace('"','');
		let indiceA = valorA.indexOf("-");//ENCUENTRA POSICION DE " - " PARA DIVIDIR CADENA EN 2
		document.getElementById('oc_selPago').value = valorA.substring(indiceA+2,valorA.length);
		document.getElementById('oc_conpago').value =valorA.substring(0,indiceA-1);
		listaOP.style.display = 'none';//LUEGO DEL CLICK, OCULTA LA LISTA
	}
})

//EVENTO CLICK PARA LISTA DE SOLICITANTES
var listaSoli = document.getElementById('listaSoli');
listaSoli.addEventListener('click', (e) => {
	if(e.target && e.target.tagName == 'LI'){
		var valorB = e.target.innerHTML.trim().replace('"','');
		document.getElementById('oc_solicita').value = valorB.substring(0,valorB.length).trim();
		listaSoli.style.display = 'none';//LUEGO DEL CLICK, OCULTA LA LISTA
	}
})

//EVENTO PARA SELECCION DE MONEDA DE PAGO
var oc_moneda = document.getElementById('oc_moneda');
oc_moneda.addEventListener('change', (e) => {
	document.getElementById('oc_selMoneda').value = oc_moneda.value;
})

var estado = document.getElementById('estado');
estado.addEventListener('change', (e)=>{
	var opcion = estado.value;
	if(opcion == 1){
		
	}
})

//EVENTO CLICK PARA LISTA DE ARTICULOS
//RELLENA CAMPO CODARTICULO 
var listaArti = document.getElementById('listaArti');
listaArti.addEventListener('click', (e) => {
	if (e.target && e.target.tagName == 'LI'){
		var valorC = e.target.innerHTML.trim().replace('"','');
		let indiceC = valorC.indexOf(" ");
		$arti = document.querySelector("#oc_codArticulo");
		$arti.value = valorC.substring(3,indiceC).trim();
		listaArti.style.display = 'none';//LUEGO DEL CLICK, OCULTA LA LISTA
		$arti.focus();//DA Y QUITA EL FOCO A INPUT CODARTICULO PARA ACTIVAR EVENTO QUE RELLENA DEMAS CAMPOS DE PROVEEDOR
		$arti.blur();
	}
})

//EVENTOS PARA CARGAR LISTAS DESPLEGABLES
document.getElementById("oc_conpago").addEventListener("keyup",lista_OpCompra);
document.getElementById("oc_proveedor").addEventListener("keyup",lista_proveedores);
document.getElementById("oc_solicita").addEventListener("keyup",lista_solicitante);
document.getElementById("oc_codArticulo").addEventListener("keyup",lista_articulo);

//FUNCION PARA AGREGAR FILAS DE CALCULO DE TOTALES/IVA/RETENCION/DESCUENTOS
function agregar_total_orden(){// AGREGA ROWS DE VALOR NETO, DESCUENTO, IVA Y VALOR TOTAL EN TABLA TBL_ORDCOMPRA
	var  tabla_ordComRef = document.querySelector("#tbl_ordcompra tbody");
	for(var i=1; i < 5; i++){
		var nuevaColumnaOrdCom = tabla_ordComRef.insertRow(-1);
		var a = 0;
		while(a < 9){			
			nueva_Celda = nuevaColumnaOrdCom.insertCell(a).outerHTML = "<th>-</th>";//RELLENA COLUMNAS 0 A 6 Y 8 CON GUION
			if(a==6){
				a+=2;
				if(i==1){nueva_Celda = nuevaColumnaOrdCom.insertCell(7).outerHTML = "<th>VALOR NETO $</th>";}
				if(i==2){nueva_Celda = nuevaColumnaOrdCom.insertCell(7).outerHTML = "<th>DESCUENTO $</th>";}
				if(i==3){nueva_Celda = nuevaColumnaOrdCom.insertCell(7).outerHTML = "<th>IVA % $</th>";}
				if(i==4){nueva_Celda = nuevaColumnaOrdCom.insertCell(7).outerHTML = "<th>VALOR TOTAL $</th>";}
			} else{a++;}
		}
	}	
}

// AGREGA ITEMS INGRESADOS POR USUARIO A TABLA TBL_ORDCOMPRA 
function agrega_columnas(form_ordenCompraRef){
	let  tabla_ordComRef = document.querySelector("#tbl_ordcompra tbody");
	var codArti = form_ordenCompraRef.get("oc_codArticulo");
	var descArti = form_ordenCompraRef.get("oc_descArticulo");
	var unidad = form_ordenCompraRef.get("oc_docenas");
	var cantidad = form_ordenCompraRef.get("oc_unidades");
	var precio = form_ordenCompraRef.get("oc_precio");
	var descuento = form_ordenCompraRef.get("oc_descuento");
	var descExt = form_ordenCompraRef.get("txt_descExt");
	var estado = document.getElementById('estado').value;

	var ct =1;
	var preDesc = 0.0;
	var sumaDesc = 0.0;
	if((codArti == "" || descArti == "" || cantidad == "" || precio == "")){//VALIDACION PARA NO INGRESAR CAMPOS VACIOS
		Swal.fire({
			icon: 'error',
			title: 'Error',
			text: 'Debe completar todos los datos'
		})
	}else{
		if((cantidad<0 || precio<0)){//VALIDACION DE CANTIDAD Y PRECIO VALIDO
			Swal.fire({
				icon: 'error',
				title: 'Error',
				text: 'Debe ingresar una cantidad o precio valido!'
			})
		}else{	
			if((descuento<0 || descuento > 99)){//VALIDACION DE DESCUENTO VALIDO
				Swal.fire({
					icon: 'error',
					title: 'Error',
					text: 'Ingrese un descuento valido'
				})
			}else if(estado == 0){//VALIDA SELECCION DE FACTURA O BOLETA DE HONORARIOS
				Swal.fire({
					icon: 'error',
					title: 'Error',
					text: 'Ingrese el tipo de documento'
				})
			}else{
				let fila = $("#tbl_ordcompra").find('tbody tr').length;//OBTIENE CANTIDAD DE FILAS DE LA TABLA EN VARIABLE FILA
				//EN UN COMIENZO EL LARGO ES DE 5(1-CABECERA / 2-FILA VALOR NETO / 3-FILA DESCUENTO / 4-FILA IVA/RETENCION / 5-FILA VALOR TOTAL)
				let nuevaColumnaOrdCom = tabla_ordComRef.insertRow(fila-4);	
				
				nueva_Celda = nuevaColumnaOrdCom.insertCell(0);
				nueva_Celda.textContent = fila-3;	//NUMERO INDICE DE TABLA
				
				nueva_Celda = nuevaColumnaOrdCom.insertCell(1);
				nueva_Celda.textContent = codArti;	//CODIGO ARTICULO 
				if(descExt != ""){
					nueva_Celda = nuevaColumnaOrdCom.insertCell(2);
					nueva_Celda.outerHTML = '<td><h6 class="media-heading"><strong>'+descArti+'</strong></h6>'+'<p style="font-size:13px; margin-bottom=auto;" class="text-muted">'+' - '+descExt+'</small></p></td>';	//DESCPRIPCION ARTICULO
				}else{
					nueva_Celda = nuevaColumnaOrdCom.insertCell(2);
					nueva_Celda.outerHTML = '<td><h6 class="media-heading"><strong>'+descArti+'</strong></h6></td>';
				}				
				nueva_Celda = nuevaColumnaOrdCom.insertCell(3);
				nueva_Celda.textContent = unidad.trim();	//UNIDAD DE MEDIDA DEL ARTICULO
				
				nueva_Celda = nuevaColumnaOrdCom.insertCell(4);
				nueva_Celda.outerHTML = '<td align="right">'+cantidad.trim()+'</td>';	//CANTIDAD DE ARTICULOS 
				
				nueva_Celda = nuevaColumnaOrdCom.insertCell(5);
				nueva_Celda.outerHTML = '<td align="right">'+number_format(precio,'0','.','.')+'</td>';	//PRECIO UNITARIO DEL ARTICULO
				let total = precio*cantidad;

				if((descuento == null || descuento == "")){		
					nueva_Celda = nuevaColumnaOrdCom.insertCell(6);
					nueva_Celda.outerHTML = '<td align="right">'+"0%"+'</td>'; //DESCUENTO APLICADO EN PORCENTAJE (CASO EN EL QUE DESCUENTO = 0)	
					
					nueva_Celda = nuevaColumnaOrdCom.insertCell(7);
					nueva_Celda.outerHTML = '<td align="right">'+0+'</td>'; //DESCUENTO APLICADO EN PESOS (CASO EN EL QUE DESCUENTO = 0)

					nueva_Celda = nuevaColumnaOrdCom.insertCell(8); //VALOR NETO DE FILA DE ALTICULO (CANTIDAD X PRECIO UNITARIO)
					nueva_Celda.outerHTML = '<td align="right">'+number_format(total,'0','.','.')+'</td>';

				}else if(descuento>0){
					nueva_Celda = nuevaColumnaOrdCom.insertCell(6);
					nueva_Celda.outerHTML = '<td align="right">'+descuento+"%"+'</td>'; //DESCUENTO APLICADO EN PORCENTAJE (CASO EN EL QUE DESCUENTO > 0)
					
					preDesc = (total*(descuento/100));
					nueva_Celda = nuevaColumnaOrdCom.insertCell(7);
					nueva_Celda.outerHTML = '<td align="right">'+number_format(preDesc,'0','.','.')+'</td>';//DESCUENTO APLICADO EN PESOS (CASO EN EL QUE DESCUENTO > 0)

					total = total - preDesc;
					nueva_Celda = nuevaColumnaOrdCom.insertCell(8); //VALOR NETO DE FILA DE ALTICULO (CANTIDAD X PRECIO UNITARIO)
					nueva_Celda.outerHTML = '<td align="right">'+number_format(total,'0','.','.')+'</td>';
				}
				var suma =0.0;
				for(let g = 1; g<fila-2;g++){
					let celdas = document.getElementById("tbl_ordcompra").rows[g].cells;
					//RECORRE TABLA EN LA POSICION 7 Y 8 PARA SUMAR DESCUENTOS Y TOTALES NETOS INDIVIDUALES
					if(celdas.outerHTML == '<>'){
						g++;
					}
					celdas = document.getElementById("tbl_ordcompra").rows[g].cells;			
					suma = suma + parseInt((celdas[8].innerHTML).replaceAll(".",""),10);
					let celdas2 = document.getElementById("tbl_ordcompra").rows[g].cells;
					sumaDesc = sumaDesc + parseInt((celdas2[7].innerHTML).replaceAll(".",""),10);					
				}
				while(ct<=fila-3){				
					ct++;
					let celdaM3 = document.getElementById('tbl_ordcompra').rows[fila-2].cells;
					//LLENA CELDAS DE TABLA CON VALORES CALCULADOS ANTERIORMENTE
					celdaM3[8].outerHTML = '<td align="right">'+number_format(suma,'0','.','.')+'</td>';
					
					let celdaM2 = document.getElementById('tbl_ordcompra').rows[fila-1].cells;
					celdaM2[8].outerHTML = '<td align="right">'+number_format(sumaDesc,'0','.','.')+'</td>';
					
					if(estado == 1){//CASO BOLETRA DE HONORARIOS
						let celdaM1 = document.getElementById('tbl_ordcompra').rows[fila].cells;
						celdaM1[7].outerHTML = '<th>RETENCIÓN $</th>';
						celdaM1[8].outerHTML = '<td align="right">'+number_format((suma*0.13),'0','.','.')+'</td>';//RETENCION DE 13%
						let celdaM0 = document.getElementById('tbl_ordcompra').rows[fila+1].cells;
						celdaM0[8].outerHTML = '<td align="right">'+number_format((suma/0.87),'0','.','.')+'</td>';
					}else if(estado == 2){//CASO FACTURA
						let celdaM1 = document.getElementById('tbl_ordcompra').rows[fila].cells;
						celdaM1[7].outerHTML = '<th>IVA 19% $</th>';
						celdaM1[8].outerHTML = '<td align="right">'+number_format((suma*0.19),'0','.','.')+'</td>';
						let celdaM0 = document.getElementById('tbl_ordcompra').rows[fila+1].cells;
						celdaM0[8].outerHTML = '<td align="right">'+number_format((suma + (suma*0.19)),'0','.','.')+'</td>';
					}					
				}		
				limpiar_campos_ingreso();			
			}
		}			
	}
}

//LIMPIA CAMPOS DE INGRESO DE ITEMS
function limpiar_campos_ingreso(){
	document.getElementById("oc_codArticulo").value = "";
	document.getElementById("oc_descArticulo").value = "";
	document.getElementById("oc_docenas").value = "";
	document.getElementById("oc_unidades").value = "";
	document.getElementById("oc_precio").value = "";
	document.getElementById("oc_descuento").value = "";
	document.getElementById("txt_descExt").value = "";
}

// LIMPIA CAMPOS FORMULARIO PRINCIPAL 
function limpiar_campos_formulario(){
	document.getElementById("oc_numero").value = "";
	document.getElementById("oc_fechem").value = "";
	document.getElementById("oc_proveedor").value = "";
	document.getElementById("oc_desPro").value = "";
	document.getElementById("oc_conpago").value = "";
	document.getElementById("oc_selPago").value = "";
	document.getElementById("oc_contacto").value = "";
	document.getElementById("oc_pentrega").value = "";
	document.getElementById("oc_moneda").value = "";
	document.getElementById("oc_solicita").value = "";
}

// VISUALIZACION DE LISTA DE CONDICIONES DE PAGO
function lista_OpCompra(){
    var minimo_letras = 0;
	var palabra = $('#oc_conpago').val();
    
	if (palabra.length > minimo_letras) {
		$.ajax({
			url: 'Pruebas.php',
			type: 'POST',
			data: {palabra:palabra},
			success:function(data){
				$('#listaOP').show();
				$('#listaOP').html(data);
			}
		});
	} else {
		//ocultamos la lista
		$('#listaOP').hide();
	}
}

// VISUALIZACION DE LISTA DE PROVEEDORES FILTRADO POR RUT
function lista_proveedores(){
    var minimo_letras = 0;
	var palabraPr = $('#oc_proveedor').val();
	//Contamos el valor del input mediante una condicional
	if (palabraPr.length > minimo_letras) {
		$.ajax({
			url: 'Pruebas.php',
			type: 'POST',
			data: {palabraPr:palabraPr},
			success:function(data){
				$('#listaProv').show();
				$('#listaProv').html(data);
			}
		});
	} else {
		//ocultamos la lista
		$('#listaProv').hide();
	}
}

// VISUALIZACION DE LISTA DE QUIEN SOLICITA ORDEN DE COMPRA
function lista_solicitante(){
	var minimo_letras = 0;
	var palabraSol = $('#oc_solicita').val();	
	if(palabraSol.length > minimo_letras){
		$.ajax({
			url: 'Pruebas.php',
			type: 'POST',
			data: {palabraSol:palabraSol},
			success:function(data){
				$('#listaSoli').show();
				$('#listaSoli').html(data);
			}
		});
	} else {
		//ocultamos la lista
		$('#listaSoli').hide();
	}
}

// VISUALIZACION DE LISTA DE ARTICULOS FILTRADO POR CODIGO DE ARTICULO
function lista_articulo(){
	var minimo_letras = 0;
	var palabraArti = $('#oc_codArticulo').val();
	if(palabraArti.length > minimo_letras){
		$.ajax({
			url: 'Pruebas.php',
			type: 'POST',
			data: {palabraArti:palabraArti},
			success:function(data){
				$('#listaArti').show();
				$('#listaArti').html(data);
			}
		});
	} else {
		$('#listaArti').hide();
	}
}

//CARGA DIRECCION DE PROVEEDOR
//ASIGNA VALORES A CAMPOS DE DIRECCION, CONTACTO, DONDICION DE PAGO, NOMBREE DE PROVEEDOR Y RUT DE PROVEEDOR
function carga_direccion(){ 
	var oc_proveedor = $("#oc_proveedor").val();
	$.ajax({
		url: 'Pruebas.php',
		type: 'GET',
		dataType: 'json',
		data:{'cargar':'cargar','oc_proveedor':oc_proveedor},
		success:function(jsonphp){
			$.each(jsonphp,function(indice, valores){
				var newDirec = (valores.DIRECCION.trim())+", "+(valores.COMUNA.trim())+", "+(valores.REGION.trim());
				$('#oc_pentrega').val(newDirec);
				$('#oc_contacto').val(valores.CONTACTO.trim());
				$('#oc_conpago').val(valores.CONDICION.trim());				
				$('#oc_desPro').val(valores.DESCRIPCION.trim());
				$('#oc_proveedor').val(formatRut(valores.RUT));
			});
		},
	});
}

// CARGA DESCRIPCION Y UNIDAD DE MEDIDA DE ARTICULO
function carga_articulo(){
	var oc_codArticulo = $("#oc_codArticulo").val();
	$.ajax({
		url: 'Pruebas.php',
		type: 'GET',
		dataType: 'json',
		data:{'cargaArti':'cargaArti','oc_codArticulo':oc_codArticulo},
		success:function(jsonphp){
			$.each(jsonphp,function(indice, valores){
				$('#oc_docenas').val(valores.MEDIDA.trim());				
				$('#oc_descArticulo').val(valores.DESCRIPCION.trim());
			});
		},
	});
}

// VALIDA SI EXISTE O NO UNA ORDEN DE COMPRA CON EL MISMO NUMERO IDENTIFICADOR
function validador(){
	var oc_validacion2 = $("#oc_validacion2").val();
	$.ajax({
		url: 'Pruebas.php',
		type: 'GET',
		dataType: 'json',
		data:{'cargador':'cargador','oc_validacion2':oc_validacion2},
		success:function(jsonphp){
			$.each(jsonphp,function(indice, valores){
				$('#oc_validacion2').val(valores.NUMERO.trim());
			});
		},
	});
}

// CHECKBOX PARA ACTIVAR Y DESACTIVAR CAMPO DE DESCUENTOS 
document.getElementById('oc_Checkdescuento').addEventListener("click", activa_descuento);
function activa_descuento(){
	var checkBox = document.querySelector("#oc_Checkdescuento");
	var input = document.querySelector("#oc_descuento");
	if(checkBox.checked) {
		input.disabled = false;
	} else{
		input.disabled = true;
		input.value ="";
	}
}

// SUBE ROWS DE TABLA TBL_ORDCOMPRA A BASE DE DATOS 
function subir_tabla(){
	
	var filas=[];		
	let tabla = document.getElementById("tbl_ordcompra");
	const frm = document.getElementById("frm_orcom");
	let frmRef = new FormData(frm);
	var c = frmRef.get("oc_fechem");
	var d = frmRef.get("oc_proveedor");
	var e = frmRef.get("oc_conpago");
	var f = frmRef.get("oc_moneda");
	var g = frmRef.get("oc_solicita");
	var descExt = frmRef.get("txt_descExt");

	if((c == "" || d == "" || e == "" || f == "" || g == "")){
		Swal.fire({
			icon: 'error',
			title: 'Error',
			text: 'Debe completar todos los campos'
		})
	}else{
		let indice = $("#tbl_ordcompra").find('tbody tr').length;
		//RECORRE TABLA Y GUARDA LOS DATOS EN VARIABLE FILA
		for(let i=1; i < indice-3; i++){
			var nArticulo = tabla.rows[i].cells[0].innerHTML;
			var codProdu = tabla.rows[i].cells[1].innerHTML;
			var descProdu = tabla.rows[i].cells[2].textContent;
			var uniProd = tabla.rows[i].cells[3].innerHTML;
			var cantiProd = tabla.rows[i].cells[4].innerHTML.replaceAll(".","");
			var prcUniProdu = tabla.rows[i].cells[5].innerHTML.replaceAll(".","");
			var descuPor = tabla.rows[i].cells[6].innerHTML;
			var descuVal = tabla.rows[i].cells[7].innerHTML.replaceAll(".","");
			var totalUni = tabla.rows[i].cells[8].innerHTML.replaceAll(".","");
			var fila ={nArticulo,codProdu, descProdu, uniProd, cantiProd, prcUniProdu, descuPor, descuVal, totalUni};
			filas.push(fila);
		}		
		$.ajax({
			type: "POST",
			url: "Pruebas.php",
			data: {valores: JSON.stringify(filas)},
			success: function(data){
			console.log(data);
			}
		});
		//UNA VEZ SE GUARDAN TODAS FILAS DE LA TABLA, SE REINICIA EL FORMULARIO Y LA TABLA
		frm.reset();
		for(let a=indice-4; a>=1; a--){tabla.deleteRow(a);}
		for(let b=1; b<6; b++){tabla.rows[b].cells[8].innerHTML = "";}	
	}	
}

// SUBE FORMULARIO PRINCIPAL A BASE DE DATOS
function subir_formulario(){
	var filasEN=[];
	//OBTIENE VALORES DE CAMPOS DE PROVEEDOR (ENCABEZADO) Y LOS GUARDA EN VARIABLE FILAEN
	var fechem 			= document.getElementById("oc_fechem").value;
	var proveedor 		= document.getElementById("oc_proveedor").value;
	var desPro 			= document.getElementById("oc_desPro").value;
	var conpago			= document.getElementById("oc_conpago").value;
	var selPago 		= document.getElementById("oc_selPago").value;	
	var contacto 		= document.getElementById("oc_contacto").value;
	var pentrega 		= document.getElementById("oc_pentrega").value;
	var selMoneda 		= document.getElementById("oc_selMoneda").value;
	var solicita 		= document.getElementById("oc_solicita").value;
	var observaciones 	= document.getElementById("txt_observaciones").value;
	var tipo 			= document.getElementById("estado").value;
	var tipoDesc;
	if(tipo == 1){
		tipoDesc = 'BOLETA DE HONORARIOS';
	}else if(tipo ==2){tipoDesc = 'FACTURA'}

	var filaEN = {fechem, proveedor, desPro,conpago, selPago, contacto, pentrega, selMoneda, solicita, observaciones, tipo, tipoDesc};
	filasEN.push(filaEN);
	$.ajax({
		type: "POST",
		url: "Pruebas.php",
		data: {valor: JSON.stringify(filasEN)},
		success: function(data){
			console.log(data);
		}
	});
	var numero = document.getElementById("oc_validacion2").value;//OBTIENE EL NUMERO DE ORDEN DE COMPRA QUE SE LE ASIGNARÁ A LA ACTUAL

	var swalBtn = Swal.mixin({
		customClass: {
		  confirmButton: 'btn btn-success',
		  cancelButton: 'btn btn-danger'
		},
		buttonsStyling: false
	})
	swalBtn.fire({
		icon: 'success',
		title: 'Exito',
		text: "Quiere generar un PDF de la orden de compra N° "+numero+"?",
		showConfirmButton: true,
		confirmButtonText: 'Generar',
		showDenyButton: true,
		showCancelButton: true,
		cancelButtonText: 'No, cancelar!'
	}).then((result) =>{
		if(result.isConfirmed = true){
			swalBtn.fire({
				type: 'info',
				title: 'Exito!',
				text: 'El archivo PDF fue generado exitosamente'
			}).then(function(){
				window.open("genera_pdf.php/?num="+numero+"","Nueva ventana",'width=600,height=900');
			});
			}else if (result.isDenied = true){
			swalBtn.fire({
				type: 'info',
				title: 'Cancelado',
				text: 'No se ha generado archivo PDF'
			})
		}
	})
}


function number_format(number, decimals, dec_point, thousands_point){
	//XXX.XXX.XXX
    if (number == null || !isFinite(number)) {
        throw new TypeError("Numero no valido");
    }
    if (!decimals) {
        var len = number.toString().split('.').length;
        decimals = len > 1 ? len : 0;
    }
    if (!dec_point) {
        dec_point = '.';
    }
    if (!thousands_point) {
        thousands_point = ',';
    }
    number = parseFloat(number).toFixed(decimals);
    number = number.replace(".", dec_point);
    var splitNum = number.split(dec_point);
    splitNum[0] = splitNum[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands_point);
    number = splitNum.join(dec_point);
    return number;
}


function formatRut(rut){
	// XX.XXX.XXX-X
	const newRut = rut.replace(/\./g,'').replace(/\-/g, '').trim().toLowerCase();
	const lastDigit = newRut.substr(-1, 1);
	const rutDigit = newRut.substr(0, newRut.length-1)
	let format = '';
	for (let i = rutDigit.length; i > 0; i--) {
	  const e = rutDigit.charAt(i-1);
	  format = e.concat(format);
	  if (i % 3 === 0){
		format = '.'.concat(format);
	  }
	}
	return format.concat('-').concat(lastDigit);
}

//ARRAY DE COMUNAS Y REGIONES DE CHILE PARA SELECCION EN VENTANA MODAL AGGPROVEEDOR
var RegionesYcomunas = {

	"regiones": [{
			"NombreRegion": "Arica y Parinacota",
			"comunas": ["Arica", "Camarones", "Putre", "General Lagos"]
	},
		{
			"NombreRegion": "Tarapacá",
			"comunas": ["Iquique", "Alto Hospicio", "Pozo Almonte", "Camiña", "Colchane", "Huara", "Pica"]
	},
		{
			"NombreRegion": "Antofagasta",
			"comunas": ["Antofagasta", "Mejillones", "Sierra Gorda", "Taltal", "Calama", "Ollagüe", "San Pedro de Atacama", "Tocopilla", "María Elena"]
	},
		{
			"NombreRegion": "Atacama",
			"comunas": ["Copiapó", "Caldera", "Tierra Amarilla", "Chañaral", "Diego de Almagro", "Vallenar", "Alto del Carmen", "Freirina", "Huasco"]
	},
		{
			"NombreRegion": "Coquimbo",
			"comunas": ["La Serena", "Coquimbo", "Andacollo", "La Higuera", "Paiguano", "Vicuña", "Illapel", "Canela", "Los Vilos", "Salamanca", "Ovalle", "Combarbalá", "Monte Patria", "Punitaqui", "Río Hurtado"]
	},
		{
			"NombreRegion": "Valparaíso",
			"comunas": ["Valparaíso", "Casablanca", "Concón", "Juan Fernández", "Puchuncaví", "Quintero", "Viña del Mar", "Isla de Pascua", "Los Andes", "Calle Larga", "Rinconada", "San Esteban", "La Ligua", "Cabildo", "Papudo", "Petorca", "Zapallar", "Quillota", "Calera", "Hijuelas", "La Cruz", "Nogales", "San Antonio", "Algarrobo", "Cartagena", "El Quisco", "El Tabo", "Santo Domingo", "San Felipe", "Catemu", "Llaillay", "Panquehue", "Putaendo", "Santa María", "Quilpué", "Limache", "Olmué", "Villa Alemana"]
	},
		{
			"NombreRegion": "Región del Libertador Gral. Bernardo O’Higgins",
			"comunas": ["Rancagua", "Codegua", "Coinco", "Coltauco", "Doñihue", "Graneros", "Las Cabras", "Machalí", "Malloa", "Mostazal", "Olivar", "Peumo", "Pichidegua", "Quinta de Tilcoco", "Rengo", "Requínoa", "San Vicente", "Pichilemu", "La Estrella", "Litueche", "Marchihue", "Navidad", "Paredones", "San Fernando", "Chépica", "Chimbarongo", "Lolol", "Nancagua", "Palmilla", "Peralillo", "Placilla", "Pumanque", "Santa Cruz"]
	},
		{
			"NombreRegion": "Región del Maule",
			"comunas": ["Talca", "ConsVtución", "Curepto", "Empedrado", "Maule", "Pelarco", "Pencahue", "Río Claro", "San Clemente", "San Rafael", "Cauquenes", "Chanco", "Pelluhue", "Curicó", "Hualañé", "Licantén", "Molina", "Rauco", "Romeral", "Sagrada Familia", "Teno", "Vichuquén", "Linares", "Colbún", "Longaví", "Parral", "ReVro", "San Javier", "Villa Alegre", "Yerbas Buenas"]
	},
		{
			"NombreRegion": "Región del Biobío",
			"comunas": ["Concepción", "Coronel", "Chiguayante", "Florida", "Hualqui", "Lota", "Penco", "San Pedro de la Paz", "Santa Juana", "Talcahuano", "Tomé", "Hualpén", "Lebu", "Arauco", "Cañete", "Contulmo", "Curanilahue", "Los Álamos", "Tirúa", "Los Ángeles", "Antuco", "Cabrero", "Laja", "Mulchén", "Nacimiento", "Negrete", "Quilaco", "Quilleco", "San Rosendo", "Santa Bárbara", "Tucapel", "Yumbel", "Alto Biobío", "Chillán", "Bulnes", "Cobquecura", "Coelemu", "Coihueco", "Chillán Viejo", "El Carmen", "Ninhue", "Ñiquén", "Pemuco", "Pinto", "Portezuelo", "Quillón", "Quirihue", "Ránquil", "San Carlos", "San Fabián", "San Ignacio", "San Nicolás", "Treguaco", "Yungay"]
	},
		{
			"NombreRegion": "Región de la Araucanía",
			"comunas": ["Temuco", "Carahue", "Cunco", "Curarrehue", "Freire", "Galvarino", "Gorbea", "Lautaro", "Loncoche", "Melipeuco", "Nueva Imperial", "Padre las Casas", "Perquenco", "Pitrufquén", "Pucón", "Saavedra", "Teodoro Schmidt", "Toltén", "Vilcún", "Villarrica", "Cholchol", "Angol", "Collipulli", "Curacautín", "Ercilla", "Lonquimay", "Los Sauces", "Lumaco", "Purén", "Renaico", "Traiguén", "Victoria", ]
	},
		{
			"NombreRegion": "Región de Los Ríos",
			"comunas": ["Valdivia", "Corral", "Lanco", "Los Lagos", "Máfil", "Mariquina", "Paillaco", "Panguipulli", "La Unión", "Futrono", "Lago Ranco", "Río Bueno"]
	},
		{
			"NombreRegion": "Región de Los Lagos",
			"comunas": ["Puerto Montt", "Calbuco", "Cochamó", "Fresia", "FruVllar", "Los Muermos", "Llanquihue", "Maullín", "Puerto Varas", "Castro", "Ancud", "Chonchi", "Curaco de Vélez", "Dalcahue", "Puqueldón", "Queilén", "Quellón", "Quemchi", "Quinchao", "Osorno", "Puerto Octay", "Purranque", "Puyehue", "Río Negro", "San Juan de la Costa", "San Pablo", "Chaitén", "Futaleufú", "Hualaihué", "Palena"]
	},
		{
			"NombreRegion": "Región Aisén del Gral. Carlos Ibáñez del Campo",
			"comunas": ["Coihaique", "Lago Verde", "Aisén", "Cisnes", "Guaitecas", "Cochrane", "O’Higgins", "Tortel", "Chile Chico", "Río Ibáñez"]
	},
		{
			"NombreRegion": "Región de Magallanes y de la Antártica Chilena",
			"comunas": ["Punta Arenas", "Laguna Blanca", "Río Verde", "San Gregorio", "Cabo de Hornos (Ex Navarino)", "AntárVca", "Porvenir", "Primavera", "Timaukel", "Natales", "Torres del Paine"]
	},
		{
			"NombreRegion": "Región Metropolitana",
			"comunas": ["Cerrillos", "Cerro Navia", "Conchalí", "El Bosque", "Estación Central", "Huechuraba", "Independencia", "La Cisterna", "La Florida", "La Granja", "La Pintana", "La Reina", "Las Condes", "Lo Barnechea", "Lo Espejo", "Lo Prado", "Macul", "Maipú", "Ñuñoa", "Pedro Aguirre Cerda", "Peñalolén", "Providencia", "Pudahuel", "Quilicura", "Quinta Normal", "Recoleta", "Renca", "San Joaquín", "San Miguel", "San Ramón", "Vitacura", "Puente Alto", "Pirque", "San José de Maipo", "Colina", "Lampa", "TilVl", "San Bernardo", "Buin", "Calera de Tango", "Paine", "Melipilla", "Alhué", "Curacaví", "María Pinto", "San Pedro", "Talagante", "El Monte", "Isla de Maipo", "Padre Hurtado", "Peñaflor"]
	}]
}
jQuery(document).ready(function () {

	var iRegion = 0;
	var htmlRegion = '<option value="sin-region">Seleccione región</option><option value="sin-region">--</option>';
	var htmlComunas = '<option value="sin-region">Seleccione comuna</option><option value="sin-region">--</option>';

	jQuery.each(RegionesYcomunas.regiones, function () {
		htmlRegion = htmlRegion + '<option value="' + RegionesYcomunas.regiones[iRegion].NombreRegion + '">' + RegionesYcomunas.regiones[iRegion].NombreRegion + '</option>';
		iRegion++;
	});

	jQuery('#regiones').html(htmlRegion);
	jQuery('#comunas').html(htmlComunas);

	jQuery('#regiones').change(function () {
		var iRegiones = 0;
		var valorRegion = jQuery(this).val();
		var htmlComuna = '<option value="sin-comuna">Seleccione comuna</option><option value="sin-comuna">--</option>';
		jQuery.each(RegionesYcomunas.regiones, function () {
			if (RegionesYcomunas.regiones[iRegiones].NombreRegion == valorRegion) {
				var iComunas = 0;
				jQuery.each(RegionesYcomunas.regiones[iRegiones].comunas, function () {
					htmlComuna = htmlComuna + '<option value="' + RegionesYcomunas.regiones[iRegiones].comunas[iComunas] + '">' + RegionesYcomunas.regiones[iRegiones].comunas[iComunas] + '</option>';
					iComunas++;
				});
			}
			iRegiones++;
		});
		jQuery('#comunas').html(htmlComuna);
	});

});